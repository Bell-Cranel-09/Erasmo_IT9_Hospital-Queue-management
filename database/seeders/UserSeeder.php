<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────────────────
        User::create([
            'name'     => 'System Admin',
            'email'    => 'admin@hospital.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ── Staff ──────────────────────────────────────────────────────
        User::create([
            'name'     => 'Front Desk Staff',
            'email'    => 'staff@hospital.com',
            'password' => Hash::make('password'),
            'role'     => 'staff',
        ]);

        // ── Doctor (needs a user account + doctor profile) ─────────────
        $doctorUser = User::create([
            'name'     => 'Dr. Maria Santos',
            'email'    => 'dr.santos@hospital.com',
            'password' => Hash::make('password'),
            'role'     => 'staff',   // Doctors log in as staff
        ]);

        Doctor::create([
            'user_id'        => $doctorUser->id,
            'department_id'  => 1,    // General Medicine (from DepartmentSeeder)
            'doctor_code'    => 'DOC-001',
            'first_name'     => 'Maria',
            'last_name'      => 'Santos',
            'specialization' => 'General Practitioner',
            'license_number' => 'LIC-2024-001',
            'phone'          => '09171234567',
        ]);

        // ── Sample Patient ─────────────────────────────────────────────
        $patientUser = User::create([
            'name'     => 'Juan Dela Cruz',
            'email'    => 'juan@example.com',
            'password' => Hash::make('password'),
            'role'     => 'patient',
        ]);

        Patient::create([
            'user_id'       => $patientUser->id,
            'patient_code'  => 'PAT-0001',
            'first_name'    => 'Juan',
            'last_name'     => 'Dela Cruz',
            'date_of_birth' => '1990-05-15',
            'gender'        => 'male',
            'phone'         => '09181234567',
            'address'       => '123 Rizal St., Davao City',
        ]);
    }
}