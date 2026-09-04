<x-layouts::app :title="__('Assets')">
    <div class="min-h-full bg-[#f5f8f8] px-4 py-5 dark:bg-zinc-950 sm:px-6 lg:px-8">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6">
            <header class="flex flex-col gap-2">
                <p class="text-sm font-medium text-[#087f85] dark:text-[#8fe3e2]">{{ __('Asset management') }}</p>
                <h1 class="text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ __('Assets') }}</h1>
                <p class="max-w-2xl text-sm leading-6 text-zinc-500 dark:text-zinc-400">{{ __('Review contributor submissions and manage marketplace status.') }}</p>
            </header>

            <nav class="overflow-x-auto border-b border-zinc-200 dark:border-zinc-800" aria-label="{{ __('Asset status') }}">
                <div class="flex min-w-max gap-6">
                    @foreach ([
                        'pending' => __('Pending'),
                        'approved' => __('Approved'),
                        'rejected' => __('Rejected'),
                    ] as $assetStatus => $label)
                        <a href="{{ route('admin.assets.index', ['status' => $assetStatus]) }}" wire:navigate class="border-b-2 px-1 pb-3 text-sm font-semibold {{ $status === $assetStatus ? 'border-[#0fa3ad] text-[#087f85] dark:text-[#8fe3e2]' : 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:border-zinc-600 dark:hover:text-zinc-200' }}">
                            {{ $label }} ({{ $counts[$assetStatus] }})
                        </a>
                    @endforeach
                </div>
            </nav>

            <livewire:admin.asset-review :status="$status" :key="'admin-asset-review-'.$status" />
        </div>
    </div>
</x-layouts::app>
