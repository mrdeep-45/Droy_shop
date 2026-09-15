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
        Schema::table('tbl_crm_notes', function (Blueprint $table) {
            $table->tinyInteger('is_completed')->default(0)->comment('0: Pending, 1: Completed, 2: Cancelled');
            $table->text('result')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_crm_notes', function (Blueprint $table) {
            $table->dropColumn(['is_completed', 'result']);
        });
    }
};
