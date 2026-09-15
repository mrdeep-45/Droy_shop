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
        Schema::table('tbl_deal_notes', function (Blueprint $table) {
            $table->integer('assigned_to')->nullable()->after('deal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_deal_notes', function (Blueprint $table) {
            $table->dropColumn('assigned_to');
        });
    }
};
