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
        Schema::table('asset_downloads', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('user_id')->constrained('users')->cascadeOnDelete();
            $table->unique(['customer_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_downloads', function (Blueprint $table) {
            $table->dropUnique(['customer_id', 'product_id']);
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
