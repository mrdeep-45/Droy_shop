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
        Schema::table('open_lead', function (Blueprint $table) {
            $table->string('unique_query_id')->nullable();
            $table->string('query_product_name')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('sender_company')->nullable();
            $table->string('sender_mobile')->nullable();
            $table->string('sender_email')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('country_id')->nullable();
            $table->string('subject')->nullable();
            $table->text('query_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('open_lead', function (Blueprint $table) {
            $table->dropColumn([
                'unique_query_id',
                'query_product_name',
                'sender_name',
                'sender_company',
                'sender_mobile',
                'sender_email',
                'city_id',
                'state_id',
                'country_id',
                'subject',
                'query_message',
            ]);
        });
    }
};
