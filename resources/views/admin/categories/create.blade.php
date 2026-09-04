<x-layouts::app :title="__('Create Category')">
    <div class="p-6">
        <div class="max-w-3xl">
            <h1 class="text-2xl font-semibold">{{ __('Create Category') }}</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Add a new category.') }}</p>

            <div class="mt-8 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-6">
                    @csrf

                    <flux:input name="name" :label="__('Name')" :value="old('name')" required />
                    <flux:textarea name="description" :label="__('Description')">{{ old('description') }}</flux:textarea>

                    <flux:button type="submit" variant="primary">{{ __('Create category') }}</flux:button>
                </form>
            </div>
        </div>
    </div>
</x-layouts::app>
