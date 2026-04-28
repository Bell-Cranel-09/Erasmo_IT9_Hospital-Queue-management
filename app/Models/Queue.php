<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = [
        'patient_id', 'department_id', 'appointment_id',
        'queue_number', 'queue_code', 'queue_date',
        'status', 'called_at', 'served_at', 'done_at',
    ];

    protected $casts = [
        'queue_date' => 'date',
        'called_at'  => 'datetime',
        'served_at'  => 'datetime',
        'done_at'    => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    // ── Helpers ────────────────────────────────────────────────
    public function isWaiting(): bool  { return $this->status === 'waiting'; }
    public function isServing(): bool  { return $this->status === 'serving'; }
    public function isDone(): bool     { return $this->status === 'done'; }
}