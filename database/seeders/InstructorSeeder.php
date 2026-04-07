<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instructors = [
            [
                'name' => 'دكتور أحمد محمد',
                'email' => 'ahmed@elghad.com',
                'password' => 'Instructor@123',
            ],
            [
                'name' => 'أستاذة سارة خالد',
                'email' => 'sara@elghad.com',
                'password' => 'Instructor@123',
            ],
            [
                'name' => 'محمد علي',
                'email' => 'mohammed@elghad.com',
                'password' => 'Instructor@123',
            ],
            [
                'name' => 'دينا حسام',
                'email' => 'dina@elghad.com',
                'password' => 'Instructor@123',
            ],
        ];

        $instructorRole = Role::where('name', 'instructor')->first();

        foreach ($instructors as $instructorData) {
            $instructor = User::firstOrCreate(
                ['email' => $instructorData['email']],
                [
                    'name' => $instructorData['name'],
                    'password' => Hash::make($instructorData['password']),
                    'email_verified_at' => now(),
                    'type' => 'instructor',
                    'uuid' => \Str::uuid()
                ]
            );

            // Assign instructor role
            if ($instructorRole && !$instructor->roles()->where('role_id', $instructorRole->id)->exists()) {
                $instructor->roles()->attach($instructorRole->id);
            }
        }
    }
}
