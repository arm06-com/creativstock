<div class="flex flex-col gap-6">
    @if ($status === \App\Models\Product::STATUS_UPLOADED)
        <section
        class="relative flex min-h-64 cursor-pointer flex-col items-center justify-center gap-4 rounded-2xl border-2 border-dashed border-[#9edbd7] bg-[#f7fcfb] px-6 py-12 text-center transition hover:border-[#0fa3ad] dark:border-[#286d70] dark:bg-[#102d31]/60 dark:hover:border-[#8fe3e2]"
        x-data="{ isDragging: false }"
        x-on:dragenter.prevent="isDragging = true"
        x-on:dragover.prevent="isDragging = true"
        x-on:dragleave.prevent="isDragging = false"
        x-on:drop.prevent="isDragging = false"
        x-bind:class="isDragging ? 'border-[#0fa3ad] bg-[#e7f7f5] dark:border-[#8fe3e2] dark:bg-[#0fa3ad]/15' : ''"
    >
        <input id="asset-upload" type="file" wire:model="uploads" multiple accept="image/jpeg,image/png,image/webp,image/gif" class="absolute inset-0 cursor-pointer opacity-0" />
        <span class="flex size-14 items-center justify-center rounded-2xl bg-[#d7f4f1] text-[#087f85] dark:bg-[#0fa3ad]/20 dark:text-[#8fe3e2]"><flux:icon name="cloud-arrow-up" /></span>
        <div class="flex flex-col gap-1 border-2 border-lime-100 dark:border-lime-900">
            <label for="asset-upload" class="cursor-pointer text-sm font-semibold text-zinc-900 dark:text-white">{{ __('Drop images here or browse your computer') }}</label>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Upload up to 50 JPG, PNG, WEBP, or GIF images. Maximum 50 MB each.') }}</p>
        </div>
        <div wire:loading wire:target="uploads" class="flex items-center gap-2 text-xs font-medium text-[#087f85] dark:text-[#8fe3e2]">
            <flux:icon name="arrow-path" class="animate-spin" variant="mini" />
            <span>{{ __('Uploading images...') }}</span>
        </div>
        </section>

        @error('uploads.*')
            <flux:callout variant="danger" icon="exclamation-triangle" :heading="$message" />
        @enderror
    @endif

    @if ($assets->isNotEmpty())
        <section class="flex flex-col gap-4">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-base font-semibold text-zinc-950 dark:text-white">{{ __('Uploaded images') }}</h2>
                <div class="flex items-center gap-4">
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $assets->count() }} {{ __('images') }}</span>
                    @if ($completedAssetCount > 0)
                        <flux:button wire:click="submitAssets" wire:confirm="{{ __('Submit all completed assets for review?') }}" icon="paper-airplane" variant="primary">
                            {{ __('Submit (:count)', ['count' => $completedAssetCount]) }}
                        </flux:button>
                    @endif
                </div>
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start;" >
                @foreach ($assets as $asset)
                    <article wire:key="asset-{{ $asset->id }}" style=" width: 250px !important; min-width: 250px !important; max-width: 250px !important; flex: 0 0 250px !important; box-sizing: border-box; overflow: hidden; " class="group rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <div wire:click="openAsset({{ $asset->id }})" role="button" tabindex="0" aria-label="{{ __('View metadata for :name', ['name' => $asset->file_name]) }}" style=" position: relative; width: 250px !important; min-width: 250px !important; max-width: 250px !important; height: 250px !important; min-height: 250px !important; max-height: 250px !important; overflow: hidden; " class="cursor-pointer bg-zinc-100 dark:bg-zinc-800">
                            <img src="{{ $asset->preview_url }}" alt="{{ $asset->file_name }}" style=" display: block !important; width: 250px !important; min-width: 250px !important; max-width: 250px !important; height: 250px !important; min-height: 250px !important; max-height: 250px !important; object-fit: cover; " />
                            @if ($status === \App\Models\Product::STATUS_UPLOADED)
                                <button type="button" wire:click.stop="deleteAsset({{ $asset->id }})" wire:confirm="{{ __('Delete this uploaded image?') }}" aria-label="{{ __('Delete :name', ['name' => $asset->file_name]) }}" title="{{ __('Delete image') }}" style=" position: absolute !important; top: 8px !important; right: 8px !important; z-index: 10 !important; display: flex !important; width: 24px !important; height: 24px !important; align-items: center !important; justify-content: center !important; border: 0 !important; border-radius: 9999px !important; background: #e11d48 !important; color: #ffffff !important; cursor: pointer !important; box-shadow: 0 4px 12px rgb(0 0 0 / 35%) !important;">
                                    <flux:icon name="x-mark" variant="mini" style="width: 16px !important; height: 16px !important; color: #ffffff !important;" />
                                </button>
                            @endif
                        </div>
                        @if ($status === \App\Models\Product::STATUS_UPLOADED)
                            <div style=" width: 250px; height: 40px; box-sizing: border-box; " class="flex items-center justify-between gap-2 px-3">
                            <p class="truncate text-xs font-medium text-zinc-700 dark:text-zinc-200" title="{{ $asset->file_name }}">{{ $asset->file_name }}</p>
                            <span class="size-2 shrink-0 rounded-full {{ $asset->hasCompleteMetadata() ? 'bg-emerald-500' : 'bg-rose-500' }}" title="{{ $asset->hasCompleteMetadata() ? __('Metadata completed') : __('Metadata incomplete') }}"></span>
                            </div>
                        @elseif ($status === \App\Models\Product::STATUS_REJECTED)
                            <div class="flex flex-col gap-1 px-3 py-2">
                                <p class="truncate text-xs font-medium text-zinc-700 dark:text-zinc-200" title="{{ $asset->file_name }}">{{ $asset->file_name }}</p>
                                <p class="text-xs text-rose-600 dark:text-rose-400" title="{{ $asset->rejection_reason }}">{{ __('Reason: :reason', ['reason' => $asset->rejection_reason ?: __('Not provided')]) }}</p>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <flux:modal wire:model="showMetadataModal" class="w-full max-w-2xl" @close="closeMetadataModal">
        <form wire:submit="saveMetadata" class="flex flex-col gap-6">
            <div class="flex flex-col gap-1">
                <flux:heading size="lg">{{ __('Asset metadata') }}</flux:heading>
                <flux:text>{{ __('Complete the details before submitting this asset for review.') }}</flux:text>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Asset type') }}</flux:label>
                    @if ($metadataReadOnly)
                        <flux:text>{{ $assetType ?: __('Not available') }}</flux:text>
                    @else
                    <flux:select wire:model="assetType">
                        <option value="">{{ __('Select asset type') }}</option>
                        @foreach (['Image', 'Vector', 'Illustration', 'Icon', 'Template'] as $type)
                            <option value="{{ $type }}">{{ __($type) }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="assetType" />
                    @endif
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Category') }}</flux:label>
                    @if ($metadataReadOnly)
                        <flux:text>{{ $categories->firstWhere('id', $categoryId)?->name ?: __('Not available') }}</flux:text>
                    @else
                    <flux:select wire:model="categoryId">
                        <option value="">{{ __('Select category') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="categoryId" />
                    @endif
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('Created with AI') }}</flux:label>
                @if ($metadataReadOnly)
                    <flux:text>{{ $isAi === '1' ? __('Yes') : __('No') }}</flux:text>
                @else
                <flux:select wire:model="isAi">
                    <option value="0">{{ __('No') }}</option>
                    <option value="1">{{ __('Yes') }}</option>
                </flux:select>
                <flux:error name="isAi" />
                @endif
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Title') }}</flux:label>
                @if ($metadataReadOnly)<flux:text>{{ $title ?: __('Not available') }}</flux:text>@else<flux:input wire:model="title" />@endif
                <flux:error name="title" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Short description') }}</flux:label>
                @if ($metadataReadOnly)<flux:text>{{ $description ?: __('Not available') }}</flux:text>@else<flux:textarea wire:model="description" rows="3" />@endif
                <flux:error name="description" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Tags') }}</flux:label>
                @if ($metadataReadOnly)<flux:text>{{ $tags ?: __('Not available') }}</flux:text>@else<flux:input wire:model="tags" placeholder="{{ __('nature, landscape, travel') }}" />@endif
                <flux:description>{{ __('Separate tags with commas. Maximum 40 tags.') }}</flux:description>
                <flux:error name="tags" />
            </flux:field>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Image size') }}</flux:label>
                    <flux:text class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">{{ $imageSize ?: __('Not available') }}</flux:text>
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Dimensions') }}</flux:label>
                    <flux:text class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">{{ $dimensions ?: __('Not available') }}</flux:text>
                </flux:field>
            </div>

            @if ($metadataReadOnly && $status === \App\Models\Product::STATUS_REJECTED && $editingAssetId)
                @php($rejectedAsset = $assets->firstWhere('id', $editingAssetId))
                @if ($rejectedAsset)
                    <div class="flex flex-col gap-2">
                        <flux:label>{{ __('Rejection reason') }}</flux:label>
                        <flux:text class="text-rose-600 dark:text-rose-400">{{ $rejectedAsset->rejection_reason ?: __('Not provided') }}</flux:text>
                    </div>
                @endif
            @endif

            <div class="flex justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="closeMetadataModal">{{ __('Close') }}</flux:button>
                @if (! $metadataReadOnly)
                    <flux:button type="submit" variant="primary">{{ $metadataComplete ? __('Update') : __('Save') }}</flux:button>
                @endif
            </div>
        </form>
    </flux:modal>
</div>
