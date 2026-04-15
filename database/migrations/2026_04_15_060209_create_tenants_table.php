<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['masjed', 'school', 'university']);
            $table->string('db_name')->unique();
            $table->string('db_user')->nullable();
            $table->string('db_password')->nullable();
            $table->string('domain')->nullable()->unique();
            $table->string('subdomain')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->foreignId('owner_user_id')->constrained('users');
            $table->json('settings')->nullable();
            // Required by stancl/tenancy for internal key-value storage
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
