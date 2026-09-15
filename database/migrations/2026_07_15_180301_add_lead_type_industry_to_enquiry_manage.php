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
        Schema::table('enquiry_manage', function (Blueprint $table) {
            $table->string('lead_type', 20)->nullable()->after('city_id');
            $table->unsignedBigInteger('industry_id')->nullable()->after('lead_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enquiry_manage', function (Blueprint $table) {
            $table->dropColumn(['lead_type', 'industry_id']);
        });
    }
};
