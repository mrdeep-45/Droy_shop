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
            $table->string('delivery_note', 255)->nullable()->after('dispatch_time');
            $table->string('po_reference', 255)->nullable()->after('buyer_order_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_performa_invoice', function (Blueprint $table) {
            $table->dropColumn(['delivery_note', 'po_reference']);
        });
    }
};
