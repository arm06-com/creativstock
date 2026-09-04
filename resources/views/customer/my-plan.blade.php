<x-layouts::app :title="__('My Plan')">
    <div class="min-h-full bg-[#f5f8f8] px-4 py-5 dark:bg-zinc-950 sm:px-6 lg:px-8">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6">
            <div>
                <p class="text-sm font-medium text-[#087f85]">{{ __('Customer account') }}</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ __('My Plan') }}</h1>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Manage your creative access and choose the plan that fits your work.') }}</p>
            </div>

            @if ($activeSubscription)
                <section class="rounded-2xl border border-[#9edfd9] bg-white p-6 shadow-sm dark:border-[#376164] dark:bg-zinc-900">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <h2 class="text-xl font-semibold text-zinc-950 dark:text-white">{{ $activeSubscription->plan->name }}</h2>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300">{{ __('Active') }}</span>
                            </div>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Your plan is ready to use.') }}</p>
                        </div>
                        <p class="text-2xl font-semibold text-[#087f85]">{{ number_format((float) $activeSubscription->plan->price, 2) }} <span class="text-sm font-normal text-zinc-500">{{ __('per month') }}</span></p>
                    </div>
                    <dl class="mt-6 grid gap-4 border-t border-zinc-200 pt-5 sm:grid-cols-3 dark:border-zinc-700">
                        <div><dt class="text-xs text-zinc-500">{{ __('Downloads used') }}</dt><dd class="mt-1 text-lg font-semibold text-zinc-950 dark:text-white">{{ $activeSubscription->downloads_used }}{{ $activeSubscription->plan->download_limit !== null ? ' / '.$activeSubscription->plan->download_limit : '' }}</dd></div>
                        <div><dt class="text-xs text-zinc-500">{{ __('Downloads remaining') }}</dt><dd class="mt-1 text-lg font-semibold text-zinc-950 dark:text-white">{{ $activeSubscription->plan->download_limit === null ? __('Unlimited') : max($activeSubscription->plan->download_limit - $activeSubscription->downloads_used, 0) }}</dd></div>
                        <div><dt class="text-xs text-zinc-500">{{ __('Users included') }}</dt><dd class="mt-1 text-lg font-semibold text-zinc-950 dark:text-white">{{ $activeSubscription->plan->user_limit ?? __('Unlimited') }}</dd></div>
                    </dl>
                    @if (($activeSubscription->plan->features ?? []) !== [])
                        <ul class="mt-5 flex flex-wrap gap-x-5 gap-y-2 border-t border-zinc-200 pt-5 text-sm text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">
                            @foreach ($activeSubscription->plan->features as $feature)
                                <li class="flex items-center gap-2"><flux:icon name="check" variant="mini" class="text-[#0fa3ad]" />{{ $feature }}</li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @else
                <section class="rounded-2xl border border-dashed border-[#9edfd9] bg-white p-8 text-center shadow-sm dark:border-[#376164] dark:bg-zinc-900">
                    <flux:icon name="credit-card" class="mx-auto size-10 text-[#0fa3ad]" />
                    <h2 class="mt-4 text-lg font-semibold text-zinc-950 dark:text-white">{{ __('Choose a plan to get started') }}</h2>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('You do not have an active plan yet. Select one below to continue.') }}</p>
                    <a href="{{ route('pricing') }}" class="mt-5 inline-flex items-center justify-center rounded-xl bg-[#0fa3ad] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#087f85]">{{ __('View pricing') }}</a>
                </section>
            @endif

            <section>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ __('Other plans') }}</h2>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Compare plans and choose a different plan from pricing.') }}</p>
                    </div>
                    <a href="{{ route('pricing') }}" class="shrink-0 text-sm font-semibold text-[#087f85] hover:underline">{{ __('View pricing') }}</a>
                </div>
                <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($plans as $plan)
                        @if (! $activeSubscription || $activeSubscription->subscription_plan_id !== $plan->id)
                            <article wire:key="customer-plan-option-{{ $plan->id }}" class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="font-semibold text-zinc-950 dark:text-white">{{ $plan->name }}</h3>
                                    <span class="text-sm font-semibold text-[#087f85]">{{ number_format((float) $plan->price, 2) }}</span>
                                </div>
                                <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">{{ $plan->download_limit ?? __('Unlimited') }} {{ __('downloads per month') }}</p>
                            </article>
                        @endif
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-layouts::app>
