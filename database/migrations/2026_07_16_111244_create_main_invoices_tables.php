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
        Schema::create('tbl_main_invoice', function (Blueprint $table) {
            $table->id('main_invoice_id');
            $table->string('invoice_no', 255)->unique();
            $table->string('invoice_type', 50)->default('domestic');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('quotation_id')->nullable();
            $table->unsignedBigInteger('performa_invoice_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->date('invoice_date');
            $table->decimal('advance_payment', 15, 2)->default(0.00);
            $table->decimal('balance_payment', 15, 2)->default(0.00);
            $table->text('terms_payment')->nullable();
            $table->string('transportation', 255)->nullable();
            $table->string('dispatch_time', 255)->nullable();
            $table->text('commercial_terms')->nullable();
            $table->tinyInteger('gst_exemption')->default(1); // 1 = NOT EXEMPTED, 0 = EXEMPTED
            $table->tinyInteger('include_gst')->default(1);
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('city_id');
            $table->string('currency', 10)->default('INR');
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('discount_total', 15, 2)->default(0.00);
            $table->decimal('gst_total', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2)->default(0.00);
            $table->decimal('grand_total_converted', 15, 2)->default(0.00);
            $table->tinyInteger('status')->default(0); // 0 = Active, 1 = Inactive
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('tbl_main_invoice_item', function (Blueprint $table) {
            $table->id('mii_id');
            $table->unsignedBigInteger('main_invoice_id');
            $table->unsignedBigInteger('product_id');
            $table->text('description')->nullable();
            $table->decimal('qty', 15, 4)->default(0.0000);
            $table->decimal('unit_price', 15, 2)->default(0.00);
            $table->decimal('unit_price_converted', 15, 2)->default(0.00);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('discount_price', 15, 2)->default(0.00);
            $table->decimal('gst_percent', 5, 2)->default(0.00);
            $table->decimal('gst_amount', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2)->default(0.00);
            $table->decimal('total_converted', 15, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::table('tbl_performa_invoice', function (Blueprint $table) {
            $table->tinyInteger('converted_to_main')->default(0)->after('status');
        });

        Schema::table('tbl_quotation', function (Blueprint $table) {
            $table->tinyInteger('converted_to_main')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_main_invoice_item');
        Schema::dropIfExists('tbl_main_invoice');

        Schema::table('tbl_performa_invoice', function (Blueprint $table) {
            $table->dropColumn('converted_to_main');
        });

        Schema::table('tbl_quotation', function (Blueprint $table) {
            $table->dropColumn('converted_to_main');
        });
    }
};
