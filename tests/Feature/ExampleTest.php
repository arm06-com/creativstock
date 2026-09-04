<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});

test('approved gallery assets can be downloaded but unapproved assets cannot', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $approved = Product::create([
        'user_id' => $user->id,
        'status' => Product::STATUS_APPROVED,
        'file_path' => 'gallery/approved.png',
        'file_name' => 'approved.png',
        'mime_type' => 'image/png',
    ]);
    Storage::disk('public')->put($approved->file_path, 'image data');
    $pending = Product::create([
        'user_id' => $user->id,
        'status' => Product::STATUS_PENDING,
        'file_path' => 'gallery/pending.png',
        'file_name' => 'pending.png',
    ]);

    $this->get(route('assets.download', $approved))->assertOk();
    $this->get(route('assets.download', $pending))->assertNotFound();
});

test('the home image modal includes related approved images', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Nature']);
    $attributes = [
        'user_id' => $user->id,
        'category_id' => $category->id,
        'asset_type' => 'Image',
        'description' => 'A gallery image.',
        'tags' => ['nature'],
        'status' => Product::STATUS_APPROVED,
        'mime_type' => 'image/png',
    ];

    Product::create($attributes + ['title' => 'Forest light', 'file_path' => 'gallery/forest.png', 'file_name' => 'forest.png']);
    Product::create($attributes + ['title' => 'Mountain light', 'file_path' => 'gallery/mountain.png', 'file_name' => 'mountain.png']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('xl:columns-5')
        ->assertSee('Related images')
        ->assertSee('Mountain light');
});
