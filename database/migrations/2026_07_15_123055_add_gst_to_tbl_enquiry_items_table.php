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
        if (!Schema::hasColumn('tbl_enquiry_items', 'gst')) {
            Schema::table('tbl_enquiry_items', function (Blueprint $table) {
                $table->decimal('gst', 8, 2)->nullable()->after('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tbl_enquiry_items', 'gst')) {
            Schema::table('tbl_enquiry_items', function (Blueprint $table) {
                $table->dropColumn('gst');
            });
        }
    }
};
