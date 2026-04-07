<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@elghad.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('Admin@123'),
                'email_verified_at' => now(),
                'type' => 'admin',
                'uuid' => \Str::uuid()
            ]
        );

        // Assign admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole->id);
        }

        // Create sample student user
        $student = User::firstOrCreate(
            ['email' => 'student@elghad.com'],
            [
                'name' => 'Sample Student',
                'password' => Hash::make('Student@123'),
                'type' => 'student',
                'uuid' => \Str::uuid()
            ]
        );

        // Assign student role
        $studentRole = Role::where('name', 'student')->first();
        if ($studentRole && !$student->roles()->where('role_id', $studentRole->id)->exists()) {
            $student->roles()->attach($studentRole->id);
        }
    }
}
