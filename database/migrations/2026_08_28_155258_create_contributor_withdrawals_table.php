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
        Schema::create('contributor_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contributor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained('contributor_payment_methods')->restrictOnDelete();
            $table->decimal('amount', 10, 4);
            $table->string('status')->default('pending')->index();
            $table->text('admin_note')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['contributor_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributor_withdrawals');
    }
};
