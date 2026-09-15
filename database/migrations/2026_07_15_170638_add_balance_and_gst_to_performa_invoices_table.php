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
        Schema::table('tbl_performa_invoice', function (Blueprint $table) {
            $table->tinyInteger('include_gst')->default(0)->after('gst_exemption');
            $table->decimal('balance_payment_percentage', 5, 2)->default(0.00)->after('advance_payment');
            $table->decimal('balance_payment', 15, 2)->default(0.00)->after('balance_payment_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_performa_invoice', function (Blueprint $table) {
            $table->dropColumn(['include_gst', 'balance_payment_percentage', 'balance_payment']);
        });
    }
};
