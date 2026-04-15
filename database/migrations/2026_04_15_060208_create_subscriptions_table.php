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
        Schema::create('subscriptions', function (Blueprint $table) {
           $table->id(); 
           $table->string('title'); 
           $table->text('description')->nullable(); 
           $table->decimal('price', 8, 2); 
           $table->string('currency', 3)->default('USD'); 
           $table->unsignedSmallInteger('duration_in_days'); 
           $table->enum('billing_period', ['monthly','annually','lifetime']); 
           $table->json('features')->nullable(); 
           $table->enum('status', ['active','archived'])->default('active'); 
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
