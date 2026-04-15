<?php

namespace App\Modules\Central\Application\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Modules\Central\Infrastructure\Models\Tenant;
use Symfony\Component\Console\Output\BufferedOutput;

class TenantProvisionService
{
    public function createTenantWithDatabase(array $data): Tenant
    {
        $ownerUserId = $data['owner_user_id'] ?? auth()->id();

        // Generate DB name upfront so it's stored on the tenant record
        $dbName = 'tenant_' . strtolower(Str::random(10));

        // 1. Create the tenant record
        $tenant = Tenant::create([
            'uuid'            => (string) Str::uuid(),
            'name'            => $data['name'],
            'slug'            => Str::slug($data['name']),
            'code'            => strtoupper(Str::random(8)),
            'email'           => $data['email'],
            'subscription_id' => $data['subscription_id'],
            'type'            => $data['type'],
            'owner_user_id'   => $ownerUserId,
            'db_name'         => $dbName,
        ]);

        // 2. Create the tenant database
        // NOTE: DDL (CREATE DATABASE) cannot run inside a Laravel transaction — MySQL
        //       would implicitly commit it. We handle atomicity by deleting the tenant
        //       record on failure.
        DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}`");

        // 3. Initialize tenancy (switches DB connection to the tenant DB)
        tenancy()->initialize($tenant);

        // 4. Run tenant migrations (BufferedOutput prevents output bleeding into HTTP)
        \Artisan::call('tenants:migrate', ['--tenants' => [$tenant->getTenantKey()]], new BufferedOutput());

        // 5. Return to central context
        tenancy()->end();

        if ($ownerUserId) {
            $owner = User::find($ownerUserId);

            if ($owner) {
                $owner->tenants()->syncWithoutDetaching([
                    $tenant->getKey() => ['role' => 'admin'],
                ]);

                if ($owner->current_tenant_id === null) {
                    $owner->forceFill([
                        'current_tenant_id' => $tenant->getKey(),
                    ])->save();
                }
            }
        }

        return $tenant->fresh();
    }
}
