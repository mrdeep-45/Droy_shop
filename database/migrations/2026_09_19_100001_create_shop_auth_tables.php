<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Shop Super Admin / Admin ────────────────────────────────
        Schema::create('shop_admin', function (Blueprint $table) {
            $table->increments('shop_admin_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('admin_type', ['super_admin', 'admin'])->default('admin');
            $table->string('profile_image')->nullable();
            $table->string('phone', 15)->nullable();
            $table->tinyInteger('status')->default(0); // 0=active, 1=deleted
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });

        // ─── Customers ──────────────────────────────────────────────
        Schema::create('shop_customers', function (Blueprint $table) {
            $table->increments('customer_id');
            $table->string('customer_code', 20)->nullable()->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 15)->nullable();
            $table->string('password');
            $table->string('profile_image')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 100)->nullable()->default('India');
            $table->string('pincode', 10)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('dob')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('otp', 10)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->string('referral_code', 20)->nullable();
            $table->string('referred_by', 20)->nullable();
            $table->tinyInteger('is_email_verified')->default(0);
            $table->tinyInteger('status')->default(0); // 0=active, 1=inactive/deleted
            $table->timestamps();
        });

        // ─── Vendors / Sellers ──────────────────────────────────────
        Schema::create('shop_vendors', function (Blueprint $table) {
            $table->increments('vendor_id');
            $table->string('vendor_code', 20)->nullable()->unique();
            $table->string('business_name');
            $table->string('owner_name');
            $table->string('email')->unique();
            $table->string('phone', 15)->nullable();
            $table->string('password');
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('gst_number', 20)->nullable();
            $table->string('pan_number', 15)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account', 30)->nullable();
            $table->string('ifsc_code', 15)->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 100)->nullable()->default('India');
            $table->string('pincode', 10)->nullable();
            $table->decimal('commission_percent', 5, 2)->default(10.00);
            $table->tinyInteger('is_approved')->default(0); // 0=pending, 1=approved, 2=rejected
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->tinyInteger('status')->default(0); // 0=active, 1=deleted
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });

        // ─── Delivery Staff ─────────────────────────────────────────
        Schema::create('shop_delivery_staff', function (Blueprint $table) {
            $table->increments('delivery_staff_id');
            $table->string('staff_code', 20)->nullable()->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 15);
            $table->string('password');
            $table->string('vehicle_type', 50)->nullable(); // bike, scooter, cycle, van
            $table->string('vehicle_number', 20)->nullable();
            $table->string('profile_image')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->tinyInteger('is_available')->default(1); // 1=available, 0=busy
            $table->tinyInteger('status')->default(0); // 0=active, 1=deleted
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_delivery_staff');
        Schema::dropIfExists('shop_vendors');
        Schema::dropIfExists('shop_customers');
        Schema::dropIfExists('shop_admin');
    }
};
