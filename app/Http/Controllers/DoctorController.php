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
    public function index()
    {
        $doctors     = Doctor::with(['department', 'user', 'schedules'])->paginate(15);
        $departments = Department::where('is_active', true)->get();

        return view('doctors.index', compact('doctors', 'departments'));
    }

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

            Doctor::create([
                'user_id'        => $user->id,
                'department_id'  => $data['department_id'],
                'doctor_code'    => 'DOC-' . str_pad(Doctor::count() + 1, 3, '0', STR_PAD_LEFT),
                'first_name'     => $data['first_name'],
                'last_name'      => $data['last_name'],
                'specialization' => $data['specialization'],
                'license_number' => $data['license_number'],
                'phone'          => $data['phone'] ?? null,
            ]);
        });

        return redirect()->route('doctors.index')->with('success', 'Doctor added successfully.');
    }

    public function schedules(Doctor $doctor)
    {
        // Load ALL schedules including inactive so staff can see history & restore
        $schedules = $doctor->schedules()->orderBy('is_active', 'desc')->orderBy('day_of_week')->get();
        $days      = DoctorSchedule::DAYS;

        return view('doctors.schedules', compact('doctor', 'schedules', 'days'));
    }

    public function storeSchedule(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'day_of_week'           => 'required|integer|between:0,6',
            'start_time'            => 'required|date_format:H:i',
            'end_time'              => 'required|date_format:H:i|after:start_time',
            'slot_duration_minutes' => 'required|integer|min:10|max:120',
            'max_patients'          => 'required|integer|min:1|max:100',
        ]);

        $exists = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $data['day_of_week'])
            ->where('is_active', true)
            ->exists();

        if ($exists) {
            return back()->with('error', 'An active schedule already exists for this day. Deactivate it first.');
        }

        $doctor->schedules()->create($data);

        return back()->with('success', 'Schedule added successfully.');
    }

    /**
     * Deactivate a schedule (soft delete).
     * Sets is_active = false. Data and linked appointments are fully preserved.
     */
    public function destroySchedule(Doctor $doctor, DoctorSchedule $schedule)
    {
        if ($schedule->doctor_id !== $doctor->id) {
            return back()->with('error', 'Schedule does not belong to this doctor.');
        }

        $schedule->update(['is_active' => false]);

        return back()->with('success', "{$schedule->day_name} schedule deactivated. All data preserved.");
    }

    /**
     * Restore a deactivated schedule.
     * Sets is_active = true again.
     */
    public function restoreSchedule(Doctor $doctor, DoctorSchedule $schedule)
    {
        if ($schedule->doctor_id !== $doctor->id) {
            return back()->with('error', 'Schedule does not belong to this doctor.');
        }

        // Check no other active schedule exists for this day first
        $conflict = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $schedule->day_of_week)
            ->where('is_active', true)
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Another active schedule already exists for this day.');
        }

        $schedule->update(['is_active' => true]);

        return back()->with('success', "{$schedule->day_name} schedule restored successfully.");
    }
}
