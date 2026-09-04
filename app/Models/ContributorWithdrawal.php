<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ContributorWithdrawal extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PAID = 'paid';

    protected $fillable = ['contributor_id', 'payment_method_id', 'amount', 'status', 'admin_note', 'requested_at', 'processed_at'];

    protected $casts = ['amount' => 'decimal:4', 'requested_at' => 'datetime', 'processed_at' => 'datetime'];

    public function contributor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contributor_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(ContributorPaymentMethod::class, 'payment_method_id');
    }
}
