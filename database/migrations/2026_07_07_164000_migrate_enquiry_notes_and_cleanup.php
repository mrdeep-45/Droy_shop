<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('enquiry_notes')) {
            $notes = DB::table('enquiry_notes')->get();
            foreach ($notes as $note) {
                DB::table('tbl_crm_notes')->insert([
                    'reference_type' => 'lead',
                    'reference_id'   => $note->enquiry_id,
                    'note'           => $note->notes,
                    'created_by'     => $note->created_by,
                    'created_at'     => $note->created_at,
                    'updated_at'     => $note->updated_at,
                ]);
            }
            Schema::dropIfExists('enquiry_notes');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('enquiry_notes')) {
            Schema::create('enquiry_notes', function (Blueprint $table) {
                $table->integer('note_id')->autoIncrement();
                $table->integer('enquiry_id');
                $table->text('notes')->nullable();
                $table->integer('created_by')->nullable();
                $table->integer('updated_by')->nullable();
                $table->datetime('created_at')->useCurrent();
                $table->datetime('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
        }
    }
};
