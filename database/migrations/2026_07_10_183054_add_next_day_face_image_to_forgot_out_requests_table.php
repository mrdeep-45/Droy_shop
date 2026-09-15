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
        Schema::table('forgot_out_requests', function (Blueprint $table) {
            $table->string('next_day_face_image')->nullable()->after('face_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forgot_out_requests', function (Blueprint $table) {
            $table->dropColumn('next_day_face_image');
        });
    }
};
