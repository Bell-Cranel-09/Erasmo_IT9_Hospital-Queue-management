<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function __construct(protected PatientService $patientService) {}

    public function index(Request $request)
    {
        $query = Patient::with('user');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name',    'like', '%'.$request->search.'%')
                  ->orWhere('last_name',   'like', '%'.$request->search.'%')
                  ->orWhere('patient_code','like', '%'.$request->search.'%');
            });
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:8|confirmed',
            'date_of_birth' => 'required|date|before:today',
            'gender'        => 'required|in:male,female,other',
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
        ]);

        try {
            $patient = $this->patientService->register($data);
            return redirect()
                ->route('patients.show', $patient)
                ->with('success', "Patient registered! Code: {$patient->patient_code}");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Patient $patient)
    {
        $patient->load(['user', 'appointments.doctor.department', 'queues.department']);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
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
        $patient->user->update(['name' => $data['first_name'].' '.$data['last_name']]);

        return back()->with('success', 'Patient profile updated successfully.');
    }

    /**
     * Delete a patient and their user account (admin only).
     */
    public function destroy(Patient $patient)
    {
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Only admins can delete patient records.');
        }

        $name = $patient->full_name;

        DB::transaction(function () use ($patient) {
            // Deleting the user cascades to the patient record (set in migration)
            $patient->user->delete();
        });

        return redirect()
            ->route('patients.index')
            ->with('success', "Patient record for {$name} has been permanently deleted.");
    }
}
