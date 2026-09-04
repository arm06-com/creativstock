<x-layouts::app :title="__('Create User')">
    <div class="p-6">
        <div class="max-w-3xl">
            <h1 class="text-2xl font-semibold">{{ __('Create User') }}</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Add a new admin, contributor, or customer account.') }}</p>

            <div class="mt-8 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                    @csrf

                    <flux:input name="name" :label="__('Name')" :value="old('name')" required />
                    <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required />
                    <flux:input name="password" :label="__('Password')" type="password" required />
                    <flux:input name="password_confirmation" :label="__('Confirm password')" type="password" required />

                    <flux:select name="role" :label="__('Role')" required>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                        @endforeach
                    </flux:select>

                    <flux:button type="submit" variant="primary">{{ __('Create user') }}</flux:button>
                </form>
            </div>
        </div>
    </div>
</x-layouts::app>
