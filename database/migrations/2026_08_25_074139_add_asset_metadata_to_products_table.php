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
        Schema::table('products', function (Blueprint $table) {
            $table->string('asset_type')->nullable()->after('category_id');
            $table->string('image_size')->nullable()->after('mime_type');
            $table->string('dimensions')->nullable()->after('image_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['asset_type', 'image_size', 'dimensions']);
        });
    }
};
