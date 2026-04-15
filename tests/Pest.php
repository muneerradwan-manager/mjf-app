<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use App\Modules\Central\Infrastructure\Models\Tenant;
use App\Modules\Central\Infrastructure\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
 // ->use(RefreshDatabase::class)
    ->in('Feature');

beforeEach(function () {
    $GLOBALS['tenant_db_files'] = [];
});

afterEach(function () {
    foreach (($GLOBALS['tenant_db_files'] ?? []) as $file) {
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    $GLOBALS['tenant_db_files'] = [];
});

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

function provisionTenantForApi(User $user): Tenant
{
    $subscription = Subscription::query()->create([
        'title' => 'Basic Plan',
        'description' => 'Default plan',
        'price' => 0,
        'currency' => 'USD',
        'duration_in_days' => 30,
        'billing_period' => 'monthly',
        'status' => 'active',
    ]);

    $dbName = 'tenant_test_' . Str::lower(Str::random(10)) . '.sqlite';
    $dbPath = database_path($dbName);
    file_put_contents($dbPath, '');
    $GLOBALS['tenant_db_files'][] = $dbPath;

    $tenant = Tenant::query()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'Tenant Test',
        'slug' => 'tenant-test-' . Str::lower(Str::random(5)),
        'code' => strtoupper(Str::random(8)),
        'email' => 'tenant-' . Str::lower(Str::random(5)) . '@example.com',
        'subscription_id' => $subscription->id,
        'type' => 'school',
        'db_name' => $dbName,
        'owner_user_id' => $user->id,
    ]);

    $user->tenants()->attach($tenant->id, ['role' => 'admin']);
    $user->forceFill(['current_tenant_id' => $tenant->id])->save();

    tenancy()->initialize($tenant);

    Artisan::call('migrate', [
        '--database' => 'tenant',
        '--path' => database_path('migrations/tenant'),
        '--realpath' => true,
        '--force' => true,
    ]);

    tenancy()->end();

    return $tenant;
}

function withTenant(User $user, callable $callback): mixed
{
    $tenant = Tenant::query()->findOrFail($user->current_tenant_id);

    tenancy()->initialize($tenant);

    try {
        return $callback($tenant);
    } finally {
        tenancy()->end();
    }
}
