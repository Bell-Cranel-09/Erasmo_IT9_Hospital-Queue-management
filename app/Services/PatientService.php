<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientService
{
    public function register(array $data): Patient
    {
        return DB::transaction(function () use ($data) {

            // Create user — Hash::make() does ONE hash, no cast doubling it
            $user = User::create([
                'name'     => $data['first_name'] . ' ' . $data['last_name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => 'patient',
                'is_active'=> true,
            ]);

            // Log the user in immediately after registering
            Auth::login($user);

            // Generate patient code
            $count   = Patient::count() + 1;
            $patCode = 'PAT-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            return Patient::create([
                'user_id'       => $user->id,
                'patient_code'  => $patCode,
                'first_name'    => $data['first_name'],
                'last_name'     => $data['last_name'],
                'date_of_birth' => $data['date_of_birth'],
                'gender'        => $data['gender'],
                'phone'         => $data['phone'] ?? null,
                'address'       => $data['address'] ?? null,
            ]);
        
    


            return $patient;
        });
    }

    /**
     * Generate a unique patient code like "PAT-0001".
     */
    private function generatePatientCode(): string
    {
        $last = Patient::latest('id')->lockForUpdate()->first();
        $nextId = $last ? $last->id + 1 : 1;

        return 'PAT-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
}