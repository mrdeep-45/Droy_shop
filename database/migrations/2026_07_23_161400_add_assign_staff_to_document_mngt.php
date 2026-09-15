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
        Schema::table('document_mngt', function (Blueprint $table) {
            $table->string('assign_to_staff', 50)->default('All Staff')->nullable()->after('expiry_date');
            $table->text('assigned_staff_ids')->nullable()->after('assign_to_staff');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_mngt', function (Blueprint $table) {
            $table->dropColumn(['assign_to_staff', 'assigned_staff_ids']);
        });
    }
};
