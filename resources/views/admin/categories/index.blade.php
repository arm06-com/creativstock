<x-layouts::app :title="__('Categories')">
    <div class="p-6">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">{{ __('Manage Categories') }}</h1>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Create, edit, and manage categories.') }}</p>
                </div>

                @can('manage-categories')
                    <flux:link :href="route('admin.categories.create')" variant="primary" wire:navigate class="cursor-pointer">
                        {{ __('Create category') }}
                    </flux:link>
                @endcan
            </div>

            @if (session('status'))
                <flux:callout variant="success" heading="{{ session('status') }}" />
            @endif

            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                <table class="min-w-full divide-y divide-zinc-200 text-left text-sm text-zinc-700 dark:divide-zinc-700 dark:text-zinc-200">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 font-semibold">{{ __('Name') }}</th>
                            <th class="px-4 py-3 font-semibold">{{ __('Slug') }}</th>
                            <th class="px-4 py-3 font-semibold">{{ __('Description') }}</th>
                            <th class="px-4 py-3 font-semibold">{{ __('Created') }}</th>
                            <th class="px-4 py-3 font-semibold">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-700 dark:bg-zinc-950">
                        @forelse ($categories as $category)
                            <tr>
                                <td class="px-4 py-4">{{ $category->name }}</td>
                                <td class="px-4 py-4">{{ $category->slug }}</td>
                                <td class="px-4 py-4">{{ Str::limit($category->description, 60) }}</td>
                                <td class="px-4 py-4">{{ $category->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-4 space-x-2">
                                    @can('manage-categories')
                                        <flux:link :href="route('admin.categories.edit', $category)" variant="secondary">
                                            {{ __('Edit') }}
                                        </flux:link>

                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" variant="danger" size="sm">{{ __('Delete') }}</flux:button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">{{ __('No categories found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</x-layouts::app>

