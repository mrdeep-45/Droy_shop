<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove company_name, contact_person, phone from tbl_deal.
     * These fields are now stored in enquiry_manage (linked via lead_id).
     */
    public function up(): void
    {
        Schema::table('tbl_deal', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'contact_person', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_deal', function (Blueprint $table) {
            $table->string('company_name', 255)->nullable()->after('deal_name');
            $table->string('contact_person', 255)->nullable()->after('company_name');
            $table->string('phone', 20)->nullable()->after('contact_person');
        });
    }
};
