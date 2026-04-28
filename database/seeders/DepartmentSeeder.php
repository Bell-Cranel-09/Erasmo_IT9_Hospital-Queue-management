<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'General Medicine',  'code' => 'GEN', 'description' => 'General consultations'],
            ['name' => 'Pediatrics',        'code' => 'PED', 'description' => 'Children\'s health'],
            ['name' => 'Cardiology',        'code' => 'CAR', 'description' => 'Heart-related conditions'],
            ['name' => 'Orthopedics',       'code' => 'ORT', 'description' => 'Bone and joint care'],
            ['name' => 'Emergency',         'code' => 'EMR', 'description' => 'Emergency services'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}