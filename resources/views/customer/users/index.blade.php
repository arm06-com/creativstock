<x-layouts::app :title="__('Users')">
    <div class="min-h-full bg-[#f5f8f8] px-4 py-5 dark:bg-zinc-950 sm:px-6 lg:px-8">
        <div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-[#087f85]">{{ __('Customer account') }}</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ __('Users') }}</h1>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Manage the people who can use your plan.') }}</p>
                </div>
                @if ($activeSubscription && ($activeSubscription->plan->user_limit === null || $userCount < $activeSubscription->plan->user_limit))
                    <a href="{{ route('customer.users.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#0fa3ad] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#087f85]" wire:navigate>
                        <flux:icon name="user-plus" variant="mini" /> {{ __('Add user') }}
                    </a>
                @endif
            </div>

            @if (session('status'))
                <flux:callout variant="success" heading="{{ session('status') }}" />
            @endif

            <section class="overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-4 border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                    <div>
                        <h2 class="font-semibold text-zinc-950 dark:text-white">{{ __('Plan users') }}</h2>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $userCount }}{{ $activeSubscription?->plan?->user_limit !== null ? ' / '.$activeSubscription->plan->user_limit : '' }} {{ __('users') }}</p>
                    </div>
                </div>
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    <div class="flex items-center gap-4 px-5 py-4">
                        <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                        <div class="min-w-0"><p class="truncate text-sm font-semibold text-zinc-950 dark:text-white">{{ auth()->user()->name }}</p><p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ auth()->user()->email }} · {{ __('Account owner') }}</p></div>
                    </div>
                    @foreach ($teamMembers as $member)
                        <div wire:key="customer-member-{{ $member->id }}" class="flex items-center gap-4 px-5 py-4">
                            <flux:avatar :name="$member->name" :initials="$member->initials()" />
                            <div class="min-w-0"><p class="truncate text-sm font-semibold text-zinc-950 dark:text-white">{{ $member->name }}</p><p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $member->email }}</p></div>
                        </div>
                    @endforeach
                </div>
            </section>

            @if (! $activeSubscription)
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('An active plan is required before you can add users.') }}</p>
            @elseif ($activeSubscription->plan->user_limit !== null && $userCount >= $activeSubscription->plan->user_limit)
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Your active plan has reached its user limit.') }}</p>
            @endif
        </div>
    </div>
</x-layouts::app>
