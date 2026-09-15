<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_deal_attachments', function (Blueprint $table) {
            $table->string('reference_type', 20)->default('deal')->after('id');
            $table->unsignedInteger('reference_id')->default(0)->after('reference_type');
        });

        // Migrate existing data
        DB::statement("UPDATE tbl_deal_attachments SET reference_id = deal_id, reference_type = 'deal'");

        Schema::table('tbl_deal_attachments', function (Blueprint $table) {
            $table->dropColumn('deal_id');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_deal_attachments', function (Blueprint $table) {
            $table->unsignedInteger('deal_id')->default(0)->after('id');
        });

        DB::statement("UPDATE tbl_deal_attachments SET deal_id = reference_id WHERE reference_type = 'deal'");

        Schema::table('tbl_deal_attachments', function (Blueprint $table) {
            $table->dropColumn('reference_id');
            $table->dropColumn('reference_type');
        });
    }
};
