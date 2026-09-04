<?php

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\CommissionSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('only super admins can access subscription plans', function () {
    $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($superAdmin)->get(route('admin.subscription-plans.index'))->assertOk();
    $this->actingAs($admin)->get(route('admin.subscription-plans.index'))->assertForbidden();
});

test('super admins can create edit delete and choose a popular subscription plan', function () {
    $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

    $component = Livewire::actingAs($superAdmin)->test('admin.subscription-plans');

    $component
        ->call('createPlan')
        ->set('name', 'Creator')
        ->set('price', '19.99')
        ->set('downloadLimit', '100')
        ->set('userLimit', '2')
        ->set('features', "Commercial license\nPriority support")
        ->set('priceId', 'pri_creator')
        ->set('isPopular', true)
        ->call('savePlan')
        ->assertHasNoErrors();

    $plan = SubscriptionPlan::firstOrFail();

    expect($plan->name)->toBe('Creator')
        ->and($plan->price)->toBe('19.99')
        ->and($plan->features)->toBe(['Commercial license', 'Priority support'])
        ->and($plan->is_popular)->toBeTrue();

    $component
        ->call('editPlan', $plan->id)
        ->set('name', 'Creator Plus')
        ->set('isPopular', false)
        ->call('savePlan')
        ->call('deletePlan', $plan->id);

    expect(SubscriptionPlan::query()->find($plan->id))->toBeNull();
});

test('selecting a popular plan unselects the previous popular plan', function () {
    $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
    $first = SubscriptionPlan::factory()->create(['is_popular' => true]);
    $second = SubscriptionPlan::factory()->create();

    Livewire::actingAs($superAdmin)
        ->test('admin.subscription-plans')
        ->call('editPlan', $second->id)
        ->set('isPopular', true)
        ->call('savePlan');

    expect($first->refresh()->is_popular)->toBeFalse()
        ->and($second->refresh()->is_popular)->toBeTrue();
});

test('super admins can update the contributor commission share', function () {
    $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

    Livewire::actingAs($superAdmin)
        ->test('admin.commission-share')
        ->assertSet('percentage', '50.00')
        ->set('percentage', '60')
        ->call('save')
        ->assertHasNoErrors();

    expect((float) CommissionSetting::current()->percentage)->toBe(60.0);
});

test('admins can view commission share but cannot change the percentage', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)->get(route('admin.commission-share.index'))->assertOk();

    Livewire::actingAs($admin)
        ->test('admin.commission-share')
        ->set('percentage', '75')
        ->call('save')
        ->assertForbidden();

    expect((float) CommissionSetting::current()->percentage)->toBe(50.0);
});
