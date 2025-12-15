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
        Schema::create('function_halls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price_per_4hrs', 10, 2);
            $table->integer('min_capacity')->default(30);
            $table->integer('max_capacity')->default(100);
            $table->string('size')->nullable();
            $table->text('description')->nullable();
            $table->json('amenities')->nullable();
            $table->string('image')->nullable();
            $table->string('image_class')->nullable();
            $table->string('badge')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('function_halls');
    }
};
