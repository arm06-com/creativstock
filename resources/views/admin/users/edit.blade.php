<x-layouts::app :title="__('Edit User')">
    <div class="p-6">
        <div class="max-w-3xl">
            <h1 class="text-2xl font-semibold">{{ __('Edit User') }}</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Update account details and role assignment.') }}</p>

            <div class="mt-8 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <flux:input name="name" :label="__('Name')" :value="old('name', $user->name)" required />
                    <flux:input name="email" :label="__('Email address')" :value="old('email', $user->email)" type="email" required />
                    <flux:input name="password" :label="__('Password')" type="password" :placeholder="__('Leave blank to keep current password')" />
                    <flux:input name="password_confirmation" :label="__('Confirm password')" type="password" />

                    <flux:select name="role" :label="__('Role')" required>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                        @endforeach
                    </flux:select>

                    <flux:button type="submit" variant="primary">{{ __('Update user') }}</flux:button>
                </form>
            </div>
        </div>
    </div>
</x-layouts::app>
