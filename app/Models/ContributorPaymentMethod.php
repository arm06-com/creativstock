<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ContributorPaymentMethod extends Model
{
    protected $fillable = ['contributor_id', 'method', 'account_name', 'account_details', 'is_default'];

    protected function casts(): array
    {
        return ['account_details' => 'encrypted', 'is_default' => 'boolean'];
    }

    public function contributor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contributor_id');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(ContributorWithdrawal::class, 'payment_method_id');
    }
}
