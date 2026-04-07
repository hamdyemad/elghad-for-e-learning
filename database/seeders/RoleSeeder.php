<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator with full access', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'instructor', 'description' => 'Course instructor', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'student', 'description' => 'Student user', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
