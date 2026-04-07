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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('facebook')->nullable()->max(500);
            $table->string('instagram')->nullable()->max(500);
            $table->string('tiktok')->nullable()->max(500);
            $table->string('mobile_number')->nullable()->max(500)->comment('Mobile phone number');
            $table->text('terms_of_use')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
