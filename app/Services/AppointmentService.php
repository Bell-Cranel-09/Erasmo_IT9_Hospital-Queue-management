<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    /**
     * Book an appointment.
     * Validates slot availability and prevents double-booking.
     */
    public function book(Patient $patient, Doctor $doctor, string $date, string $time, string $reason = ''): Appointment
    {
        $carbonDate = Carbon::parse($date);

        // 1. Get the doctor's schedule for this day of the week
        $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $carbonDate->dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            throw new \Exception("The doctor has no schedule on " . $carbonDate->format('l') . ".");
        }

        // 2. Validate the requested time is a valid slot
        $validSlots = $schedule->generateTimeSlots();
        $formattedTime = Carbon::parse($time)->format('H:i');

        if (!in_array($formattedTime, $validSlots)) {
            throw new \Exception("The selected time ({$formattedTime}) is not a valid appointment slot.");
        }

        // 3. Check if patient already has an appointment with this doctor that day
        $patientConflict = Appointment::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->where('appointment_date', $date)
            ->whereNotIn('status', ['canceled'])
            ->exists();

        if ($patientConflict) {
            throw new \Exception("You already have an appointment with this doctor on this date.");
        }

        // 4. Check if the slot is already booked (DB unique constraint is a backup)
        $slotTaken = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', $date)
            ->where('appointment_time', $formattedTime)
            ->whereNotIn('status', ['canceled'])
            ->exists();

        if ($slotTaken) {
            throw new \Exception("This time slot is already booked. Please choose another.");
        }

        // 5. Check if max patients for this day is reached
        $bookedCount = Appointment::where('doctor_schedule_id', $schedule->id)
            ->where('appointment_date', $date)
            ->whereNotIn('status', ['canceled'])
            ->count();

        if ($bookedCount >= $schedule->max_patients) {
            throw new \Exception("This doctor's schedule is fully booked for the selected date.");
        }

        // 6. Create the appointment inside a transaction
        return DB::transaction(function () use ($patient, $doctor, $schedule, $date, $formattedTime, $reason) {
            $referenceCode = $this->generateReferenceCode($date);

            return Appointment::create([
                'patient_id'         => $patient->id,
                'doctor_id'          => $doctor->id,
                'doctor_schedule_id' => $schedule->id,
                'appointment_date'   => $date,
                'appointment_time'   => $formattedTime,
                'status'             => 'pending',
                'reason'             => $reason,
                'reference_code'     => $referenceCode,
            ]);
        });
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(Appointment $appointment): void
    {
        if ($appointment->isCompleted()) {
            throw new \Exception("Cannot cancel a completed appointment.");
        }

        $appointment->update(['status' => 'canceled']);
    }

    /**
     * Confirm an appointment (done by staff/admin).
     */
    public function confirm(Appointment $appointment): void
    {
        if (!$appointment->isPending()) {
            throw new \Exception("Only pending appointments can be confirmed.");
        }

        $appointment->update(['status' => 'confirmed']);
    }

    /**
     * Get available time slots for a doctor on a given date.
     */
    public function getAvailableSlots(Doctor $doctor, string $date): array
    {
        $carbonDate = Carbon::parse($date);

        $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $carbonDate->dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            return [];
        }

        return $schedule->availableSlotsForDate($date);
    }

    /**
     * Auto-generate a unique reference code.
     * Format: APT-YYYYMMDD-XXXX
     */
    private function generateReferenceCode(string $date): string
    {
        $dateStr = Carbon::parse($date)->format('Ymd');
        $count   = Appointment::whereDate('appointment_date', $date)->count() + 1;

        return 'APT-' . $dateStr . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}