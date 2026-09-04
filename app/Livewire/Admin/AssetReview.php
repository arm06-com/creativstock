<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Component;

class AssetReview extends Component
{
    public string $status = Product::STATUS_PENDING;

    public bool $showReviewModal = false;

    public ?int $selectedAssetId = null;

    public string $price = '';

    public string $rejectionReason = '';

    public function openAsset(int $assetId): void
    {
        $asset = $this->assetForReview($assetId);

        $this->selectedAssetId = $asset->id;
        $this->price = $asset->price !== null ? (string) $asset->price : '';
        $this->rejectionReason = $asset->rejection_reason ?? '';
        $this->showReviewModal = true;
    }

    public function approveAsset(): void
    {
        abort_unless($this->status === Product::STATUS_PENDING, 403);

        $this->validate([
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $this->assetForReview($this->selectedAssetId)->update([
            'status' => Product::STATUS_APPROVED,
            'price' => $this->price,
            'rejection_reason' => null,
        ]);

        $this->closeReviewModal();
    }

    public function rejectAsset(): void
    {
        abort_unless(in_array($this->status, [Product::STATUS_PENDING, Product::STATUS_APPROVED], true), 403);

        $this->validate([
            'rejectionReason' => ['required', 'string', 'max:2000'],
        ]);

        $this->assetForReview($this->selectedAssetId)->update([
            'status' => Product::STATUS_REJECTED,
            'rejection_reason' => $this->rejectionReason,
            'price' => null,
        ]);

        $this->closeReviewModal();
    }

    public function deleteApprovedAsset(): void
    {
        abort_unless($this->status === Product::STATUS_APPROVED, 403);

        $asset = $this->assetForReview($this->selectedAssetId);

        if ($asset->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($asset->file_path);
        }

        $asset->delete();

        $this->closeReviewModal();
    }

    public function closeReviewModal(): void
    {
        $this->reset(['showReviewModal', 'selectedAssetId', 'price', 'rejectionReason']);
        $this->resetValidation();
    }

    private function assetForReview(?int $assetId): Product
    {
        return Product::query()
            ->with(['user', 'category'])
            ->where('id', $assetId)
            ->where('status', $this->status)
            ->firstOrFail();
    }

    public function render(): mixed
    {
        abort_unless(auth()->user()?->can('manage-products'), 403);

        return view('livewire.admin.asset-review', [
            'assets' => Product::query()
                ->with(['user', 'category'])
                ->where('status', $this->status)
                ->latest()
                ->get(),
        ]);
    }
}
