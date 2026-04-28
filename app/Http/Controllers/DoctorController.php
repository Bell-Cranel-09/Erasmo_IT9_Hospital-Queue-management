<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    /**
     * List all doctors — passes $departments for the Add Doctor modal.
     */
    public function index()
    {
        $doctors     = Doctor::with(['department', 'user', 'schedules'])->paginate(15);
        $departments = Department::where('is_active', true)->get();

        return view('doctors.index', compact('doctors', 'departments'));
    }

    /**
     * Store a new doctor (admin only).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'department_id'  => 'required|exists:departments,id',
            'specialization' => 'required|string|max:100',
            'license_number' => 'required|string|unique:doctors,license_number',
            'phone'          => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['first_name'] . ' ' . $data['last_name'],
                'email'    => $data['email'],
                'password' => Hash::make('password123'),
                'role'     => 'staff',
            ]);

            $count      = Doctor::count() + 1;
            $doctorCode = 'DOC-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            Doctor::create([
                'user_id'        => $user->id,
                'department_id'  => $data['department_id'],
                'doctor_code'    => $doctorCode,
                'first_name'     => $data['first_name'],
                'last_name'      => $data['last_name'],
                'specialization' => $data['specialization'],
                'license_number' => $data['license_number'],
                'phone'          => $data['phone'] ?? null,
            ]);
        });

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor added successfully.');
    }

    /**
     * Show schedule management page for a doctor.
     */
    public function schedules(Doctor $doctor)
    {
        $schedules = $doctor->schedules()->orderBy('day_of_week')->get();
        $days      = DoctorSchedule::DAYS;

        return view('doctors.schedules', compact('doctor', 'schedules', 'days'));
    }

    /**
     * Save a new schedule slot for a doctor.
     */
    public function storeSchedule(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'day_of_week'           => 'required|integer|between:0,6',
            'start_time'            => 'required|date_format:H:i',
            'end_time'              => 'required|date_format:H:i|after:start_time',
            'slot_duration_minutes' => 'required|integer|min:10|max:120',
            'max_patients'          => 'required|integer|min:1|max:100',
        ]);

        // Check if doctor already has an active schedule on this day
        $overlap = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $data['day_of_week'])
            ->where('is_active', true)
            ->exists();

        if ($overlap) {
            return back()->with('error', 'This doctor already has an active schedule on that day.');
        }

        $doctor->schedules()->create($data);

        return back()->with('success', 'Schedule added successfully.');
    }
}
