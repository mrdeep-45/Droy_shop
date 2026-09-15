<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('tbl_deal_calls', 'tbl_crm_calls');
        Schema::rename('tbl_deal_notes', 'tbl_crm_notes');
        Schema::rename('tbl_deal_attachments', 'tbl_crm_attachments');
    }

    public function down(): void
    {
        Schema::rename('tbl_crm_calls', 'tbl_deal_calls');
        Schema::rename('tbl_crm_notes', 'tbl_deal_notes');
        Schema::rename('tbl_crm_attachments', 'tbl_deal_attachments');
    }
};
