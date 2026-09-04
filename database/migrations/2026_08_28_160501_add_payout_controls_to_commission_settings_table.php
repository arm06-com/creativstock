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
        Schema::table('commission_settings', function (Blueprint $table) {
            $table->decimal('minimum_payout', 10, 2)->default(20)->after('percentage');
            $table->json('payment_methods')->nullable()->after('minimum_payout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commission_settings', function (Blueprint $table) {
            $table->dropColumn(['minimum_payout', 'payment_methods']);
        });
    }
};
