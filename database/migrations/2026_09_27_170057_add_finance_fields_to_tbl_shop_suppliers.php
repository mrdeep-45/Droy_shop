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
        Schema::table('tbl_shop_suppliers', function (Blueprint $table) {
            $table->string('payment_terms', 100)->nullable()->after('ifsc_code');
            $table->integer('credit_days')->default(0)->after('payment_terms');
            $table->decimal('credit_limit', 12, 2)->default(0)->after('credit_days');
            $table->decimal('opening_balance', 12, 2)->default(0)->after('credit_limit');
            $table->tinyInteger('is_active')->default(1)->after('opening_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_shop_suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'payment_terms',
                'credit_days',
                'credit_limit',
                'opening_balance',
                'is_active',
            ]);
        });
    }
};
