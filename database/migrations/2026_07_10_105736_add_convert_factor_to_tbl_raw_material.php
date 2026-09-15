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
        Schema::table('tbl_raw_material', function (Blueprint $table) {
            $table->decimal('convert_factor', 15, 4)->nullable()->after('alt_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_raw_material', function (Blueprint $table) {
            $table->dropColumn('convert_factor');
        });
    }
};
