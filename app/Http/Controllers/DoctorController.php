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
        $doctors = Doctor::with(['department', 'schedules'])->paginate(15);
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
                'doctor_code'    => 'DOC-' . str_pad(Doctor::withTrashed()->count() + 1, 3, '0', STR_PAD_LEFT),
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
        $schedules = $doctor->schedules()->orderByDesc('is_active')->orderBy('day_of_week')->get();
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
            return back()->with('error', 'An active schedule already exists for this day.');
        }

        $doctor->schedules()->create($data);

        return back()->with('success', 'Schedule added successfully.');
    }

    public function destroySchedule(Doctor $doctor, DoctorSchedule $schedule)
    {
        if ((int) $schedule->doctor_id !== (int) $doctor->id) {
            return back()->with('error', 'Schedule does not belong to this doctor.');
        }

        $schedule->update(['is_active' => false]);

        return back()->with('success', "{$schedule->day_name} schedule deactivated. Data preserved.");
    }

    public function restoreSchedule(Doctor $doctor, DoctorSchedule $schedule)
    {
        if ((int) $schedule->doctor_id !== (int) $doctor->id) {
            return back()->with('error', 'Schedule does not belong to this doctor.');
        }

        $conflict = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $schedule->day_of_week)
            ->where('is_active', true)
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Another active schedule exists for this day.');
        }

        $schedule->update(['is_active' => true]);

        return back()->with('success', "{$schedule->day_name} schedule restored.");
    }

    /**
     * Soft delete a doctor (admin only).
     * Deactivates schedules, cancels future appointments, sets deleted_at.
     * All data stays in the database.
     */
   public function destroy(Request $request, $id)
    {
    if (!auth()->user()->isAdmin()) {
        return redirect()->route('doctors.index')->with('error', 'Only admins can delete doctors.');
    }

    $doctor = Doctor::findOrFail($id);
    $name   = $doctor->full_name;
    $userId = $doctor->user_id;

    // Deactivate all schedules
    \App\Models\DoctorSchedule::where('doctor_id', $id)->update(['is_active' => false]);

    // Cancel upcoming appointments
    \App\Models\Appointment::where('doctor_id', $id)
        ->whereIn('status', ['pending', 'confirmed'])
        ->where('appointment_date', '>=', today())
        ->update(['status' => 'canceled']);

    // Soft delete doctor directly via query
    \App\Models\Doctor::where('id', $id)->update(['deleted_at' => now()]);

    // Soft delete user directly via query
    \App\Models\User::where('id', $userId)->update(['deleted_at' => now()]);

    return redirect()->route('doctors.index')
        ->with('success', "{$name} has been deleted successfully.");
    }
    /**
     * Show soft-deleted doctors (admin only).
     */

    public function trashed()
    {
    if (!auth()->user()->isAdmin()) {
        return back()->with('error', 'Unauthorized.');
    }

    // onlyTrashed() shows ONLY soft-deleted records
    $doctors = Doctor::onlyTrashed()
        ->with(['department'])
        ->orderBy('deleted_at', 'desc')
        ->paginate(15);

    return view('doctors.trashed', compact('doctors'));
    }

    /**
     * Restore a soft-deleted doctor (admin only).
     */
    public function restore($id)
    {
    if (!auth()->user()->isAdmin()) {
        return back()->with('error', 'Only admins can restore doctors.');
    }

    $doctor = Doctor::withTrashed()->findOrFail($id);
    $name   = $doctor->full_name;

    DB::transaction(function () use ($doctor) {
        // Restore the doctor
        $doctor->restore();

        // Restore the user using the stored user_id directly
        \App\Models\User::withTrashed()->where('id', $doctor->user_id)->restore();
    });

    return redirect()->route('doctors.index')
        ->with('success', "{$name} has been restored successfully.");
    }
}
