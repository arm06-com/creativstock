<x-layouts::app :title="__('My Assets')">
    <div class="min-h-full bg-[#f5f8f8] px-4 py-5 dark:bg-zinc-950 sm:px-6 lg:px-8">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-[#087f85]">{{ __('Customer account') }}</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ __('My Assets') }}</h1>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Assets you have downloaded are collected here.') }}</p>
                </div>
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#0fa3ad] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#087f85]" wire:navigate>
                    <flux:icon name="magnifying-glass" variant="mini" />
                    {{ __('Explore more') }}
                </a>
            </div>

            @if ($assets->isEmpty())
                <section class="rounded-2xl border border-dashed border-[#9edfd9] bg-white p-10 text-center shadow-sm dark:border-[#376164] dark:bg-zinc-900">
                    <flux:icon name="photo" class="mx-auto size-10 text-[#0fa3ad]" />
                    <h2 class="mt-4 text-lg font-semibold text-zinc-950 dark:text-white">{{ __('No downloaded assets yet') }}</h2>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Explore the marketplace and download your first asset.') }}</p>
                    <a href="{{ route('home') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-[#087f85] hover:underline" wire:navigate>{{ __('Explore more') }} <flux:icon name="arrow-right" variant="mini" /></a>
                </section>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($assets as $asset)
                        <article wire:key="customer-asset-{{ $asset->id }}" class="overflow-hidden rounded-2xl border border-zinc-200/80 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                            <img src="{{ $asset->preview_url ?: asset('images/placeholder.png') }}" alt="{{ $asset->title ?: $asset->file_name }}" class="aspect-square w-full object-cover" />
                            <div class="flex flex-col gap-1 p-4">
                                <h2 class="truncate text-sm font-semibold text-zinc-950 dark:text-white">{{ $asset->title ?: $asset->file_name }}</h2>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $asset->category?->name ?: __('Creative asset') }}</p>
                                <a href="{{ route('assets.download', $asset) }}" class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-[#087f85] hover:underline">
                                    <flux:icon name="arrow-down-tray" variant="mini" /> {{ __('Download again') }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
