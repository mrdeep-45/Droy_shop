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
        Schema::create('tbl_campaign', function (Blueprint $table) {
            $table->id('campaign_id');
            // Basic Details
            $table->integer('assigned_to')->nullable()->comment('References mst_staff');
            $table->string('campaign_name');
            $table->integer('campaign_type_id')->comment('References mst_campaign_type');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('expected_budget', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->nullable();
            $table->text('description')->nullable();

            // Exhibition Details
            $table->integer('num_visitors')->nullable();
            $table->text('machines_displayed')->nullable();
            $table->text('design_details')->nullable();
            $table->text('marketing_collateral')->nullable();
            $table->string('stall_number')->nullable();
            $table->text('venue_location')->nullable();

            // Audit / Status columns
            $table->tinyInteger('status')->default(0)->comment('0 = Active, 1 = Deleted');
            $table->integer('created_by');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Foreign keys
            $table->foreign('assigned_to')->references('staff_id')->on('mst_staff')->onDelete('set null');
            $table->foreign('campaign_type_id')->references('campaign_type_id')->on('mst_campaign_type')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_campaign');
    }
};
