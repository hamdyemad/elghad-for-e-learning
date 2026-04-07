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
        Schema::create('package_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_transaction_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // Unique constraint: a user can only subscribe once to a package
            $table->unique(['user_id', 'package_id']);

            // Indexes for queries
            $table->index('user_id');
            $table->index('package_id');
            $table->index('subscribed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_student');
    }
};
