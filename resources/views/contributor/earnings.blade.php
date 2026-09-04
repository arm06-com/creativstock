<x-layouts::app :title="__('My Earnings')">
    <div class="min-h-full bg-[#f5f8f8] px-4 py-5 dark:bg-zinc-950 sm:px-6 lg:px-8">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6">
            <div>
                <p class="text-sm font-medium text-[#087f85]">{{ __('Contributor account') }}</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ __('My Earnings') }}</h1>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Your commission history from downloaded assets.') }}</p>
            </div>

            @if (session('status'))
                <flux:callout variant="success" heading="{{ session('status') }}" />
            @endif

            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ([['label' => __('Today'), 'value' => $todayEarnings], ['label' => __('Selected range'), 'value' => $rangeEarnings], ['label' => __('Total earnings'), 'value' => $totalEarnings], ['label' => __('Available balance'), 'value' => $availableBalance], ['label' => __('Withdrawn'), 'value' => $withdrawnAmount]] as $summary)
                    <article class="rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $summary['label'] }}</p>
                        <p class="mt-2 text-2xl font-semibold text-[#087f85]">$ {{ number_format((float) $summary['value'], 4) }}</p>
                    </article>
                @endforeach
            </section>

            <div class="flex flex-col gap-4 rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row sm:items-end sm:justify-between">
                <form method="GET" action="{{ route('contributor.earnings') }}" class="grid gap-3 sm:grid-cols-2">
                    <flux:input name="from" type="date" :label="__('From')" :value="$from" />
                    <flux:input name="to" type="date" :label="__('To')" :value="$to" />
                    <flux:button type="submit" variant="outline">{{ __('Apply range') }}</flux:button>
                </form>
                <a href="{{ route('contributor.withdraw') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#0fa3ad] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#087f85]" wire:navigate><flux:icon name="arrow-up-tray" variant="mini" />{{ __('Withdraw Your Earning') }}</a>
            </div>

            <section class="overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                    <h2 class="font-semibold text-zinc-950 dark:text-white">{{ __('Earning history') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-left text-sm dark:divide-zinc-800">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th class="px-5 py-3 font-semibold">{{ __('Asset') }}</th>
                                <th class="px-5 py-3 font-semibold">{{ __('Download count') }}</th>
                                <th class="px-5 py-3 text-right font-semibold">{{ __('Earned commission') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($earnings as $earning)
                        <tr>
                            <td class="px-5 py-4"><p class="font-semibold text-zinc-950 dark:text-white">{{ $earning->product->title ?: $earning->product->file_name }}</p><p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $earning->created_at->format('M j, Y') }} · {{ $earning->commission_percentage }}% {{ __('share') }}</p></td>
                            <td class="px-5 py-4 text-zinc-600 dark:text-zinc-300">{{ $earning->product->downloads_count }}</td>
                            <td class="px-5 py-4 text-right font-semibold text-[#087f85]">$ {{ number_format((float) $earning->amount, 4) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">{{ __('Earnings will appear here after your assets are downloaded.') }}</td></tr>
                    @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800"><h2 class="font-semibold text-zinc-950 dark:text-white">{{ __('Withdrawal status') }}</h2></div>
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($withdrawals as $withdrawal)
                        <div class="flex items-center justify-between gap-4 px-5 py-4"><div><p class="text-sm font-medium text-zinc-950 dark:text-white">{{ __('Withdrawal request') }}</p><p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $withdrawal->requested_at?->format('M j, Y') }}</p></div><div class="text-right"><p class="font-semibold text-[#087f85]">$ {{ number_format((float) $withdrawal->amount, 4) }}</p><span class="text-xs font-semibold uppercase text-zinc-500">{{ __($withdrawal->status) }}</span></div></div>
                    @empty
                        <p class="px-5 py-8 text-sm text-zinc-500 dark:text-zinc-400">{{ __('No withdrawal requests yet.') }}</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-layouts::app>
