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
        Schema::table('tbl_daily_wages_staff', function (Blueprint $table) {
            $table->string('uan_no')->nullable()->after('is_pf_applicable');
            $table->string('pf_no')->nullable()->after('uan_no');
            $table->string('esi_no')->nullable()->after('pf_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_daily_wages_staff', function (Blueprint $table) {
            $table->dropColumn(['uan_no', 'pf_no', 'esi_no']);
        });
    }
};
