<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('professor_profit', 10, 2)->default(0)->comment('Percentage or fixed amount for instructor profit');
            $table->enum('status', ['draft', 'published', 'public'])->default('draft');
            $table->string('level')->nullable(); // beginner, intermediate, advanced
            $table->string('duration')->nullable(); // e.g., "10 hours"
            $table->string('thumbnail')->nullable();
            $table->boolean('is_free')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
};
