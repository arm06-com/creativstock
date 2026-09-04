<?php

use App\Models\AssetDownload;
use App\Models\CustomerSubscription;
use App\Models\CommissionSetting;
use App\Models\ContributorEarning;
use App\Models\ContributorPaymentMethod;
use App\Models\ContributorWithdrawal;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);
test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('admin dashboard displays sales and revenue analytics', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $plan = SubscriptionPlan::factory()->create(['name' => 'Starter', 'price' => 25]);

    CustomerSubscription::query()->create([
        'user_id' => $admin->id,
        'subscription_plan_id' => $plan->id,
        'status' => CustomerSubscription::STATUS_ACTIVE,
        'downloads_used' => 3,
        'created_at' => now()->subDays(2),
        'updated_at' => now()->subDays(2),
    ]);

    AssetDownload::query()->create([
        'user_id' => $admin->id,
        'customer_id' => $admin->id,
        'product_id' => Product::create([
            'user_id' => $admin->id,
            'status' => Product::STATUS_APPROVED,
            'title' => 'Quarterly pack',
            'file_path' => 'products/quarterly-pack.png',
            'file_name' => 'quarterly-pack.png',
            'mime_type' => 'image/png',
        ])->id,
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Sales & revenue')
        ->assertSee('Daily sales')
        ->assertSee('Revenue');
});

test('homepage shows designer favorite asset types separately', function () {
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    Product::create([
        'user_id' => $contributor->id,
        'asset_type' => 'Vector',
        'title' => 'Favorite vector',
        'status' => Product::STATUS_APPROVED,
        'file_path' => 'assets/vector.png',
        'file_name' => 'vector.png',
    ]);
    Product::create([
        'user_id' => $contributor->id,
        'asset_type' => 'Image',
        'title' => 'Recent photo',
        'status' => Product::STATUS_APPROVED,
        'file_path' => 'assets/photo.png',
        'file_name' => 'photo.png',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Designers Fav')
        ->assertSee('Favorite vector')
        ->assertSee('Recent photo');
});

test('homepage includes mobile navigation and responsive layouts', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('mobile-menu-toggle')
        ->assertSee('grid-cols-2')
        ->assertSee('md:grid-cols-3');
});

test('homepage top contributors prefer the user profile photo as the avatar', function () {
    Storage::fake('public');

    $contributor = User::factory()->create([
        'name' => 'Maya Chen',
        'role' => User::ROLE_CONTRIBUTOR,
        'profile_photo_path' => 'profile-photos/maya.png',
    ]);
    Storage::disk('public')->put('profile-photos/maya.png', 'avatar content');

    Product::create([
        'user_id' => $contributor->id,
        'asset_type' => 'Image',
        'title' => 'Top contributor upload',
        'status' => Product::STATUS_APPROVED,
        'file_path' => 'assets/top-contributor-upload.png',
        'file_name' => 'top-contributor-upload.png',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Top Contributors')
        ->assertSee(Storage::disk('public')->url('profile-photos/maya.png'));
});

test('customers can view the my plan page with available plans', function () {
    $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
    SubscriptionPlan::factory()->create(['name' => 'Creator']);

    $this->actingAs($customer)
        ->get(route('customer.my-plan'))
        ->assertOk()
        ->assertSee('Choose a plan to get started')
        ->assertSee('View pricing')
        ->assertDontSee('Choose Creator');
});

test('non-customers cannot view the my plan page', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)->get(route('customer.my-plan'))->assertForbidden();
});

test('super admin can update the site settings', function () {
    $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

    Storage::fake('public');

    $logo = UploadedFile::fake()->create('site-logo.svg', 50, 'image/svg+xml');
    $favicon = UploadedFile::fake()->create('favicon.ico', 50, 'image/x-icon');

    $response = $this->actingAs($user)
        ->post(route('admin.settings.store'), [
            'site_title' => 'Creative Market Pro',
            'meta_description' => 'Premium creative digital marketplace',
            'logo' => $logo,
            'favicon' => $favicon,
        ]);

    $response->assertStatus(302);

    $settings = SiteSetting::current();

    expect($settings->site_title)->toBe('Creative Market Pro');
    expect($settings->meta_description)->toBe('Premium creative digital marketplace');
    expect($settings->logo_path)->not->toBeNull();
    expect($settings->favicon_path)->not->toBeNull();
    expect(Storage::disk('public')->exists($settings->logo_path))->toBeTrue();
    expect(Storage::disk('public')->exists($settings->favicon_path))->toBeTrue();

    $this->actingAs($user)
        ->get(route('admin.settings'))
        ->assertOk()
        ->assertSee('Creative Market Pro');
});

test('super admin can update paddle gateway credentials', function () {
    $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

    $response = $this->actingAs($user)
        ->post(route('admin.settings.store'), [
            'site_title' => 'Creative Market Pro',
            'meta_description' => 'Premium creative digital marketplace',
            'paddle_environment' => 'live',
            'paddle_api_key' => 'pdl_live_test_key',
            'paddle_client_token' => 'live_client_token',
            'paddle_webhook_secret' => 'live_webhook_secret',
            'paddle_verify_ssl' => true,
        ]);

    $response->assertStatus(302);

    $settings = SiteSetting::current();

    expect($settings->paddle_environment)->toBe('production');
    expect($settings->paddle_api_key)->not->toBeNull();
    expect($settings->paddle_client_token)->not->toBeNull();
    expect($settings->paddle_webhook_secret)->not->toBeNull();
    expect((bool) $settings->paddle_verify_ssl)->toBeTrue();
    expect(config('paddle.environment'))->toBe('production');
    expect(config('paddle.api_key'))->toBe('pdl_live_test_key');
    expect(config('paddle.client_token'))->toBe('live_client_token');
    expect(config('paddle.webhook_secret'))->toBe('live_webhook_secret');
    expect(config('paddle.verify_ssl'))->toBeTrue();
});

test('customers see their active plan limits without purchase controls', function () {
    $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
    $activePlan = SubscriptionPlan::factory()->create(['name' => 'Creator']);
    SubscriptionPlan::factory()->create(['name' => 'Creator Plus']);
    CustomerSubscription::query()->create([
        'user_id' => $customer->id,
        'subscription_plan_id' => $activePlan->id,
        'status' => CustomerSubscription::STATUS_ACTIVE,
    ]);

    $this->actingAs($customer)
        ->get(route('customer.my-plan'))
        ->assertOk()
        ->assertSee('Creator')
        ->assertSee('Active')
        ->assertSee('Downloads used')
        ->assertSee('Downloads remaining')
        ->assertSee('Other plans')
        ->assertSee('Creator Plus')
        ->assertSee('View pricing')
        ->assertDontSee('Migrate plan')
        ->assertDontSee('data-paddle-price');

});

test('customers can download within their active plan limit', function () {
    $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
    $plan = SubscriptionPlan::factory()->create(['download_limit' => 1]);
    $subscription = CustomerSubscription::query()->create([
        'user_id' => $customer->id,
        'subscription_plan_id' => $plan->id,
        'status' => CustomerSubscription::STATUS_ACTIVE,
    ]);
    Storage::fake('public');
    $product = Product::create([
        'user_id' => $customer->id,
        'status' => Product::STATUS_APPROVED,
        'title' => 'Golden Sunrise',
        'file_path' => 'assets/download.png',
        'file_name' => 'download.png',
        'mime_type' => 'image/png',
    ]);
    Storage::disk('public')->put($product->file_path, 'image data');

    $this->actingAs($customer)
        ->get(route('assets.download', $product))
        ->assertOk()
        ->assertHeader('content-type', 'application/zip')
        ->assertHeaderContains('content-disposition', 'filename=golden-sunrise.zip');

    expect($subscription->refresh()->downloads_used)->toBe(1);
});

test('customers cannot download after reaching their active plan limit', function () {
    $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
    $plan = SubscriptionPlan::factory()->create(['download_limit' => 1]);
    CustomerSubscription::query()->create([
        'user_id' => $customer->id,
        'subscription_plan_id' => $plan->id,
        'status' => CustomerSubscription::STATUS_ACTIVE,
        'downloads_used' => 1,
    ]);
    $product = Product::create([
        'user_id' => $customer->id,
        'status' => Product::STATUS_APPROVED,
        'file_path' => 'assets/limit.png',
        'file_name' => 'limit.png',
        'mime_type' => 'image/png',
    ]);

    $this->actingAs($customer)->get(route('assets.download', $product))->assertForbidden();
});

test('downloading the same asset again does not use another download', function () {
    $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
    $member = User::factory()->create(['role' => User::ROLE_CUSTOMER, 'customer_id' => $customer->id]);
    $plan = SubscriptionPlan::factory()->create(['download_limit' => 1]);
    $subscription = CustomerSubscription::query()->create([
        'user_id' => $customer->id,
        'subscription_plan_id' => $plan->id,
        'status' => CustomerSubscription::STATUS_ACTIVE,
    ]);
    Storage::fake('public');
    $product = Product::create([
        'user_id' => $customer->id,
        'status' => Product::STATUS_APPROVED,
        'file_path' => 'assets/repeat.png',
        'file_name' => 'repeat.png',
        'mime_type' => 'image/png',
    ]);
    Storage::disk('public')->put($product->file_path, 'image data');

    $this->actingAs($customer)->get(route('assets.download', $product))->assertOk();
    $this->actingAs($member)->get(route('assets.download', $product))->assertOk();

    expect($subscription->refresh()->downloads_used)->toBe(1);
    expect($product->downloads()->count())->toBe(1);
});

test('a contributor receives the configured share for a first asset download', function () {
    $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    $plan = SubscriptionPlan::factory()->create(['price' => 20, 'download_limit' => 50]);
    $subscription = CustomerSubscription::query()->create([
        'user_id' => $customer->id,
        'subscription_plan_id' => $plan->id,
        'status' => CustomerSubscription::STATUS_ACTIVE,
    ]);
    Storage::fake('public');
    $product = Product::create([
        'user_id' => $contributor->id,
        'status' => Product::STATUS_APPROVED,
        'file_path' => 'assets/commission.png',
        'file_name' => 'commission.png',
        'mime_type' => 'image/png',
    ]);
    Storage::disk('public')->put($product->file_path, 'image data');

    $this->actingAs($customer)->get(route('assets.download', $product))->assertOk();

    $earning = ContributorEarning::query()->firstOrFail();
    expect($subscription->refresh()->downloads_used)->toBe(1)
        ->and((float) $earning->amount)->toBe(0.2)
        ->and((float) $earning->commission_percentage)->toBe(50.0)
        ->and($product->refresh()->downloads()->count())->toBe(1)
        ->and($earning->contributor_id)->toBe($contributor->id);
});

test('contributors can save a payment method and request only their available earnings', function () {
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR]);
    CommissionSetting::current()->update(['minimum_payout' => 0.1]);
    ContributorEarning::create([
        'contributor_id' => $contributor->id,
        'product_id' => Product::create(['user_id' => $contributor->id, 'status' => Product::STATUS_APPROVED, 'file_path' => 'asset.png', 'file_name' => 'asset.png'])->id,
        'asset_download_id' => 
            $contributor->assetDownloads()->create(['product_id' => Product::query()->where('user_id', $contributor->id)->firstOrFail()->id])->id,
        'plan_price' => 20,
        'plan_download_limit' => 50,
        'commission_percentage' => 50,
        'amount' => 0.2,
    ]);

    $this->actingAs($contributor)->post(route('contributor.payment-methods.store'), [
        'method' => 'paypal', 'account_name' => 'Contributor', 'account_details' => 'creator@example.com',
    ])->assertRedirect();

    $this->actingAs($contributor)->post(route('contributor.withdraw.store'), ['amount' => 0.2])
        ->assertRedirect(route('contributor.earnings'));

    expect(ContributorPaymentMethod::query()->where('contributor_id', $contributor->id)->count())->toBe(1)
        ->and(ContributorWithdrawal::query()->where('contributor_id', $contributor->id)->value('status'))->toBe(ContributorWithdrawal::STATUS_PENDING);
});

test('customers can view only their downloaded assets', function () {
    $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
    $otherCustomer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
    $plan = SubscriptionPlan::factory()->create(['download_limit' => null]);
    CustomerSubscription::query()->create([
        'user_id' => $customer->id,
        'subscription_plan_id' => $plan->id,
        'status' => CustomerSubscription::STATUS_ACTIVE,
    ]);
    $ownedAsset = Product::create(['user_id' => $otherCustomer->id, 'status' => Product::STATUS_APPROVED, 'file_path' => 'assets/owned.png', 'file_name' => 'owned.png']);
    $otherAsset = Product::create(['user_id' => $otherCustomer->id, 'status' => Product::STATUS_APPROVED, 'file_path' => 'assets/other.png', 'file_name' => 'other.png']);
    $customer->assetDownloads()->create(['product_id' => $ownedAsset->id]);

    $this->actingAs($customer)->get(route('customer.my-assets'))
        ->assertOk()
        ->assertSee('owned.png')
        ->assertDontSee('other.png');
});

test('customers can add users only within their active plan limit', function () {
    $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
    $plan = SubscriptionPlan::factory()->create(['user_limit' => 2]);
    CustomerSubscription::query()->create([
        'user_id' => $customer->id,
        'subscription_plan_id' => $plan->id,
        'status' => CustomerSubscription::STATUS_ACTIVE,
    ]);

    $this->actingAs($customer)->post(route('customer.users.store'), [
        'name' => 'Team Member', 'email' => 'member@example.com', 'password' => 'password', 'password_confirmation' => 'password',
    ])->assertRedirect(route('customer.users.index'));

    $this->actingAs($customer)->post(route('customer.users.store'), [
        'name' => 'Second Member', 'email' => 'second@example.com', 'password' => 'password', 'password_confirmation' => 'password',
    ])->assertForbidden();
});

test('customer team members share the owner plan, downloads, and dashboard', function () {
    $owner = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
    $member = User::factory()->create(['role' => User::ROLE_CUSTOMER, 'customer_id' => $owner->id]);
    $plan = SubscriptionPlan::factory()->create(['download_limit' => 5]);
    $subscription = CustomerSubscription::query()->create([
        'user_id' => $owner->id,
        'subscription_plan_id' => $plan->id,
        'status' => CustomerSubscription::STATUS_ACTIVE,
    ]);
    $product = Product::create([
        'user_id' => $owner->id,
        'status' => Product::STATUS_APPROVED,
        'file_path' => 'assets/team.png',
        'file_name' => 'team.png',
        'mime_type' => 'image/png',
    ]);
    Storage::fake('public');
    Storage::disk('public')->put($product->file_path, 'image data');

    $this->actingAs($member)->get(route('customer.my-plan'))->assertOk()->assertSee($plan->name);
    $this->actingAs($member)->get(route('assets.download', $product))->assertOk();

    expect($subscription->refresh()->downloads_used)->toBe(1);
    $this->assertDatabaseHas('asset_downloads', ['user_id' => $member->id, 'product_id' => $product->id]);
    $this->actingAs($member)->get(route('customer.my-assets'))->assertOk()->assertSee('team.png');
    $this->actingAs($member)->get(route('customer.users.index'))->assertForbidden();
});
