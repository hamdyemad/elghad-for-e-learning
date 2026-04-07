<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Status field if not exists
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('type');
            }

            // Profile fields
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('password')->comment('Profile image path');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('avatar');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('gender');
            }

            // Instructor specific fields
            if (!Schema::hasColumn('users', 'specialization')) {
                $table->string('specialization', 255)->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('specialization');
            }
            if (!Schema::hasColumn('users', 'hourly_rate')) {
                $table->decimal('hourly_rate', 10, 2)->nullable()->after('bio')->comment('Hourly rate for instructors');
            }
            if (!Schema::hasColumn('users', 'experience_years')) {
                $table->integer('experience_years')->nullable()->after('hourly_rate');
            }

            // Instructor flag (legacy/compatibility) if not exists
            if (!Schema::hasColumn('users', 'is_instructor')) {
                $table->boolean('is_instructor')->default(false)->after('experience_years');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Conditionally drop columns that were added by this migration
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'avatar')) {
                $table->dropColumn('avatar');
            }
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'date_of_birth')) {
                $table->dropColumn('date_of_birth');
            }
            if (Schema::hasColumn('users', 'gender')) {
                $table->dropColumn('gender');
            }
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('users', 'specialization')) {
                $table->dropColumn('specialization');
            }
            if (Schema::hasColumn('users', 'bio')) {
                $table->dropColumn('bio');
            }
            if (Schema::hasColumn('users', 'hourly_rate')) {
                $table->dropColumn('hourly_rate');
            }
            if (Schema::hasColumn('users', 'experience_years')) {
                $table->dropColumn('experience_years');
            }
            if (Schema::hasColumn('users', 'is_instructor')) {
                $table->dropColumn('is_instructor');
            }
        });
    }
};
