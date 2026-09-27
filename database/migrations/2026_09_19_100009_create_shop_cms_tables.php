<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Banners & Sliders ───────────────────────────────────────
        Schema::create('tbl_shop_banners', function (Blueprint $table) {
            $table->increments('banner_id');
            $table->string('banner_title');
            $table->string('banner_subtitle')->nullable();
            $table->string('banner_image');
            $table->string('banner_mobile_image')->nullable();
            $table->string('banner_link')->nullable();
            $table->string('banner_cta_text')->nullable()->comment('Call to action button text');
            $table->enum('banner_position', ['homepage_main', 'homepage_secondary', 'category', 'sidebar'])
                  ->default('homepage_main');
            $table->string('target_type')->nullable()->comment('category, product, brand, custom');
            $table->unsignedInteger('target_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });

        // ─── CMS Pages (About Us, T&C, Privacy, etc.) ───────────────
        Schema::create('tbl_shop_cms_pages', function (Blueprint $table) {
            $table->increments('page_id');
            $table->string('page_title');
            $table->string('page_slug')->unique();
            $table->longText('page_content');
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('show_in_footer')->default(1);
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });

        // ─── Blog ────────────────────────────────────────────────────
        Schema::create('tbl_shop_blogs', function (Blueprint $table) {
            $table->increments('blog_id');
            $table->string('blog_title');
            $table->string('blog_slug')->unique();
            $table->longText('blog_content');
            $table->string('blog_image')->nullable();
            $table->string('blog_excerpt', 500)->nullable();
            $table->string('blog_category')->nullable();
            $table->text('tags')->nullable();
            $table->unsignedInteger('author_id')->nullable();
            $table->string('author_type', 20)->default('admin')->comment('admin, vendor');
            $table->tinyInteger('is_published')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_shop_blogs');
        Schema::dropIfExists('tbl_shop_cms_pages');
        Schema::dropIfExists('tbl_shop_banners');
    }
};
