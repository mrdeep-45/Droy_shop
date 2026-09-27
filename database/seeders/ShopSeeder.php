<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * ShopSeeder
 * Seeds all default shop data:
 *  - 1 Super Admin account
 *  - Default shop roles
 *  - All admin sidebar menus & submenus
 *  - Default CMS pages (About, Privacy, T&C, FAQ)
 *  - Sample brands, categories
 */
class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ── 1. Shop Super Admin ──────────────────────────────────────
        if (!DB::table('shop_admin')->where('email', 'superadmin@hexashop.com')->exists()) {
            DB::table('shop_admin')->insert([
                'name'       => 'HexaShop Super Admin',
                'email'      => 'superadmin@hexashop.com',
                'password'   => md5('Admin@123'),  // md5 to match project convention
                'admin_type' => 'super_admin',
                'phone'      => '9999999999',
                'status'     => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->command->info('✅ Shop Super Admin created: superadmin@hexashop.com / Admin@123');
        }

        // ── 2. Shop Roles ────────────────────────────────────────────
        $roles = [
            ['role_name' => 'Super Admin', 'role_type' => 'super_admin'],
            ['role_name' => 'Shop Admin',  'role_type' => 'admin'],
            ['role_name' => 'Vendor',      'role_type' => 'vendor'],
            ['role_name' => 'Delivery',    'role_type' => 'delivery'],
            ['role_name' => 'Customer',    'role_type' => 'customer'],
        ];
        foreach ($roles as $role) {
            if (!DB::table('shop_roles')->where('role_name', $role['role_name'])->exists()) {
                DB::table('shop_roles')->insert(array_merge($role, [
                    'status'     => 0,
                    'created_at' => $now,
                ]));
            }
        }
        $this->command->info('✅ Shop roles seeded.');

        // ── 3. Shop Admin Menus ──────────────────────────────────────
        $this->seedMenus($now);

        // ── 4. Default CMS Pages ─────────────────────────────────────
        $this->seedCmsPages($now);

        // ── 5. Sample Master Data ─────────────────────────────────────
        $this->seedMasterData($now);

        $this->command->info('🎉 ShopSeeder completed successfully!');
    }

    private function seedMenus(Carbon $now): void
    {
        $menus = [
            // [menu_title, menu_icon, menu_route, menu_section, is_submenu, menu_order, submenus[]]
            ['Dashboard', 'bx bxs-dashboard', 'shop.dashboard', null, 0, 1, []],

            // CATALOG
            ['Products',   'bx bx-package',    null,                    'CATALOG',   1, 2, [
                ['All Products', 'bx bx-circle', 'shop.product.index', 1],
            ]],
            ['Categories', 'bx bx-category',   'shop.category.index',   'CATALOG',   0, 3, []],
            ['Brands',     'bx bx-diamond',     'shop.brand.index',      'CATALOG',   0, 4, []],
            ['Units',      'bx bx-ruler',       'shop.unit.index',       'CATALOG',   0, 5, []],
            ['Variants',   'bx bx-grid-alt',    'shop.variant.index',    'CATALOG',   0, 6, []],

            // INVENTORY
            ['Stock Mgmt',    'bx bx-box',       'shop.inventory.index',   'INVENTORY', 0, 7, []],
            ['Raw Materials', 'bx bx-layer',     'shop.rawmaterial.index', 'INVENTORY', 0, 8, []],
            ['Suppliers',     'bx bx-store-alt', 'shop.supplier.index',    'INVENTORY', 0, 9, []],
            ['Purchases',     'bx bx-cart-add',  'shop.purchase.index',    'INVENTORY', 0, 10, []],

            // SALES
            ['Orders',          'bx bx-receipt',   'shop.order.index',   'SALES', 0, 11, []],
            ['Returns/Refunds', 'bx bx-undo',       'shop.return.index',  'SALES', 0, 12, []],
            ['Coupons',         'bx bx-tag',        'shop.coupon.index',  'SALES', 0, 13, []],

            // CUSTOMERS
            ['Customer List',   'bx bx-group',   'shop.customer.index', 'CUSTOMERS', 0, 14, []],
            ['Reviews',         'bx bx-star',    'shop.review.index',   'CUSTOMERS', 0, 15, []],

            // VENDORS
            ['Vendor Mgmt',    'bx bx-buildings', 'shop.vendor.index',          'VENDORS', 0, 16, []],
            ['Vendor Earnings', 'bx bx-wallet',   'shop.vendor_earning.index',  'VENDORS', 0, 17, []],

            // DELIVERY
            ['Delivery Staff',  'bx bx-cycling',  'shop.delivery.index',        'DELIVERY', 0, 18, []],
            ['Assignments',     'bx bx-map-pin',  'shop.delivery_assign.index', 'DELIVERY', 0, 19, []],

            // MARKETING
            ['Banners',    'bx bx-image',      'shop.banner.index', 'MARKETING', 0, 20, []],
            ['CMS Pages',  'bx bx-file-blank', 'shop.cms.index',    'MARKETING', 0, 21, []],
            ['Blog',       'bx bx-news',       'shop.blog.index',   'MARKETING', 0, 22, []],

            // REPORTS
            ['Reports', 'bx bx-bar-chart-alt-2', null, 'REPORTS', 1, 23, [
                ['Sales Report',     'bx bx-circle', 'shop.report.sales',     1],
                ['Inventory Report', 'bx bx-circle', 'shop.report.inventory', 2],
                ['Vendor Report',    'bx bx-circle', 'shop.report.vendor',    3],
                ['Customer Report',  'bx bx-circle', 'shop.report.customer',  4],
            ]],

            // SETTINGS
            ['Settings',      'bx bx-cog',     'shop.setting.index',       'SETTINGS', 0, 24, []],
            ['Activity Logs', 'bx bx-history', 'shop.activity_log.index',  'SETTINGS', 0, 25, []],
        ];

        foreach ($menus as $menu) {
            [$title, $icon, $route, $section, $hasSubmenu, $order, $submenus] = $menu;

            if (DB::table('shop_menus')->where('menu_title', $title)->where('menu_type', 'admin')->exists()) {
                continue;
            }

            $menuId = DB::table('shop_menus')->insertGetId([
                'menu_title'   => $title,
                'menu_icon'    => $icon,
                'menu_route'   => $route,
                'menu_section' => $section,
                'menu_type'    => 'admin',
                'is_submenu'   => $hasSubmenu,
                'menu_order'   => $order,
                'status'       => 0,
            ]);

            foreach ($submenus as $idx => $sub) {
                DB::table('shop_submenus')->insert([
                    'shop_menu_id'   => $menuId,
                    'submenu_title'  => $sub[0],
                    'submenu_icon'   => $sub[1],
                    'submenu_route'  => $sub[2],
                    'submenu_order'  => $sub[3],
                    'status'         => 0,
                ]);
            }
        }
        $this->command->info('✅ Shop menus & submenus seeded.');
    }

    private function seedCmsPages(Carbon $now): void
    {
        $pages = [
            ['About Us',          'about-us',       '<h2>About HexaShop</h2><p>We are a leading e-commerce platform...</p>',         'About Us — HexaShop'],
            ['Privacy Policy',    'privacy-policy', '<h2>Privacy Policy</h2><p>We value your privacy...</p>',                        'Privacy Policy — HexaShop'],
            ['Terms & Conditions','terms',           '<h2>Terms & Conditions</h2><p>By using our services you agree to...</p>',       'Terms & Conditions — HexaShop'],
            ['FAQs',              'faqs',            '<h2>Frequently Asked Questions</h2><p>Have questions? We have answers...</p>',  'FAQs — HexaShop'],
            ['Return Policy',     'return-policy',  '<h2>Return Policy</h2><p>We offer 7-day hassle-free returns...</p>',            'Return Policy — HexaShop'],
            ['Shipping Policy',   'shipping-policy','<h2>Shipping Policy</h2><p>We deliver across India within 3-7 business days...', 'Shipping Policy — HexaShop'],
        ];

        foreach ($pages as [$title, $slug, $content, $metaTitle]) {
            if (!DB::table('tbl_shop_cms_pages')->where('page_slug', $slug)->exists()) {
                DB::table('tbl_shop_cms_pages')->insert([
                    'page_title'       => $title,
                    'page_slug'        => $slug,
                    'page_content'     => $content,
                    'meta_title'       => $metaTitle,
                    'is_active'        => 1,
                    'show_in_footer'   => 1,
                    'status'           => 0,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
            }
        }
        $this->command->info('✅ CMS pages seeded.');
    }

    private function seedMasterData(Carbon $now): void
    {
        // Sample Brands
        $brands = ['Nike', 'Adidas', 'H&M', 'Zara', 'Allen Solly', 'Raymond', 'Peter England'];
        foreach ($brands as $brand) {
            if (!DB::table('mst_shop_brands')->where('brand_name', $brand)->exists()) {
                DB::table('mst_shop_brands')->insert([
                    'brand_name'  => $brand,
                    'brand_slug'  => strtolower(str_replace(['&', ' '], ['-', '-'], $brand)),
                    'status'      => 0,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        // Sample Categories
        $topCategories = [
            ['Men\'s Clothing',   'mens-clothing'],
            ['Women\'s Clothing', 'womens-clothing'],
            ['Kids\' Clothing',   'kids-clothing'],
            ['Accessories',       'accessories'],
            ['Footwear',          'footwear'],
        ];
        foreach ($topCategories as [$name, $slug]) {
            if (!DB::table('mst_shop_categories')->where('category_slug', $slug)->exists()) {
                $parentId = DB::table('mst_shop_categories')->insertGetId([
                    'category_name' => $name,
                    'category_slug' => $slug,
                    'status'        => 0,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
                // Add sub-categories for Men's Clothing
                if ($name === "Men's Clothing") {
                    $subs = [['T-Shirts', 't-shirts'], ['Shirts', 'shirts'], ['Trousers', 'trousers'], ['Jeans', 'jeans'], ['Kurtas', 'kurtas']];
                    foreach ($subs as [$sn, $ss]) {
                        DB::table('mst_shop_categories')->insert([
                            'parent_id'     => $parentId,
                            'category_name' => $sn,
                            'category_slug' => $ss,
                            'status'        => 0,
                            'created_at'    => $now,
                            'updated_at'    => $now,
                        ]);
                    }
                }
            }
        }

        // Sample Units
        $units = [['Piece', 'Pcs'], ['Set', 'Set'], ['Dozen', 'Doz'], ['Kilogram', 'Kg'], ['Meter', 'Mtr']];
        foreach ($units as [$name, $short]) {
            if (!DB::table('mst_shop_units')->where('unit_name', $name)->exists()) {
                DB::table('mst_shop_units')->insert(['unit_name' => $name, 'unit_short' => $short, 'status' => 0, 'created_at' => $now, 'updated_at' => $now]);
            }
        }

        // Sample Variants
        $variants = [
            ['Size',     'text'],
            ['Color',    'color'],
            ['Material', 'text'],
        ];
        foreach ($variants as [$name, $type]) {
            if (!DB::table('mst_shop_variants')->where('variant_name', $name)->exists()) {
                $variantId = DB::table('mst_shop_variants')->insertGetId(['variant_name' => $name, 'variant_type' => $type, 'status' => 0, 'created_at' => $now]);
                if ($name === 'Size') {
                    foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', '28', '30', '32', '34', '36', '38', '40'] as $i => $val) {
                        DB::table('mst_shop_variant_values')->insert(['variant_id' => $variantId, 'value_name' => $val, 'sort_order' => $i + 1, 'status' => 0, 'created_at' => $now]);
                    }
                } elseif ($name === 'Color') {
                    $colors = [['Red','#FF0000'],['Blue','#0000FF'],['Green','#008000'],['Black','#000000'],['White','#FFFFFF'],['Yellow','#FFFF00'],['Navy','#000080'],['Grey','#808080']];
                    foreach ($colors as $i => [$colorName, $hex]) {
                        DB::table('mst_shop_variant_values')->insert(['variant_id' => $variantId, 'value_name' => $colorName, 'color_code' => $hex, 'sort_order' => $i + 1, 'status' => 0, 'created_at' => $now]);
                    }
                }
            }
        }

        $this->command->info('✅ Master data seeded (brands, categories, units, variants).');
    }
}
