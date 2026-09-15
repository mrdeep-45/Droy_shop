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
        Schema::table('tbl_deal', function (Blueprint $table) {
            $table->string('iwo_no')->nullable()->after('deal_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_deal', function (Blueprint $table) {
            $table->dropColumn('iwo_no');
        });
    }
};
