<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a central user can login, view profile, and logout', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $loginResponse = $this->postJson('/api/login', [
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $loginResponse
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.user.email', 'admin@example.com');

    $token = $loginResponse->json('data.token');

    $this->withToken($token)
        ->getJson('/api/me')
        ->assertOk()
        ->assertJsonPath('data.email', 'admin@example.com');

    $this->withToken($token)
        ->postJson('/api/logout')
        ->assertOk()
        ->assertJsonPath('message', 'Logout successful');

    expect($user->fresh()->tokens()->count())->toBe(0);

    $this->app['auth']->forgetGuards();

    $this->withToken($token)
        ->getJson('/api/me')
        ->assertUnauthorized();
});

test('login fails with invalid credentials', function () {
    User::factory()->create([
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $this->postJson('/api/login', [
        'email' => 'admin@example.com',
        'password' => 'wrong-password',
    ])
        ->assertUnauthorized()
        ->assertJsonPath('message', 'Invalid credentials.');
});
