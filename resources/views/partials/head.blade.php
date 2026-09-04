@php
    $siteSettings = \App\Models\SiteSetting::current();
    $siteTitle = filled($title ?? null) ? $title.' - '.($siteSettings->site_title ?: config('app.name', 'Creativ Stock')) : ($siteSettings->site_title ?: config('app.name', 'Creativ Stock'));
    $siteMetaDescription = $siteSettings->meta_description ?: config('app.meta_description', 'Marketplace for creative assets.');
    $faviconPath = $siteSettings->favicon_path
        ? 'storage/'.$siteSettings->favicon_path
        : collect([
            'images/brand/favicon.svg',
            'images/brand/favicon.png',
            'images/brand/favicon.webp',
        ])->first(fn (string $path): bool => file_exists(public_path($path))) ?? 'favicon.svg';
@endphp

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="{{ $siteMetaDescription }}">

<title>
    {{ $siteTitle }}
</title>

<link rel="icon" href="{{ asset($faviconPath) }}" sizes="any">
@if (str_ends_with($faviconPath, '.svg'))
    <link rel="icon" href="{{ asset($faviconPath) }}" type="image/svg+xml">
@endif
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
