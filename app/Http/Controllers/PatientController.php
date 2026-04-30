<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    /**
     * List all patients (admin/staff only).
     */
    public function index(Request $request)
    {
        // Patients cannot view the full patient list
        if (auth()->user()->isPatient()) {
            return redirect()->route('patients.edit', auth()->user()->patient)
                ->with('info', 'You can only view and edit your own profile.');
        }

        $query = Patient::with('user');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name',     'like', '%' . $request->search . '%')
                  ->orWhere('last_name',    'like', '%' . $request->search . '%')
                  ->orWhere('patient_code', 'like', '%' . $request->search . '%');
            });
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('patients.index', compact('patients'));
    }

    /**
     * Show registration form (public — guests only).
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Handle patient self-registration (public route).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'date_of_birth' => 'required|date|before:today',
            'gender'        => 'required|in:male,female,other',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $user = User::create([
                    'name'      => $data['first_name'] . ' ' . $data['last_name'],
                    'email'     => $data['email'],
                    'password'  => Hash::make($data['password']),
                    'role'      => 'patient',
                    'is_active' => true,
                ]);

                $patCode = 'PAT-' . str_pad(Patient::count() + 1, 4, '0', STR_PAD_LEFT);

                Patient::create([
                    'user_id'       => $user->id,
                    'patient_code'  => $patCode,
                    'first_name'    => $data['first_name'],
                    'last_name'     => $data['last_name'],
                    'date_of_birth' => $data['date_of_birth'],
                    'gender'        => $data['gender'],
                    'phone'         => $data['phone'] ?? null,
                    'address'       => $data['address'] ?? null,
                ]);

                Auth::login($user);
            });

            return redirect()->route('dashboard')
                ->with('success', 'Welcome! Your account has been created successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Registration failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show a patient profile.
     * Patients can only view their own profile.
     * Admin/Staff can view any profile.
     */
    public function show(Patient $patient)
    {
        // If the logged-in user is a patient, only allow viewing their own profile
        if (auth()->user()->isPatient()) {
            $ownPatient = auth()->user()->patient;
            if (!$ownPatient || $ownPatient->id !== $patient->id) {
                return redirect()->route('patients.show', $ownPatient)
                    ->with('error', 'You can only view your own profile.');
            }
        }

        $patient->load(['user', 'appointments.doctor.department', 'queues.department']);

        return view('patients.show', compact('patient'));
    }

    /**
     * Show edit form.
     * Patients can only edit their own profile.
     * Admin/Staff can edit any profile.
     */
    public function edit(Patient $patient)
    {
        // Patients can only edit their own profile
        if (auth()->user()->isPatient()) {
            $ownPatient = auth()->user()->patient;
            if (!$ownPatient || $ownPatient->id !== $patient->id) {
                return redirect()->route('patients.edit', $ownPatient)
                    ->with('error', 'You can only edit your own profile.');
            }
        }

        return view('patients.edit', compact('patient'));
    }

    /**
     * Update patient profile.
     * Patients can only update their own profile.
     */
    public function update(Request $request, Patient $patient)
    {
        // Patients can only update their own profile
        if (auth()->user()->isPatient()) {
            $ownPatient = auth()->user()->patient;
            if (!$ownPatient || $ownPatient->id !== $patient->id) {
                return redirect()->route('patients.edit', $ownPatient)
                    ->with('error', 'You can only edit your own profile.');
            }
        }

        $data = $request->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'date_of_birth'   => 'required|date|before:today',
            'gender'          => 'required|in:male,female,other',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:500',
            'medical_history' => 'nullable|string',
        ]);

        $patient->update($data);
        $patient->user->update([
            'name' => $data['first_name'] . ' ' . $data['last_name'],
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete a patient (admin only).
     * Marks user as inactive — does NOT permanently delete.
     */
    public function destroy(Patient $patient)
    {
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Only admins can delete patient records.');
        }

        $name = $patient->full_name;

        // Soft delete: deactivate instead of permanently removing
        $patient->user->update(['is_active' => false]);

        return redirect()->route('patients.index')
            ->with('success', "{$name}'s account has been deactivated. Data is preserved.");
    }
}
