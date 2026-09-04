<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create your Creativ Stock account')"
            :description="__('Choose the registration path that best fits your needs.')" />

        <div class="grid gap-4 sm:grid-cols-2">
            <flux:card class="group hover:border-brand-500">
                <div class="space-y-5">
                    <div>
                        <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Customer') }}</h2>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Sign up to browse, buy, and manage your digital assets.') }}</p>
                    </div>

                    <flux:link :href="route('register.customer')" class="w-full" variant="primary">
                        {{ __('Register as a customer') }}
                    </flux:link>
                </div>
            </flux:card>

            <flux:card class="group hover:border-brand-500">
                <div class="space-y-5">
                    <div>
                        <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Contributor') }}</h2>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Create an account to upload and sell digital assets on Creativ Stock.') }}</p>
                    </div>

                    <flux:link :href="route('register.contributor')" class="w-full" variant="secondary">
                        {{ __('Register as a contributor') }}
                    </flux:link>
                </div>
            </flux:card>
        </div>

        <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
