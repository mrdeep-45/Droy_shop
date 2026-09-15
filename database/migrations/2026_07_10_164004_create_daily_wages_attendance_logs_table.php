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
        Schema::create('daily_wages_attendance_logs', function (Blueprint $table) {
            $table->id('alog_id');
            $table->unsignedInteger('attendance_id');
            $table->unsignedInteger('staff_id');
            $table->text('action')->nullable();
            $table->unsignedInteger('performed_by')->nullable();
            $table->text('performed_by_name')->nullable();
            $table->dateTime('old_time_in')->nullable();
            $table->dateTime('old_time_out')->nullable();
            $table->dateTime('new_time_in')->nullable();
            $table->dateTime('new_time_out')->nullable();
            $table->text('remark')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_wages_attendance_logs');
    }
};
