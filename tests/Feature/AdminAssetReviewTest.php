<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function createReviewAsset(User $contributor, string $status = Product::STATUS_PENDING): Product
{
    return Product::create([
        'user_id' => $contributor->id,
        'category_id' => Category::create(['name' => 'Review category'])->id,
        'asset_type' => 'Image',
        'title' => 'Review image',
        'description' => 'Image details for review.',
        'tags' => ['review'],
        'is_ai' => false,
        'status' => $status,
        'file_path' => 'contributors/review.png',
        'file_name' => 'review.png',
        'image_size' => '12 KB',
        'dimensions' => '111x111',
    ]);
}

test('admins can access asset review queues', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->get(route('admin.assets.index', ['status' => Product::STATUS_PENDING]))
        ->assertOk()
        ->assertSee('Assets')
        ->assertSee('Pending (0)');
});

test('admins must provide a price before approving an asset', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    $asset = createReviewAsset($contributor);

    Livewire::actingAs($admin)
        ->test('admin.asset-review', ['status' => Product::STATUS_PENDING])
        ->call('openAsset', $asset->id)
        ->call('approveAsset')
        ->assertHasErrors(['price' => 'required'])
        ->set('price', '9.99')
        ->call('approveAsset')
        ->assertHasNoErrors();

    expect($asset->refresh()->status)->toBe(Product::STATUS_APPROVED)
        ->and($asset->price)->toBe('9.99')
        ->and($asset->rejection_reason)->toBeNull();
});

test('admins must provide a reason before rejecting an asset', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    $asset = createReviewAsset($contributor);

    Livewire::actingAs($admin)
        ->test('admin.asset-review', ['status' => Product::STATUS_PENDING])
        ->call('openAsset', $asset->id)
        ->call('rejectAsset')
        ->assertHasErrors(['rejectionReason' => 'required'])
        ->set('rejectionReason', 'Please provide a more descriptive title.')
        ->call('rejectAsset')
        ->assertHasNoErrors();

    expect($asset->refresh()->status)->toBe(Product::STATUS_REJECTED)
        ->and($asset->rejection_reason)->toBe('Please provide a more descriptive title.')
        ->and($asset->price)->toBeNull();
});

test('admins can open a pending asset modal before approving it', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    $asset = createReviewAsset($contributor);

    Livewire::actingAs($admin)
        ->test('admin.asset-review', ['status' => Product::STATUS_PENDING])
        ->call('openAsset', $asset->id)
        ->assertSet('showReviewModal', true)
        ->assertSet('selectedAssetId', $asset->id);
});

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
