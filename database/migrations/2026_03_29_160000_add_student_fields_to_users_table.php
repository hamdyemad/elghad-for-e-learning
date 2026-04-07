<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->after('email')->nullable();
            $table->text('address')->after('phone')->nullable();
            $table->date('date_of_birth')->after('address')->nullable();
            $table->date('enrollment_date')->after('date_of_birth')->nullable();
            $table->text('notes')->after('enrollment_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address', 'date_of_birth', 'enrollment_date', 'notes']);
        });
    }
};
