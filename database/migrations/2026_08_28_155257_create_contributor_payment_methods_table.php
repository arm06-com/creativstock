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
        Schema::create('contributor_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contributor_id')->constrained('users')->cascadeOnDelete();
            $table->string('method');
            $table->string('account_name');
            $table->text('account_details');
            $table->boolean('is_default')->default(true);
            $table->timestamps();

            $table->index(['contributor_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributor_payment_methods');
    }
};
