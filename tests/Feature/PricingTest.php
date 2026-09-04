<?php

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pricing page reflects subscription plans created by the super admin', function () {
    SubscriptionPlan::factory()->create([
        'name' => 'Creator Plus',
        'price' => 24.99,
        'download_limit' => 250,
        'user_limit' => 3,
        'features' => ['Commercial use', 'Priority support'],
        'is_popular' => true,
    ]);

    $this->get(route('pricing'))
        ->assertOk()
        ->assertSee('Creator Plus')
        ->assertSee('24.99')
        ->assertSee('250')
        ->assertSee('3')
        ->assertSee('Commercial use')
        ->assertSee('Most popular');
});

test('pricing page shows an empty state when no plans exist', function () {
    $this->get(route('pricing'))
        ->assertOk()
        ->assertSee('Subscription plans will be available soon.');
});

test('pricing plans are displayed from lowest to highest price', function () {
    SubscriptionPlan::factory()->create(['name' => 'Pro', 'price' => 29.99]);
    SubscriptionPlan::factory()->create(['name' => 'Starter', 'price' => 9.99]);
    SubscriptionPlan::factory()->create(['name' => 'Team', 'price' => 19.99]);

    $response = $this->get(route('pricing'));
    $response->assertOk();
    preg_match_all('/<h2[^>]*>(.*?)<\/h2>/s', $response->getContent(), $matches);
    $planHeadings = array_map(fn (string $heading): string => trim(strip_tags(html_entity_decode($heading))), $matches[1]);

    expect($planHeadings)->toBe(['Starter', 'Team', 'Pro']);
});

test('customers purchase plans from pricing with a Paddle transaction return URL', function () {
    $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
    $plan = SubscriptionPlan::factory()->create(['name' => 'Creator']);

    $this->actingAs($customer)
        ->get(route('pricing'))
        ->assertOk()
        ->assertSee('data-paddle-price')
        ->assertSee('successUrl')
        ->assertDontSee('{transaction_id}');
});
