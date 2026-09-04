<x-layouts::app :title="__('Users')">
    <div class="p-6">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">{{ __('Manage Users') }}</h1>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Manage contributors and customers.') }}</p>
                </div>

                @can('create-users')
                    <flux:link :href="route('admin.users.create')" variant="primary">
                        {{ __('Create user') }}
                    </flux:link>
                @endcan
            </div>

            @if (session('status'))
                <flux:callout variant="success" heading="{{ session('status') }}" />
            @endif

            <div class="rounded-xl border border-zinc-200 bg-white p-2 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex flex-wrap gap-2">
                    @if (auth()->user()?->isSuperAdmin())
                        <a href="{{ route('admin.users.index', ['tab' => 'admins']) }}" class="rounded-lg px-4 py-2 text-sm font-medium transition {{ $activeTab === 'admins' ? 'bg-[#d9f3f0] text-[#087f85] dark:bg-[#0fa3ad]/20 dark:text-[#9be9e4]' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}">
                            {{ __('Admins') }}
                        </a>
                    @endif
                    <a href="{{ route('admin.users.index', ['tab' => 'contributors']) }}" class="rounded-lg px-4 py-2 text-sm font-medium transition {{ $activeTab === 'contributors' ? 'bg-[#d9f3f0] text-[#087f85] dark:bg-[#0fa3ad]/20 dark:text-[#9be9e4]' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}">
                        {{ __('Contributors') }}
                    </a>
                    <a href="{{ route('admin.users.index', ['tab' => 'customers']) }}" class="rounded-lg px-4 py-2 text-sm font-medium transition {{ $activeTab === 'customers' ? 'bg-[#d9f3f0] text-[#087f85] dark:bg-[#0fa3ad]/20 dark:text-[#9be9e4]' : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800' }}">
                        {{ __('Customers') }}
                    </a>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                <table class="min-w-full divide-y divide-zinc-200 text-left text-sm text-zinc-700 dark:divide-zinc-700 dark:text-zinc-200">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 font-semibold">{{ __('Name') }}</th>
                            <th class="px-4 py-3 font-semibold">{{ __('Email') }}</th>
                            <th class="px-4 py-3 font-semibold">{{ __('Status') }}</th>
                            <th class="px-4 py-3 font-semibold">{{ __('Created') }}</th>
                            <th class="px-4 py-3 font-semibold">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-950">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-4 py-4">{{ $user->name }}</td>
                                <td class="px-4 py-4">{{ $user->email }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300' }}">
                                        {{ $user->active ? __('Active') : __('Inactive') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">{{ $user->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-4 space-x-2">
                                    @can('manage-users')
                                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            @if ($user->active)
                                                <flux:button type="submit" variant="danger" size="sm">
                                                    {{ __('Deactivate') }}
                                                </flux:button>
                                            @else
                                                <flux:button type="submit" variant="filled" size="sm" class="bg-emerald-500 text-white hover:bg-emerald-600 dark:bg-emerald-500 dark:hover:bg-emerald-600">
                                                    {{ __('Activate') }}
                                                </flux:button>
                                            @endif
                                        </form>
                                    @endcan

                                    @can('edit-users')
                                        <flux:link :href="route('admin.users.edit', $user)" variant="secondary">
                                            {{ __('Edit') }}
                                        </flux:link>
                                    @endcan

                                    @can('delete-users')
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" variant="danger" size="sm">
                                                {{ __('Delete') }}
                                            </flux:button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    @if ($activeTab === 'customers')
                                        {{ __('No customers found.') }}
                                    @elseif ($activeTab === 'admins')
                                        {{ __('No admins found.') }}
                                    @else
                                        {{ __('No contributors found.') }}
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-layouts::app>
