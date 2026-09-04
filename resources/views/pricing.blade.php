<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#f5f8f8] text-zinc-950 antialiased dark:bg-[#102d31] dark:text-white">
        <main class="mx-auto flex min-h-screen w-full max-w-7xl flex-col px-5 py-5 sm:px-8 lg:px-10">
            <header class="flex items-center justify-between gap-6">
                <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                    <span class="flex size-11 items-center justify-center overflow-hidden rounded-2xl bg-[#0fa3ad] p-1.5 shadow-lg shadow-[#0fa3ad]/20"><x-app-logo-icon class="size-full object-contain" /></span>
                    <span class="text-base font-semibold tracking-tight">{{ config('app.name', 'Creativ Stock') }}</span>
                </a>
                <nav class="flex items-center gap-2 text-sm font-medium">
                    <a href="{{ route('home') }}" class="rounded-xl px-4 py-2.5 text-zinc-600 transition hover:bg-white hover:text-[#087f85] dark:text-zinc-300 dark:hover:bg-white/10 dark:hover:text-[#9be9e4]" wire:navigate>{{ __('Explore') }}</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-xl bg-[#0fa3ad] px-4 py-2.5 text-white shadow-sm transition hover:bg-[#087f85]" wire:navigate>{{ __('Dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl px-4 py-2.5 text-zinc-600 transition hover:bg-white hover:text-[#087f85] dark:text-zinc-300 dark:hover:bg-white/10 dark:hover:text-[#9be9e4]" wire:navigate>{{ __('Log in') }}</a>
                    @endauth
                </nav>
            </header>

            <section class="mx-auto flex max-w-3xl flex-col gap-4 py-16 text-center sm:py-20">
                <p class="text-xs font-semibold uppercase tracking-widest text-[#087f85] dark:text-[#8fe3e2]">{{ __('Simple plans for steady work') }}</p>
                <h1 class="text-4xl font-semibold tracking-tight text-[#17383c] sm:text-6xl dark:text-white">{{ __('Choose the space your ideas need.') }}</h1>
                <p class="text-lg leading-8 text-zinc-600 dark:text-[#c5d8d8]">{{ __('Flexible access to a growing library of thoughtful creative assets.') }}</p>
            </section>

            @if ($plans->isNotEmpty())
                <section class="grid items-stretch gap-5 pb-16 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($plans as $plan)
                        <article class="relative flex flex-col gap-6 rounded-2xl border {{ $plan->is_popular ? 'border-[#0fa3ad] ring-2 ring-[#0fa3ad]/20' : 'border-zinc-200 dark:border-zinc-800' }} bg-white p-6 shadow-sm dark:bg-zinc-900">
                            <div class="flex items-start justify-between gap-3">
                                <h2 class="text-xl font-semibold text-zinc-950 dark:text-white">{{ $plan->name }}</h2>
                                @if ($plan->is_popular)
                                    <span class="shrink-0 rounded-full bg-[#0fa3ad] px-3 py-1 text-xs font-semibold text-white">{{ __('Most popular') }}</span>
                                @endif
                            </div>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-baseline gap-2"><span class="text-4xl font-semibold text-[#17383c] dark:text-white">{{ number_format((float) $plan->price, 2) }}</span><span class="text-sm text-zinc-500">{{ __('per month') }}</span></div>
                            </div>
                            <dl class="grid gap-3 border-y border-zinc-200 py-4 text-sm dark:border-zinc-700">
                                <div class="flex justify-between gap-4"><dt class="text-zinc-500">{{ __('Downloads') }}</dt><dd class="font-semibold">{{ $plan->download_limit ?? __('Unlimited') }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-zinc-500">{{ __('Users') }}</dt><dd class="font-semibold">{{ $plan->user_limit ?? __('Unlimited') }}</dd></div>
                            </dl>
                            <ul class="flex flex-1 flex-col gap-3 text-sm text-zinc-600 dark:text-zinc-300">
                                @foreach ($plan->features ?? [] as $feature)
                                    <li class="flex gap-2"><flux:icon name="check" variant="mini" class="mt-0.5 text-[#0fa3ad]" />{{ $feature }}</li>
                                @endforeach
                            </ul>
                            @auth
                                @if (auth()->user()->isCustomer())
                                    <button type="button" data-paddle-plan="{{ $plan->id }}" data-paddle-price="{{ $plan->price_id }}" class="inline-flex items-center justify-center rounded-xl {{ $plan->is_popular ? 'bg-[#0fa3ad] text-white hover:bg-[#087f85]' : 'border border-[#c9dedd] text-[#17383c] hover:border-[#0fa3ad] hover:text-[#087f85] dark:border-[#376164] dark:text-white' }} px-4 py-3 text-sm font-semibold transition">{{ __('Choose :plan', ['plan' => $plan->name]) }}</button>
                                @else
                                    <a href="{{ route('register.customer', ['redirect' => 'pricing']) }}" class="inline-flex items-center justify-center rounded-xl {{ $plan->is_popular ? 'bg-[#0fa3ad] text-white hover:bg-[#087f85]' : 'border border-[#c9dedd] text-[#17383c] hover:border-[#0fa3ad] hover:text-[#087f85] dark:border-[#376164] dark:text-white' }} px-4 py-3 text-sm font-semibold transition">{{ __('Register as customer') }}</a>
                                @endif
                            @else
                                <a href="{{ route('register.customer', ['redirect' => 'pricing']) }}" class="inline-flex items-center justify-center rounded-xl {{ $plan->is_popular ? 'bg-[#0fa3ad] text-white hover:bg-[#087f85]' : 'border border-[#c9dedd] text-[#17383c] hover:border-[#0fa3ad] hover:text-[#087f85] dark:border-[#376164] dark:text-white' }} px-4 py-3 text-sm font-semibold transition">{{ __('Choose :plan', ['plan' => $plan->name]) }}</a>
                            @endauth
                        </article>
                    @endforeach
                </section>
            @else
                <section class="flex min-h-56 items-center justify-center rounded-2xl border border-dashed border-[#b8e4e0] bg-white/60 px-6 text-center dark:border-[#377d80] dark:bg-white/5"><p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Subscription plans will be available soon.') }}</p></section>
            @endif

            <footer class="mt-auto flex flex-col gap-3 border-t border-[#dcebea] py-5 text-xs text-zinc-500 sm:flex-row sm:items-center sm:justify-between dark:border-[#244447] dark:text-[#9fb7b8]"><span>{{ __('A focused marketplace for creative people.') }}</span><span>{{ config('app.name', 'Creativ Stock') }}</span></footer>
        </main>
        @auth
            @if (auth()->user()->isCustomer() && $plans->isNotEmpty())
                <script src="https://cdn.paddle.com/paddle/v2/paddle.js"></script>
                <script>
                    Paddle.Environment.set(@js(config('paddle.environment')));
                    Paddle.Initialize({
                        token: @js(config('paddle.client_token')),
                        eventCallback: (event) => {
                            if (event.name === 'checkout.completed' && event.data?.transaction_id) {
                                fetch(@js(route('customer.my-plan.sync')), {
                                    method: 'POST',
                                    credentials: 'same-origin',
                                    keepalive: true,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    },
                                    body: JSON.stringify({ transaction_id: event.data.transaction_id }),
                                }).finally(() => window.location.assign(@js(route('customer.my-plan'))));
                            }
                        },
                    });
                    document.addEventListener('click', (event) => {
                        const button = event.target.closest('[data-paddle-price]');

                        if (button) {
                            Paddle.Checkout.open({
                                items: [{ priceId: button.dataset.paddlePrice, quantity: 1 }],
                                customData: { user_id: @js(auth()->id()), plan_id: Number(button.dataset.paddlePlan) },
                                settings: { displayMode: 'redirect', successUrl: @js(route('customer.my-plan')) },
                            });
                        }
                    });
                </script>
            @endif
        @endauth
        @fluxScripts
    </body>
</html>
