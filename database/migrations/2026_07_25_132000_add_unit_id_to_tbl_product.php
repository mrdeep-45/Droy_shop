<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_product', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_product', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('gst');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_product', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_product', 'unit_id')) {
                $table->dropColumn('unit_id');
            }
        });
    }
};
