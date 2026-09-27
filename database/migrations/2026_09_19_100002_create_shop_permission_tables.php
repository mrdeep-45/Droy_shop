<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Shop Roles ─────────────────────────────────────────────
        Schema::create('shop_roles', function (Blueprint $table) {
            $table->increments('shop_role_id');
            $table->string('role_name');
            $table->enum('role_type', ['super_admin', 'admin', 'vendor', 'delivery', 'customer'])
                  ->default('admin');
            $table->tinyInteger('status')->default(0); // 0=active, 1=deleted
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });

        // ─── Shop Menus ─────────────────────────────────────────────
        Schema::create('shop_menus', function (Blueprint $table) {
            $table->increments('shop_menu_id');
            $table->string('menu_title');
            $table->string('menu_icon')->nullable()->default('bx bx-cube');
            $table->string('menu_route')->nullable();
            $table->string('menu_section')->nullable(); // Section header label e.g. "CATALOG"
            $table->enum('menu_type', ['admin', 'vendor', 'delivery', 'customer'])->default('admin');
            $table->tinyInteger('is_submenu')->default(0); // 1=has children
            $table->integer('menu_order')->default(0);
            $table->tinyInteger('status')->default(0);
        });

        // ─── Shop Sub-menus ─────────────────────────────────────────
        Schema::create('shop_submenus', function (Blueprint $table) {
            $table->increments('shop_submenu_id');
            $table->unsignedInteger('shop_menu_id');
            $table->string('submenu_title');
            $table->string('submenu_icon')->nullable()->default('bx bx-chevron-right');
            $table->string('submenu_route')->nullable();
            $table->integer('submenu_order')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->foreign('shop_menu_id')
                  ->references('shop_menu_id')
                  ->on('shop_menus')
                  ->onDelete('cascade');
        });

        // ─── Shop Role → Menu Permissions ───────────────────────────
        Schema::create('shop_role_permissions', function (Blueprint $table) {
            $table->increments('permission_id');
            $table->unsignedInteger('shop_role_id');
            $table->unsignedInteger('shop_menu_id');
            $table->unsignedInteger('shop_submenu_id')->nullable();
            $table->tinyInteger('can_view')->default(0);
            $table->tinyInteger('can_create')->default(0);
            $table->tinyInteger('can_update')->default(0);
            $table->tinyInteger('can_delete')->default(0);
            $table->timestamps();

            $table->foreign('shop_role_id')
                  ->references('shop_role_id')
                  ->on('shop_roles')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_role_permissions');
        Schema::dropIfExists('shop_submenus');
        Schema::dropIfExists('shop_menus');
        Schema::dropIfExists('shop_roles');
    }
};
