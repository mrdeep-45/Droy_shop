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
            $table->string('buyer_order_no', 100)->nullable()->after('quotation_id');
            $table->date('buyer_order_date')->nullable()->after('buyer_order_no');
            $table->string('destination', 255)->nullable()->after('transportation');
            $table->text('attachments')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_performa_invoice', function (Blueprint $table) {
            $table->dropColumn(['buyer_order_no', 'buyer_order_date', 'destination', 'attachments']);
        });
    }
};
