<x-layouts::app :title="__('Financial Report')">
    <div class="p-6">
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ __('Financial Report') }}</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Subscription, payout, and contributor performance overview.') }}</p>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach([
                ['label' => __('Monthly recurring revenue'), 'value' => '$ '.number_format((float) $mrr, 2), 'detail' => __('Active plans')],
                ['label' => __('Active subscribers'), 'value' => number_format((int) $activeCustomers), 'detail' => __('Paid accounts')],
                ['label' => __('Total downloads'), 'value' => number_format((int) $totalDownloads), 'detail' => __('Marketplace downloads')],
                ['label' => __('Contributor payouts'), 'value' => '$ '.number_format((float) $contributorRevenue, 2), 'detail' => __('Commission paid out')],
            ] as $stat)
                <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $stat['detail'] }}</p>
                </div>
            @endforeach
        </div>

        @php
            $maxRevenue = max((float) ($monthlyRevenue->max('amount') ?? 0), 1);
        @endphp

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ __('Revenue trend') }}</h2>
                <div class="mt-5 space-y-3">
                    @forelse($monthlyRevenue as $month)
                        @php
                            $monthWidth = $month['amount'] > 0
                                ? min(max(((float) $month['amount'] / $maxRevenue) * 100, 8), 100)
                                : 0;
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm text-zinc-600 dark:text-zinc-300">
                                <span>{{ $month['label'] }}</span>
                                <span>$ {{ number_format((float) $month['amount'], 2) }}</span>
                            </div>
                            <div class="h-2.5 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <div class="h-full rounded-full bg-[#087f85]" style="width: {{ $monthWidth }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No revenue data yet.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ __('Plan revenue mix') }}</h2>
                <div class="mt-5 space-y-4">
                    @forelse($planBreakdown as $plan)
                        <div>
                            <div class="flex items-center justify-between text-sm text-zinc-600 dark:text-zinc-300">
                                <span>{{ $plan['name'] }}</span>
                                <span>$ {{ number_format((float) $plan['revenue'], 2) }}</span>
                            </div>
                            <div class="mt-2 flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                                <span>{{ $plan['count'] }} {{ __('active') }}</span>
                                <span>•</span>
                                <span>{{ __('Revenue share') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No subscription revenue yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ __('Top contributors') }}</h2>
                <div class="mt-5 space-y-4">
                    @forelse($topContributors as $contributor)
                        <div class="flex items-center justify-between rounded-xl bg-zinc-50 p-3 dark:bg-zinc-800">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $contributor->contributor?->name ?? __('Unknown contributor') }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $contributor->contributor?->email ?? '' }}</p>
                            </div>
                            <span class="font-semibold text-[#087f85]">$ {{ number_format((float) $contributor->total_earnings, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No contributor earnings recorded yet.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ __('Payout activity') }}</h2>
                <div class="mt-5 space-y-4">
                    <div class="rounded-xl bg-emerald-50 p-3 dark:bg-emerald-950/20">
                        <p class="text-xs uppercase tracking-[0.12em] text-emerald-700 dark:text-emerald-300">{{ __('Pending payouts') }}</p>
                        <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-300">$ {{ number_format((float) $pendingPayouts, 2) }}</p>
                    </div>
                    <div class="rounded-xl bg-sky-50 p-3 dark:bg-sky-950/20">
                        <p class="text-xs uppercase tracking-[0.12em] text-sky-700 dark:text-sky-300">{{ __('Paid payouts') }}</p>
                        <p class="mt-2 text-2xl font-semibold text-sky-700 dark:text-sky-300">$ {{ number_format((float) $paidPayouts, 2) }}</p>
                    </div>

                    @forelse($recentWithdrawals as $withdrawal)
                        <div class="flex items-center justify-between rounded-xl bg-zinc-50 p-3 dark:bg-zinc-800">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">{{ $withdrawal->contributor?->name ?? __('Contributor') }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $withdrawal->status }} · {{ $withdrawal->requested_at?->format('M j, Y') }}</p>
                            </div>
                            <span class="font-semibold text-zinc-900 dark:text-white">$ {{ number_format((float) $withdrawal->amount, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No withdrawal activity yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
