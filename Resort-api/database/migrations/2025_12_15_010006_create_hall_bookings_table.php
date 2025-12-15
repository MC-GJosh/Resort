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
        Schema::create('hall_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('function_hall_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->date('event_date');
            $table->integer('guest_count');
            
            // Catering options
            $table->string('catering_package')->nullable();
            $table->string('main_dish')->nullable();
            $table->string('appetizer')->nullable();
            $table->string('drink')->nullable();
            $table->boolean('avail_mini_bar')->default(false);
            
            $table->string('payment_method')->default('GCash');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hall_bookings');
    }
};
