<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
 public function index(Request $request)
{
    $user = auth()->user();

    if ($user->isPatient()) {
        $patient = $user->patient;
        $query   = $patient
            ? $patient->appointments()->with(['doctor.department'])
            : Appointment::whereRaw('0=1'); // empty query fallback

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->orderByDesc('appointment_date')->paginate(15)->withQueryString();

    } else {
        $query = Appointment::with(['patient', 'doctor.department']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->orderByDesc('appointment_date')->paginate(15)->withQueryString();
    }

    return view('appointments.index', compact('appointments'));
}

public function create(Request $request)
{
    $departments      = Department::where('is_active', true)->get();
    $doctors          = collect();
    $patients         = collect();
    $selectedDoctorId = $request->get('doctor_id');

    if (auth()->user()->isAdmin() || auth()->user()->isStaff()) {
        $patients = Patient::whereNull('deleted_at')->orderBy('first_name')->get();
    }

    if ($request->filled('department_id')) {
        $doctors = Doctor::where('department_id', $request->department_id)
            ->whereNull('deleted_at')
            ->get();
    }

    return view('appointments.create', compact(
        'departments', 'doctors', 'patients', 'selectedDoctorId'
    ));
}
    /**
     * AJAX — get doctors for a department (used by the booking form dropdown).
     */
    public function doctorsByDept(Request $request)
    {
        $request->validate(['department_id' => 'required|exists:departments,id']);

        $doctors = Doctor::where('department_id', $request->department_id)
            ->whereNull('deleted_at')
            ->get()
            ->map(fn($d) => [
                'id'             => $d->id,
                'full_name'      => $d->full_name,
                'specialization' => $d->specialization,
            ]);

        return response()->json(['doctors' => $doctors]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->isPatient()) {
            $patientId = $user->patient->id;
        } else {
            $request->validate(['patient_id' => 'required|exists:patients,id']);
            $patientId = $request->patient_id;
        }

        $data = $request->validate([
            'doctor_id'        => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'reason'           => 'nullable|string|max:500',
        ]);

        $exists = Appointment::where('doctor_id',        $data['doctor_id'])
            ->where('appointment_date', $data['appointment_date'])
            ->where('appointment_time', $data['appointment_time'])
            ->whereNotIn('status', ['canceled'])->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'That time slot is already booked. Please choose another.');
        }

        Appointment::create([
            'patient_id'       => $patientId,
            'doctor_id'        => $data['doctor_id'],
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'reason'           => $data['reason'] ?? null,
            'status'           => 'pending',
            'reference_code'   => 'APT-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        ]);

        return redirect()->route('appointments.index')->with('success', 'Appointment booked successfully!');
    }

    public function show(Appointment $appointment)
    {
        $user = auth()->user();
        if ($user->isPatient() && $appointment->patient_id !== $user->patient->id) {
            return redirect()->route('appointments.index')->with('error', 'Unauthorized.');
        }
        $appointment->load(['patient', 'doctor.department']);
        return view('appointments.show', compact('appointment'));
    }

    public function slots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date'      => 'required|date',
        ]);

        $date      = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek;

        $schedule = DoctorSchedule::where('doctor_id', $request->doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)->first();

        if (!$schedule) {
            return response()->json(['slots' => [], 'message' => 'Doctor is not available on this day.']);
        }

        $slots  = [];
        $start  = Carbon::parse($request->date . ' ' . $schedule->start_time);
        $end    = Carbon::parse($request->date . ' ' . $schedule->end_time);
        $booked = Appointment::where('doctor_id',        $request->doctor_id)
            ->where('appointment_date', $request->date)
            ->whereNotIn('status', ['canceled'])
            ->pluck('appointment_time')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        while ($start->lt($end)) {
            $timeStr = $start->format('H:i');
            $slots[] = [
                'time'      => $timeStr,
                'label'     => $start->format('h:i A'),
                'available' => !in_array($timeStr, $booked),
            ];
            $start->addMinutes($schedule->slot_duration_minutes);
        }

        return response()->json(['slots' => $slots, 'message' => count($slots) ? null : 'No slots available.']);
    }

    public function confirm(Appointment $appointment)
    {
        $appointment->update(['status' => 'confirmed']);
        return back()->with('success', 'Appointment confirmed.');
    }

    public function cancel(Appointment $appointment)
    {
        $user = auth()->user();
        if ($user->isPatient() && $appointment->patient_id !== $user->patient->id) {
            return back()->with('error', 'Unauthorized.');
        }
        $appointment->update(['status' => 'canceled']);
        return back()->with('success', 'Appointment canceled.');
    }
}
