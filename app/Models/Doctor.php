<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "user_id", "department_id", "doctor_code",
        "first_name", "last_name", "specialization",
        "license_number", "phone", "is_available",
    ];

    protected $casts = [
        "is_available" => "boolean",
        "deleted_at"   => "datetime",
    ];

    public function user()
    {
        // CORRECT
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function getFullNameAttribute(): string
    {
        return "Dr. {$this->first_name} {$this->last_name}";
    }

    public function scheduleForDay(int $dayOfWeek)
    {
        return $this->schedules()
            ->where("day_of_week", $dayOfWeek)
            ->where("is_active", true)
            ->first();
    }
}
