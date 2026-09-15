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
        if (!Schema::hasColumn('tbl_deal', 'deal_type')) {
            Schema::table('tbl_deal', function (Blueprint $table) {
                $table->string('deal_type', 50)->nullable()->after('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tbl_deal', 'deal_type')) {
            Schema::table('tbl_deal', function (Blueprint $table) {
                $table->dropColumn('deal_type');
            });
        }
    }
};
