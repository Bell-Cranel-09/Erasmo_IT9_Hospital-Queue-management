<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'code', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    // ── Relationships ──────────────────────────────────────────
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    // ── Helpers ────────────────────────────────────────────────

    /** Returns today's queues that are still waiting */
    public function todayWaitingQueues()
    {
        return $this->queues()
            ->whereDate('queue_date', today())
            ->where('status', 'waiting')
            ->orderBy('queue_number');
    }

    /** Returns the queue currently being served today */
    public function currentlyServing()
    {
        return $this->queues()
            ->whereDate('queue_date', today())
            ->where('status', 'serving')
            ->first();
    }
}