<?php

namespace App\Http\Controllers;

use App\Models\ArchivedPatient;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    /**
     * Check if current user is admin or staff.
     */
    private function isAdminOrStaff(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isStaff();
    }

    /**
     * List active patients (admin/staff only).
     */
    public function index(Request $request)
    {
        if (auth()->user()->isPatient()) {
            $patient = auth()->user()->patient;
            if ($patient) return redirect()->route('patients.show', $patient);
            return redirect()->route('dashboard')->with('error', 'Patient profile not found.');
        }

        $query = Patient::whereNull('patients.deleted_at')->with('user');

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
     * Show registration form (guests only).
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Handle patient self-registration.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
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

                $count   = Patient::withTrashed()->count() + 1;
                $patCode = 'PAT-' . str_pad($count, 4, '0', STR_PAD_LEFT);

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
     * Show patient profile.
     * Admin/Staff can view any. Patients can only view their own.
     */
    public function show(Patient $patient)
    {
        if (auth()->user()->isPatient()) {
            $own = auth()->user()->patient;
            if (!$own || $own->id !== $patient->id) {
                return redirect()->route('patients.show', $own)
                    ->with('error', 'You can only view your own profile.');
            }
        }

        $patient->load(['user', 'appointments.doctor.department', 'queues.department']);

        return view('patients.show', compact('patient'));
    }

    /**
     * Show edit form.
     * Admin/Staff can edit any. Patients can only edit their own.
     */
    public function edit(Patient $patient)
    {
        if (auth()->user()->isPatient()) {
            $own = auth()->user()->patient;
            if (!$own || $own->id !== $patient->id) {
                return redirect()->route('patients.edit', $own)
                    ->with('error', 'You can only edit your own profile.');
            }
        }

        return view('patients.edit', compact('patient'));
    }

    /**
     * Update patient profile.
     * Admin/Staff can update any. Patients can only update their own.
     */
    public function update(Request $request, Patient $patient)
    {
        if (auth()->user()->isPatient()) {
            $own = auth()->user()->patient;
            if (!$own || $own->id !== $patient->id) {
                return redirect()->route('patients.edit', $own)
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
     * SOFT DELETE a patient (admin/staff).
     */
    public function destroy(Patient $patient)
    {
        if (!$this->isAdminOrStaff()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $name      = $patient->full_name;
        $patientId = $patient->id;
        $userId    = $patient->user_id;

        Patient::where('id', $patientId)->update(['deleted_at' => now()]);
        User::where('id', $userId)->update(['deleted_at' => now()]);

        return redirect()->route('patients.index')
            ->with('success', "{$name} has been deleted. View in Deleted Patients to restore or archive.");
    }

    /**
     * Show soft-deleted patients (admin/staff).
     */
    public function trashed()
    {
        if (!$this->isAdminOrStaff()) {
            return back()->with('error', 'Unauthorized.');
        }

        $patients = Patient::withTrashed()
            ->whereNotNull('patients.deleted_at')
            ->with('user')
            ->orderBy('deleted_at', 'desc')
            ->paginate(15);

        return view('patients.trashed', compact('patients'));
    }

    /**
     * Restore a soft-deleted patient (admin/staff).
     */
    public function restore($id)
    {
        if (!$this->isAdminOrStaff()) {
            return back()->with('error', 'Unauthorized.');
        }

        $patient = Patient::withTrashed()->findOrFail($id);
        $name    = $patient->full_name;

        Patient::withTrashed()->where('id', $id)->update(['deleted_at' => null]);
        User::withTrashed()->where('id', $patient->user_id)->update(['deleted_at' => null]);

        return redirect()->route('patients.trashed')
            ->with('success', "{$name} has been restored successfully.");
    }

    /**
     * Move a soft-deleted patient to the archive table (admin/staff).
     * Saves full snapshot then permanently deletes the records.
     */
    public function archive($id)
    {
        if (!$this->isAdminOrStaff()) {
            return back()->with('error', 'Unauthorized.');
        }

        $patient = Patient::withTrashed()->with([
            'user',
            'appointments.doctor',
            'queues.department',
        ])->findOrFail($id);

        $name = $patient->full_name;

        DB::transaction(function () use ($patient) {

            $appointmentsSnapshot = $patient->appointments->map(fn($a) => [
                'id'               => $a->id,
                'reference_code'   => $a->reference_code,
                'doctor'           => $a->doctor->full_name ?? 'N/A',
                'appointment_date' => $a->appointment_date?->format('Y-m-d'),
                'appointment_time' => $a->appointment_time,
                'status'           => $a->status,
                'reason'           => $a->reason,
            ])->toArray();

            $queuesSnapshot = $patient->queues->map(fn($q) => [
                'id'           => $q->id,
                'queue_code'   => $q->queue_code,
                'queue_number' => $q->queue_number,
                'department'   => $q->department->name ?? 'N/A',
                'queue_date'   => $q->queue_date?->format('Y-m-d'),
                'status'       => $q->status,
            ])->toArray();

            ArchivedPatient::create([
                'original_patient_id'   => $patient->id,
                'original_user_id'      => $patient->user_id,
                'patient_code'          => $patient->patient_code,
                'first_name'            => $patient->first_name,
                'last_name'             => $patient->last_name,
                'date_of_birth'         => $patient->date_of_birth,
                'gender'                => $patient->gender,
                'phone'                 => $patient->phone,
                'address'               => $patient->address,
                'medical_history'       => $patient->medical_history,
                'email'                 => $patient->user->email ?? 'N/A',
                'user_name'             => $patient->user->name ?? 'N/A',
                'total_appointments'    => $patient->appointments->count(),
                'total_queues'          => $patient->queues->count(),
                'appointments_snapshot' => $appointmentsSnapshot,
                'queues_snapshot'       => $queuesSnapshot,
                'deleted_by_user_id'    => auth()->id(),
                'deleted_by_name'       => auth()->user()->name,
                'deletion_reason'       => 'Archived by ' . auth()->user()->role,
                'archived_at'           => now(),
            ]);

            User::withTrashed()->where('id', $patient->user_id)->forceDelete();
            Patient::withTrashed()->where('id', $patient->id)->forceDelete();
        });

        return redirect()->route('patients.trashed')
            ->with('success', "{$name} has been archived. Full record saved to Archives.");
    }

    /**
     * Show archived patients (admin/staff).
     */
    public function archives()
    {
        if (!$this->isAdminOrStaff()) {
            return back()->with('error', 'Unauthorized.');
        }

        $archives = ArchivedPatient::orderByDesc('archived_at')->paginate(15);

        return view('patients.archives', compact('archives'));
    }
}
