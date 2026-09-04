<x-layouts::app :title="__('Dashboard')">
    @php
        $isAdminDashboard = (bool) ($isAdminDashboard ?? false);
        $salesChartData = collect($dailySales ?? []);
        $revenueChartData = collect($dailyRevenue ?? []);
        $salesSummary = is_array($salesSummary ?? null)
            ? $salesSummary
            : [
                'totalRevenue' => 0.0,
                'totalSales' => 0,
                'avgOrderValue' => 0.0,
            ];
        $salesSummary['totalRevenue'] = (float) ($salesSummary['totalRevenue'] ?? 0.0);
        $salesSummary['totalSales'] = (int) ($salesSummary['totalSales'] ?? 0);
        $salesSummary['avgOrderValue'] = (float) ($salesSummary['avgOrderValue'] ?? 0.0);
    @endphp

    <div class="min-h-full bg-[#f5f8f8] px-4 py-5 dark:bg-zinc-950 sm:px-6 lg:px-8">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6">
            <section class="relative overflow-hidden rounded-2xl bg-[#17383c] px-6 py-7 text-white shadow-sm sm:px-8">
                <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="flex max-w-2xl flex-col gap-4">
                        <div class="flex items-center gap-3 text-sm font-medium text-[#8fe3e2]">
                            <span class="flex size-9 items-center justify-center rounded-xl bg-[#0fa3ad] text-white shadow-lg shadow-[#0fa3ad]/20">
                                <x-app-logo-icon class="size-5 fill-current" />
                            </span>
                            <span>{{ $isContributor ? __('Contributor workspace') : __('Admin workspace') }}</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <p class="text-sm text-[#b8cccd]">{{ now()->format('l, F j, Y') }}</p>
                            <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">{{ __('Good to see you, :name.', ['name' => auth()->user()->name]) }}</h1>
                            <p class="max-w-xl text-sm leading-6 text-[#c5d8d8]">{{ $dashboardDescription }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-sm">
                        <span class="flex size-10 items-center justify-center rounded-full bg-[#d7f4f1] text-sm font-bold text-[#126c70]">{{ auth()->user()->initials() }}</span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-[#b8cccd]">{{ $accountLabel }}</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -inset-e-8 -top-16 size-56 rounded-full border-28 border-[#0fa3ad]/20"></div>
                <div class="absolute -bottom-24 inset-e-36 size-48 rounded-full border-18 border-white/5"></div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="{{ __('Portfolio overview') }}">
                @foreach ($stats as $stat)
                    <article class="flex min-h-36 flex-col justify-between rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="flex items-start justify-between gap-4">
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $stat['label'] }}</p>
                            <span class="flex size-9 items-center justify-center rounded-xl {{ $stat['tone'] === 'teal' ? 'bg-[#d7f4f1] text-[#087f85] dark:bg-[#0fa3ad]/20 dark:text-[#8fe3e2]' : ($stat['tone'] === 'amber' ? 'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300' : ($stat['tone'] === 'emerald' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300')) }}">
                                <flux:icon :name="$stat['icon']" variant="mini" />
                            </span>
                        </div>
                        <div class="flex items-end justify-between gap-3">
                            <p class="text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $stat['value'] }}</p>
                            <p class="text-right text-xs text-zinc-500 dark:text-zinc-400">{{ $stat['detail'] }}</p>
                        </div>
                    </article>
                @endforeach
            </section>

            @if ($isAdminDashboard)
                @php
                    $adminSalesChartData = collect($salesChartData ?? []);
                    $adminRevenueChartData = collect($revenueChartData ?? []);
                    $maxDailySales = max((float) ($adminSalesChartData->max('value') ?? 0), 1.0);
                    $maxDailyRevenue = max((float) ($adminRevenueChartData->max('value') ?? 0), 1.0);
                    $salesChartPoints = $adminSalesChartData->map(function ($sale, $index) use ($adminSalesChartData, $maxDailySales) {
                        $x = 10 + ($index * (100 / max($adminSalesChartData->count() - 1, 1)));
                        $y = 90 - (($sale['value'] / $maxDailySales) * 70);

                        return [$x, $y];
                    })->values();
                    $salesChartPolyline = $salesChartPoints->map(fn ($point) => implode(',', $point))->implode(' ');
                    $salesChartArea = $salesChartPoints->isNotEmpty()
                        ? $salesChartPoints->map(fn ($point) => implode(',', $point))->implode(' ') . ' 90,90 10,90'
                        : '10,90 90,90';
                @endphp

                <section class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
                    <div class="flex flex-col gap-4 border-b border-zinc-100 pb-5 dark:border-zinc-800 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#087f85] dark:text-[#8fe3e2]">{{ __('Sales & revenue') }}</p>
                            <h2 class="mt-2 text-xl font-semibold text-zinc-950 dark:text-white">{{ __('Business analytics') }}</h2>
                        </div>
                        <div class="grid gap-2 sm:grid-cols-3">
                            <div class="rounded-xl bg-[#edf9f8] px-3 py-2 text-sm dark:bg-[#0fa3ad]/10">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Revenue') }}</p>
                                <p class="mt-1 font-semibold text-zinc-900 dark:text-white">$ {{ number_format((float) $salesSummary['totalRevenue'], 2) }}</p>
                            </div>
                            <div class="rounded-xl bg-zinc-100 px-3 py-2 text-sm dark:bg-zinc-800">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Daily sales') }}</p>
                                <p class="mt-1 font-semibold text-zinc-900 dark:text-white">{{ $salesSummary['totalSales'] }}</p>
                            </div>
                            <div class="rounded-xl bg-amber-50 px-3 py-2 text-sm dark:bg-amber-400/10">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Avg order') }}</p>
                                <p class="mt-1 font-semibold text-zinc-900 dark:text-white">$ {{ number_format((float) $salesSummary['avgOrderValue'], 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
                        
                        <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/60">
                            <div class="mb-4 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Revenue') }}</h3>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('This month') }}</span>
                            </div>
                            <div class="space-y-3">
                                @foreach ($adminRevenueChartData as $day)
                                    @php
                                        $revenueWidth = max(($day['value'] / $maxDailyRevenue) * 100, 6);
                                    @endphp
                                    <div>
                                        <div class="mb-1 flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400">
                                            <span>{{ Str::substr($day['label'], 0, 3) }}</span>
                                            <span>$ {{ number_format((float) $day['value'], 2) }}</span>
                                        </div>
                                        <div class="h-2 rounded-full bg-zinc-200 dark:bg-zinc-800">
                                            <div class="h-full rounded-full bg-[#087f85]" style="width: {{ $revenueWidth }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <div class="grid gap-6 lg:grid-cols-[1.35fr_0.65fr]">
                <section class="overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between gap-4 border-b border-zinc-100 px-5 py-4 dark:border-zinc-800 sm:px-6">
                        <div>
                            <h2 class="text-base font-semibold text-zinc-950 dark:text-white">{{ $activityTitle }}</h2>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $activityDescription }}</p>
                        </div>
                        <span class="rounded-full bg-[#e7f7f5] px-3 py-1 text-xs font-semibold text-[#087f85] dark:bg-[#0fa3ad]/15 dark:text-[#8fe3e2]">{{ __('Live view') }}</span>
                    </div>

                    @if ($recentProducts->isEmpty())
                        <div class="flex min-h-56 flex-col items-center justify-center gap-3 px-6 py-10 text-center">
                            <span class="flex size-12 items-center justify-center rounded-2xl bg-[#e7f7f5] text-[#0fa3ad] dark:bg-[#0fa3ad]/15 dark:text-[#8fe3e2]"><flux:icon name="photo" /></span>
                            <div>
                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $emptyTitle }}</p>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $emptyDescription }}</p>
                            </div>
                        </div>
                    @else
                        <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach ($recentProducts as $product)
                                <div class="flex items-center gap-4 px-5 py-4 sm:px-6">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-300"><flux:icon name="photo" variant="mini" /></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $product->title ?: $product->file_name }}</p>
                                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $isCustomer ? ($product->user?->name.' · '.$product->created_at->diffForHumans()) : ($isContributor ? $product->created_at->diffForHumans() : ($product->user?->name.' · '.$product->created_at->diffForHumans())) }}</p>
                                        </div>
                                        <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $isCustomer ? __('Downloaded') : ucfirst($product->status) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                <aside class="flex flex-col gap-4 rounded-2xl bg-[#0fa3ad] p-6 text-white shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <span class="flex size-10 items-center justify-center rounded-xl bg-white/15"><flux:icon name="sparkles" /></span>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#d7f4f1]">{{ __('Workspace') }}</span>
                    </div>
                    <div class="flex flex-col gap-2">
                        <h2 class="text-xl font-semibold">{{ $isContributor ? __('Build a catalog people remember.') : __('Keep the marketplace moving.') }}</h2>
                        <p class="text-sm leading-6 text-[#e1fbf8]">{{ $isContributor ? __('Keep your profile, metadata, and creative direction consistent as your collection grows.') : __('Review product health and give your creative community a clear path to market.') }}</p>
                    </div>
                    <div class="mt-auto flex items-center gap-2 border-t border-white/20 pt-4 text-xs font-medium text-[#d7f4f1]">
                        <flux:icon name="shield-check" variant="mini" />
                        <span>{{ __('Your workspace is private and secure') }}</span>
                    </div>
                </aside>
            </div>

            @if ($isContributor)
                <section class="overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between gap-4 border-b border-zinc-100 px-5 py-4 dark:border-zinc-800 sm:px-6">
                        <div>
                            <h2 class="text-base font-semibold text-zinc-950 dark:text-white">{{ __('Recent earnings') }}</h2>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Your latest commission from asset downloads.') }}</p>
                        </div>
                        <a href="{{ route('contributor.earnings') }}" class="text-sm font-semibold text-[#087f85] hover:underline" wire:navigate>{{ __('View all') }}</a>
                    </div>
                    @forelse ($recentEarnings as $earning)
                        <div class="flex items-center justify-between gap-4 border-b border-zinc-100 px-5 py-4 last:border-0 dark:border-zinc-800 sm:px-6">
                            <div class="min-w-0"><p class="truncate text-sm font-medium text-zinc-950 dark:text-white">{{ $earning->product->title ?: $earning->product->file_name }}</p><p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $earning->created_at->diffForHumans() }} · {{ $earning->commission_percentage }}% {{ __('share') }}</p></div>
                            <span class="shrink-0 text-sm font-semibold text-[#087f85]">$ {{ number_format((float) $earning->amount, 4) }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">{{ __('Your earning history will appear here after an asset download.') }}</p>
                    @endforelse
                </section>
            @endif
        </div>
    </div>
</x-layouts::app>
