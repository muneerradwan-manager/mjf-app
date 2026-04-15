<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Central\Infrastructure\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        Subscription::create([
            'title' => 'Basic Plan',
            'description' => 'Default plan',
            'price' => 0,
            'currency' => 'USD',
            'duration_in_days' => 30,
            'billing_period' => 'monthly',
            'status' => 'active'
        ]);
    }
}
