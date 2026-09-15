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
        Schema::table('tbl_daily_wages_salaries', function (Blueprint $table) {
            $table->string('payment_type')->nullable()->after('ctc');
            $table->date('payment_from_date')->nullable()->after('payment_type');
            $table->date('payment_to_date')->nullable()->after('payment_from_date');
            $table->text('paid_dates')->nullable()->after('payment_to_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_daily_wages_salaries', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'payment_from_date', 'payment_to_date', 'paid_dates']);
        });
    }
};
