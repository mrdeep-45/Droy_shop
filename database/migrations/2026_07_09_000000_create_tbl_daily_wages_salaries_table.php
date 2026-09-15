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
        if (!Schema::hasTable('tbl_daily_wages_salaries')) {
            Schema::create('tbl_daily_wages_salaries', function (Blueprint $table) {
                $table->id();
                $table->string('staff_code');
                $table->integer('month');
                $table->integer('year');
                $table->decimal('total_working_hours', 8, 2);
                $table->decimal('per_hour_rate', 8, 2);
                $table->decimal('gross_salary', 10, 2);
                $table->decimal('employee_pf', 10, 2)->default(0);
                $table->decimal('ctc', 10, 2);
                $table->integer('created_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_daily_wages_salaries');
    }
};
