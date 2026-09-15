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
            if (!Schema::hasColumn('tbl_performa_invoice', 'discount_total')) {
                $table->decimal('discount_total', 15, 2)->default(0.00)->after('subtotal');
            }
        });

        Schema::table('tbl_performa_invoice_item', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_performa_invoice_item', 'discount_percentage')) {
                $table->decimal('discount_percentage', 15, 2)->default(0.00)->after('unit_price_converted');
            }
            if (!Schema::hasColumn('tbl_performa_invoice_item', 'discount_price')) {
                $table->decimal('discount_price', 15, 2)->default(0.00)->after('discount_percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_performa_invoice', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_performa_invoice', 'discount_total')) {
                $table->dropColumn('discount_total');
            }
        });

        Schema::table('tbl_performa_invoice_item', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_performa_invoice_item', 'discount_percentage')) {
                $table->dropColumn('discount_percentage');
            }
            if (Schema::hasColumn('tbl_performa_invoice_item', 'discount_price')) {
                $table->dropColumn('discount_price');
            }
        });
    }
};
