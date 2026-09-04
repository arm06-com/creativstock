<?php

namespace App\Http\Controllers;

use App\Models\AssetDownload;
use App\Models\Product;
use App\Models\CustomerSubscription;
use App\Models\ContributorEarning;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $isCustomer = $user->isCustomer();
        $customer = $isCustomer ? $user->customerAccount() : null;
        $isContributor = $user->isContributor();
        $isAdminDashboard = $user->isAdmin() || $user->isSuperAdmin();
        $canManageProducts = $user->hasPermission('manage-products');
        $products = Product::query();

        if ($isCustomer) {
            $accountUserIds = $user->customerAccountUserIds();
            $products->whereHas('downloads', fn ($query) => $query->whereIn('user_id', $accountUserIds));
        } elseif ($isContributor) {
            $products->where('user_id', $user->id);
        } elseif (! $canManageProducts) {
            $products->whereRaw('1 = 0');
        }

        $stats = $isCustomer
            ? [
                ['label' => __('Downloaded assets'), 'value' => (clone $products)->count(), 'detail' => __('Shared with your team'), 'icon' => 'arrow-down-tray', 'tone' => 'teal'],
                ['label' => __('Team users'), 'value' => count($user->customerAccountUserIds()), 'detail' => __('On your active plan'), 'icon' => 'users', 'tone' => 'slate'],
                ['label' => __('Downloads used'), 'value' => $customer?->customerSubscriptions()->where('status', CustomerSubscription::STATUS_ACTIVE)->value('downloads_used') ?? 0, 'detail' => __('Across your team'), 'icon' => 'chart-bar', 'tone' => 'amber'],
                ['label' => __('Plan status'), 'value' => $customer?->customerSubscriptions()->where('status', CustomerSubscription::STATUS_ACTIVE)->exists() ? __('Active') : __('Inactive'), 'detail' => __('Shared workspace'), 'icon' => 'check-badge', 'tone' => 'emerald'],
            ]
            : ($isContributor
            ? [
                ['label' => __('Total assets'), 'value' => (clone $products)->count(), 'detail' => __('Across your portfolio'), 'icon' => 'squares-2x2', 'tone' => 'teal'],
                ['label' => __('Uploaded'), 'value' => (clone $products)->where('status', Product::STATUS_UPLOADED)->count(), 'detail' => __('Ready for review'), 'icon' => 'arrow-up-tray', 'tone' => 'slate'],
                ['label' => __('In review'), 'value' => (clone $products)->where('status', Product::STATUS_PENDING)->count(), 'detail' => __('Awaiting a decision'), 'icon' => 'clock', 'tone' => 'amber'],
                    ['label' => __('Total earnings'), 'value' => '$ '.number_format((float) ContributorEarning::query()->where('contributor_id', $user->id)->sum('amount'), 4), 'detail' => __('From asset downloads'), 'icon' => 'banknotes', 'tone' => 'emerald'],
            ]
            : [
                ['label' => __('Total products'), 'value' => (clone $products)->count(), 'detail' => __('Across the marketplace'), 'icon' => 'squares-2x2', 'tone' => 'teal'],
                ['label' => __('Needs review'), 'value' => (clone $products)->where('status', Product::STATUS_UPLOADED)->count(), 'detail' => __('Awaiting moderation'), 'icon' => 'clipboard-document-check', 'tone' => 'slate'],
                ['label' => __('In review'), 'value' => (clone $products)->where('status', Product::STATUS_PENDING)->count(), 'detail' => __('Currently processing'), 'icon' => 'clock', 'tone' => 'amber'],
                ['label' => __('Approved'), 'value' => (clone $products)->where('status', Product::STATUS_APPROVED)->count(), 'detail' => __('Live in marketplace'), 'icon' => 'check-badge', 'tone' => 'emerald'],
            ]);

        $recentProducts = (clone $products)
            ->with($isCustomer ? ['user'] : ($isContributor ? [] : ['user']))
            ->latest()
            ->limit(5)
            ->get();

        $recentEarnings = $isContributor
            ? ContributorEarning::query()->with('product')->where('contributor_id', $user->id)->latest()->limit(5)->get()
            : collect();

        $dailySales = collect();
        $dailyRevenue = collect();
        $salesSummary = [
            'totalRevenue' => 0.0,
            'totalSales' => 0,
            'avgOrderValue' => 0.0,
        ];

        if ($isAdminDashboard) {
            $dailySales = collect(range(29, 0))->map(function ($offset) {
                $date = now()->subDays($offset)->startOfDay();
                $count = CustomerSubscription::query()
                    ->whereDate('created_at', $date)
                    ->count();

                return [
                    'label' => $date->shortEnglishDayOfWeek,
                    'date' => $date->format('Y-m-d'),
                    'value' => $count,
                ];
            });

            $dailyRevenue = collect(range(29, 0))->map(function ($offset) {
                $date = now()->subDays($offset)->startOfDay();
                $revenue = (float) CustomerSubscription::query()
                    ->join('subscription_plans', 'subscription_plans.id', '=', 'customer_subscriptions.subscription_plan_id')
                    ->whereDate('customer_subscriptions.created_at', $date)
                    ->sum('subscription_plans.price');

                return [
                    'label' => $date->shortEnglishDayOfWeek,
                    'date' => $date->format('Y-m-d'),
                    'value' => $revenue,
                ];
            });

            $salesSummary['totalRevenue'] = (float) CustomerSubscription::query()
                ->join('subscription_plans', 'subscription_plans.id', '=', 'customer_subscriptions.subscription_plan_id')
                ->where('customer_subscriptions.status', CustomerSubscription::STATUS_ACTIVE)
                ->sum('subscription_plans.price');

            $salesSummary['totalSales'] = (int) CustomerSubscription::query()->count();
            $salesSummary['avgOrderValue'] = $salesSummary['totalSales'] > 0
                ? $salesSummary['totalRevenue'] / $salesSummary['totalSales']
                : 0.0;
        }

        return view('dashboard', [
            'accountLabel' => $isCustomer ? __('Customer account') : ($isContributor ? __('Contributor account') : __('Admin account')),
            'activityDescription' => $isCustomer ? __('Downloads shared across your customer team.') : ($isContributor ? __('Your latest assets and review status.') : __('The latest products moving through your marketplace.')),
            'activityTitle' => $isCustomer ? __('Recent downloads') : ($isContributor ? __('Recent portfolio activity') : __('Recent marketplace activity')),
            'dashboardDescription' => $isCustomer ? __('Your shared creative workspace at a glance.') : ($isContributor ? __('Your creative portfolio at a glance. Keep your catalog organized and ready for the next review.') : __('Monitor product health, review activity, and marketplace operations from one place.')),
            'emptyDescription' => $isCustomer ? __('Downloaded assets will appear here for everyone on your plan.') : ($isContributor ? __('Recent submissions will appear here when uploads are available again.') : __('Product activity will appear here as your marketplace grows.')),
            'emptyTitle' => $isCustomer ? __('Your team has no downloads yet.') : ($isContributor ? __('Your portfolio is ready for its first asset.') : __('Your marketplace is ready for its first product.')),
            'isCustomer' => $isCustomer,
            'isContributor' => $isContributor,
            'isAdminDashboard' => $isAdminDashboard,
            'dailySales' => $dailySales,
            'dailyRevenue' => $dailyRevenue,
            'salesSummary' => $salesSummary,
            'recentProducts' => $recentProducts,
            'recentEarnings' => $recentEarnings,
            'stats' => $stats,
        ]);
    }
}
