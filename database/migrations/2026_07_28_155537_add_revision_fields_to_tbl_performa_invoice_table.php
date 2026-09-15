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
        Schema::table('tbl_performa_invoice', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_invoice_id')->nullable()->after('quotation_id');
            $table->tinyInteger('is_revised')->default(0)->after('status');
            $table->integer('revised_count')->default(0)->after('is_revised');

            $table->foreign('parent_invoice_id')
                ->references('invoice_id')
                ->on('tbl_performa_invoice')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_performa_invoice', function (Blueprint $table) {
            $table->dropForeign(['parent_invoice_id']);
            $table->dropColumn(['parent_invoice_id', 'is_revised', 'revised_count']);
        });
    }
};
