<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivedPatient extends Model
{
    protected $fillable = [
        'original_patient_id',
        'original_user_id',
        'patient_code',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'medical_history',
        'email',
        'user_name',
        'total_appointments',
        'total_queues',
        'appointments_snapshot',
        'queues_snapshot',
        'deleted_by_user_id',
        'deleted_by_name',
        'deletion_reason',
        'archived_at',
    ];

    protected $casts = [
        'date_of_birth'          => 'date',
        'appointments_snapshot'  => 'array',
        'queues_snapshot'        => 'array',
        'archived_at'            => 'datetime',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
