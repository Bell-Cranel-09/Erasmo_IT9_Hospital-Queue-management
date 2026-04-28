<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'doctor_id', 'day_of_week', 'start_time',
        'end_time', 'slot_duration_minutes', 'max_patients', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    // Day name mapping for readability
    const DAYS = [
        0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday',
        3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // ── Helpers ────────────────────────────────────────────────
    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day_of_week] ?? 'Unknown';
    }

    /**
     * Generate all time slots for this schedule.
     * Returns an array like ['08:00', '08:30', '09:00', ...]
     */
    public function generateTimeSlots(): array
    {
        $slots = [];
        $current = Carbon::parse($this->start_time);
        $end     = Carbon::parse($this->end_time);

        while ($current->lt($end)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes($this->slot_duration_minutes);
        }

        return $slots;
    }

    /**
     * Get available (unbooked) time slots for a specific date.
     */
    public function availableSlotsForDate(string $date): array
    {
        $allSlots = $this->generateTimeSlots();

        // Get already booked times for this doctor on this date
        $bookedTimes = Appointment::where('doctor_id', $this->doctor_id)
            ->where('appointment_date', $date)
            ->whereNotIn('status', ['canceled'])
            ->pluck('appointment_time')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        return array_values(array_diff($allSlots, $bookedTimes));
    }
}