<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('contributors can view their asset status counts', function () {
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    $createProduct = fn (int $userId, string $status): Product => Product::create([
        'user_id' => $userId,
        'status' => $status,
        'file_path' => 'assets/example.jpg',
        'file_name' => 'example.jpg',
    ]);

    $createProduct($contributor->id, Product::STATUS_UPLOADED);
    $createProduct($contributor->id, Product::STATUS_PENDING);
    $createProduct($contributor->id, Product::STATUS_PENDING);
    $createProduct(User::factory()->create()->id, Product::STATUS_APPROVED);

    $this->actingAs($contributor)
        ->get(route('contributor.assets.index'))
        ->assertOk()
        ->assertSee('Uploaded (1)')
        ->assertSee('Pending (2)')
        ->assertSee('Approved (0)')
        ->assertSee('Rejected (0)');
});

test('non contributors cannot view contributor assets', function () {
    $user = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

    $this->actingAs($user)
        ->get(route('contributor.assets.index'))
        ->assertForbidden();
});

test('admins cannot view contributor assets', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->get(route('contributor.assets.index'))
        ->assertForbidden();
});

test('contributors can upload multiple images', function () {
    Storage::fake('public');
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true);
    $uploads = [
        UploadedFile::fake()->createWithContent('cover.png', $png),
        UploadedFile::fake()->createWithContent('texture.png', $png),
    ];

    Livewire::actingAs($contributor)
        ->test('contributor.asset-upload')
        ->set('uploads', $uploads)
        ->assertHasNoErrors()
        ->assertSee('cover.png')
        ->assertSee('texture.png');

    expect(Product::query()->where('user_id', $contributor->id)->where('status', Product::STATUS_UPLOADED)->count())->toBe(2);
    Storage::disk('public')->assertExists(Product::query()->where('user_id', $contributor->id)->firstOrFail()->file_path);
});

test('contributors can add metadata to an uploaded image', function () {
    Storage::fake('public');
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    $category = Category::create(['name' => 'Nature', 'description' => 'Nature assets']);
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true);

    $component = Livewire::actingAs($contributor)
        ->test('contributor.asset-upload')
        ->set('uploads', [UploadedFile::fake()->createWithContent('nature.png', $png)])
        ->assertSee('bg-rose-500');
    $asset = Product::query()->where('user_id', $contributor->id)->firstOrFail();

    $component
        ->call('editAsset', $asset->id)
        ->assertSet('showMetadataModal', true)
        ->assertSet('metadataComplete', false)
        ->set('assetType', 'Image')
        ->set('categoryId', $category->id)
        ->set('isAi', true)
        ->set('title', 'Morning in the forest')
        ->set('description', 'A soft forest scene at sunrise.')
        ->set('tags', 'nature, forest, sunrise')
        ->call('saveMetadata')
        ->assertHasNoErrors()
        ->assertSet('showMetadataModal', false);

    $component
        ->call('editAsset', $asset->id)
        ->assertSet('metadataComplete', true)
        ->assertSet('isAi', true)
        ->assertSet('assetType', 'Image')
        ->assertSet('categoryId', $category->id)
        ->assertSet('title', 'Morning in the forest')
        ->assertSet('description', 'A soft forest scene at sunrise.')
        ->assertSet('tags', 'nature, forest, sunrise')
        ->assertSee('bg-emerald-500')
        ->assertSee('Update');

    $asset->refresh();

    expect($asset->asset_type)->toBe('Image')
        ->and($asset->category_id)->toBe($category->id)
        ->and($asset->is_ai)->toBeTrue()
        ->and($asset->title)->toBe('Morning in the forest')
        ->and($asset->description)->toBe('A soft forest scene at sunrise.')
        ->and($asset->tags)->toBe(['nature', 'forest', 'sunrise'])
        ->and($asset->image_size)->not->toBeNull()
        ->and($asset->dimensions)->toBe('1x1');
});

test('contributors can submit assets with completed metadata', function () {
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    $category = Category::create(['name' => 'Technology']);
    $readyAsset = Product::create([
        'user_id' => $contributor->id,
        'category_id' => $category->id,
        'asset_type' => 'Image',
        'title' => 'Ready asset',
        'description' => 'Ready for review.',
        'tags' => ['technology'],
        'is_ai' => true,
        'status' => Product::STATUS_UPLOADED,
        'file_path' => 'contributors/ready.png',
        'file_name' => 'ready.png',
    ]);
    $incompleteAsset = Product::create([
        'user_id' => $contributor->id,
        'status' => Product::STATUS_UPLOADED,
        'file_path' => 'contributors/incomplete.png',
        'file_name' => 'incomplete.png',
    ]);

    Livewire::actingAs($contributor)
        ->test('contributor.asset-upload')
        ->assertSee('Submit (1)')
        ->call('submitAssets');

    expect($readyAsset->refresh()->status)->toBe(Product::STATUS_PENDING)
        ->and($incompleteAsset->refresh()->status)->toBe(Product::STATUS_UPLOADED);
});

test('contributors can delete their uploaded image', function () {
    Storage::fake('public');
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    $asset = Product::create([
        'user_id' => $contributor->id,
        'status' => Product::STATUS_UPLOADED,
        'file_path' => 'contributors/'.$contributor->id.'/delete-me.png',
        'file_name' => 'delete-me.png',
    ]);
    Storage::disk('public')->put($asset->file_path, 'image contents');

    Livewire::actingAs($contributor)
        ->test('contributor.asset-upload')
        ->call('deleteAsset', $asset->id);

    expect(Product::query()->find($asset->id))->toBeNull();
    Storage::disk('public')->assertMissing($asset->file_path);
});

test('uploaded assets use the current request host for preview urls', function () {
    app('url')->forceRootUrl('http://localhost:8000');

    $asset = Product::create([
        'user_id' => User::factory()->create(['role' => User::ROLE_CONTRIBUTOR])->id,
        'status' => Product::STATUS_UPLOADED,
        'file_path' => 'contributors/42/preview.png',
        'file_name' => 'preview.png',
    ]);

    expect($asset->preview_url)->toBe(url('storage/contributors/42/preview.png'));
});

test('contributors can view pending and approved assets without mutation controls', function (string $status) {
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    $category = Category::create(['name' => 'Technology']);
    $asset = Product::create([
        'user_id' => $contributor->id,
        'category_id' => $category->id,
        'asset_type' => 'Image',
        'title' => $status.' asset title',
        'description' => 'Read-only asset description.',
        'tags' => ['technology'],
        'is_ai' => true,
        'status' => $status,
        'file_path' => 'contributors/'.$status.'.png',
        'file_name' => $status.'.png',
    ]);

    $component = Livewire::actingAs($contributor)
        ->test('contributor.asset-upload', ['status' => $status])
        ->assertDontSee('Drop images here or browse your computer')
        ->assertSee($asset->file_name)
        ->assertDontSee('Delete image')
        ->call('openAsset', $asset->id)
        ->assertSet('metadataReadOnly', true)
        ->assertSet('isAi', '1')
        ->assertSee($asset->title)
        ->assertDontSee('Update');

    $component
        ->set('title', 'Changed title')
        ->call('saveMetadata')
        ->call('deleteAsset', $asset->id);

    expect($asset->refresh()->title)->toBe($status.' asset title')
        ->and(Product::query()->find($asset->id))->not->toBeNull();
})->with([
    Product::STATUS_PENDING,
    Product::STATUS_APPROVED,
    Product::STATUS_REJECTED,
]);

test('contributors can see the rejection reason for rejected assets', function () {
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    $asset = Product::create([
        'user_id' => $contributor->id,
        'status' => Product::STATUS_REJECTED,
        'file_path' => 'contributors/rejected.png',
        'file_name' => 'rejected.png',
        'rejection_reason' => 'Please provide a more specific title.',
    ]);

    Livewire::actingAs($contributor)
        ->test('contributor.asset-upload', ['status' => Product::STATUS_REJECTED])
        ->assertSee('Reason: Please provide a more specific title.')
        ->call('openAsset', $asset->id)
        ->assertSee('Please provide a more specific title.');
});
