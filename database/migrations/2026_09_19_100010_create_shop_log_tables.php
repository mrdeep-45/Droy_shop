<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Shop Activity Logs (Audit Trail) ────────────────────────
        Schema::create('tbl_shop_activity_logs', function (Blueprint $table) {
            $table->increments('log_id');
            $table->enum('user_type', ['admin', 'customer', 'vendor', 'delivery', 'system'])->default('admin');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('action', 100)->comment('created, updated, deleted, login, logout...');
            $table->string('model_type', 100)->nullable()->comment('ShopProduct, ShopOrder...');
            $table->unsignedInteger('model_id')->nullable();
            $table->string('model_label')->nullable()->comment('Human readable e.g. Order #ORD-001');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('route_name')->nullable();
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_type', 'user_id']);
            $table->index(['model_type', 'model_id']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_shop_activity_logs');
    }
};
