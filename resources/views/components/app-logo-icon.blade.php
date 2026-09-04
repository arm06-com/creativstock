@php
    $siteSettings = \App\Models\SiteSetting::current();
    $logoPath = $siteSettings->logo_path ? 'storage/'.$siteSettings->logo_path : collect([
        'images/brand/logo.svg',
        'images/brand/logo.png',
        'images/brand/logo.webp',
    ])->first(fn (string $path): bool => file_exists(public_path($path)));
@endphp

@if ($logoPath)
    <img src="{{ asset($logoPath) }}" alt="{{ config('app.name', 'Creativ Stock') }}" {{ $attributes->except('fill') }} />
@else
    <span {{ $attributes }} aria-hidden="true"></span>
@endif
