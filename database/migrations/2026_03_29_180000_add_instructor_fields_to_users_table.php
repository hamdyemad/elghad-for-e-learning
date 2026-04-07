<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('specialization')->after('type')->nullable();
            $table->text('bio')->after('specialization')->nullable();
            $table->decimal('hourly_rate', 10, 2)->after('bio')->nullable();
            $table->integer('experience_years')->after('hourly_rate')->nullable();
            $table->boolean('is_instructor')->after('experience_years')->default(false);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['specialization', 'bio', 'hourly_rate', 'experience_years', 'is_instructor']);
        });
    }
};
