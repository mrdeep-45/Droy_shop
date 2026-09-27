<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Customer Addresses ──────────────────────────────────────
        Schema::create('tbl_shop_addresses', function (Blueprint $table) {
            $table->increments('address_id');
            $table->unsignedInteger('customer_id');
            $table->string('full_name');
            $table->string('phone', 15);
            $table->string('alternate_phone', 15)->nullable();
            $table->text('address_line1');
            $table->text('address_line2')->nullable();
            $table->string('landmark')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('country', 100)->default('India');
            $table->string('pincode', 10);
            $table->enum('address_type', ['home', 'work', 'other'])->default('home');
            $table->tinyInteger('is_default')->default(0);
            $table->timestamps();
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade');
        });

        // ─── Wishlist ────────────────────────────────────────────────
        Schema::create('tbl_shop_wishlist', function (Blueprint $table) {
            $table->increments('wishlist_id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('product_variant_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('customer_id')->references('customer_id')->on('shop_customers')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('tbl_shop_products')->onDelete('cascade');
            $table->unique(['customer_id', 'product_id', 'product_variant_id'], 'wishlist_unique');
        });

        // ─── Cart ────────────────────────────────────────────────────
        Schema::create('tbl_shop_cart', function (Blueprint $table) {
            $table->increments('cart_id');
            $table->unsignedInteger('customer_id')->nullable()->comment('NULL = guest cart');
            $table->string('session_id', 100)->nullable()->comment('For guest carts');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('product_variant_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0)->comment('Price snapshot at add-to-cart');
            $table->timestamps();
            $table->foreign('product_id')->references('product_id')->on('tbl_shop_products')->onDelete('cascade');
        });

        // ─── Coupons & Offers ────────────────────────────────────────
        Schema::create('tbl_shop_coupons', function (Blueprint $table) {
            $table->increments('coupon_id');
            $table->string('coupon_code', 30)->unique();
            $table->string('coupon_name');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_order_amount', 12, 2)->default(0);
            $table->decimal('max_discount_amount', 12, 2)->nullable()
                  ->comment('Cap for percent type');
            $table->integer('usage_limit')->nullable()->comment('NULL = unlimited');
            $table->integer('per_user_limit')->default(1);
            $table->integer('used_count')->default(0);
            $table->enum('applicable_to', ['all', 'category', 'product', 'vendor'])->default('all');
            $table->unsignedInteger('applicable_id')->nullable()
                  ->comment('category_id / product_id / vendor_id');
            $table->tinyInteger('is_first_order_only')->default(0);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });

        // ─── Coupon Usage Log ────────────────────────────────────────
        Schema::create('tbl_shop_coupon_usage', function (Blueprint $table) {
            $table->increments('usage_id');
            $table->unsignedInteger('coupon_id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('order_id')->nullable();
            $table->decimal('discount_applied', 12, 2)->default(0);
            $table->timestamp('used_at')->useCurrent();
            $table->foreign('coupon_id')->references('coupon_id')->on('tbl_shop_coupons')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_shop_coupon_usage');
        Schema::dropIfExists('tbl_shop_coupons');
        Schema::dropIfExists('tbl_shop_cart');
        Schema::dropIfExists('tbl_shop_wishlist');
        Schema::dropIfExists('tbl_shop_addresses');
    }
};
