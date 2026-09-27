<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Orders (Header) ─────────────────────────────────────────
        Schema::create('tbl_shop_orders', function (Blueprint $table) {
            $table->increments('order_id');
            $table->string('order_number', 30)->unique();
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('address_id')->nullable()->comment('Snapshot stored in JSON too');
            $table->json('shipping_address')->nullable()->comment('Snapshot of address at order time');
            $table->unsignedInteger('coupon_id')->nullable();
            $table->string('coupon_code', 30)->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('coupon_discount', 12, 2)->default(0);
            $table->decimal('shipping_charge', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('payment_method', ['cod', 'razorpay', 'stripe', 'upi', 'wallet'])->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded', 'partial_refund'])
                  ->default('pending');
            $table->string('payment_transaction_id')->nullable();
            $table->enum('order_status', [
                'pending', 'confirmed', 'processing', 'ready_to_ship',
                'shipped', 'out_for_delivery', 'delivered', 'cancelled',
                'return_requested', 'returned'
            ])->default('pending');
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('expected_delivery_date')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->tinyInteger('invoice_generated')->default(0);
            $table->string('invoice_number')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->foreign('customer_id')
                  ->references('customer_id')
                  ->on('shop_customers')
                  ->onDelete('cascade');
        });

        // ─── Order Items ─────────────────────────────────────────────
        Schema::create('tbl_shop_order_items', function (Blueprint $table) {
            $table->increments('order_item_id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('product_variant_id')->nullable();
            $table->unsignedInteger('vendor_id')->nullable();
            $table->string('product_name');
            $table->string('product_code')->nullable();
            $table->string('product_image')->nullable();
            $table->json('variant_details')->nullable()->comment('{"Size":"XL","Color":"Red"}');
            $table->string('variant_label')->nullable();
            $table->string('sku')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('base_price', 12, 2)->comment('MRP snapshot');
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->decimal('gst_amount', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);
            $table->enum('item_status', [
                'pending', 'confirmed', 'shipped', 'delivered', 'cancelled', 'returned'
            ])->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('tbl_shop_orders')
                  ->onDelete('cascade');
        });

        // ─── Order Tracking / Status Log ────────────────────────────
        Schema::create('tbl_shop_order_tracking', function (Blueprint $table) {
            $table->increments('tracking_id');
            $table->unsignedInteger('order_id');
            $table->string('status');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('courier_name')->nullable();
            $table->integer('created_by')->nullable();
            $table->string('created_by_type')->nullable()->comment('admin, vendor, delivery, system');
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('tbl_shop_orders')
                  ->onDelete('cascade');
        });

        // ─── Delivery Assignments ────────────────────────────────────
        Schema::create('tbl_shop_delivery_assignments', function (Blueprint $table) {
            $table->increments('assignment_id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('delivery_staff_id');
            $table->integer('assigned_by')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->string('delivery_otp', 10)->nullable();
            $table->timestamp('otp_verified_at')->nullable();
            $table->enum('delivery_status', ['assigned', 'picked', 'out_for_delivery', 'delivered', 'failed'])
                  ->default('assigned');
            $table->text('failure_reason')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->decimal('delivery_latitude', 10, 8)->nullable();
            $table->decimal('delivery_longitude', 11, 8)->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('order_id')->on('tbl_shop_orders')->onDelete('cascade');
            $table->foreign('delivery_staff_id')->references('delivery_staff_id')->on('shop_delivery_staff')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_shop_delivery_assignments');
        Schema::dropIfExists('tbl_shop_order_tracking');
        Schema::dropIfExists('tbl_shop_order_items');
        Schema::dropIfExists('tbl_shop_orders');
    }
};
