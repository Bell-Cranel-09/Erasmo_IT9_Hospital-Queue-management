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
     * Check if current user is admin or staff.
     */
    private function isAdminOrStaff(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isStaff();
    }

    public function index()
    {
        $doctors     = Doctor::with(['department', 'schedules'])->paginate(15);
        $departments = Department::where('is_active', true)->get();

        return view('doctors.index', compact('doctors', 'departments'));
    }

    public function store(Request $request)
    {
        if (!$this->isAdminOrStaff()) {
            return back()->with('error', 'Unauthorized.');
        }

        $data = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'department_id'  => 'required|exists:departments,id',
            'specialization' => 'required|string|max:100',
            'license_number' => 'required|string|unique:doctors,license_number,NULL,id,deleted_at,NULL',
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
        if (!$this->isAdminOrStaff()) {
            return back()->with('error', 'Unauthorized.');
        }

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
        if (!$this->isAdminOrStaff()) {
            return back()->with('error', 'Unauthorized.');
        }

        if ((int) $schedule->doctor_id !== (int) $doctor->id) {
            return back()->with('error', 'Schedule does not belong to this doctor.');
        }

        $schedule->update(['is_active' => false]);

        return back()->with('success', "{$schedule->day_name} schedule deactivated. Data preserved.");
    }

    public function restoreSchedule(Doctor $doctor, DoctorSchedule $schedule)
    {
        if (!$this->isAdminOrStaff()) {
            return back()->with('error', 'Unauthorized.');
        }

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
     * Soft delete a doctor (admin/staff).
     */
    public function destroy($id)
    {
        if (!$this->isAdminOrStaff()) {
            return back()->with('error', 'Unauthorized.');
        }

        $doctor = Doctor::findOrFail($id);
        $name   = $doctor->full_name;
        $userId = $doctor->user_id;

        DoctorSchedule::where('doctor_id', $id)->update(['is_active' => false]);

        \App\Models\Appointment::where('doctor_id', $id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('appointment_date', '>=', today())
            ->update(['status' => 'canceled']);

        Doctor::where('id', $id)->update(['deleted_at' => now()]);
        User::where('id', $userId)->update(['deleted_at' => now()]);

        return redirect()->route('doctors.index')
            ->with('success', "{$name} has been deleted. Records are preserved and can be restored.");
    }

    /**
     * Show soft-deleted doctors (admin/staff).
     */
    public function trashed()
    {
        if (!$this->isAdminOrStaff()) {
            return back()->with('error', 'Unauthorized.');
        }

        $doctors = Doctor::onlyTrashed()
            ->with(['department'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(15);

        return view('doctors.trashed', compact('doctors'));
    }

    /**
     * Restore a soft-deleted doctor (admin/staff).
     */
    public function restore($id)
    {
        if (!$this->isAdminOrStaff()) {
            return back()->with('error', 'Unauthorized.');
        }

        $doctor = Doctor::withTrashed()->findOrFail($id);
        $name   = $doctor->full_name;

        Doctor::withTrashed()->where('id', $id)->update(['deleted_at' => null]);
        User::withTrashed()->where('id', $doctor->user_id)->update(['deleted_at' => null]);

        return redirect()->route('doctors.index')
            ->with('success', "{$name} has been restored successfully.");
    }
}
