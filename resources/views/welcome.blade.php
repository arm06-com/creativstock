<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <style>
            [x-cloak] { display: none !important; }
            .desktop-nav { display: none; }
            .mobile-menu-toggle { display: inline-flex; }

            @media (min-width: 768px) {
                .desktop-nav { display: flex !important; }
                .mobile-menu-toggle { display: none !important; }
            }

            @media (max-width: 767px) {
                .desktop-nav { display: none !important; }
            }
        </style>
    </head>
    <body class="min-h-screen bg-[#f5f8f8] text-zinc-950 antialiased dark:bg-[#102d31] dark:text-white">
        <main id="top" x-data="{ mobileMenuOpen: false }" class="mx-auto flex min-h-screen w-full max-w-7xl flex-col px-4 py-4 sm:px-8 lg:px-10">
            <header class="relative flex items-center justify-between gap-4 border-b border-zinc-200/80 px-2 py-4 dark:border-zinc-800 dark:bg-[#102d31]/90 sm:px-3">
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3" style="width: 160px !important;" wire:navigate>
                    <x-app-logo-icon class="size-full object-contain" />
                </a>

                @if (Route::has('login'))
                    <nav class="desktop-nav items-center gap-2 text-sm font-medium">
                        <a href="{{ route('pricing') }}" class="rounded-xl px-4 py-2.5 text-zinc-600 transition hover:bg-white hover:text-[#087f85] dark:text-zinc-300 dark:hover:bg-white/10 dark:hover:text-[#9be9e4]" wire:navigate>{{ __('Pricing') }}</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-xl bg-[#0fa3ad] px-4 py-2.5 text-white shadow-sm transition hover:bg-[#087f85]" wire:navigate>{{ __('Dashboard') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-xl px-4 py-2.5 text-zinc-600 transition hover:bg-white hover:text-[#087f85] dark:text-zinc-300 dark:hover:bg-white/10 dark:hover:text-[#9be9e4]" wire:navigate>{{ __('Log in') }}</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-xl bg-[#17383c] px-4 py-2.5 text-white shadow-sm transition hover:bg-[#0fa3ad] dark:bg-white dark:text-[#17383c] dark:hover:bg-[#d7f4f1]" wire:navigate>{{ __('Create account for free') }}</a>
                            @endif
                        @endauth
                    </nav>

                    <button
                        type="button"
                        id="mobile-menu-toggle"
                        class="mobile-menu-toggle items-center justify-center rounded-xl border border-zinc-200 bg-white p-2.5 text-zinc-700 shadow-sm transition hover:border-[#0fa3ad] hover:text-[#087f85] dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200"
                        aria-label="{{ __('Toggle navigation menu') }}"
                        aria-expanded="false"
                        x-on:click="mobileMenuOpen = !mobileMenuOpen; $el.setAttribute('aria-expanded', mobileMenuOpen)"
                    >
                        <flux:icon name="bars-2" variant="mini" class="size-5" />
                    </button>
                @endif
            </header>

            <div x-show="mobileMenuOpen" x-cloak x-collapse class="overflow-hidden md:hidden">
                <nav class="mt-3 flex flex-col gap-2 rounded-2xl border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <a href="{{ route('pricing') }}" class="rounded-xl px-3 py-2.5 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 hover:text-[#087f85] dark:text-zinc-200 dark:hover:bg-zinc-800" wire:navigate>{{ __('Pricing') }}</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-xl bg-[#0fa3ad] px-3 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-[#087f85]" wire:navigate>{{ __('Dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl px-3 py-2.5 text-sm font-medium text-zinc-700 transition hover:bg-zinc-100 hover:text-[#087f85] dark:text-zinc-200 dark:hover:bg-zinc-800" wire:navigate>{{ __('Log in') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-xl bg-[#17383c] px-3 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-[#0fa3ad] dark:bg-white dark:text-[#17383c] dark:hover:bg-[#d7f4f1]" wire:navigate>{{ __('Create account fo free') }}</a>
                        @endif
                    @endauth
                </nav>
            </div>
            <section class="relative mt-8 overflow-hidden rounded-4xl bg-[#17383c] px-6 py-16 shadow-2xl shadow-[#17383c]/15 sm:px-12 lg:px-20 lg:py-20">
                <div class="pointer-events-none absolute inset-y-0 right-0 hidden w-2/5 bg-[radial-gradient(circle_at_center,#0fa3ad_0,transparent_65%)] opacity-40 lg:block"></div>
                <div class="relative flex max-w-3xl flex-col gap-7">
                    <span class="w-fit rounded-full border border-[#5ab8b5]/50 bg-[#0fa3ad]/20 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-[#b8efeb]">{{ __('The creator marketplace') }}</span>
                    <div class="flex flex-col gap-4">
                        <h1 class="max-w-2xl text-4xl font-semibold leading-[1.06] tracking-tight text-white sm:text-5xl lg:text-6xl">{{ __('Find the right material for your next idea.') }}</h1>
                        <p class="max-w-xl text-base leading-7 text-[#c5d8d8] sm:text-lg sm:leading-8">{{ __('Thoughtful images, illustrations, and templates made by independent creators.') }}</p>
                    </div>

                    <form method="GET" action="{{ route('home') }}" class="flex flex-col gap-3 rounded-2xl bg-white p-2 shadow-xl sm:flex-row">
                        <label class="flex min-w-0 flex-1 items-center gap-3 px-3 text-zinc-400">
                            <flux:icon name="magnifying-glass" variant="mini" />
                            <span class="sr-only">{{ __('Search assets') }}</span>
                            <input type="search" name="search" value="{{ $search }}" placeholder="{{ __('Search images, ideas, and styles') }}" class="min-w-0 flex-1 border-0 bg-transparent py-2 text-sm text-zinc-900 outline-none placeholder:text-zinc-400 focus:ring-0" />
                        </label>
                        <label class="flex items-center border-t border-zinc-200 px-3 sm:border-l sm:border-t-0">
                            <span class="sr-only">{{ __('Category') }}</span>
                            <select name="category" class="border-0 bg-transparent py-2 text-sm font-medium text-zinc-700 outline-none focus:ring-0">
                                <option value="">{{ __('All categories') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected($selectedCategory === $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button type="submit" class="rounded-xl bg-[#0fa3ad] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#087f85]">{{ __('Search') }}</button>
                    </form>
                </div>
            </section>
            <section
                class="flex flex-col gap-5 py-12"
                x-data="{
                    paused: false,
                    timer: null,
                    getVisibleCount() {
                        const width = window.innerWidth;
                        if (width >= 1280) return 5;
                        if (width >= 1024) return 4;
                        if (width >= 768) return 3;
                        return 2;
                    },
                    updateTrack() {
                        const track = this.$refs.categoryTrack;
                        if (!track) return;

                        const cards = [...track.querySelectorAll('a')];
                        if (!cards.length) return;

                        const visibleCount = this.getVisibleCount();
                        const gap = window.innerWidth >= 768 ? 16 : 12;
                        const availableWidth = track.clientWidth;
                        const cardWidth = (availableWidth - (gap * (visibleCount - 1))) / visibleCount;

                        cards.forEach((card) => {
                            card.style.width = `${Math.max(cardWidth, 140)}px`;
                            card.style.flex = '0 0 auto';
                        });

                        track.style.gap = `${gap}px`;
                        track.style.overflow = 'hidden';
                    },
                    move(direction) {
                        const track = this.$refs.categoryTrack;
                        if (!track) return;

                        const cards = [...track.querySelectorAll('a')];
                        if (!cards.length) return;

                        const gap = 16;
                        const itemWidth = cards[0].getBoundingClientRect().width + gap;
                        track.scrollBy({ left: itemWidth * direction, behavior: 'smooth' });
                    }
                }"
                x-init="updateTrack(); timer = setInterval(() => { if (!paused) move(1) }, 4000); window.addEventListener('resize', updateTrack); return () => { clearInterval(timer); window.removeEventListener('resize', updateTrack); }"
                x-on:mouseenter="paused = true"
                x-on:mouseleave="paused = false"
                x-on:focusin="paused = true"
                x-on:focusout="paused = false"
            >
                <div class="flex items-end justify-between gap-4">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-2xl font-semibold tracking-tight">{{ __('Categories') }}</h2>
                    </div>
                    @if ($categories->isNotEmpty())
                        <div class="flex items-center gap-2">
                            <button type="button" x-on:click="move(-1)" class="flex size-9 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-600 transition hover:border-[#0fa3ad] hover:text-[#087f85] focus:outline-none focus:ring-2 focus:ring-[#0fa3ad] dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300" aria-label="{{ __('Previous category') }}" title="{{ __('Previous category') }}">
                                <flux:icon name="chevron-left" variant="mini" />
                            </button>
                            <button type="button" x-on:click="move(1)" class="flex size-9 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-600 transition hover:border-[#0fa3ad] hover:text-[#087f85] focus:outline-none focus:ring-2 focus:ring-[#0fa3ad] dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300" aria-label="{{ __('Next category') }}" title="{{ __('Next category') }}">
                                <flux:icon name="chevron-right" variant="mini" />
                            </button>
                        </div>
                    @endif
                </div>

                <div class="overflow-hidden">
                    <div x-ref="categoryTrack" class="flex overflow-hidden pb-2" style="scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
                        @forelse ($categories as $category)
                            @php
                                $categoryAsset = $approvedAssets->firstWhere('category_id', $category->id);
                            @endphp
                            <a href="{{ route('home', ['category' => $category->id]) }}" class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
                                <div class="h-28 overflow-hidden bg-[#d7f4f1] dark:bg-[#174448] sm:h-32 md:h-28 xl:h-32">
                                    @if ($categoryAsset)
                                        <img src="{{ $categoryAsset->preview_url ?: asset('images/placeholder.png') }}" alt="{{ $category->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                                    @else
                                        <div class="flex size-full items-center justify-center text-[#087f85] dark:text-[#8fe3e2]"><flux:icon name="photo" class="size-8" /></div>
                                    @endif
                                </div>
                                <div class="flex h-[52px] items-center justify-between gap-2 px-3 py-3 sm:px-4">
                                    <span class="truncate text-sm font-semibold">{{ $category->name }}</span>
                                    <span class="text-xs text-zinc-500">{{ $category->products_count }}</span>
                                </div>
                            </a>
                        @empty
                            <p class="text-sm text-zinc-500">{{ __('Categories will appear here soon.') }}</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section id="recent-images" class="flex flex-col gap-5 pb-12" x-data="{ selectedAsset: null }">
                <div class="flex items-end justify-between gap-4">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-2xl font-semibold tracking-tight">{{ __('Recent images') }}</h2>
                    </div>
                </div>

                @if ($approvedAssets->isNotEmpty() || $designerFavAssets->isNotEmpty())
                    @if ($approvedAssets->isNotEmpty())
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5">
                        @foreach ($approvedAssets as $asset)
                            @php
                                $assetDetails = function ($asset, int $depth = 1) use (&$assetDetails, $approvedAssets, $customerSubscription): array {
                                    return [
                                        'id' => $asset->id,
                                        'title' => $asset->title ?: $asset->file_name,
                                        'description' => $asset->description,
                                        'category' => $asset->category?->name,
                                        'type' => $asset->asset_type,
                                        'isAi' => $asset->is_ai,
                                        'size' => $asset->image_size,
                                        'dimensions' => $asset->dimensions,
                                        'image' => $asset->preview_url ?: asset('images/placeholder.png'),
                                        'download' => route('assets.download', $asset),
                                        'canDownload' => (bool) $customerSubscription,
                                        'purchase' => auth()->check()
                                            ? route('pricing')
                                            : route('register.customer', ['redirect' => 'pricing']),
                                        'related' => $depth > 0
                                            ? $approvedAssets->where('category_id', $asset->category_id)->where('id', '!=', $asset->id)->take(4)->map(fn ($relatedAsset) => $assetDetails($relatedAsset, $depth - 1))->values()->all()
                                            : [],
                                    ];
                                };
                                $assetDetails = $assetDetails($asset, 2);
                            @endphp

                            <article class="group relative overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
                                <button
                                    type="button"
                                    x-on:click="selectedAsset = @js($assetDetails)"
                                    class="block w-full cursor-pointer text-left"
                                >
                                    <div class="overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                        <img
                                            src="{{ $asset->preview_url ?: asset('images/placeholder.png') }}"
                                            alt="{{ $asset->title ?: $asset->file_name }}"
                                            class="block aspect-[4/5] w-full object-cover transition duration-500 group-hover:scale-105"
                                        />
                                    </div>
                                </button>

                                @if ($asset->is_ai)
                                    <span class="pointer-events-none absolute left-3 top-3 rounded-full bg-[#17383c]/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-[#d7f4f1] shadow-lg backdrop-blur-sm">
                                        {{ __('AI') }}
                                    </span>
                                @endif

                                <button
                                    type="button"
                                    x-on:click="selectedAsset = @js($assetDetails)"
                                    aria-label="{{ __('View :name', ['name' => $asset->file_name]) }}"
                                    title="{{ __('View image details') }}"
                                    class="absolute right-3 top-3 flex size-9 items-center justify-center rounded-full bg-[#17383c]/85 text-white opacity-0 shadow-lg transition group-hover:opacity-100 focus:opacity-100"
                                >
                                    <flux:icon name="eye" variant="mini" />
                                </button>
                            </article>
                        @endforeach
                    </div>

                    @else
                        <div class="flex min-h-48 items-center justify-center rounded-2xl border border-dashed border-[#b8e4e0] bg-white/60 px-6 text-center dark:border-[#377d80] dark:bg-white/5">
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Approved images will appear here as the collection grows.') }}</p>
                        </div>
                    @endif

                    @endif

                    @if ($designerFavAssets->isNotEmpty())
                        <section id="designers-fav" class="mt-8 flex flex-col gap-5 border-t border-zinc-200 pt-10 dark:border-zinc-800">
                            <div class="flex items-end justify-between gap-4">
                                <div class="flex flex-col gap-1">
                                    <h2 class="text-2xl font-semibold tracking-tight">{{ __('Designers Fav') }}</h2>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Vectors, icons, illustrations, and templates selected for creative work.') }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5" style="width: 100% !important;">
                                @foreach ($designerFavAssets as $asset)
                                    @php
                                        $designerAssetDetails = function ($asset, int $depth = 1) use (&$designerAssetDetails, $designerFavAssets, $customerSubscription): array {
                                            return [
                                                'id' => $asset->id,
                                                'title' => $asset->title ?: $asset->file_name,
                                                'description' => $asset->description,
                                                'category' => $asset->category?->name,
                                                'type' => $asset->asset_type,
                                                'isAi' => $asset->is_ai,
                                                'size' => $asset->image_size,
                                                'dimensions' => $asset->dimensions,
                                                'image' => $asset->preview_url ?: asset('images/placeholder.png'),
                                                'download' => route('assets.download', $asset),
                                                'canDownload' => (bool) $customerSubscription,
                                                'purchase' => auth()->check() ? route('pricing') : route('register.customer', ['redirect' => 'pricing']),
                                                'related' => $depth > 0
                                                    ? $designerFavAssets->where('category_id', $asset->category_id)->where('id', '!=', $asset->id)->take(4)->map(fn ($relatedAsset) => $designerAssetDetails($relatedAsset, $depth - 1))->values()->all()
                                                    : [],
                                            ];
                                        };
                                        $designerAssetDetails = $designerAssetDetails($asset, 2);
                                    @endphp

                                    <article class="group relative block w-full overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
                                        <button type="button" x-on:click="selectedAsset = @js($designerAssetDetails)" class="block w-full cursor-pointer text-left">
                                            <div class="overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                                <img src="{{ $asset->preview_url ?: asset('images/placeholder.png') }}" alt="{{ $asset->title ?: $asset->file_name }}" class="block aspect-[4/5] w-full object-cover transition duration-500 group-hover:scale-105" />
                                            </div>
                                        </button>

                                        @if ($asset->is_ai)
                                            <span class="pointer-events-none absolute left-3 top-3 rounded-full bg-[#17383c]/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-[#d7f4f1] shadow-lg backdrop-blur-sm">{{ __('AI') }}</span>
                                        @endif

                                        <button type="button" x-on:click="selectedAsset = @js($designerAssetDetails)" aria-label="{{ __('View :name', ['name' => $asset->file_name]) }}" title="{{ __('View image details') }}" class="absolute right-3 top-3 flex size-9 items-center justify-center rounded-full bg-[#17383c]/85 text-white opacity-0 shadow-lg transition group-hover:opacity-100 focus:opacity-100">
                                            <flux:icon name="eye" variant="mini" />
                                        </button>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="relative mt-10 overflow-hidden rounded-3xl border border-[#32666a] bg-[#17383c] px-6 py-8 text-white shadow-xl shadow-[#17383c]/15 sm:px-10 sm:py-10 lg:px-14 lg:py-12">
                        <div class="pointer-events-none absolute inset-0 opacity-30" style="background-image: linear-gradient(120deg, transparent 0%, rgba(15, 163, 173, 0.18) 48%, transparent 100%), linear-gradient(rgba(143, 227, 226, 0.06) 1px, transparent 1px), linear-gradient(90deg, rgba(143, 227, 226, 0.06) 1px, transparent 1px); background-size: auto, 32px 32px, 32px 32px;"></div>
                        <div class="relative flex flex-col gap-8 md:flex-row md:items-center md:justify-between md:gap-10 lg:gap-12">
                            <div class="max-w-2xl">
                                <div class="flex items-center gap-3 text-sm font-semibold text-[#8fe3e2]">
                                    <span class="flex size-11 shrink-0 items-center justify-center rounded-xl border border-[#5ab8b5]/40 bg-[#0fa3ad] text-white shadow-lg shadow-[#0fa3ad]/20"><flux:icon name="sparkles" variant="mini" /></span>
                                    <span class="uppercase tracking-[0.18em]">{{ __('For independent creators') }}</span>
                                </div>
                                <h2 class="mt-5 max-w-xl text-3xl font-semibold leading-tight tracking-tight sm:text-4xl">{{ __('Start selling your creativity') }}</h2>
                                <p class="mt-4 max-w-xl text-base leading-7 text-[#c5d8d8]">{{ __('Turn your best work into a growing creative business. Share your vision, reach new audiences, and earn from every approved download.') }}</p>
                                <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-3 border-t border-white/15 pt-5 text-xs font-medium text-[#b8cccd]">
                                    <span class="flex items-center gap-2"><flux:icon name="photo" variant="mini" class="text-[#8fe3e2]" />{{ __('Showcase your work') }}</span>
                                    <span class="flex items-center gap-2"><flux:icon name="users" variant="mini" class="text-[#8fe3e2]" />{{ __('Reach new people') }}</span>
                                    <span class="flex items-center gap-2"><flux:icon name="banknotes" variant="mini" class="text-[#8fe3e2]" />{{ __('Earn from downloads') }}</span>
                                </div>
                            </div>
                            <div class="flex w-full shrink-0 flex-col gap-3 border-t border-white/15 pt-6 sm:w-auto sm:items-start sm:border-t-0 sm:pt-0 md:min-w-56 lg:min-w-64">
                                <a href="{{ route('register.contributor') }}" class="inline-flex min-h-11 max-w-full items-center justify-center gap-2 self-start rounded-xl bg-[#0fa3ad] px-5 py-2.5 text-center text-sm font-semibold text-white shadow-lg shadow-[#0fa3ad]/20 transition hover:bg-[#087f85] focus:outline-none focus:ring-2 focus:ring-[#8fe3e2] focus:ring-offset-2 focus:ring-offset-[#17383c]" style="width: fit-content !important;" wire:navigate>
                                    <flux:icon name="arrow-up-tray" variant="mini" />
                                    {{ __('Create contributor account') }}
                                </a>
                                <span class="text-center text-xs text-[#9fb7b8]">{{ __('Upload your work. Grow your audience.') }}</span>
                            </div>
                        </div>
                    </section>

                    @if ($topContributors->isNotEmpty())
                        <section
                            class="flex flex-col gap-8 p-6 rounded-2xl border border-white/70 bg-white/95 p-6 shadow-xl shadow-zinc-900/10 backdrop-blur-sm sm:p-10 dark:border-zinc-700/70 dark:bg-zinc-900/95"
                            x-data="{ active: 0, paused: false, timer: null, total: {{ $topContributors->count() }}, visible() { return window.innerWidth >= 1280 ? 3 : (window.innerWidth >= 640 ? 2 : 1) }, pages() { return Math.max(1, this.total - this.visible() + 1) }, next() { this.active = (this.active + 1) % this.pages() }, previous() { this.active = (this.active - 1 + this.pages()) % this.pages() }, offset() { return window.innerWidth >= 1280 ? 33.333 : (window.innerWidth >= 640 ? 50 : 100) } }"
                            x-init="timer = setInterval(() => { if (!paused) next() }, 5000); return () => clearInterval(timer)"
                            x-on:mouseenter="paused = true"
                            x-on:mouseleave="paused = false"
                            x-on:focusin="paused = true"
                            x-on:focusout="paused = false"
                            style="margin-bottom: 100px !important;"
                        >
                            <div class="flex items-end justify-between gap-4 px-2 sm:px-0">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#087f85]">{{ __('The creative community') }}</p>
                                    <h2 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white sm:text-4xl">{{ __('Top Contributors') }}</h2>
                                    
                                </div>
                                <div class="hidden gap-2 sm:flex">
                                    <button type="button" x-on:click="previous()" class="flex size-10 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-700 shadow-sm transition hover:border-[#0fa3ad] hover:text-[#087f85] focus:outline-none focus:ring-2 focus:ring-[#0fa3ad] dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200" aria-label="{{ __('Previous contributors') }}" title="{{ __('Previous contributors') }}"><flux:icon name="arrow-left" variant="mini" /></button>
                                    <button type="button" x-on:click="next()" class="flex size-10 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-700 shadow-sm transition hover:border-[#0fa3ad] hover:text-[#087f85] focus:outline-none focus:ring-2 focus:ring-[#0fa3ad] dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200" aria-label="{{ __('Next contributors') }}" title="{{ __('Next contributors') }}"><flux:icon name="arrow-right" variant="mini" /></button>
                                </div>
                            </div>

                            <div class="relative px-2 sm:px-0">
                                <div class="overflow-hidden">
                                    <div class="flex transition-transform duration-500 ease-out" :style="`transform: translateX(-${active * offset()}%);`">
                                        @foreach ($topContributors as $contributor)
                                            <article wire:key="top-contributor-{{ $contributor->id }}" class="w-full shrink-0 px-0 sm:px-2 xl:w-1/3">
                                                <div class="overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                                                    <div class="grid grid-cols-2 gap-2 overflow-hidden rounded-xl">
                                                        @foreach ($contributor->products->take(2) as $portfolioAsset)
                                                            <img src="{{ $portfolioAsset->preview_url ?: asset('images/placeholder.png') }}" alt="{{ $portfolioAsset->title ?: $portfolioAsset->file_name }}" class="aspect-4/3 h-full w-full object-cover" />
                                                        @endforeach
                                                    </div>
                                                    <div class="relative flex flex-col items-center pb-2 text-center">
                                                        @if ($contributor->profilePhotoUrl())
                                                            <img src="{{ $contributor->profilePhotoUrl() }}" alt="{{ $contributor->name }}" class="-mt-10 size-20 rounded-full border-4 border-white object-cover shadow-md dark:border-zinc-900" />
                                                        @else
                                                            <span class="-mt-10 flex size-20 items-center justify-center rounded-full border-4 border-white bg-[#d7f4f1] text-xl font-semibold text-[#087f85] shadow-md dark:border-zinc-900">{{ $contributor->initials() }}</span>
                                                        @endif
                                                        <h3 class="mt-5 text-xl font-semibold text-zinc-950 dark:text-white">{{ $contributor->name }}</h3>
                                                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __(':count approved assets', ['count' => $contributor->products_count]) }}</p>
                                                    </div>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="mt-5 flex justify-between sm:hidden">
                                    <button type="button" x-on:click="previous()" class="flex size-10 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-700 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200" aria-label="{{ __('Previous contributors') }}"><flux:icon name="arrow-left" variant="mini" /></button>
                                    <button type="button" x-on:click="next()" class="flex size-10 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-700 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200" aria-label="{{ __('Next contributors') }}"><flux:icon name="arrow-right" variant="mini" /></button>
                                </div>
                            </div>
                        </section>
                    @endif

                    <div
                        x-cloak
                        x-show="selectedAsset"
                        x-on:keydown.escape.window="selectedAsset = null"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4"
                        role="dialog"
                        aria-modal="true"
                        x-on:click.self="selectedAsset = null"
                        style="position: fixed !important; inset: 0 !important; z-index: 50 !important; width: 100vw !important; max-width: none !important; height: 100vh !important; max-height: none !important; margin: 0 !important; padding: 16px !important; overflow-y: auto !important;"
                    >
                        <div
                            class="absolute inset-0 bg-black/75"
                            style="position: absolute !important; inset: 0 !important; z-index: 0 !important; background: rgb(0 0 0 / 75%) !important;"
                            aria-hidden="true"
                        ></div>
                        <div
                            x-show="selectedAsset"
                            x-transition
                            class="relative z-10 max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-2xl bg-white p-5 shadow-2xl dark:bg-zinc-900 sm:p-7"
                            style="position: relative !important; z-index: 10 !important; max-height: calc(100vh - 32px) !important; overflow-y: auto !important;"
                        >
                            <!-- Close -->
                            <div class="flex items-start justify-between gap-4">
                                <button
                                    type="button"
                                    x-on:click="selectedAsset = null"
                                    aria-label="{{ __('Close') }}"
                                    class="ml-auto flex size-9 items-center justify-center rounded-full text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-950 dark:hover:bg-zinc-800 dark:hover:text-white"
                                >
                                    <flux:icon name="x-mark" variant="mini" />
                                </button>
                            </div>

                            <!-- Image + Metadata -->
                            <div class="mt-5 grid grid-cols-1 gap-6 md:grid-cols-2">

                                <!-- Product Image -->
                                <div class="flex min-h-75 items-center justify-center overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                                    <img
                                        :src="selectedAsset?.image"
                                        :alt="selectedAsset?.title"
                                        class="max-h-[55vh] w-full rounded-xl object-contain"
                                    />
                                </div>

                                <!-- Metadata -->
                                <dl class="flex flex-col gap-4 text-sm">

                                    <div>
                                        <h2
                                            class="text-xl font-semibold text-zinc-950 dark:text-white"
                                            x-text="selectedAsset?.title"
                                        ></h2>

                                        <p
                                            class="mt-1 text-sm text-zinc-500"
                                            x-text="selectedAsset?.category || 'Uncategorized'"
                                        ></p>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-zinc-500">
                                            {{ __('Description') }}
                                        </dt>
                                        <dd
                                            class="mt-1 text-zinc-800 dark:text-zinc-200"
                                            x-text="selectedAsset?.description || 'No description provided.'"
                                        ></dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-zinc-500">
                                            {{ __('Asset type') }}
                                        </dt>
                                        <dd
                                            class="mt-1 text-zinc-800 dark:text-zinc-200"
                                            x-text="selectedAsset?.type || 'Not available'"
                                        ></dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-zinc-500">
                                            {{ __('Created with AI') }}
                                        </dt>
                                        <dd
                                            class="mt-1 text-zinc-800 dark:text-zinc-200"
                                            x-text="selectedAsset?.isAi ? 'Yes' : 'No'"
                                        ></dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-zinc-500">
                                            {{ __('Image size') }}
                                        </dt>
                                        <dd
                                            class="mt-1 text-zinc-800 dark:text-zinc-200"
                                            x-text="selectedAsset?.size || 'Not available'"
                                        ></dd>
                                    </div>

                                    <div>
                                        <dt class="text-xs text-zinc-500">
                                            {{ __('Dimensions') }}
                                        </dt>
                                        <dd
                                            class="mt-1 text-zinc-800 dark:text-zinc-200"
                                            x-text="selectedAsset?.dimensions || 'Not available'"
                                        ></dd>
                                    </div>

                                    <a
                                        x-cloak
                                        x-show="selectedAsset?.canDownload"
                                        :href="selectedAsset?.download"
                                        class="mt-auto inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-[#0fa3ad] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#087f85]"
                                        style="cursor: pointer !important;"
                                    >
                                        <flux:icon name="arrow-down-tray" variant="mini" />
                                        {{ __('Download image') }}
                                    </a>
                                    <a
                                        x-cloak
                                        x-show="! selectedAsset?.canDownload"
                                        :href="selectedAsset?.purchase"
                                        class="mt-auto inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-[#0fa3ad] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#087f85]"
                                    >
                                        <flux:icon name="credit-card" variant="mini" />
                                        {{ __('Purchase a plan to download') }}
                                    </a>
                                </dl>
                            </div>

                            <!-- Related Images -->
                            <section
                                x-show="selectedAsset?.related?.length"
                                class="mt-7 border-t border-zinc-200 pt-5 dark:border-zinc-700"
                            >
                                <h3 class="text-sm font-semibold text-zinc-950 dark:text-white">
                                    {{ __('Related images') }}
                                </h3>

                                <div class="mt-3 grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4" style="width: 100% !important; display: grid !important; gap: 12px !important;">
                                    <template
                                        x-for="related in (selectedAsset?.related || [])"
                                        :key="related.id"
                                    >
                                        <button
                                            type="button"
                                            x-on:click="selectedAsset = related"
                                            class="group min-w-0 cursor-pointer overflow-hidden rounded-xl border border-zinc-200 text-left dark:border-zinc-700"
                                        >
                                            <img
                                                :src="related.image"
                                                :alt="related.title"
                                                class="aspect-square w-full object-cover transition duration-300 group-hover:scale-105"
                                            />

                                            <span
                                                class="block truncate px-2 py-2 text-xs font-medium text-zinc-700 dark:text-zinc-200"
                                                x-text="related.title"
                                            ></span>
                                        </button>
                                    </template>
                                </div>
                            </section>
                        </div>
                    </div>
            </section>

            <section id="faq" class="relative isolate mb-32 py-0" x-data="{ openQuestion: 0 }">
                <img
                    src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&q=80&w=1600"
                    alt="{{ __('MacBook Pro, white ceramic mug and black smartphone on table') }}"
                    class="hidden object-cover lg:block"
                    style="height: 400px !important; width: 100% !important; object-fit: cover !important; display: block !important; overflow:hidden !important; border: 1px solid rgba(0, 0, 0, 0.1) !important; border-radius: 15px !important; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important; transition: transform 0.3s ease-in-out !important; transform: scale(1) !important;"
                />

                <div class="absolute mx-auto grid max-w-6xl items-stretch md:grid-cols-[minmax(0,1fr)_280px] lg:grid-cols-[minmax(0,1fr)_336px] " style="top: -100px !important; left: 0 !important; right: 0 !important; bottom: 0 !important; position: absolute !important; align-items: center !important; justify-content: center !important; gap: 28px !important;">
                    <div class="rounded-2xl border border-white/70 bg-white/95 p-6 shadow-xl shadow-zinc-900/10 backdrop-blur-sm sm:p-10 dark:border-zinc-700/70 dark:bg-zinc-900/95">
                        <div class="flex flex-col gap-8">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#087f85] dark:text-[#8fe3e2]">{{ __('Need to know') }}</p>
                                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-950 sm:text-4xl dark:text-white">{{ __('FAQ') }}</h2>
                            </div>

                            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach ([
                                    __('What is Creativ Stock?') => __('Creativ Stock is a focused marketplace for thoughtful images, illustrations, vectors, and templates made by independent creators.'),
                                    __('How do I download free photos?') => __('Create a customer account and choose a plan to access downloads from the growing collection.'),
                                    __('Can I use these pictures for another online platform?') => __('Yes. You can use downloaded assets in your online projects according to the usage terms for your plan.'),
                                    __('How do contributors earn money?') => __('Contributors earn a share when customers download their approved assets through an active subscription.'),
                                    __('Can I cancel my subscription?') => __('You can manage your subscription from your customer account and cancel future renewals at any time.'),
                                ] as $question => $answer)
                                    @php($questionIndex = $loop->index)
                                    <div>
                                        <button
                                            type="button"
                                            x-on:click="openQuestion = openQuestion === {{ $questionIndex }} ? null : {{ $questionIndex }}"
                                            class="flex w-full items-center justify-between gap-4 py-5 text-left text-base font-medium text-zinc-950 transition hover:text-[#087f85] dark:text-white dark:hover:text-[#8fe3e2]"
                                            :aria-expanded="openQuestion === {{ $questionIndex }}"
                                            aria-controls="faq-answer-{{ $questionIndex }}"
                                        >
                                            <span>{{ $question }}</span>
                                            <span class="flex size-7 shrink-0 items-center justify-center rounded-full border border-zinc-300 text-sm dark:border-zinc-600" x-text="openQuestion === {{ $questionIndex }} ? '-' : '+'" aria-hidden="true"></span>
                                        </button>
                                        <div id="faq-answer-{{ $questionIndex }}" x-show="openQuestion === {{ $questionIndex }}" x-collapse>
                                            <p class="max-w-2xl pb-5 pr-10 text-sm leading-7 text-zinc-500 dark:text-zinc-400">{{ $answer }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="hidden h-[500px] overflow-hidden rounded-2xl shadow-xl shadow-zinc-900/15 md:block">
                        <img
                            src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?q=80&w=900&auto=format&fit=crop"
                            alt="{{ __('Young girl with colorful paint') }}"
                            class="size-full object-cover"
                        />
                    </div>
                </div>
            </section>

            <section class="relative mx-0 mb-10 overflow-hidden rounded-3xl border border-[#b8e4e0] bg-[#d7f4f1] px-6 py-10 text-[#17383c] shadow-sm sm:px-10 sm:py-12 lg:px-16 lg:py-14 dark:border-[#376164] dark:bg-[#174448] dark:text-white">
                <div class="pointer-events-none absolute -right-20 -top-20 size-64 rounded-full border-32 border-white/25 dark:border-[#8fe3e2]/10"></div>
                <div class="pointer-events-none absolute -bottom-24 left-1/3 size-56 rounded-full border-24 border-[#0fa3ad]/10"></div>
                <div class="relative flex flex-col items-start gap-7 md:flex-row md:items-center md:justify-between md:gap-12">
                    <div class="max-w-2xl">
                        <div class="flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.18em] text-[#087f85] dark:text-[#8fe3e2]">
                            <flux:icon name="sparkles" variant="mini" />
                            <span>{{ __('Go Premium') }}</span>
                        </div>
                        <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-tight sm:text-4xl">{{ __('Choose our pricing plan') }}</h2>
                        <p class="mt-3 max-w-xl text-base leading-7 text-[#376164] dark:text-[#c5d8d8]">{{ __('Unlock more downloads, bring your team together, and give every idea room to grow.') }}</p>
                    </div>
                    <a href="{{ route('pricing') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#17383c] px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-[#17383c]/15 transition hover:bg-[#0fa3ad] focus:outline-none focus:ring-2 focus:ring-[#087f85] focus:ring-offset-2 focus:ring-offset-[#d7f4f1] dark:bg-[#0fa3ad] dark:hover:bg-[#087f85] dark:focus:ring-[#8fe3e2] dark:focus:ring-offset-[#174448]" wire:navigate>
                        {{ __('View pricing Plans') }}
                        <flux:icon name="arrow-right" variant="mini" />
                    </a>
                </div>
            </section>


            <button
                id="back-to-top"
                type="button"
                class="fixed right-5 z-50 hidden size-11 items-center justify-center rounded-full bg-[#0fa3ad] text-white shadow-lg shadow-[#0fa3ad]/25 transition hover:bg-[#087f85] md:right-8"
                aria-label="{{ __('Back to top') }}"
                title="{{ __('Back to top') }}"
            >
                <flux:icon name="arrow-up" variant="mini" />
            </button>

            <script>
                (() => {
                    const button = document.getElementById('back-to-top');
                    const footer = document.getElementById('site-footer');

                    if (!button || !footer) {
                        return;
                    }

                    const checkButton = () => {
                        const scrollY = window.scrollY || window.pageYOffset;
                        const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                        const footerDistance = footer.getBoundingClientRect().top - viewportHeight;

                        const shouldShow = scrollY > viewportHeight && footerDistance > 110;
                        button.style.display = shouldShow ? 'flex' : 'none';
                        button.style.bottom = footerDistance < 110 ? `${Math.max(20, 110 - footerDistance)}px` : '20px';
                    };

                    button.addEventListener('click', () => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });

                    window.addEventListener('scroll', checkButton, { passive: true });
                    window.addEventListener('resize', checkButton);
                    checkButton();
                })();
            </script>

            <footer id="site-footer" class="relative mt-6 overflow-hidden border-t border-zinc-200 bg-white text-zinc-800 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-200">
                <div class="mx-auto grid max-w-7xl gap-12 px-5 py-12 sm:px-8 lg:grid-cols-[1.5fr_repeat(3,1fr)] lg:px-10 lg:py-16">
                    <div class="max-w-sm">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3" wire:navigate>
                           <img src="{{ asset('images/brand/logo.png') }}" alt="{{ config('app.name', 'Creativ Stock') }}" class="object-contain" style="width: 200px !important;" />
                        </a>
                        <p class="mt-6 max-w-xs text-base leading-8 text-zinc-500 dark:text-zinc-400">{{ __('Everything you need to bring your next creative project to life.') }}</p>
                        <div class="mt-7 flex items-center gap-3">
                            <a href="{{ route('home') }}" aria-label="{{ __('Explore Creativ Stock') }}" title="{{ __('Explore Creativ Stock') }}" class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 transition hover:bg-[#d7f4f1] hover:text-[#087f85] dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-[#0fa3ad]/20 dark:hover:text-[#8fe3e2]"><flux:icon name="globe-alt" variant="mini" /></a>
                            <a href="mailto:hello@example.com" aria-label="{{ __('Contact support') }}" title="{{ __('Contact support') }}" class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 transition hover:bg-[#d7f4f1] hover:text-[#087f85] dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-[#0fa3ad]/20 dark:hover:text-[#8fe3e2]"><flux:icon name="envelope" variant="mini" /></a>
                            <a href="{{ route('register.contributor') }}" aria-label="{{ __('Become a contributor') }}" title="{{ __('Become a contributor') }}" class="flex size-10 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 transition hover:bg-[#d7f4f1] hover:text-[#087f85] dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-[#0fa3ad]/20 dark:hover:text-[#8fe3e2]"><flux:icon name="arrow-up-tray" variant="mini" /></a>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-sm font-bold text-zinc-950 dark:text-white">{{ __('Content') }}</h2>
                        <nav class="mt-5 flex flex-col gap-4 text-sm text-zinc-500 dark:text-zinc-400">
                            <a href="{{ route('home') }}" class="transition hover:text-[#087f85]" wire:navigate>{{ __('Home') }}</a>
                            <a href="{{ route('home') }}#recent-images" class="transition hover:text-[#087f85]" wire:navigate>{{ __('Images') }}</a>
                            <a href="{{ route('home') }}#designers-fav" class="transition hover:text-[#087f85]" wire:navigate>{{ __('Vectors') }}</a>
                            <a href="{{ route('home') }}#designers-fav" class="transition hover:text-[#087f85]" wire:navigate>{{ __('Illustrations') }}</a>
                            <a href="{{ route('home') }}#designers-fav" class="transition hover:text-[#087f85]" wire:navigate>{{ __('Templates') }}</a>
                        </nav>
                    </div>

                    <div>
                        <h2 class="text-sm font-bold text-zinc-950 dark:text-white">{{ __('Support') }}</h2>
                        <nav class="mt-5 flex flex-col gap-4 text-sm text-zinc-500 dark:text-zinc-400">
                            <a href="mailto:hello@example.com" class="transition hover:text-[#087f85]">{{ __('Get help') }}</a>
                            <a href="mailto:hello@example.com" class="transition hover:text-[#087f85]">{{ __('Accessibility') }}</a>
                        </nav>
                        <h2 class="mt-8 text-sm font-bold text-zinc-950 dark:text-white">{{ __('Legal') }}</h2>
                        <nav class="mt-5 flex flex-col gap-4 text-sm text-zinc-500 dark:text-zinc-400">
                            <a href="mailto:hello@example.com?subject=Terms%20of%20Use" class="transition hover:text-[#087f85]">{{ __('Terms of Use') }}</a>
                            <a href="mailto:hello@example.com?subject=Privacy%20Policy" class="transition hover:text-[#087f85]">{{ __('Privacy Policy') }}</a>
                        </nav>
                    </div>

                    <div>
                        <h2 class="text-sm font-bold text-zinc-950 dark:text-white">{{ __('Our Company') }}</h2>
                        <nav class="mt-5 flex flex-col gap-4 text-sm text-zinc-500 dark:text-zinc-400">
                            <a href="{{ route('home') }}" class="transition hover:text-[#087f85]" wire:navigate>{{ __('About') }}</a>
                            <a href="{{ route('register.contributor') }}" class="transition hover:text-[#087f85]" wire:navigate>{{ __('Careers') }}</a>
                            <a href="{{ route('home') }}" class="transition hover:text-[#087f85]" wire:navigate>{{ __('Blog') }}</a>
                        </nav>
                        <h2 class="mt-8 text-sm font-bold text-zinc-950 dark:text-white">{{ __('For contributors') }}</h2>
                        <a href="{{ route('register.contributor') }}" class="mt-5 inline-flex text-sm text-zinc-500 transition hover:text-[#087f85] dark:text-zinc-400" wire:navigate>{{ __('Sell your content') }}</a>
                        <a href="{{ route('pricing') }}" class="mt-8 inline-flex items-center justify-center rounded-xl bg-[#0fa3ad] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#087f85]" wire:navigate>{{ __('Buy Now') }}</a>
                    </div>
                </div>

                <div class="bg-black text-white">
                    <div class="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-5 text-sm sm:px-8 md:flex-row md:items-center md:justify-between lg:px-10">
                        <p class="text-zinc-300">{{ config('app.name', 'Creativ Stock') }} © {{ now()->year }}. {{ __('All rights reserved.') }}</p>
                        <div class="flex flex-wrap items-center gap-3 text-zinc-300">
                            <span>{{ __('We accept') }}</span>
                            <span class="rounded bg-white px-2 py-1 text-xs font-black italic text-[#1a4ca0]">VISA</span>
                            <span class="rounded bg-white px-2 py-1 text-xs font-black text-[#e21d3a]">MasterCard</span>
                            <span class="rounded bg-white px-2 py-1 text-xs font-black italic text-[#1976b9]">AMEX</span>
                            <span class="rounded bg-white px-2 py-1 text-xs font-black italic text-[#1769aa]">PayPal</span>
                        </div>
                    </div>
                </div>
            </footer>
            
        </main>
        @fluxScripts
    </body>
</html>
