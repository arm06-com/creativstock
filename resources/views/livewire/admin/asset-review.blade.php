<div class="flex flex-col gap-6">
    @if ($assets->isEmpty())
        <section class="flex min-h-64 flex-col items-center justify-center gap-3 rounded-2xl border border-zinc-200/80 bg-white px-6 py-12 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <span class="flex size-12 items-center justify-center rounded-2xl bg-[#e7f7f5] text-[#0fa3ad] dark:bg-[#0fa3ad]/15 dark:text-[#8fe3e2]"><flux:icon name="photo" /></span>
            <h2 class="text-sm font-semibold text-zinc-900 dark:text-white">{{ __('No :status assets', ['status' => $status]) }}</h2>
        </section>
    @else
        <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start;">
            @foreach ($assets as $asset)
                <article wire:key="admin-asset-{{ $asset->id }}" style="width: 250px !important; min-width: 250px !important; max-width: 250px !important; flex: 0 0 250px !important; box-sizing: border-box; overflow: hidden;" class="group rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <button type="button" wire:click="openAsset({{ $asset->id }})" aria-label="{{ __('View metadata for :name', ['name' => $asset->file_name]) }}" class="block cursor-pointer text-left">
                        <div style="width: 250px !important; min-width: 250px !important; max-width: 250px !important; height: 250px !important; min-height: 250px !important; max-height: 250px !important; overflow: hidden;" class="bg-zinc-100 dark:bg-zinc-800">
                            <img src="{{ $asset->preview_url ?: asset('images/placeholder.png') }}" alt="{{ $asset->file_name }}" style="display: block !important; width: 250px !important; min-width: 250px !important; max-width: 250px !important; height: 250px !important; min-height: 250px !important; max-height: 250px !important; object-fit: cover;" />
                        </div>
                        <div style="width: 250px; height: 40px; box-sizing: border-box;" class="flex items-center gap-2 px-3">
                            <p class="truncate text-xs font-medium text-zinc-700 dark:text-zinc-200" title="{{ $asset->file_name }}">{{ $asset->file_name }}</p>
                        </div>
                    </button>
                </article>
            @endforeach
        </div>
    @endif

    <flux:modal wire:model="showReviewModal" class="w-full max-w-2xl" @close="closeReviewModal">
        @if ($selectedAssetId)
            @php($selectedAsset = $assets->firstWhere('id', $selectedAssetId))
            @if ($selectedAsset)
                <div class="flex flex-col gap-6">
                    <div class="grid gap-5 sm:grid-cols-[180px_1fr]">
                        <div class="flex flex-col gap-3">
                            <img src="{{ $selectedAsset->preview_url ?: asset('images/placeholder.png') }}" alt="{{ $selectedAsset->file_name }}" class="aspect-square w-full rounded-xl object-cover" />
                            @if ($status === \App\Models\Product::STATUS_PENDING)
                                <a
                                    href="{{ $selectedAsset->preview_url ?: asset('images/placeholder.png') }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-[#0fa3ad] bg-[#e7f7f5] px-3 py-2 text-sm font-medium text-[#087f85] transition hover:bg-[#d9f3f0] dark:border-[#5ab8b5] dark:bg-[#0fa3ad]/15 dark:text-[#8fe3e2]"
                                >
                                    <flux:icon name="arrow-top-right-on-square" variant="mini" />
                                    {{ __('View full image') }}
                                </a>
                            @endif
                        </div>
                        <div class="flex flex-col gap-3">
                            <flux:heading size="lg">{{ $selectedAsset->title ?: $selectedAsset->file_name }}</flux:heading>
                            <dl class="grid gap-2 text-sm">
                                <div class="flex justify-between gap-4"><dt class="text-zinc-500">{{ __('Contributor') }}</dt><dd class="font-medium text-zinc-900 dark:text-white">{{ $selectedAsset->user->name }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-zinc-500">{{ __('Type') }}</dt><dd class="font-medium text-zinc-900 dark:text-white">{{ $selectedAsset->asset_type ?: __('Not available') }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-zinc-500">{{ __('Category') }}</dt><dd class="font-medium text-zinc-900 dark:text-white">{{ $selectedAsset->category?->name ?: __('Uncategorized') }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-zinc-500">{{ __('Created with AI') }}</dt><dd class="font-medium text-zinc-900 dark:text-white">{{ $selectedAsset->is_ai ? __('Yes') : __('No') }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-zinc-500">{{ __('Size') }}</dt><dd class="font-medium text-zinc-900 dark:text-white">{{ $selectedAsset->image_size ?: __('Not available') }}</dd></div>
                                <div class="flex justify-between gap-4"><dt class="text-zinc-500">{{ __('Dimensions') }}</dt><dd class="font-medium text-zinc-900 dark:text-white">{{ $selectedAsset->dimensions ?: __('Not available') }}</dd></div>
                            </dl>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2"><flux:label>{{ __('Description') }}</flux:label><flux:text>{{ $selectedAsset->description ?: __('No description provided.') }}</flux:text></div>
                    <div class="flex flex-col gap-2"><flux:label>{{ __('Tags') }}</flux:label><flux:text>{{ implode(', ', $selectedAsset->tags ?? []) ?: __('No tags provided.') }}</flux:text></div>

                    @if ($status === \App\Models\Product::STATUS_PENDING)
                        <div class="grid gap-4 sm:grid-cols-2">
                            <flux:field><flux:label>{{ __('Price') }}</flux:label><flux:input type="number" min="0" step="0.01" wire:model="price" placeholder="0.00" /><flux:error name="price" /></flux:field>
                            <flux:field><flux:label>{{ __('Rejection reason') }}</flux:label><flux:textarea wire:model="rejectionReason" rows="2" placeholder="{{ __('Required only when rejecting') }}" /><flux:error name="rejectionReason" /></flux:field>
                        </div>
                        <div class="flex justify-end gap-3">
                            <flux:button type="button" variant="ghost" wire:click="closeReviewModal">{{ __('Cancel') }}</flux:button>
                            <flux:button type="button" variant="danger" wire:click="rejectAsset">{{ __('Reject') }}</flux:button>
                            <flux:button type="button" variant="primary" wire:click="approveAsset">{{ __('Approve') }}</flux:button>
                        </div>
                    @else
                        @if ($status === \App\Models\Product::STATUS_APPROVED)
                            <flux:text>{{ __('Approved price: :price', ['price' => $selectedAsset->price]) }}</flux:text>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <flux:field>
                                    <flux:label>{{ __('Rejection reason') }}</flux:label>
                                    <flux:textarea wire:model="rejectionReason" rows="2" placeholder="{{ __('Required only when rejecting') }}" />
                                    <flux:error name="rejectionReason" />
                                </flux:field>
                                <div class="flex flex-col justify-end gap-3">
                                    <flux:button type="button" variant="danger" wire:click="deleteApprovedAsset" wire:confirm="{{ __('Delete this approved asset permanently?') }}">
                                        {{ __('Delete') }}
                                    </flux:button>
                                    <flux:button type="button" variant="primary" wire:click="rejectAsset" wire:confirm="{{ __('Reject this approved asset?') }}">
                                        {{ __('Reject') }}
                                    </flux:button>
                                </div>
                            </div>
                        @else
                            <flux:callout variant="danger" :heading="__('Rejection reason')" :text="$selectedAsset->rejection_reason ?: __('No reason provided.')" />
                        @endif
                        <div class="flex justify-end"><flux:button type="button" variant="ghost" wire:click="closeReviewModal">{{ __('Close') }}</flux:button></div>
                    @endif
                </div>
            @endif
        @endif
    </flux:modal>
</div>
