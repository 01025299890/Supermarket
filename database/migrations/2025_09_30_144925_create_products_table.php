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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('type');
            $table->string('brand')->nullable();
            $table->string('name');
            $table->decimal('unit_quantity', 8, 2);
            $table->enum('unit', ['kg', 'g', 'l', 'ml', 'pcs'])->default('kg');
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->unsignedInteger('available_quantity')->default(0);
            $table->decimal('rating', 3, 1)->default(0);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
