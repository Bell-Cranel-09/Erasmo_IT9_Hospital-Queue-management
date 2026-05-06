<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(protected AppointmentService $appointmentService) {}

    /**
     * List appointments with optional filters.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor.department', 'schedule']);

        // Patients only see their own appointments
        if (auth()->user()->isPatient()) {
            $patient = auth()->user()->patient;
            if ($patient) {
                $query->where('patient_id', $patient->id);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->orderBy('appointment_date')->orderBy('appointment_time')->paginate(15);

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show the booking form.
     */
    public function create(Request $request)
    {
        $doctors  = Doctor::with('department')->where('is_available', true)->get();
        $patients = Patient::whereNull('deleted_at')->orderBy('first_name')->get();

        return view('appointments.create', compact('doctors', 'patients'));
    }

    /**
     * AJAX endpoint: return available time slots for a doctor on a date.
     * Called by the JS in appointments/create.blade.php
     */
    public function slots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date'      => 'required|date|after_or_equal:today',
        ]);

        $doctor = Doctor::findOrFail($request->doctor_id);
        $slots  = $this->appointmentService->getAvailableSlots($doctor, $request->date);

        return response()->json(['slots' => $slots]);
    }

    /**
     * Store a new appointment.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'doctor_id'  => 'required|exists:doctors,id',
            'date'       => 'required|date|after_or_equal:today',
            'time'       => 'required',
            'reason'     => 'nullable|string|max:500',
            'patient_id' => 'nullable|exists:patients,id', // staff/admin may pass this
        ]);

        // Determine patient: admin/staff pass patient_id; patients use their profile
        if (auth()->user()->isPatient()) {
            $patient = auth()->user()->patient;
            if (!$patient) {
                return back()->with('error', 'Patient profile not found. Please complete your registration.');
            }
        } else {
            $patient = Patient::findOrFail($data['patient_id'] ?? null);
        }

        $doctor = Doctor::findOrFail($data['doctor_id']);

        try {
            $appointment = $this->appointmentService->book(
                $patient,
                $doctor,
                $data['date'],
                $data['time'],
                $data['reason'] ?? ''
            );

            return redirect()
                ->route('appointments.show', $appointment)
                ->with('success', "Appointment booked successfully! Reference: {$appointment->reference_code}");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show a single appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor.department', 'schedule', 'queue']);

        return view('appointments.show', compact('appointment'));
    }

    /**
     * Confirm an appointment (staff/admin only).
     */
    public function confirm(Appointment $appointment)
    {
        try {
            $this->appointmentService->confirm($appointment);
            return back()->with('success', 'Appointment confirmed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(Appointment $appointment)
    {
        try {
            $this->appointmentService->cancel($appointment);
            return back()->with('success', 'Appointment has been canceled.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
