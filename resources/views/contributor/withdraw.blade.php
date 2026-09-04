<x-layouts::app :title="__('Withdraw Earnings')">
    <div class="min-h-full bg-[#f5f8f8] px-4 py-5 dark:bg-zinc-950 sm:px-6 lg:px-8">
        <div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
            <div>
                <p class="text-sm font-medium text-[#087f85]">{{ __('Contributor account') }}</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ __('Withdraw Earnings') }}</h1>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Set up where you want to receive your earnings and request a withdrawal.') }}</p>
            </div>

            @if (session('status'))
                <flux:callout variant="success" heading="{{ session('status') }}" />
            @endif
            @if ($errors->any())
                <flux:callout variant="danger" heading="{{ $errors->first() }}" />
            @endif

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border border-[#9edfd9] bg-white p-6 shadow-sm dark:border-[#376164] dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ __('Request withdrawal') }}</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Available balance:') }} <strong>$ {{ number_format($availableBalance, 4) }}</strong></p>
                    <form method="POST" action="{{ route('contributor.withdraw.store') }}" class="mt-5 flex flex-col gap-4">
                        @csrf
                        <flux:input name="amount" type="number" min="{{ $payoutSettings->minimum_payout }}" max="{{ $availableBalance }}" step="0.01" :label="__('Amount')" required />
                        <flux:button type="submit" variant="primary" :disabled="$availableBalance <= 0 || $paymentMethods->isEmpty()">{{ __('Withdraw Your Earning') }}</flux:button>
                    </form>
                    <p class="mt-3 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Minimum withdrawal:') }} $ {{ number_format((float) $payoutSettings->minimum_payout, 2) }}</p>
                    @if ($paymentMethods->isEmpty())
                        <p class="mt-3 text-xs text-amber-700">{{ __('Add a payment method before requesting a withdrawal.') }}</p>
                    @endif
                </section>

                <section class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ __('Set up payment method') }}</h2>
                    <form method="POST" action="{{ route('contributor.payment-methods.store') }}" class="mt-5 flex flex-col gap-4">
                        @csrf
                        <flux:select name="method" :label="__('Payment method')" required>
                            @foreach ($payoutSettings->payment_methods ?? [] as $method)
                                <option value="{{ $method }}">{{ $method === 'paypal' ? __('PayPal') : __('Bank transfer') }}</option>
                            @endforeach
                        </flux:select>
                        <flux:input name="account_name" :label="__('Account name')" required />
                        <flux:textarea name="account_details" :label="__('Payment details')" placeholder="{{ __('PayPal email or bank transfer details') }}" required />
                        <flux:button type="submit" variant="outline">{{ __('Save payment method') }}</flux:button>
                    </form>
                </section>
            </div>

            @if ($paymentMethods->isNotEmpty())
                <section class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ __('Saved payment methods') }}</h2>
                    <div class="mt-4 flex flex-col gap-3">
                        @foreach ($paymentMethods as $paymentMethod)
                            <div wire:key="payment-method-{{ $paymentMethod->id }}" class="flex items-center justify-between gap-4 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700"><div><p class="font-medium text-zinc-950 dark:text-white">{{ $paymentMethod->method === 'paypal' ? 'PayPal' : __('Bank transfer') }}</p><p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $paymentMethod->account_name }}</p></div>@if ($paymentMethod->is_default)<span class="text-xs font-semibold text-emerald-700">{{ __('Default') }}</span>@endif</div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-layouts::app>
