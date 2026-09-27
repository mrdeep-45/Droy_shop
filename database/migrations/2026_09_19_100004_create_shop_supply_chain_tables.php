<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Suppliers ──────────────────────────────────────────────
        Schema::create('tbl_shop_suppliers', function (Blueprint $table) {
            $table->increments('supplier_id');
            $table->string('supplier_code', 30)->nullable()->unique();
            $table->string('supplier_name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('alternate_phone', 15)->nullable();
            $table->string('gst_number', 20)->nullable();
            $table->string('pan_number', 15)->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 100)->nullable()->default('India');
            $table->string('pincode', 10)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account', 30)->nullable();
            $table->string('ifsc_code', 15)->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });

        // ─── Raw Materials (Garment-specific) ────────────────────────
        Schema::create('tbl_shop_raw_materials', function (Blueprint $table) {
            $table->increments('raw_material_id');
            $table->string('material_code', 30)->nullable()->unique();
            $table->string('material_name');
            $table->unsignedInteger('shop_unit_id')->nullable();
            $table->unsignedInteger('supplier_id')->nullable();
            $table->decimal('current_stock', 12, 3)->default(0);
            $table->decimal('min_stock_level', 12, 3)->default(0);
            $table->decimal('max_stock_level', 12, 3)->nullable();
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->foreign('shop_unit_id')
                  ->references('shop_unit_id')
                  ->on('mst_shop_units')
                  ->onDelete('set null');
            $table->foreign('supplier_id')
                  ->references('supplier_id')
                  ->on('tbl_shop_suppliers')
                  ->onDelete('set null');
        });

        // ─── Purchase Orders ─────────────────────────────────────────
        Schema::create('tbl_shop_purchases', function (Blueprint $table) {
            $table->increments('purchase_id');
            $table->string('purchase_number', 30)->unique();
            $table->unsignedInteger('supplier_id');
            $table->date('purchase_date');
            $table->date('expected_delivery_date')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('shipping_charge', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            $table->enum('purchase_status', ['draft', 'ordered', 'received', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->foreign('supplier_id')
                  ->references('supplier_id')
                  ->on('tbl_shop_suppliers')
                  ->onDelete('cascade');
        });

        // ─── Purchase Order Items ────────────────────────────────────
        Schema::create('tbl_shop_purchase_items', function (Blueprint $table) {
            $table->increments('purchase_item_id');
            $table->unsignedInteger('purchase_id');
            $table->unsignedInteger('product_id')->nullable();
            $table->unsignedInteger('raw_material_id')->nullable();
            $table->unsignedInteger('product_variant_id')->nullable();
            $table->string('item_name')->nullable(); // snapshot of name at time of purchase
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);
            $table->decimal('received_qty', 12, 3)->default(0);
            $table->timestamps();
            $table->foreign('purchase_id')
                  ->references('purchase_id')
                  ->on('tbl_shop_purchases')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_shop_purchase_items');
        Schema::dropIfExists('tbl_shop_purchases');
        Schema::dropIfExists('tbl_shop_raw_materials');
        Schema::dropIfExists('tbl_shop_suppliers');
    }
};
