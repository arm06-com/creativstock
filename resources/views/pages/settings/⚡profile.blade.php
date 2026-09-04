<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Flux\Flux;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules, WithFileUploads;

    public string $name = '';
    public string $email = '';
    public ?UploadedFile $photo = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function rules(): array
    {
        return [
            ...$this->profileRules(Auth::user()->id),
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate();

        if ($this->photo) {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $validated['profile_photo_path'] = $this->photo->store('profile-photos', 'public');
            $this->photo = null;
        }

        unset($validated['photo']);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your profile details and photo')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="flex items-center gap-4">
                <label for="profile-photo" class="flex size-16 cursor-pointer items-center justify-center overflow-hidden rounded-full border border-zinc-200 bg-zinc-100 text-lg font-semibold text-zinc-600 shadow-sm transition hover:border-[#0fa3ad] hover:ring-2 hover:ring-[#d7f4f1] dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    @php($profilePreviewUrl = $photo ? $photo->temporaryUrl() : Auth::user()->profilePhotoUrl())
                    @if ($profilePreviewUrl)
                        <img src="{{ $profilePreviewUrl }}" alt="{{ Auth::user()->name }}" class="size-full object-cover" />
                    @else
                        {{ Auth::user()->initials() }}
                    @endif
                </label>

                <div class="flex-1">
                    <label for="profile-photo" class="block text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ __('Profile picture') }}</label>
                    <input id="profile-photo" type="file" accept="image/png,image/jpeg,image/webp" wire:model="photo" class="sr-only" />
                    <label for="profile-photo" class="mt-2 inline-flex cursor-pointer items-center rounded-md border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-700 shadow-sm transition hover:border-[#0fa3ad] hover:text-[#0d8f95] dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
                        {{ __('Choose photo') }}
                    </label>
                    @if ($photo)
                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Selected: :name', ['name' => $photo->getClientOriginalName()]) }}</p>
                    @else
                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">{{ __('PNG, JPG, or WEBP up to 2MB.') }}</p>
                    @endif
                </div>
            </div>

            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

            </div>
        </form>

    </x-pages::settings.layout>
</section>
