<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ContributorEarning extends Model
{
    protected $fillable = [
        'contributor_id',
        'asset_download_id',
        'product_id',
        'plan_price',
        'plan_download_limit',
        'commission_percentage',
        'amount',
    ];

    protected $casts = [
        'plan_price' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'amount' => 'decimal:4',
    ];

    public function contributor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contributor_id');
    }

    public function download(): BelongsTo
    {
        return $this->belongsTo(AssetDownload::class, 'asset_download_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
