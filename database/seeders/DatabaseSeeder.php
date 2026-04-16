<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Central accounts
     * ─────────────────────────────────────────────────────────────────
     * Super Admin : admin@mjf.edu          / password
     * Tenant 1    : owner@alnour.edu       / password  (Al-Nour School)
     * Tenant 2    : owner@alfurqan.edu     / password  (Al-Furqan Academy)
     *
     * Tenant accounts (same password: "password" for all)
     * ─────────────────────────────────────────────────────────────────
     * Admin    : admin@<tenantname>.edu
     * Teachers : e.g. sara.mousa@tenant.edu   (5 per tenant)
     * Students : e.g. ahmad.ghamdi@student.edu (15 per tenant)
     */
    public function run(): void
    {
        // 1. Subscription plans
        $this->call(SubscriptionSeeder::class);

        // 2. Super admin (can access all tenants)
        User::firstOrCreate(
            ['email' => 'admin@mjf.edu'],
            [
                'name'           => 'MJF Super Admin',
                'password'       => Hash::make('password'),
                'is_super_admin' => true,
            ]
        );

        // 3. Tenants + their full datasets
        $this->call(TenantSeeder::class);
    }
}
