<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Central\Infrastructure\Models\Subscription;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'title'            => 'Basic Plan',
                'description'      => 'Ideal for small mosques and community centers. Includes core student & teacher management.',
                'price'            => 0,
                'currency'         => 'USD',
                'duration_in_days' => 30,
                'billing_period'   => 'monthly',
                'features'         => ['students' => 50, 'teachers' => 5, 'storage_gb' => 1, 'announcements' => true, 'events' => false],
                'status'           => 'active',
            ],
            [
                'title'            => 'Standard Plan',
                'description'      => 'Perfect for schools with full academic workflows — classes, assignments, grades, and more.',
                'price'            => 49.99,
                'currency'         => 'USD',
                'duration_in_days' => 30,
                'billing_period'   => 'monthly',
                'features'         => ['students' => 500, 'teachers' => 50, 'storage_gb' => 10, 'announcements' => true, 'events' => true],
                'status'           => 'active',
            ],
            [
                'title'            => 'Premium Plan',
                'description'      => 'Enterprise-grade for universities and large institutions. Unlimited capacity and priority support.',
                'price'            => 199.99,
                'currency'         => 'USD',
                'duration_in_days' => 365,
                'billing_period'   => 'annually',
                'features'         => ['students' => -1, 'teachers' => -1, 'storage_gb' => 100, 'announcements' => true, 'events' => true, 'api_access' => true],
                'status'           => 'active',
            ],
        ];

        foreach ($plans as $plan) {
            Subscription::firstOrCreate(['title' => $plan['title']], $plan);
        }
    }
}
