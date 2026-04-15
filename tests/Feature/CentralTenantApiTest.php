<?php

use App\Models\User;
use Mockery\MockInterface;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Modules\Central\Application\Services\TenantProvisionService;
use App\Modules\Central\Infrastructure\Models\Tenant;
use App\Modules\Central\Infrastructure\Models\Subscription;

uses(RefreshDatabase::class);

test('creating a tenant requires authentication', function () {
    $this->postJson('/api/central/tenants', [
        'name' => 'Tenant One',
        'email' => 'tenant@example.com',
        'subscription_id' => 1,
        'type' => 'school',
    ])->assertUnauthorized();
});

test('an authenticated user can create a tenant without sending owner_user_id', function () {
    $user = User::factory()->create();
    $subscription = Subscription::create([
        'title' => 'Basic Plan',
        'description' => 'Default plan',
        'price' => 0,
        'currency' => 'USD',
        'duration_in_days' => 30,
        'billing_period' => 'monthly',
        'status' => 'active',
    ]);

    $tenant = new Tenant([
        'uuid' => (string) Str::uuid(),
        'name' => 'Tenant One',
        'slug' => 'tenant-one',
        'code' => 'TENANT01',
        'email' => 'tenant@example.com',
        'subscription_id' => $subscription->id,
        'type' => 'school',
        'db_name' => 'tenant_testing_01',
        'owner_user_id' => $user->id,
    ]);
    $tenant->id = 999;
    $tenant->exists = true;

    $expectedPayload = [
        'name' => 'Tenant One',
        'email' => 'tenant@example.com',
        'subscription_id' => $subscription->id,
        'type' => 'school',
    ];

    $this->mock(TenantProvisionService::class, function (MockInterface $mock) use ($expectedPayload, $tenant) {
        $mock->shouldReceive('createTenantWithDatabase')
            ->once()
            ->with($expectedPayload)
            ->andReturn($tenant);
    });

    Sanctum::actingAs($user);

    $this->postJson('/api/central/tenants', $expectedPayload)
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.email', 'tenant@example.com');
});

test('an authenticated user can set the current tenant when they have access to it', function () {
    $user = User::factory()->create();
    $subscription = Subscription::create([
        'title' => 'Basic Plan',
        'description' => 'Default plan',
        'price' => 0,
        'currency' => 'USD',
        'duration_in_days' => 30,
        'billing_period' => 'monthly',
        'status' => 'active',
    ]);

    $tenant = Tenant::create([
        'uuid' => (string) Str::uuid(),
        'name' => 'Tenant One',
        'slug' => 'tenant-one',
        'code' => 'TENANT01',
        'email' => 'tenant@example.com',
        'subscription_id' => $subscription->id,
        'type' => 'school',
        'db_name' => 'tenant_testing_02',
        'owner_user_id' => $user->id,
    ]);

    $user->tenants()->attach($tenant->id, ['role' => 'admin']);

    Sanctum::actingAs($user);

    $this->postJson('/api/central/current-tenant', [
        'tenant_id' => $tenant->id,
    ])
        ->assertOk()
        ->assertJsonPath('data.current_tenant_id', $tenant->id);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'current_tenant_id' => $tenant->id,
    ]);
});
