<x-layouts::app :title="__('My Assets')">
    <div class="min-h-full bg-[#f5f8f8] px-4 py-5 dark:bg-zinc-950 sm:px-6 lg:px-8">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6">
            <header class="flex flex-col gap-2">
                <p class="text-sm font-medium text-[#087f85] dark:text-[#8fe3e2]">{{ __('Contributor workspace') }}</p>
                <h1 class="text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ __('My Assets') }}</h1>
                <p class="max-w-2xl text-sm leading-6 text-zinc-500 dark:text-zinc-400">{{ __('Track your uploads and review progress in one place.') }}</p>
            </header>

            <nav class="overflow-x-auto border-b border-zinc-200 dark:border-zinc-800" aria-label="{{ __('Asset status') }}">
                <div class="flex min-w-max gap-6">
                    @foreach ([
                        'uploaded' => __('Uploaded'),
                        'pending' => __('Pending'),
                        'approved' => __('Approved'),
                        'rejected' => __('Rejected'),
                    ] as $status => $label)
                        <a href="{{ route('contributor.assets.index', ['status' => $status]) }}" wire:navigate class="border-b-2 px-1 pb-3 text-sm font-semibold {{ $activeStatus === $status ? 'border-[#0fa3ad] text-[#087f85] dark:text-[#8fe3e2]' : 'border-transparent text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:text-zinc-400 dark:hover:border-zinc-600 dark:hover:text-zinc-200' }}">
                            {{ $label }} ({{ $counts[$status] }})
                        </a>
                    @endforeach
                </div>
            </nav>

            <livewire:contributor.asset-upload :status="$activeStatus" :key="'asset-gallery-'.$activeStatus" />
        </div>
    </div>
</x-layouts::app>