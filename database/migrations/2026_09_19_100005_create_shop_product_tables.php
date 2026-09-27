<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Products ────────────────────────────────────────────────
        Schema::create('tbl_shop_products', function (Blueprint $table) {
            $table->increments('product_id');
            $table->string('product_code', 50)->nullable()->unique();
            $table->string('product_name');
            $table->string('product_slug')->nullable()->unique();
            $table->unsignedInteger('brand_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('shop_unit_id')->nullable();
            $table->unsignedInteger('vendor_id')->nullable()->comment('NULL = own product');
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->decimal('base_price', 12, 2)->default(0)->comment('MRP');
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('cost_price', 12, 2)->default(0)->comment('Purchase cost');
            $table->string('hsn_code', 20)->nullable();
            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->integer('stock_qty')->default(0)->comment('Total/default stock');
            $table->integer('min_order_qty')->default(1);
            $table->integer('max_order_qty')->nullable();
            $table->integer('low_stock_threshold')->default(10);
            $table->string('weight', 20)->nullable()->comment('e.g., 500g, 1kg');
            $table->string('dimensions', 50)->nullable()->comment('LxWxH in cm');
            $table->string('sku', 100)->nullable()->unique();
            $table->tinyInteger('has_variants')->default(0); // 0=no variants, 1=has variants
            $table->tinyInteger('is_featured')->default(0);
            $table->tinyInteger('is_new_arrival')->default(0);
            $table->tinyInteger('is_bestseller')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->integer('view_count')->default(0);
            $table->tinyInteger('status')->default(0); // 0=active, 1=deleted
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('brand_id')->references('brand_id')->on('mst_shop_brands')->onDelete('set null');
            $table->foreign('category_id')->references('category_id')->on('mst_shop_categories')->onDelete('set null');
            $table->foreign('shop_unit_id')->references('shop_unit_id')->on('mst_shop_units')->onDelete('set null');
            $table->foreign('vendor_id')->references('vendor_id')->on('shop_vendors')->onDelete('set null');
        });

        // ─── Product Images ──────────────────────────────────────────
        Schema::create('tbl_shop_product_images', function (Blueprint $table) {
            $table->increments('image_id');
            $table->unsignedInteger('product_id');
            $table->string('image_path');
            $table->string('image_alt')->nullable();
            $table->tinyInteger('is_primary')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('tbl_shop_products')
                  ->onDelete('cascade');
        });

        // ─── Product Variants (e.g. Red/XL, Blue/M) ─────────────────
        Schema::create('tbl_shop_product_variants', function (Blueprint $table) {
            $table->increments('product_variant_id');
            $table->unsignedInteger('product_id');
            $table->string('sku', 100)->nullable()->unique();
            $table->json('variant_combination')->nullable()
                  ->comment('e.g. {"Size":"XL","Color":"Red"}');
            $table->string('variant_label')->nullable()
                  ->comment('Human readable e.g. XL / Red');
            $table->decimal('price_override', 12, 2)->nullable()
                  ->comment('NULL = use product selling_price');
            $table->decimal('base_price_override', 12, 2)->nullable();
            $table->decimal('cost_price_override', 12, 2)->nullable();
            $table->integer('stock_qty')->default(0);
            $table->string('image_path')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('tbl_shop_products')
                  ->onDelete('cascade');
        });

        // ─── Inventory Ledger ────────────────────────────────────────
        Schema::create('tbl_shop_inventory', function (Blueprint $table) {
            $table->increments('inventory_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('product_variant_id')->nullable();
            $table->enum('transaction_type', [
                'opening', 'purchase', 'sale', 'return', 'adjustment', 'damage', 'transfer'
            ])->default('purchase');
            $table->decimal('quantity', 12, 3);
            $table->decimal('before_stock', 12, 3)->default(0);
            $table->decimal('after_stock', 12, 3)->default(0);
            $table->string('reference_type')->nullable()->comment('Order, Purchase, Adjustment...');
            $table->unsignedInteger('reference_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('tbl_shop_products')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_shop_inventory');
        Schema::dropIfExists('tbl_shop_product_variants');
        Schema::dropIfExists('tbl_shop_product_images');
        Schema::dropIfExists('tbl_shop_products');
    }
};
