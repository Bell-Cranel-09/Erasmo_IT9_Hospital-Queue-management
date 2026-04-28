<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'doctor_schedule_id',
        'appointment_date', 'appointment_time', 'status',
        'reason', 'notes', 'reference_code',
    ];

    protected $casts = ['appointment_date' => 'date'];

    // ── Relationships ──────────────────────────────────────────
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function schedule()
    {
        return $this->belongsTo(DoctorSchedule::class, 'doctor_schedule_id');
    }

    public function queue()
    {
        return $this->hasOne(Queue::class);
    }

    // ── Helpers ────────────────────────────────────────────────
    public function isPending(): bool    { return $this->status === 'pending'; }
    public function isConfirmed(): bool  { return $this->status === 'confirmed'; }
    public function isCompleted(): bool  { return $this->status === 'completed'; }
    public function isCanceled(): bool   { return $this->status === 'canceled'; }
}