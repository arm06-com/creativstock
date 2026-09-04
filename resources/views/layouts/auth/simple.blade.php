<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    @php
                        $siteSettings = \App\Models\SiteSetting::current();
                        $faviconPath = $siteSettings->favicon_path
                            ? 'storage/'.$siteSettings->favicon_path
                            : collect([
                                'images/brand/favicon.svg',
                                'images/brand/favicon.png',
                                'images/brand/favicon.webp',
                            ])->first(fn (string $path): bool => file_exists(public_path($path))) ?? 'images/brand/favicon.png';
                    @endphp
                    <span class="mb-1 flex h-10 w-10 items-center justify-center overflow-hidden rounded-md bg-white shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                        <img src="{{ asset($faviconPath) }}" alt="{{ config('app.name', 'Creativ Stock') }}" class="h-full w-full object-contain" />
                    </span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
