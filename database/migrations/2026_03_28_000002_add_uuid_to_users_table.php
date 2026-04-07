<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // If uuid column already exists, clean it up first
        if (Schema::hasColumn('users', 'uuid')) {
            // Try to drop existing unique index
            try {
                DB::statement('DROP INDEX IF EXISTS users_uuid_unique ON users');
            } catch (\Exception $e) {
                // Ignore errors
            }
            // Drop the column to start fresh
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }

        // Add uuid column as nullable
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });

        // Populate UUIDs for all existing users
        \App\Models\User::whereNull('uuid')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                $user->uuid = \Str::uuid();
                $user->save();
            }
        });

        // Make uuid not nullable and add unique constraint
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'uuid')) {
            // Drop unique index first
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropUnique('users_uuid_unique');
                });
            } catch (\Exception $e) {
                // Ignore if index doesn't exist
            }
            // Drop column
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};
