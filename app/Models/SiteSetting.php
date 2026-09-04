<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_title',
        'meta_description',
        'logo_path',
        'favicon_path',
        'paddle_environment',
        'paddle_api_key',
        'paddle_client_token',
        'paddle_webhook_secret',
        'paddle_test_price_id',
        'paddle_verify_ssl',
    ];

    public static function current(): self
    {
        return self::query()->firstOrCreate(
            ['id' => 1],
            [
                'site_title' => config('app.name', 'Creativ Stock'),
                'meta_description' => config('app.meta_description', 'Marketplace for creative assets.'),
                'paddle_environment' => config('paddle.environment', 'sandbox'),
                'paddle_verify_ssl' => config('paddle.verify_ssl', true),
            ],
        );
    }
}
