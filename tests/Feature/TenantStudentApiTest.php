<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Modules\Central\Infrastructure\Models\Tenant;
use App\Modules\Tenant\Infrastructure\Models\Student;
use App\Modules\Tenant\Infrastructure\Models\TenantUser;

uses(RefreshDatabase::class);

test('tenant students routes require authentication', function () {
    $this->getJson('/api/tenant/students')
        ->assertUnauthorized();
});

test('tenant students routes require an active tenant selection', function () {
    $user = User::factory()->create();
    $token = $user->createToken('tenant-api')->plainTextToken;

    $this->withToken($token)
        ->getJson('/api/tenant/students')
        ->assertStatus(409)
        ->assertJsonPath('message', 'No active tenant selected. Use /api/central/current-tenant first.');
});

test('an authenticated user can list and create students in the active tenant', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/tenant/students', [
            'name' => 'Student One',
            'email' => 'student1@example.com',
            'password' => 'password123',
            'student_id_number' => 'STD-001',
            'phone' => '123456789',
            'parent_name' => 'Parent One',
        ])
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.user.email', 'student1@example.com')
        ->assertJsonPath('data.student_id_number', 'STD-001');

    $this->withToken($token)
        ->getJson('/api/tenant/students')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.user.name', 'Student One');
});

test('an authenticated user can update and delete a tenant student', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;

    $student = withTenant($user, function () {
        $tenantUser = TenantUser::query()->create([
            'name' => 'Original Student',
            'email' => 'original.student@example.com',
            'password' => 'password123',
            'type' => 'student',
        ]);

        return Student::query()->create([
            'user_id' => $tenantUser->id,
            'student_id_number' => 'STD-OLD',
        ]);
    });

    $this->withToken($token)
        ->putJson("/api/tenant/students/{$student->id}", [
            'name' => 'Updated Student',
            'email' => 'updated.student@example.com',
            'password' => 'newpassword123',
            'student_id_number' => 'STD-NEW',
            'parent_name' => 'Updated Parent',
        ])
        ->assertOk()
        ->assertJsonPath('data.user.name', 'Updated Student')
        ->assertJsonPath('data.student_id_number', 'STD-NEW');

    $this->withToken($token)
        ->deleteJson("/api/tenant/students/{$student->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Student deleted successfully');

    withTenant($user, function () {
        expect(Student::query()->count())->toBe(0);
        expect(TenantUser::query()->count())->toBe(0);
    });
});
