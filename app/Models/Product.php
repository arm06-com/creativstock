<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    public const STATUS_UPLOADED = 'uploaded';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'category_id',
        'asset_type',
        'title',
        'description',
        'tags',
        'is_ai',
        'price',
        'rejection_reason',
        'status',
        'file_path',
        'file_name',
        'mime_type',
        'image_size',
        'dimensions',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_ai' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(AssetDownload::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(ContributorEarning::class);
    }

    public function hasCompleteMetadata(): bool
    {
        return filled($this->asset_type)
            && filled($this->category_id)
            && filled($this->title)
            && filled($this->description)
            && filled($this->tags);
    }

    public function getPreviewUrlAttribute(): string
    {
        $path = ltrim((string) ($this->file_path ?? ''), '/');

        if (blank($path)) {
            return '';
        }

        return url('storage/'.$path);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_UPLOADED => __('Uploaded'),
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_APPROVED => __('Approved'),
            self::STATUS_REJECTED => __('Rejected'),
            default => __('Uploaded'),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_UPLOADED => 'bg-slate-100 text-slate-700',
            self::STATUS_PENDING => 'bg-amber-100 text-amber-700',
            self::STATUS_APPROVED => 'bg-emerald-100 text-emerald-700',
            self::STATUS_REJECTED => 'bg-rose-100 text-rose-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }
}
