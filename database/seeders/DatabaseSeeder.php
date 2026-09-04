<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
        ]);

        foreach ([
            ['name' => 'Starter', 'price' => 9.00, 'download_limit' => 25, 'user_limit' => 1, 'features' => ['25 downloads per month', 'Personal workspace'], 'price_id' => 'pri_starter_demo', 'is_popular' => false],
            ['name' => 'Creator', 'price' => 19.00, 'download_limit' => 100, 'user_limit' => 3, 'features' => ['100 downloads per month', '3 users included'], 'price_id' => 'pri_creator_demo', 'is_popular' => true],
            ['name' => 'Studio', 'price' => 49.00, 'download_limit' => null, 'user_limit' => 10, 'features' => ['Unlimited downloads', '10 users included'], 'price_id' => 'pri_studio_demo', 'is_popular' => false],
        ] as $plan) {
            SubscriptionPlan::updateOrCreate(['price_id' => $plan['price_id']], $plan);
        }
    }
}
