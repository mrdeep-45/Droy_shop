<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Returns & Refunds ───────────────────────────────────────
        Schema::create('tbl_shop_returns', function (Blueprint $table) {
            $table->increments('return_id');
            $table->string('return_number', 30)->unique();
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('order_item_id')->nullable();
            $table->unsignedInteger('customer_id');
            $table->string('reason');
            $table->text('description')->nullable();
            $table->json('return_images')->nullable();
            $table->enum('return_type', ['refund', 'exchange', 'store_credit'])->default('refund');
            $table->enum('return_status', ['requested', 'approved', 'rejected', 'pickup_scheduled', 'picked', 'processed'])
                  ->default('requested');
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->enum('refund_method', ['original', 'wallet', 'bank'])->nullable();
            $table->string('refund_transaction_id')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->integer('handled_by')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
            $table->foreign('order_id')->references('order_id')->on('tbl_shop_orders')->onDelete('cascade');
            $table->foreign('customer_id')->references('customer_id')->on('shop_customers')->onDelete('cascade');
        });

        // ─── Product Reviews & Ratings ───────────────────────────────
        Schema::create('tbl_shop_reviews', function (Blueprint $table) {
            $table->increments('review_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedInteger('order_item_id')->nullable();
            $table->tinyInteger('rating')->unsigned(); // 1–5
            $table->string('review_title')->nullable();
            $table->text('review_body')->nullable();
            $table->json('review_images')->nullable();
            $table->tinyInteger('is_verified_purchase')->default(0);
            $table->tinyInteger('is_approved')->default(0);
            $table->integer('helpful_count')->default(0);
            $table->text('admin_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
            $table->foreign('product_id')->references('product_id')->on('tbl_shop_products')->onDelete('cascade');
            $table->foreign('customer_id')->references('customer_id')->on('shop_customers')->onDelete('cascade');
        });

        // ─── Notifications ───────────────────────────────────────────
        Schema::create('tbl_shop_notifications', function (Blueprint $table) {
            $table->increments('notification_id');
            $table->enum('user_type', ['customer', 'vendor', 'delivery', 'admin'])->default('customer');
            $table->unsignedInteger('user_id');
            $table->string('title');
            $table->text('message');
            $table->string('type', 50)->nullable()->comment('order_placed, order_shipped, etc.');
            $table->string('icon')->nullable();
            $table->string('color', 20)->nullable();
            $table->json('data')->nullable()->comment('Extra payload e.g. order_id');
            $table->string('link')->nullable();
            $table->tinyInteger('is_read')->default(0);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // ─── Vendor Earnings ─────────────────────────────────────────
        Schema::create('tbl_shop_vendor_earnings', function (Blueprint $table) {
            $table->increments('earning_id');
            $table->unsignedInteger('vendor_id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('order_item_id');
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('commission_percent', 5, 2)->default(10.00);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('net_earning', 12, 2)->default(0);
            $table->enum('earning_status', ['pending', 'processing', 'paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->foreign('vendor_id')->references('vendor_id')->on('shop_vendors')->onDelete('cascade');
            $table->foreign('order_id')->references('order_id')->on('tbl_shop_orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_shop_vendor_earnings');
        Schema::dropIfExists('tbl_shop_notifications');
        Schema::dropIfExists('tbl_shop_reviews');
        Schema::dropIfExists('tbl_shop_returns');
    }
};
