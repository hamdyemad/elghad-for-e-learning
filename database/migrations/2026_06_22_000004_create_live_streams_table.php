<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_streams', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('url');
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index('course_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_streams');
    }
};
