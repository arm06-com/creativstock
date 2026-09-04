<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Contributor\AssetController;
use App\Http\Controllers\Contributor\EarningsController;
use App\Http\Controllers\Contributor\WithdrawalController;
use App\Http\Controllers\CustomerPlanController;
use App\Http\Controllers\CustomerUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaddleWebhookController;
use App\Models\Category;
use App\Models\CommissionSetting;
use App\Models\ContributorEarning;
use App\Models\ContributorWithdrawal;
use App\Models\CustomerSubscription;
use App\Models\AssetDownload;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

Route::get('/', function (Request $request) {
    $approvedAssets = Product::query()
        ->with('category')
        ->where('status', Product::STATUS_APPROVED)
        ->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->string('search')->toString();

            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        })
        ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
        ->latest()
        ->limit(12)
        ->get();

    return view('welcome', [
        'approvedAssets' => $approvedAssets,
        'topContributors' => User::query()
            ->where('role', User::ROLE_CONTRIBUTOR)
            ->whereHas('products', fn ($query) => $query->where('status', Product::STATUS_APPROVED))
            ->with(['products' => fn ($query) => $query->where('status', Product::STATUS_APPROVED)->latest()->limit(2)])
            ->withCount(['products' => fn ($query) => $query->where('status', Product::STATUS_APPROVED)])
            ->orderByDesc('products_count')
            ->limit(6)
            ->get(),
        'designerFavAssets' => Product::query()
            ->with('category')
            ->where('status', Product::STATUS_APPROVED)
            ->whereIn('asset_type', ['Vector', 'Icon', 'Illustration', 'Template'])
            ->latest()
            ->limit(12)
            ->get(),
        'categories' => Category::query()->withCount('products')->orderBy('name')->get(),
        'customerSubscription' => $request->user()?->isCustomer()
            ? $request->user()->customerAccount()->customerSubscriptions()->with('plan')->where('status', CustomerSubscription::STATUS_ACTIVE)->latest()->first()
            : null,
        'selectedCategory' => $request->integer('category') ?: null,
        'search' => $request->string('search')->toString(),
    ]);
})->name('home');

Route::get('/assets/{product}/download', function (Product $product) {
    abort_unless($product->status === Product::STATUS_APPROVED, 404);

    if (auth()->user()?->isCustomer()) {
        $customer = auth()->user()->customerAccount();
        $accountUserIds = auth()->user()->customerAccountUserIds();
        DB::transaction(function () use ($accountUserIds, $customer, $product): void {
            $subscription = $customer->customerSubscriptions()
                ->with('plan')
                ->where('status', CustomerSubscription::STATUS_ACTIVE)
                ->latest()
                ->lockForUpdate()
                ->first();

            abort_unless($subscription, 403);

            $alreadyDownloaded = AssetDownload::query()
                ->where('product_id', $product->id)
                ->where(function ($query) use ($accountUserIds, $customer): void {
                    $query->where('customer_id', $customer->id)
                        ->orWhere(function ($query) use ($accountUserIds): void {
                            $query->whereNull('customer_id')->whereIn('user_id', $accountUserIds);
                        });
                })
                ->exists();

            if (! $alreadyDownloaded) {
                abort_if($subscription->plan->download_limit !== null && $subscription->downloads_used >= $subscription->plan->download_limit, 403);

                $subscription->increment('downloads_used');
                $download = $product->downloads()->create(['user_id' => auth()->id(), 'customer_id' => $customer->id]);

                if ($product->user?->isContributor() && $subscription->plan->download_limit !== null && $subscription->plan->download_limit > 0) {
                    $commissionPercentage = (float) CommissionSetting::current()->percentage;
                    $amount = ((float) $subscription->plan->price / $subscription->plan->download_limit) * ($commissionPercentage / 100);

                    ContributorEarning::create([
                        'contributor_id' => $product->user_id,
                        'asset_download_id' => $download->id,
                        'product_id' => $product->id,
                        'plan_price' => $subscription->plan->price,
                        'plan_download_limit' => $subscription->plan->download_limit,
                        'commission_percentage' => $commissionPercentage,
                        'amount' => $amount,
                    ]);
                }
            }
        });
    }

    $title = trim((string) ($product->title ?: $product->file_name));
    $baseName = Str::of($title)
        ->replaceMatches('/[\\/:*?"<>|]+/', ' ')
        ->replaceMatches('/\s+/', ' ')
        ->trim()
        ->pipe(fn ($value) => Str::slug($value))
        ->toString();

    $downloadBaseName = $baseName !== '' ? $baseName : pathinfo((string) $product->file_name, PATHINFO_FILENAME);
    $fileExtension = pathinfo((string) $product->file_name, PATHINFO_EXTENSION) ?: pathinfo((string) $product->file_path, PATHINFO_EXTENSION);
    $zipFileName = $downloadBaseName.'.zip';
    $zipEntryName = $downloadBaseName.($fileExtension !== '' ? '.'.$fileExtension : '');

    $zipPath = tempnam(sys_get_temp_dir(), 'creativ-stock-');
    $zip = new \ZipArchive;

    if ($zip->open($zipPath, \ZipArchive::OVERWRITE) !== true) {
        abort(500, 'Unable to create zip download.');
    }

    $zip->addFile(Storage::disk('public')->path($product->file_path), $zipEntryName);
    $zip->close();

    return response()->download(
        $zipPath,
        $zipFileName,
        [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename='.$zipFileName,
        ],
    )->deleteFileAfterSend(true);
})->name('assets.download');

Route::get('/pricing', function () {
    return view('pricing', [
        'plans' => SubscriptionPlan::query()
            ->orderBy('price')
            ->get(),
    ]);
})->name('pricing');

// Accept browser logs from Boost tooling via GET or POST to avoid MethodNotAllowed exceptions
Route::match(['get', 'post'], '_boost/browser-logs', function () {
    return response('', 204);
});

Route::get('/register/customer', function (Request $request) {
    if ($request->query('redirect') === 'pricing') {
        $request->session()->put('url.intended', route('pricing'));
    }

    return view('pages::auth.register', [
        'role' => User::ROLE_CUSTOMER,
        'title' => __('Customer registration'),
        'description' => __('Create an account to browse and buy digital assets.'),
    ]);
})->name('register.customer');

Route::get('/register/contributor', function () {
    return view('pages::auth.register', [
        'role' => User::ROLE_CONTRIBUTOR,
        'title' => __('Contributor registration'),
        'description' => __('Join Creativ Stock as a contributor to upload and sell digital assets.'),
    ]);
})->name('register.contributor');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('my-plan', CustomerPlanController::class)->name('customer.my-plan');
        Route::post('my-plan/sync', [CustomerPlanController::class, 'sync'])->name('customer.my-plan.sync');
        Route::get('my-assets', function (Request $request) {
            abort_unless($request->user()->isCustomer(), 403);

            return view('customer.my-assets', [
                'assets' => Product::query()
                    ->with('category')
                    ->whereHas('downloads', fn ($query) => $query->whereIn('user_id', $request->user()->customerAccountUserIds()))
                    ->latest()
                    ->get(),
            ]);
        })->name('customer.my-assets');
        Route::get('users', [CustomerUserController::class, 'index'])->name('customer.users.index');
        Route::get('users/create', [CustomerUserController::class, 'create'])->name('customer.users.create');
        Route::post('users', [CustomerUserController::class, 'store'])->name('customer.users.store');
    });

    Route::middleware(['permission:manage-products'])->group(function () {
        Route::get('contributor/assets', [AssetController::class, 'index'])->name('contributor.assets.index');
        Route::get('contributor/earnings', EarningsController::class)->name('contributor.earnings');
        Route::get('contributor/withdraw', [WithdrawalController::class, 'create'])->name('contributor.withdraw');
        Route::post('contributor/withdraw', [WithdrawalController::class, 'store'])->name('contributor.withdraw.store');
        Route::post('contributor/payment-methods', [WithdrawalController::class, 'savePaymentMethod'])->name('contributor.payment-methods.store');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::middleware(['permission:manage-users'])->group(function () {
            Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
            Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
            Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
            Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
            Route::patch('users/{user}/toggle', [UserManagementController::class, 'toggleActive'])->name('users.toggle');
            Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        });

        Route::middleware(['permission:manage-products'])->group(function () {
            Route::view('products', 'admin.products.index')->name('products.index');
            Route::get('assets/{status?}', function (?string $status = null) {
                $status ??= Product::STATUS_PENDING;

                abort_unless(in_array($status, [
                    Product::STATUS_PENDING,
                    Product::STATUS_APPROVED,
                    Product::STATUS_REJECTED,
                ], true), 404);

                $counts = Product::query()
                    ->whereIn('status', [
                        Product::STATUS_PENDING,
                        Product::STATUS_APPROVED,
                        Product::STATUS_REJECTED,
                    ])
                    ->selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->all();

                return view('admin.assets.index', [
                    'counts' => [
                        Product::STATUS_PENDING => $counts[Product::STATUS_PENDING] ?? 0,
                        Product::STATUS_APPROVED => $counts[Product::STATUS_APPROVED] ?? 0,
                        Product::STATUS_REJECTED => $counts[Product::STATUS_REJECTED] ?? 0,
                    ],
                    'status' => $status,
                ]);
            })->name('assets.index');
        });

        Route::middleware(['permission:manage-categories'])->group(function () {
            Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
            Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
            Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
            Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        });

        Route::middleware(['permission:manage-settings'])->group(function () {
            Route::get('settings', function () {
                abort_unless(auth()->user()->isSuperAdmin(), 403);

                $settings = \App\Models\SiteSetting::current();

                return view('admin.settings', [
                    'settings' => $settings,
                ]);
            })->name('settings');

            Route::post('settings', function (Illuminate\Http\Request $request) {
                abort_unless(auth()->user()->isSuperAdmin(), 403);

                $validated = $request->validate([
                    'site_title' => ['nullable', 'string', 'max:255'],
                    'meta_description' => ['nullable', 'string', 'max:500'],
                    'logo' => ['nullable', 'file', 'max:2048'],
                    'favicon' => ['nullable', 'file', 'max:1024'],
                    'paddle_environment' => ['nullable', 'in:sandbox,production,live'],
                    'paddle_api_key' => ['nullable', 'string', 'max:255'],
                    'paddle_client_token' => ['nullable', 'string', 'max:255'],
                    'paddle_webhook_secret' => ['nullable', 'string', 'max:255'],
                    'paddle_test_price_id' => ['nullable', 'string', 'max:255'],
                    'paddle_verify_ssl' => ['nullable', 'boolean'],
                ]);

                $settings = \App\Models\SiteSetting::current();

                if ($request->hasFile('logo')) {
                    $logoPath = $request->file('logo')->store('images/brand', 'public');
                    $settings->logo_path = $logoPath;
                }

                if ($request->hasFile('favicon')) {
                    $faviconPath = $request->file('favicon')->store('images/brand', 'public');
                    $settings->favicon_path = $faviconPath;
                }

                $settings->site_title = $validated['site_title'] ?? $settings->site_title ?? config('app.name', 'Creativ Stock');
                $settings->meta_description = $validated['meta_description'] ?? $settings->meta_description ?? 'Marketplace for creative assets.';
                $paddleEnvironment = $validated['paddle_environment'] ?? $settings->paddle_environment ?? 'sandbox';
                $settings->paddle_environment = $paddleEnvironment === 'live' ? 'production' : $paddleEnvironment;
                $settings->paddle_test_price_id = $request->has('paddle_test_price_id') ? trim((string) $request->input('paddle_test_price_id')) ?: null : ($settings->paddle_test_price_id ?? null);
                $settings->paddle_verify_ssl = $request->has('paddle_verify_ssl') ? (bool) $request->boolean('paddle_verify_ssl') : ($settings->paddle_verify_ssl ?? true);

                if ($request->has('paddle_api_key')) {
                    $settings->paddle_api_key = trim((string) $request->input('paddle_api_key')) === '' ? null : \Illuminate\Support\Facades\Crypt::encryptString(trim((string) $request->input('paddle_api_key')));
                }

                if ($request->has('paddle_client_token')) {
                    $settings->paddle_client_token = trim((string) $request->input('paddle_client_token')) === '' ? null : \Illuminate\Support\Facades\Crypt::encryptString(trim((string) $request->input('paddle_client_token')));
                }

                if ($request->has('paddle_webhook_secret')) {
                    $settings->paddle_webhook_secret = trim((string) $request->input('paddle_webhook_secret')) === '' ? null : \Illuminate\Support\Facades\Crypt::encryptString(trim((string) $request->input('paddle_webhook_secret')));
                }

                $settings->save();

                config()->set('app.name', $settings->site_title);
                config()->set('app.meta_description', $settings->meta_description);
                config()->set('paddle.environment', $settings->paddle_environment);
                config()->set('paddle.api_key', $settings->paddle_api_key ? \Illuminate\Support\Facades\Crypt::decryptString($settings->paddle_api_key) : config('paddle.api_key'));
                config()->set('paddle.client_token', $settings->paddle_client_token ? \Illuminate\Support\Facades\Crypt::decryptString($settings->paddle_client_token) : config('paddle.client_token'));
                config()->set('paddle.webhook_secret', $settings->paddle_webhook_secret ? \Illuminate\Support\Facades\Crypt::decryptString($settings->paddle_webhook_secret) : config('paddle.webhook_secret'));
                config()->set('paddle.test_price_id', $settings->paddle_test_price_id ?: config('paddle.test_price_id'));
                config()->set('paddle.verify_ssl', (bool) $settings->paddle_verify_ssl);

                return redirect()->route('admin.settings')->with('status', __('Site settings updated successfully.'));
            })->name('settings.store');
        });

        Route::middleware(['permission:manage-subscriptions'])->group(function () {
            Route::get('financial-report', function () {
                abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin(), 403);

                $activeSubscriptions = CustomerSubscription::query()
                    ->with('plan')
                    ->where('status', CustomerSubscription::STATUS_ACTIVE)
                    ->get();

                $monthlyRevenue = collect(range(5, 0))->map(function ($offset) {
                    $start = now()->subMonths($offset)->startOfMonth();
                    $revenue = (float) CustomerSubscription::query()
                        ->join('subscription_plans', 'subscription_plans.id', '=', 'customer_subscriptions.subscription_plan_id')
                        ->where('customer_subscriptions.status', CustomerSubscription::STATUS_ACTIVE)
                        ->whereBetween('customer_subscriptions.starts_at', [$start, $start->copy()->endOfMonth()])
                        ->sum('subscription_plans.price');

                    return [
                        'label' => $start->translatedFormat('M'),
                        'amount' => $revenue,
                    ];
                });

                $planBreakdown = $activeSubscriptions
                    ->groupBy(fn ($subscription) => $subscription->plan?->name ?? __('Unknown plan'))
                    ->map(fn ($subscriptions, $name) => [
                        'name' => $name,
                        'count' => $subscriptions->count(),
                        'revenue' => (float) $subscriptions->sum(fn ($subscription) => (float) ($subscription->plan?->price ?? 0)),
                    ])
                    ->sortByDesc('revenue')
                    ->take(5)
                    ->values();

                $topContributors = ContributorEarning::query()
                    ->selectRaw('contributor_id, SUM(amount) as total_earnings')
                    ->with('contributor')
                    ->groupBy('contributor_id')
                    ->orderByDesc('total_earnings')
                    ->limit(5)
                    ->get();

                $recentWithdrawals = ContributorWithdrawal::query()
                    ->with('contributor')
                    ->latest('requested_at')
                    ->limit(5)
                    ->get();

                return view('admin.financial-report.index', [
                    'totalRevenue' => (float) $activeSubscriptions->sum(fn ($subscription) => (float) ($subscription->plan?->price ?? 0)),
                    'mrr' => (float) $activeSubscriptions->sum(fn ($subscription) => (float) ($subscription->plan?->price ?? 0)),
                    'activeCustomers' => $activeSubscriptions->count(),
                    'totalDownloads' => AssetDownload::query()->count(),
                    'contributorRevenue' => (float) ContributorEarning::query()->sum('amount'),
                    'pendingPayouts' => (float) ContributorWithdrawal::query()->where('status', ContributorWithdrawal::STATUS_PENDING)->sum('amount'),
                    'paidPayouts' => (float) ContributorWithdrawal::query()->whereIn('status', [ContributorWithdrawal::STATUS_APPROVED, ContributorWithdrawal::STATUS_PAID])->sum('amount'),
                    'monthlyRevenue' => $monthlyRevenue,
                    'planBreakdown' => $planBreakdown,
                    'topContributors' => $topContributors,
                    'recentWithdrawals' => $recentWithdrawals,
                ]);
            })->name('financial-report.index');
            Route::get('subscription-plans', function () {
                abort_unless(auth()->user()->isSuperAdmin(), 403);

                return view('admin.subscription-plans.index');
            })->name('subscription-plans.index');
            Route::get('commission-share', function () {
                abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin(), 403);

                return view('admin.commission-share.index');
            })->name('commission-share.index');
        });
    });
});

Route::get('/test-paddle', function () {
    return view('test-paddle');
});

Route::post('/paddle/webhook', PaddleWebhookController::class);

require __DIR__.'/settings.php';
