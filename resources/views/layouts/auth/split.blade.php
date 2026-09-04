<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800">
                <div class="absolute inset-0 bg-neutral-900"></div>
                <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
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
                    <span class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-md bg-white/10 ring-1 ring-white/15">
                        <img src="{{ asset($faviconPath) }}" alt="{{ config('app.name', 'Creativ Stock') }}" class="h-full w-full object-contain" />
                    </span>
                    <span class="ms-3">{{ config('app.name', 'Laravel') }}</span>
                </a>

                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                <div class="relative z-20 mt-auto">
                    <blockquote class="space-y-2">
                        <flux:heading size="lg">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
                        <footer><flux:heading>{{ trim($author) }}</flux:heading></footer>
                    </blockquote>
                </div>
            </div>
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
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
                        <span class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-md bg-white shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-700">
                            <img src="{{ asset($faviconPath) }}" alt="{{ config('app.name', 'Creativ Stock') }}" class="h-full w-full object-contain" />
                        </span>

                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>
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
