<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Brands ─────────────────────────────────────────────────
        Schema::create('mst_shop_brands', function (Blueprint $table) {
            $table->increments('brand_id');
            $table->string('brand_name');
            $table->string('brand_slug')->nullable();
            $table->string('brand_logo')->nullable();
            $table->text('brand_description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('is_featured')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });

        // ─── Categories (Self-Referencing for Sub-categories) ────────
        Schema::create('mst_shop_categories', function (Blueprint $table) {
            $table->increments('category_id');
            $table->unsignedInteger('parent_id')->nullable()->comment('NULL = top-level, set = sub-category');
            $table->string('category_name');
            $table->string('category_slug')->nullable();
            $table->string('category_image')->nullable();
            $table->string('category_banner')->nullable();
            $table->text('category_description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('is_featured')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->foreign('parent_id')
                  ->references('category_id')
                  ->on('mst_shop_categories')
                  ->onDelete('set null');
        });

        // ─── Units (Shop-specific, separate from CRM tbl_unit) ───────
        Schema::create('mst_shop_units', function (Blueprint $table) {
            $table->increments('shop_unit_id');
            $table->string('unit_name');
            $table->string('unit_short', 20)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });

        // ─── Variant Types (Size, Color, Material…) ─────────────────
        Schema::create('mst_shop_variants', function (Blueprint $table) {
            $table->increments('variant_id');
            $table->string('variant_name'); // e.g. Size, Color, Material
            $table->enum('variant_type', ['text', 'color', 'image'])->default('text');
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });

        // ─── Variant Values (S, M, L, Red, Blue…) ───────────────────
        Schema::create('mst_shop_variant_values', function (Blueprint $table) {
            $table->increments('variant_value_id');
            $table->unsignedInteger('variant_id');
            $table->string('value_name'); // S, M, L, Red, Blue
            $table->string('color_code', 10)->nullable(); // For color type: #FF0000
            $table->string('value_image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->foreign('variant_id')
                  ->references('variant_id')
                  ->on('mst_shop_variants')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_shop_variant_values');
        Schema::dropIfExists('mst_shop_variants');
        Schema::dropIfExists('mst_shop_units');
        Schema::dropIfExists('mst_shop_categories');
        Schema::dropIfExists('mst_shop_brands');
    }
};
