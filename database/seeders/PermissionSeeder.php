<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User management
            ['name' => 'users.view', 'description' => 'View users', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'users.create', 'description' => 'Create users', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'users.edit', 'description' => 'Edit users', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'users.delete', 'description' => 'Delete users', 'created_at' => now(), 'updated_at' => now()],

            // Category management
            ['name' => 'categories.view', 'description' => 'View categories', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'categories.create', 'description' => 'Create categories', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'categories.edit', 'description' => 'Edit categories', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'categories.delete', 'description' => 'Delete categories', 'created_at' => now(), 'updated_at' => now()],

            // Course management
            ['name' => 'courses.view', 'description' => 'View courses', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'courses.create', 'description' => 'Create courses', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'courses.edit', 'description' => 'Edit courses', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'courses.delete', 'description' => 'Delete courses', 'created_at' => now(), 'updated_at' => now()],

            // Content management
            ['name' => 'lessons.view', 'description' => 'View lessons', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'lessons.create', 'description' => 'Create lessons', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'lessons.edit', 'description' => 'Edit lessons', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'lessons.delete', 'description' => 'Delete lessons', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission['name']], $permission);
        }

        // Assign all permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching(
                Permission::all()->pluck('id')->toArray()
            );
        }

        // Assign basic view permissions to student role
        $studentRole = Role::where('name', 'student')->first();
        if ($studentRole) {
            $studentPermissions = [
                'categories.view',
                'courses.view',
                'lessons.view',
            ];
            $studentRole->permissions()->syncWithoutDetaching(
                Permission::whereIn('name', $studentPermissions)->pluck('id')->toArray()
            );
        }

        // Instructor permissions (same as student + create/edit their own courses)
        $instructorRole = Role::where('name', 'instructor')->first();
        if ($instructorRole) {
            $instructorPermissions = [
                'categories.view',
                'courses.view',
                'courses.create',
                'courses.edit',
                'lessons.view',
                'lessons.create',
                'lessons.edit',
            ];
            $instructorRole->permissions()->syncWithoutDetaching(
                Permission::whereIn('name', $instructorPermissions)->pluck('id')->toArray()
            );
        }
    }
}
