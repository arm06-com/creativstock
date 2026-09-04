<?php

use App\Models\CustomerSubscription;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\User;

it('allows admin and super admin to view the financial report', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

    $plan = SubscriptionPlan::factory()->create(['price' => 29.99, 'download_limit' => 20]);
    CustomerSubscription::query()->create([
        'user_id' => $admin->id,
        'subscription_plan_id' => $plan->id,
        'status' => CustomerSubscription::STATUS_ACTIVE,
        'downloads_used' => 5,
    ]);

    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    $product = Product::create([
        'user_id' => $contributor->id,
        'status' => Product::STATUS_APPROVED,
        'title' => 'Financial Asset',
        'file_path' => 'assets/revenue.png',
        'file_name' => 'revenue.png',
        'mime_type' => 'image/png',
    ]);
    $product->downloads()->create(['user_id' => $admin->id, 'customer_id' => $admin->id]);

    $this->actingAs($admin)
        ->get(route('admin.financial-report.index'))
        ->assertOk()
        ->assertSee('Financial Report')
        ->assertSee('Monthly recurring revenue');

    $this->actingAs($superAdmin)
        ->get(route('admin.financial-report.index'))
        ->assertOk();
});
