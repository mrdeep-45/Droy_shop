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
        Schema::table('tbl_customer', function (Blueprint $table) {
            $table->string('phone', 15)->nullable()->change();
            $table->string('alt_phone', 15)->nullable()->change();
        });

        Schema::table('enquiry_manage', function (Blueprint $table) {
            $table->string('phone', 15)->nullable()->change();
            $table->string('alternate_phone_1', 15)->nullable()->change();
        });

        Schema::table('tbl_performa_invoice', function (Blueprint $table) {
            $table->string('phone', 15)->nullable()->change();
        });

        Schema::table('tbl_quotation', function (Blueprint $table) {
            $table->string('phone', 15)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional rollback logic
    }
};
