<?php

namespace App\Livewire\Contributor;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class AssetUpload extends Component
{
    use WithFileUploads;

    public array $uploads = [];

    public string $status = Product::STATUS_UPLOADED;

    public bool $showMetadataModal = false;

    public bool $metadataComplete = false;

    public bool $metadataReadOnly = false;

    public ?int $editingAssetId = null;

    public string $assetType = '';

    public ?int $categoryId = null;

    public string $isAi = '0';

    public string $title = '';

    public string $description = '';

    public string $tags = '';

    public string $imageSize = '';

    public string $dimensions = '';

    public function updatedUploads(): void
    {
        if ($this->status !== Product::STATUS_UPLOADED) {
            return;
        }

        if ($this->uploads === []) {
            return;
        }

        $this->validate([
            'uploads' => ['array', 'min:1', 'max:50'],
            'uploads.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:51200'],
        ]);

        foreach ($this->uploads as $upload) {
            $path = $upload->store('contributors/'.auth()->id(), 'public');
            $imageInfo = getimagesize($upload->getRealPath());

            Product::create([
                'user_id' => auth()->id(),
                'status' => Product::STATUS_UPLOADED,
                'file_path' => $path,
                'file_name' => $upload->getClientOriginalName(),
                'mime_type' => $upload->getMimeType(),
                'image_size' => $this->formatFileSize($upload->getSize()),
                'dimensions' => $imageInfo ? $imageInfo[0].'x'.$imageInfo[1] : null,
            ]);
        }

        $this->reset('uploads');
        $this->dispatch('assets-uploaded');
    }

    public function openAsset(int $assetId): void
    {
        $asset = $this->ownedAsset($assetId);

        $this->editingAssetId = $asset->id;
        $this->metadataComplete = $asset->hasCompleteMetadata();
        $this->metadataReadOnly = $this->status !== Product::STATUS_UPLOADED;
        $this->assetType = $asset->asset_type ?? '';
        $this->categoryId = $asset->category_id;
        $this->isAi = $asset->is_ai ? '1' : '0';
        $this->title = $asset->title ?? '';
        $this->description = $asset->description ?? '';
        $this->tags = implode(', ', $asset->tags ?? []);
        $this->imageSize = $asset->image_size ?? '';
        $this->dimensions = $asset->dimensions ?? '';
        $this->showMetadataModal = true;
    }

    public function editAsset(int $assetId): void
    {
        $this->openAsset($assetId);
    }

    public function saveMetadata(): void
    {
        if ($this->status !== Product::STATUS_UPLOADED) {
            return;
        }

        $this->validate([
            'assetType' => ['required', 'in:Image,Vector,Illustration,Icon,Template'],
            'categoryId' => ['required', 'integer', 'exists:categories,id'],
            'isAi' => ['boolean'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'tags' => ['required', 'string'],
        ]);

        $tagValues = collect(explode(',', $this->tags))
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->unique()
            ->values();

        if ($tagValues->count() > 40) {
            $this->addError('tags', __('You may add up to 40 tags.'));

            return;
        }

        $this->ownedUploadedAsset($this->editingAssetId)->update([
            'asset_type' => $this->assetType,
            'category_id' => $this->categoryId,
            'is_ai' => $this->isAi === '1',
            'title' => $this->title,
            'description' => $this->description,
            'tags' => $tagValues->all(),
        ]);

        $this->closeMetadataModal();
    }

    public function closeMetadataModal(): void
    {
        $this->reset([
            'showMetadataModal',
            'metadataReadOnly',
            'metadataComplete',
            'editingAssetId',
            'assetType',
            'categoryId',
            'isAi',
            'title',
            'description',
            'tags',
            'imageSize',
            'dimensions',
        ]);
        $this->resetValidation();
    }

    public function deleteAsset(int $assetId): void
    {
        if ($this->status !== Product::STATUS_UPLOADED) {
            return;
        }

        $asset = $this->ownedUploadedAsset($assetId);

        Storage::disk('public')->delete($asset->file_path);
        $asset->delete();
    }

    public function submitAssets(): void
    {
        $assets = Product::query()
            ->where('user_id', auth()->id())
            ->where('status', Product::STATUS_UPLOADED)
            ->get()
            ->filter(fn (Product $asset): bool => $asset->hasCompleteMetadata());

        $assets->each(fn (Product $asset): bool => $asset->update([
            'status' => Product::STATUS_PENDING,
        ]));
    }

    private function ownedUploadedAsset(?int $assetId): Product
    {
        return $this->ownedAsset($assetId);
    }

    private function ownedAsset(?int $assetId): Product
    {
        return Product::query()
            ->where('id', $assetId)
            ->where('user_id', auth()->id())
            ->where('status', $this->status)
            ->firstOrFail();
    }

    private function formatFileSize(int|float $bytes): string
    {
        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 2).' MB';
        }

        return number_format($bytes / 1024, 2).' KB';
    }

    public function render()
    {
        abort_unless(auth()->user()?->isContributor(), 403);

        $assets = Product::query()
            ->where('user_id', auth()->id())
            ->where('status', $this->status)
            ->latest()
            ->get();

        return view('livewire.contributor.asset-upload', [
            'categories' => Category::query()->orderBy('name')->get(),
            'assets' => $assets,
            'completedAssetCount' => $this->status === Product::STATUS_UPLOADED
                ? $assets->filter(fn (Product $asset): bool => $asset->hasCompleteMetadata())->count()
                : 0,
        ]);
    }
}
