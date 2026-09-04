<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    protected $fillable = ['percentage', 'minimum_payout', 'payment_methods'];

    protected $attributes = [
        'percentage' => 50,
        'minimum_payout' => 20,
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'minimum_payout' => 'decimal:2',
        'payment_methods' => 'array',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate(
            ['id' => 1],
            ['payment_methods' => ['paypal', 'bank_transfer']],
        );
    }
}
