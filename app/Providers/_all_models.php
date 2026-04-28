<?php
// =============================================================
// FILE: app/Models/User.php
// =============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['is_active' => 'boolean'];

    // ── Helpers ──────────────────────────────────────────────
    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isStaff(): bool   { return $this->role === 'staff'; }
    public function isPatient(): bool { return $this->role === 'patient'; }

    // ── Relationships ─────────────────────────────────────────
    // A user who is a doctor has one doctor profile
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    // A user who is a patient has one patient profile
    public function patientProfile()
    {
        return $this->hasOne(PatientProfile::class);
    }

    // A patient can have many appointments
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    // A patient can join many queues (one per dept per day)
    public function queues()
    {
        return $this->hasMany(Queue::class, 'patient_id');
    }
}


// =============================================================
// FILE: app/Models/Department.php
// =============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'code', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    // One department has many doctors
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    // One department has many queues
    public function queues()
    {
        return $this->hasMany(Queue::class);
    }
}


// =============================================================
// FILE: app/Models/Doctor.php
// =============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id', 'department_id', 'license_number',
        'specialization', 'is_available'
    ];

    protected $casts = ['is_available' => 'boolean'];

    // Doctor belongs to a User (their login account)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Doctor belongs to a Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Doctor has many schedules (e.g. Mon 8am–12pm, Wed 1pm–5pm)
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    // Doctor has many appointments
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}


// =============================================================
// FILE: app/Models/Schedule.php
// =============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'doctor_id', 'day_of_week', 'start_time',
        'end_time', 'max_patients', 'is_active'
    ];

    protected $casts = ['is_active' => 'boolean'];

    // Map day number to readable name
    public function getDayNameAttribute(): string
    {
        return ['Sunday','Monday','Tuesday','Wednesday',
                'Thursday','Friday','Saturday'][$this->day_of_week];
    }

    // Schedule belongs to a Doctor
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Schedule has many appointments
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // Count how many confirmed/pending appointments exist on a given date
    public function bookedCountForDate(string $date): int
    {
        return $this->appointments()
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
    }

    // Check if slots are still available on a given date
    public function hasAvailabilityOn(string $date): bool
    {
        return $this->bookedCountForDate($date) < $this->max_patients;
    }
}


// =============================================================
// FILE: app/Models/PatientProfile.php
// =============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientProfile extends Model
{
    protected $fillable = [
        'user_id', 'patient_number', 'date_of_birth',
        'gender', 'address', 'emergency_contact', 'medical_history'
    ];

    // Profile belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


// =============================================================
// FILE: app/Models/Appointment.php
// =============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'schedule_id',
        'appointment_date', 'appointment_time',
        'status', 'reason', 'notes'
    ];

    // Appointment belongs to a Patient (User)
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // Appointment belongs to a Doctor
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Appointment belongs to a Schedule slot
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}


// =============================================================
// FILE: app/Models/Queue.php
// =============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = [
        'patient_id', 'department_id', 'queue_number',
        'queue_code', 'queue_date', 'status',
        'called_at', 'served_at', 'done_at'
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'served_at' => 'datetime',
        'done_at'   => 'datetime',
    ];

    // Queue belongs to a Patient
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // Queue belongs to a Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
