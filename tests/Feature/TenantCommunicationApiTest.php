<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Modules\Tenant\Infrastructure\Models\Announcement;
use App\Modules\Tenant\Infrastructure\Models\Event;
use App\Modules\Tenant\Infrastructure\Models\TenantUser;

uses(RefreshDatabase::class);

function seedTenantUserForComm(User $user, string $type = 'admin'): TenantUser
{
    return withTenant($user, function () use ($type) {
        $suffix = Str::lower(Str::random(6));
        return TenantUser::query()->create([
            'name'     => ucfirst($type) . ' ' . $suffix,
            'email'    => "{$type}-{$suffix}@example.com",
            'password' => 'password123',
            'type'     => $type,
        ]);
    });
}

// ─── Announcements ────────────────────────────────────────────────────────────

test('an authenticated user can create and list announcements in the active tenant', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $tenantUser = seedTenantUserForComm($user, 'admin');

    $this->withToken($token)
        ->postJson('/api/tenant/announcements', [
            'title'         => 'Welcome Announcement',
            'content'       => 'Welcome to the new semester!',
            'created_by'    => $tenantUser->id,
            'audience_type' => 'all',
            'published_at'  => '2026-05-01 09:00:00',
        ])
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.title', 'Welcome Announcement')
        ->assertJsonPath('data.audience_type', 'all')
        ->assertJsonPath('data.creator.id', $tenantUser->id);

    $this->withToken($token)
        ->getJson('/api/tenant/announcements')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Welcome Announcement');
});

test('an authenticated user can show a single announcement', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $tenantUser = seedTenantUserForComm($user);

    $announcement = withTenant($user, function () use ($tenantUser) {
        return Announcement::query()->create([
            'title'      => 'Show Test',
            'content'    => 'Content here',
            'created_by' => $tenantUser->id,
        ]);
    });

    $this->withToken($token)
        ->getJson("/api/tenant/announcements/{$announcement->id}")
        ->assertOk()
        ->assertJsonPath('data.title', 'Show Test')
        ->assertJsonPath('data.creator.id', $tenantUser->id);
});

test('an authenticated user can update an announcement', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $tenantUser = seedTenantUserForComm($user);

    $announcement = withTenant($user, function () use ($tenantUser) {
        return Announcement::query()->create([
            'title'         => 'Original Title',
            'content'       => 'Original content',
            'created_by'    => $tenantUser->id,
            'audience_type' => 'students',
        ]);
    });

    $this->withToken($token)
        ->putJson("/api/tenant/announcements/{$announcement->id}", [
            'title'         => 'Updated Title',
            'content'       => 'Updated content',
            'created_by'    => $tenantUser->id,
            'audience_type' => 'teachers',
        ])
        ->assertOk()
        ->assertJsonPath('data.title', 'Updated Title')
        ->assertJsonPath('data.audience_type', 'teachers');
});

test('an authenticated user can delete an announcement', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $tenantUser = seedTenantUserForComm($user);

    $announcement = withTenant($user, function () use ($tenantUser) {
        return Announcement::query()->create([
            'title'      => 'To Delete',
            'content'    => 'Will be deleted',
            'created_by' => $tenantUser->id,
        ]);
    });

    $this->withToken($token)
        ->deleteJson("/api/tenant/announcements/{$announcement->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Announcement deleted successfully');

    withTenant($user, function () {
        expect(Announcement::query()->count())->toBe(0);
    });
});

// ─── Events ───────────────────────────────────────────────────────────────────

test('an authenticated user can create and list events in the active tenant', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $tenantUser = seedTenantUserForComm($user);

    $this->withToken($token)
        ->postJson('/api/tenant/events', [
            'title'       => 'Annual Science Fair',
            'description' => 'Students showcase their projects',
            'start_date'  => '2026-06-10 09:00:00',
            'end_date'    => '2026-06-10 17:00:00',
            'location'    => 'Main Hall',
            'created_by'  => $tenantUser->id,
        ])
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.title', 'Annual Science Fair')
        ->assertJsonPath('data.location', 'Main Hall')
        ->assertJsonPath('data.creator.id', $tenantUser->id);

    $this->withToken($token)
        ->getJson('/api/tenant/events')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Annual Science Fair');
});

test('an authenticated user can show a single event', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $tenantUser = seedTenantUserForComm($user);

    $event = withTenant($user, function () use ($tenantUser) {
        return Event::query()->create([
            'title'      => 'Show Event',
            'start_date' => '2026-07-01 10:00:00',
            'created_by' => $tenantUser->id,
        ]);
    });

    $this->withToken($token)
        ->getJson("/api/tenant/events/{$event->id}")
        ->assertOk()
        ->assertJsonPath('data.title', 'Show Event')
        ->assertJsonPath('data.creator.id', $tenantUser->id);
});

test('an authenticated user can update an event', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $tenantUser = seedTenantUserForComm($user);

    $event = withTenant($user, function () use ($tenantUser) {
        return Event::query()->create([
            'title'      => 'Original Event',
            'start_date' => '2026-08-01 08:00:00',
            'location'   => 'Room A',
            'created_by' => $tenantUser->id,
        ]);
    });

    $this->withToken($token)
        ->putJson("/api/tenant/events/{$event->id}", [
            'title'       => 'Updated Event',
            'description' => 'New description',
            'start_date'  => '2026-08-05 09:00:00',
            'end_date'    => '2026-08-05 12:00:00',
            'location'    => 'Room B',
            'created_by'  => $tenantUser->id,
        ])
        ->assertOk()
        ->assertJsonPath('data.title', 'Updated Event')
        ->assertJsonPath('data.location', 'Room B');
});

test('an authenticated user can delete an event', function () {
    $user = User::factory()->create();
    provisionTenantForApi($user);
    $token = $user->createToken('tenant-api')->plainTextToken;
    $tenantUser = seedTenantUserForComm($user);

    $event = withTenant($user, function () use ($tenantUser) {
        return Event::query()->create([
            'title'      => 'To Delete Event',
            'start_date' => '2026-09-01 10:00:00',
            'created_by' => $tenantUser->id,
        ]);
    });

    $this->withToken($token)
        ->deleteJson("/api/tenant/events/{$event->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Event deleted successfully');

    withTenant($user, function () {
        expect(Event::query()->count())->toBe(0);
    });
});
