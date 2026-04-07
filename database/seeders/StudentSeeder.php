<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'name' => 'أحمد محمد',
                'email' => 'ahmed.student@example.com',
                'password' => 'Student@123',
                'phone' => '+966500000001',
                'status' => 'active',
            ],
            [
                'name' => 'سارة علي',
                'email' => 'sara.student@example.com',
                'password' => 'Student@123',
                'phone' => '+966500000002',
                'status' => 'active',
            ],
            [
                'name' => 'محمد خالد',
                'email' => 'mohammed.student@example.com',
                'password' => 'Student@123',
                'phone' => '+966500000003',
                'status' => 'inactive',
            ],
            [
                'name' => 'فاطمة أحمد',
                'email' => 'fatima.student@example.com',
                'password' => 'Student@123',
                'phone' => '+966500000004',
                'status' => 'active',
            ],
            [
                'name' => 'عبدالله سالم',
                'email' => 'abdullah.student@example.com',
                'password' => 'Student@123',
                'phone' => '+966500000005',
                'status' => 'active',
            ],
            [
                'name' => 'نورة عبدالرحمن',
                'email' => 'noura.student@example.com',
                'password' => 'Student@123',
                'phone' => '+966500000006',
                'status' => 'inactive',
            ],
        ];

        $studentRole = Role::where('name', 'student')->first();

        foreach ($students as $studentData) {
            $student = User::firstOrCreate(
                ['email' => $studentData['email']],
                [
                    'name' => $studentData['name'],
                    'password' => Hash::make($studentData['password']),
                    'email_verified_at' => now(),
                    'type' => 'student',
                    'status' => $studentData['status'],
                    'phone' => $studentData['phone'],
                    'uuid' => \Str::uuid()
                ]
            );

            // Assign student role
            if ($studentRole && !$student->roles()->where('role_id', $studentRole->id)->exists()) {
                $student->roles()->attach($studentRole->id);
            }
        }
    }
}
