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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('topic');
            $table->string('title');
            $table->string('outsource_link')->nullable();
            $table->string('outsource_type')->nullable()->comment('vimeo, firebase, vdocipher');
            $table->boolean('is_free')->default(false);
            $table->integer('duration')->nullable()->comment('Duration in seconds');
            $table->string('file_pdf')->nullable();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
