<x-layouts::app :title="__('Add User')">
    <div class="min-h-full bg-[#f5f8f8] px-4 py-5 dark:bg-zinc-950 sm:px-6 lg:px-8">
        <div class="mx-auto w-full max-w-3xl">
            <div>
                <p class="text-sm font-medium text-[#087f85]">{{ __('Customer account') }}</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ __('Add user') }}</h1>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Give someone access to your active plan.') }}</p>
            </div>
            <form method="POST" action="{{ route('customer.users.store') }}" class="mt-6 flex flex-col gap-5 rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                @csrf
                <flux:input name="name" :label="__('Name')" :value="old('name')" required />
                <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required />
                <flux:input name="password" :label="__('Password')" type="password" required />
                <flux:input name="password_confirmation" :label="__('Confirm password')" type="password" required />
                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('customer.users.index') }}" class="text-sm font-semibold text-zinc-500 hover:text-zinc-800 dark:hover:text-white" wire:navigate>{{ __('Cancel') }}</a>
                    <flux:button type="submit" variant="primary">{{ __('Add user') }}</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
