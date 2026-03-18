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
        Schema::table('disputes', function (Blueprint $table) {
            $table->decimal('dispute_amount', 10, 2)->nullable()->after('reason');
            $table->boolean('is_refunded')->default(false)->after('resolved_by');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('is_refunded');
            $table->string('refund_transaction_id')->nullable()->after('refund_amount');
            $table->timestamp('refunded_at')->nullable()->after('refund_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disputes', function (Blueprint $table) {
            $table->dropColumn([
                'dispute_amount',
                'is_refunded',
                'refund_amount',
                'refund_transaction_id',
                'refunded_at'
            ]);
        });
    }
};
