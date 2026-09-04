<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('paddle_environment')->nullable()->after('favicon_path');
            $table->text('paddle_api_key')->nullable()->after('paddle_environment');
            $table->text('paddle_client_token')->nullable()->after('paddle_api_key');
            $table->text('paddle_webhook_secret')->nullable()->after('paddle_client_token');
            $table->string('paddle_test_price_id')->nullable()->after('paddle_webhook_secret');
            $table->boolean('paddle_verify_ssl')->nullable()->default(true)->after('paddle_test_price_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'paddle_environment',
                'paddle_api_key',
                'paddle_client_token',
                'paddle_webhook_secret',
                'paddle_test_price_id',
                'paddle_verify_ssl',
            ]);
        });
    }
};
