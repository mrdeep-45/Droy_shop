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
        Schema::table('daily_wages_attendance_logs', function (Blueprint $table) {
            $table->dropColumn('attendance_id');
            $table->string('staff_code')->nullable()->after('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_wages_attendance_logs', function (Blueprint $table) {
            $table->unsignedInteger('attendance_id')->nullable();
            $table->dropColumn('staff_code');
        });
    }
};
