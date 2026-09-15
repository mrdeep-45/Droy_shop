<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Makes lead_id nullable so direct deals (created without a lead) can be saved.
     */
    public function up(): void
    {
        Schema::table('tbl_deal', function (Blueprint $table) {
            $table->unsignedBigInteger('lead_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_deal', function (Blueprint $table) {
            $table->unsignedBigInteger('lead_id')->nullable(false)->change();
        });
    }
};
