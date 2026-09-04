<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-[#dcebea] bg-[#f4f9f8] dark:border-[#244447] dark:bg-[#102d31]">
            <flux:sidebar.header class="border-b border-[#dcebea] px-3 pb-4 dark:border-[#244447]">
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav class="gap-1 px-3 py-5">
                <flux:sidebar.group :heading="__('Workspace')" class="grid gap-1">
                    <flux:sidebar.item icon="squares-2x2" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate class="rounded-xl px-3 py-2.5 font-medium data-current:bg-[#d9f3f0] data-current:text-[#087f85] dark:data-current:bg-[#0fa3ad]/20 dark:data-current:text-[#9be9e4]">
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    @if (auth()->user()->isContributor())
                        <flux:sidebar.item icon="photo" :href="route('contributor.assets.index')" :current="request()->routeIs('contributor.assets.*')" wire:navigate class="rounded-xl px-3 py-2.5 font-medium data-current:bg-[#d9f3f0] data-current:text-[#087f85] dark:data-current:bg-[#0fa3ad]/20 dark:data-current:text-[#9be9e4]">
                            {{ __('My Assets') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="banknotes" :href="route('contributor.earnings')" :current="request()->routeIs('contributor.earnings')" wire:navigate class="rounded-xl px-3 py-2.5 font-medium data-current:bg-[#d9f3f0] data-current:text-[#087f85] dark:data-current:bg-[#0fa3ad]/20 dark:data-current:text-[#9be9e4]">
                            {{ __('My Earnings') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="arrow-up-tray" :href="route('contributor.withdraw')" :current="request()->routeIs('contributor.withdraw*')" wire:navigate class="rounded-xl px-3 py-2.5 font-medium data-current:bg-[#d9f3f0] data-current:text-[#087f85] dark:data-current:bg-[#0fa3ad]/20 dark:data-current:text-[#9be9e4]">
                            {{ __('Withdraw Earnings') }}
                        </flux:sidebar.item>
                    @endif

                    @if (auth()->user()->isCustomer())
                        <flux:sidebar.item icon="photo" :href="route('customer.my-assets')" :current="request()->routeIs('customer.my-assets')" wire:navigate class="rounded-xl px-3 py-2.5 font-medium data-current:bg-[#d9f3f0] data-current:text-[#087f85] dark:data-current:bg-[#0fa3ad]/20 dark:data-current:text-[#9be9e4]">
                            {{ __('My Assets') }}
                        </flux:sidebar.item>
                        @if (auth()->user()->customer_id === null)
                            <flux:sidebar.item icon="users" :href="route('customer.users.index')" :current="request()->routeIs('customer.users.*')" wire:navigate class="rounded-xl px-3 py-2.5 font-medium data-current:bg-[#d9f3f0] data-current:text-[#087f85] dark:data-current:bg-[#0fa3ad]/20 dark:data-current:text-[#9be9e4]">
                                {{ __('Users') }}
                            </flux:sidebar.item>
                        @endif
                        <flux:sidebar.item icon="credit-card" :href="route('customer.my-plan')" :current="request()->routeIs('customer.my-plan')" class="rounded-xl px-3 py-2.5 font-medium data-current:bg-[#d9f3f0] data-current:text-[#087f85] dark:data-current:bg-[#0fa3ad]/20 dark:data-current:text-[#9be9e4]">
                            {{ __('My Plan') }}
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav class="gap-1 px-3 pb-5">
                @if (auth()->user()->can('manage-users') || auth()->user()->can('manage-categories') || (auth()->user()->can('manage-products') && ! auth()->user()->isContributor()) || auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                    <flux:sidebar.group :heading="__('Manage')" class="grid gap-1">
                        @can('manage-users')
                            <flux:sidebar.item icon="users" :href="route('admin.users.index', ['tab' => 'contributors'])" :current="request()->routeIs('admin.users.*')" wire:navigate class="rounded-xl px-3 py-2.5">
                                {{ __('Users') }}
                            </flux:sidebar.item>
                        @endcan

                        @if (auth()->user()->can('manage-products') && ! auth()->user()->isContributor())
                            <flux:sidebar.item icon="photo" :href="route('admin.assets.index')" :current="request()->routeIs('admin.assets.index')" wire:navigate class="rounded-xl px-3 py-2.5">
                                {{ __('Assets') }}
                            </flux:sidebar.item>
                        @endif

                        @can('manage-categories')
                            <flux:sidebar.item icon="tag" :href="route('admin.categories.index')" :current="request()->routeIs('admin.categories.*')" wire:navigate class="rounded-xl px-3 py-2.5">
                                {{ __('Categories') }}
                            </flux:sidebar.item>
                        @endcan

                        @if (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                            <flux:sidebar.item icon="chart-bar" :href="route('admin.financial-report.index')" :current="request()->routeIs('admin.financial-report.*')" wire:navigate class="rounded-xl px-3 py-2.5">
                                {{ __('Financial Report') }}
                            </flux:sidebar.item>
                        @endif
                        @if (auth()->user()->isSuperAdmin())
                            <flux:sidebar.item icon="cog-6-tooth" :href="route('admin.settings')" :current="request()->routeIs('admin.settings*')" wire:navigate class="rounded-xl px-3 py-2.5">
                                {{ __('Site Settings') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item icon="credit-card" :href="route('admin.subscription-plans.index')" :current="request()->routeIs('admin.subscription-plans.*')" wire:navigate class="rounded-xl px-3 py-2.5">
                                {{ __('Subscription Plans') }}
                            </flux:sidebar.item>
                        @endif
                        @if (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                            <flux:sidebar.item icon="banknotes" :href="route('admin.commission-share.index')" :current="request()->routeIs('admin.commission-share.*')" wire:navigate class="rounded-xl px-3 py-2.5">
                                {{ __('Commission Share') }}
                            </flux:sidebar.item>
                        @endif
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    :src="auth()->user()->profilePhotoUrl()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                    :src="auth()->user()->profilePhotoUrl()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
