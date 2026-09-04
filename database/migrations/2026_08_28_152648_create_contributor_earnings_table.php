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
        Schema::create('contributor_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contributor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('asset_download_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('plan_price', 10, 2);
            $table->unsignedInteger('plan_download_limit');
            $table->decimal('commission_percentage', 5, 2);
            $table->decimal('amount', 10, 4);
            $table->timestamps();

            $table->index(['contributor_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributor_earnings');
    }
};
