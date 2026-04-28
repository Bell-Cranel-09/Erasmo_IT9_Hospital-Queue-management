<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id', 'patient_code', 'first_name', 'last_name',
        'date_of_birth', 'gender', 'phone', 'address', 'medical_history',
    ];

    protected $casts = ['date_of_birth' => 'date'];

    // ── Relationships ──────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    // ── Helpers ────────────────────────────────────────────────
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function activeQueue()
    {
        return $this->queues()
            ->whereDate('queue_date', today())
            ->whereIn('status', ['waiting', 'serving'])
            ->first();
    }
}