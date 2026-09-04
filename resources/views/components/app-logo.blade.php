@props([
    'sidebar' => false,
])

@php
    $siteSettings = \App\Models\SiteSetting::current();
    $logoPath = $siteSettings->logo_path ? 'storage/'.$siteSettings->logo_path : collect([
        'images/brand/logo.svg',
        'images/brand/logo.png',
        'images/brand/logo.webp',
    ])->first(fn (string $path): bool => file_exists(public_path($path)));
@endphp

@if($sidebar)
    <flux:sidebar.brand {{ $attributes }}>
        <x-slot name="logo" class="flex items-center justify-center overflow-hidden bg-transparent p-0">
            @if ($logoPath)
                <img src="{{ asset($logoPath) }}" alt="{{ config('app.name', 'Creativ Stock') }}" class="h-10 w-auto max-w-[250px] object-contain" />
            @else
                <x-app-logo-icon class="size-8 fill-current" />
            @endif
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand {{ $attributes }}>
        <x-slot name="logo" class="flex items-center justify-center overflow-hidden bg-transparent p-0">
            @if ($logoPath)
                <img src="{{ asset($logoPath) }}" alt="{{ config('app.name', 'Creativ Stock') }}" class="h-10 w-auto max-w-[250px] object-contain" />
            @else
                <x-app-logo-icon class="size-8 fill-current text-white dark:text-black" />
            @endif
        </x-slot>
    </flux:brand>
@endif
