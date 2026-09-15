<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old table if it exists with a different structure
        Schema::dropIfExists('mst_tooltip');

        Schema::create('mst_tooltip', function (Blueprint $table) {
            $table->increments('tooltip_id');
            $table->string('module_name', 100);
            $table->string('field_key', 150)->unique();
            $table->string('field_label', 200);
            $table->text('tooltip_text');
            $table->tinyInteger('status')->default(0)->comment('0=active, 1=deleted');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_tooltip');
    }
};
