<div class="flex flex-col gap-6">
    <div>
        <p class="text-sm font-medium text-[#087f85] dark:text-[#8fe3e2]">{{ __('Super Admin') }}</p>
        <h1 class="text-2xl font-semibold text-zinc-950 dark:text-white">{{ __('Commission Share') }}</h1>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Set and review the percentage contributors receive from qualifying asset downloads.') }}</p>
    </div>
    @if (session('status'))
        <flux:callout variant="success" heading="{{ session('status') }}" />
    @endif
    <section class="max-w-xl rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <form wire:submit="save" class="flex flex-col gap-5">
            <flux:field>
                <flux:label>{{ __('Contributor commission percentage') }}</flux:label>
                <flux:input type="number" min="0" max="100" step="0.01" wire:model="percentage" suffix="%" :disabled="! auth()->user()->isSuperAdmin()" />
                <flux:error name="percentage" />
                <flux:description>{{ __('Default: 50%. This rate applies to new qualifying downloads.') }}</flux:description>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Minimum payout amount') }}</flux:label>
                <flux:input type="number" min="0.01" step="0.01" wire:model="minimumPayout" prefix="$" :disabled="! auth()->user()->isSuperAdmin()" />
                <flux:error name="minimumPayout" />
                <flux:description>{{ __('Default: $20.00. Contributors must reach this amount before requesting a withdrawal.') }}</flux:description>
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Applicable payment methods') }}</flux:label>
                <div class="flex flex-col gap-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <flux:checkbox wire:model="paymentMethods" value="paypal" label="{{ __('PayPal') }}" :disabled="! auth()->user()->isSuperAdmin()" />
                    <flux:checkbox wire:model="paymentMethods" value="bank_transfer" label="{{ __('Bank transfer') }}" :disabled="! auth()->user()->isSuperAdmin()" />
                </div>
                <flux:error name="paymentMethods" />
                <flux:description>{{ __('Contributors can only choose payment methods enabled here.') }}</flux:description>
            </flux:field>
            <div class="rounded-lg bg-[#e7f7f5] p-4 text-sm text-[#126c70] dark:bg-[#0fa3ad]/15 dark:text-[#9be9e4]">{{ __('Example: a $20 plan with 50 downloads at 50% gives the contributor $0.20 per asset.') }}</div>
            @if (auth()->user()->isSuperAdmin())
                <div class="flex justify-end"><flux:button type="submit" variant="primary">{{ __('Save commission share') }}</flux:button></div>
            @else
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Only super admins can change the commission percentage.') }}</p>
            @endif
        </form>
    </section>

    <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
            <h2 class="font-semibold text-zinc-950 dark:text-white">{{ __('Contributor earnings') }}</h2>
            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Total commission earned from qualifying downloads.') }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-left text-sm dark:divide-zinc-800">
                <thead class="bg-zinc-50 dark:bg-zinc-800"><tr><th class="px-5 py-3 font-semibold">{{ __('Contributor') }}</th><th class="px-5 py-3 font-semibold">{{ __('Download count') }}</th><th class="px-5 py-3 text-right font-semibold">{{ __('Earned commission') }}</th></tr></thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($contributorTotals as $total)
                        <tr><td class="px-5 py-4"><p class="font-medium text-zinc-950 dark:text-white">{{ $total->contributor->name }}</p><p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $total->contributor->email }}</p></td><td class="px-5 py-4 text-zinc-600 dark:text-zinc-300">{{ $total->download_count }}</td><td class="px-5 py-4 text-right font-semibold text-[#087f85]">$ {{ number_format((float) $total->total_earnings, 4) }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">{{ __('Contributor earnings will appear after assets are downloaded.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
