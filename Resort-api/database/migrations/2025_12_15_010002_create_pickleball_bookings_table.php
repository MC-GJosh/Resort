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
        Schema::create('pickleball_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('court_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('time_slot');
            $table->string('customer_name');
            $table->string('phone')->nullable();
            $table->string('payment_method')->default('GCash');
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('confirmed');
            $table->timestamps();

            // Prevent double booking
            $table->unique(['court_id', 'date', 'time_slot'], 'unique_court_slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickleball_bookings');
    }
};
