<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// ═══════════════════════════════════════════════════════
//  SESSION HELPERS
// ═══════════════════════════════════════════════════════

if (!function_exists('getShopLoginType')) {
    function getShopLoginType(): ?string
    {
        return Session::get('shop_login_type');
    }
}

if (!function_exists('getShopSessionId')) {
    /** Returns the ID of the currently logged-in shop user (any role). */
    function getShopSessionId(): ?int
    {
        return Session::get('shop_admin_id')
            ?? Session::get('customer_id')
            ?? Session::get('vendor_id')
            ?? Session::get('delivery_staff_id');
    }
}

if (!function_exists('getShopCreatedBy')) {
    /** Returns the integer value to store in `created_by` for shop tables. */
    function getShopCreatedBy(): int
    {
        return (int) getShopSessionId();
    }
}

if (!function_exists('getShopUpdatedBy')) {
    /** Returns the integer value to store in `updated_by` for shop tables. */
    function getShopUpdatedBy(): int
    {
        return (int) getShopSessionId();
    }
}

if (!function_exists('getShopUserName')) {
    /** Returns the display name of the currently logged-in shop user. */
    function getShopUserName(): ?string
    {
        $loginType = getShopLoginType();

        return match ($loginType) {
            'ShopAdmin', 'ShopSuperAdmin' => DB::table('shop_admin')
                ->where('shop_admin_id', Session::get('shop_admin_id'))
                ->value('name'),
            'Customer' => DB::table('shop_customers')
                ->where('customer_id', Session::get('customer_id'))
                ->value('name'),
            'Vendor' => DB::table('shop_vendors')
                ->where('vendor_id', Session::get('vendor_id'))
                ->value('business_name'),
            'DeliveryStaff' => DB::table('shop_delivery_staff')
                ->where('delivery_staff_id', Session::get('delivery_staff_id'))
                ->value('name'),
            default => 'User',
        };
    }
}

// ═══════════════════════════════════════════════════════
//  PERMISSION HELPERS (Shop-specific)
// ═══════════════════════════════════════════════════════

if (!function_exists('checkShopPermissions')) {
    /**
     * Check shop role-based permissions for a given route name.
     * Super Admin and Admin always get full access.
     */
    function checkShopPermissions(string $routeName): array
    {
        $loginType = getShopLoginType();

        // Super Admin / Admin → always full access
        if (in_array($loginType, ['ShopSuperAdmin', 'ShopAdmin'])) {
            return [
                'canView'          => true,
                'canCreate'        => true,
                'canUpdate'        => true,
                'canDelete'        => true,
                'hasAnyPermission' => true,
            ];
        }

        $permissions = [
            'canView'          => false,
            'canCreate'        => false,
            'canUpdate'        => false,
            'canDelete'        => false,
            'hasAnyPermission' => false,
        ];

        // Find menu or submenu by route name
        $menuData = DB::table('shop_submenus')
            ->where('submenu_route', $routeName)
            ->where('status', 0)
            ->select('shop_menu_id', 'shop_submenu_id')
            ->first()
            ??
            DB::table('shop_menus')
            ->where('menu_route', $routeName)
            ->where('status', 0)
            ->select('shop_menu_id', DB::raw('NULL as shop_submenu_id'))
            ->first();

        if (!$menuData) {
            return $permissions;
        }

        $roleId = Session::get('shop_role_id');

        $permissionCheck = DB::table('shop_role_permissions')
            ->where('shop_role_id', $roleId)
            ->where('shop_menu_id', $menuData->shop_menu_id)
            ->when($menuData->shop_submenu_id, function ($q, $sub) {
                return $q->where('shop_submenu_id', $sub);
            }, function ($q) {
                return $q->whereNull('shop_submenu_id');
            })
            ->first();

        if ($permissionCheck) {
            $permissions['canView']   = (bool) ($permissionCheck->can_view   ?? 0);
            $permissions['canCreate'] = (bool) ($permissionCheck->can_create ?? 0);
            $permissions['canUpdate'] = (bool) ($permissionCheck->can_update ?? 0);
            $permissions['canDelete'] = (bool) ($permissionCheck->can_delete ?? 0);
            if ($permissions['canCreate'] || $permissions['canUpdate'] || $permissions['canDelete']) {
                $permissions['canView']          = true;
                $permissions['hasAnyPermission'] = true;
            }
        }

        return $permissions;
    }
}

if (!function_exists('shopPermissionsCheck')) {
    /**
     * Shorthand to check a single permission key for the current route.
     * Usage in Blade: @if(shopPermissionsCheck('canCreate'))
     */
    function shopPermissionsCheck(string $permission, ?string $routeName = null): bool
    {
        $route       = $routeName ?? request()->route()?->getName();
        $permissions = checkShopPermissions($route ?? '');

        return (bool) ($permissions[$permission] ?? false);
    }
}

// ═══════════════════════════════════════════════════════
//  NUMBER GENERATORS
// ═══════════════════════════════════════════════════════

if (!function_exists('generateOrderNumber')) {
    /**
     * Generates unique order number: ORD-YYYYMMDD-XXXXX
     * e.g. ORD-20260919-00001
     */
    function generateOrderNumber(): string
    {
        $prefix  = 'ORD-' . date('Ymd') . '-';
        $last    = DB::table('tbl_shop_orders')
            ->where('order_number', 'like', $prefix . '%')
            ->orderBy('order_id', 'desc')
            ->value('order_number');
        $lastSeq = $last ? (int) substr($last, -5) : 0;
        return $prefix . str_pad($lastSeq + 1, 5, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generateInvoiceNumber')) {
    /**
     * Generates unique invoice number: INV-YYYY-XXXXX
     * e.g. INV-2026-00001
     */
    function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Y') . '-';
        $last   = DB::table('tbl_shop_orders')
            ->where('invoice_number', 'like', $prefix . '%')
            ->orderBy('order_id', 'desc')
            ->value('invoice_number');
        $lastSeq = $last ? (int) substr($last, -5) : 0;
        return $prefix . str_pad($lastSeq + 1, 5, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generateVendorCode')) {
    function generateVendorCode(): string
    {
        $last = DB::table('shop_vendors')->orderBy('vendor_id', 'desc')->value('vendor_id');
        return 'VND-' . str_pad(($last ?? 0) + 1, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generateCustomerCode')) {
    function generateCustomerCode(): string
    {
        $last = DB::table('shop_customers')->orderBy('customer_id', 'desc')->value('customer_id');
        return 'CUST-' . str_pad(($last ?? 0) + 1, 5, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generatePurchaseNumber')) {
    function generatePurchaseNumber(): string
    {
        $prefix = 'PO-' . date('Ymd') . '-';
        $last   = DB::table('tbl_shop_purchases')
            ->where('purchase_number', 'like', $prefix . '%')
            ->orderBy('purchase_id', 'desc')
            ->value('purchase_number');
        $lastSeq = $last ? (int) substr($last, -4) : 0;
        return $prefix . str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generateReturnNumber')) {
    function generateReturnNumber(): string
    {
        $prefix = 'RET-' . date('Ymd') . '-';
        $last   = DB::table('tbl_shop_returns')
            ->where('return_number', 'like', $prefix . '%')
            ->orderBy('return_id', 'desc')
            ->value('return_number');
        $lastSeq = $last ? (int) substr($last, -4) : 0;
        return $prefix . str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
    }
}

// ═══════════════════════════════════════════════════════
//  FORMATTING HELPERS
// ═══════════════════════════════════════════════════════

if (!function_exists('formatShopPrice')) {
    /** Formats a number as Indian Rupee: ₹1,23,456.00 */
    function formatShopPrice(float|int|string $amount, int $decimals = 2): string
    {
        $amount = (float) $amount;
        return '₹' . number_format($amount, $decimals);
    }
}

if (!function_exists('calculateDiscountPercent')) {
    /** Calculates what % discount was applied between MRP and selling price. */
    function calculateDiscountPercent(float $mrp, float $sellingPrice): float
    {
        if ($mrp <= 0) return 0.0;
        return round((($mrp - $sellingPrice) / $mrp) * 100, 2);
    }
}

if (!function_exists('calculateDiscountedPrice')) {
    /** Applies a % discount on an amount and returns the discounted price. */
    function calculateDiscountedPrice(float $amount, float $discountPercent): float
    {
        return $amount - ($amount * $discountPercent / 100);
    }
}

// ═══════════════════════════════════════════════════════
//  LOOKUP HELPERS
// ═══════════════════════════════════════════════════════

if (!function_exists('getShopCustomerName')) {
    function getShopCustomerName(int $customerId): string
    {
        return DB::table('shop_customers')->where('customer_id', $customerId)->value('name') ?? 'N/A';
    }
}

if (!function_exists('getShopVendorName')) {
    function getShopVendorName(int $vendorId): string
    {
        return DB::table('shop_vendors')->where('vendor_id', $vendorId)->value('business_name') ?? 'N/A';
    }
}

if (!function_exists('getShopProductName')) {
    function getShopProductName(int $productId): string
    {
        return DB::table('tbl_shop_products')->where('product_id', $productId)->value('product_name') ?? 'N/A';
    }
}

if (!function_exists('getShopCategoryName')) {
    function getShopCategoryName(int $categoryId): string
    {
        return DB::table('mst_shop_categories')->where('category_id', $categoryId)->value('category_name') ?? 'N/A';
    }
}

if (!function_exists('getShopBrandName')) {
    function getShopBrandName(int $brandId): string
    {
        return DB::table('mst_shop_brands')->where('brand_id', $brandId)->value('brand_name') ?? 'N/A';
    }
}

if (!function_exists('getShopAdminName')) {
    function getShopAdminName(int $adminId): string
    {
        return DB::table('shop_admin')->where('shop_admin_id', $adminId)->value('name') ?? 'Admin';
    }
}

// ═══════════════════════════════════════════════════════
//  ACTIVITY LOG HELPER
// ═══════════════════════════════════════════════════════

if (!function_exists('logShopActivity')) {
    /**
     * Records an action in the shop activity log.
     *
     * @param string      $action     e.g. 'created', 'updated', 'deleted', 'login'
     * @param string|null $modelType  e.g. 'ShopProduct'
     * @param int|null    $modelId    The PK of the affected record
     * @param string|null $modelLabel Human-readable label e.g. 'iPhone 15 Pro'
     * @param array|null  $oldValues  Previous values (for updates)
     * @param array|null  $newValues  New values
     * @param string|null $description Optional free-text note
     */
    function logShopActivity(
        string  $action,
        ?string $modelType = null,
        ?int    $modelId = null,
        ?string $modelLabel = null,
        ?array  $oldValues = null,
        ?array  $newValues = null,
        ?string $description = null
    ): void {
        try {
            DB::table('tbl_shop_activity_logs')->insert([
                'user_type'   => match (getShopLoginType()) {
                    'ShopSuperAdmin', 'ShopAdmin' => 'admin',
                    'Customer'      => 'customer',
                    'Vendor'        => 'vendor',
                    'DeliveryStaff' => 'delivery',
                    default         => 'system',
                },
                'user_id'     => getShopSessionId(),
                'user_name'   => getShopUserName(),
                'action'      => $action,
                'model_type'  => $modelType,
                'model_id'    => $modelId,
                'model_label' => $modelLabel,
                'old_values'  => $oldValues ? json_encode($oldValues) : null,
                'new_values'  => $newValues ? json_encode($newValues) : null,
                'description' => $description,
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
                'route_name'  => request()->route()?->getName(),
                'url'         => request()->fullUrl(),
                'method'      => request()->method(),
                'created_at'  => now(),
            ]);
        } catch (\Throwable $e) {
            // Never let logging break the main request
            logger()->error('ShopActivityLog failed: ' . $e->getMessage());
        }
    }
}

// ═══════════════════════════════════════════════════════
//  SLUG GENERATOR
// ═══════════════════════════════════════════════════════

if (!function_exists('generateShopSlug')) {
    /**
     * Creates a unique slug for shop tables.
     * @param string $text       The text to slugify
     * @param string $table      DB table to check uniqueness against
     * @param string $slugColumn Column name of slug in that table
     * @param int|null $exceptId Exclude this ID when checking (for updates)
     */
    function generateShopSlug(string $text, string $table, string $slugColumn = 'slug', ?int $exceptId = null): string
    {
        $slug     = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
        $original = $slug;
        $count    = 1;

        while (true) {
            $query = DB::table($table)->where($slugColumn, $slug);
            if ($exceptId) {
                $pkCol = match ($table) {
                    'tbl_shop_products'   => 'product_id',
                    'mst_shop_categories' => 'category_id',
                    'mst_shop_brands'     => 'brand_id',
                    'tbl_shop_cms_pages'  => 'page_id',
                    'tbl_shop_blogs'      => 'blog_id',
                    default               => 'id',
                };
                $query->where($pkCol, '!=', $exceptId);
            }
            if (!$query->exists()) {
                break;
            }
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}

// ═══════════════════════════════════════════════════════
//  NOTIFICATION HELPER
// ═══════════════════════════════════════════════════════

if (!function_exists('sendShopNotification')) {
    /**
     * Inserts a notification record for a shop user.
     */
    function sendShopNotification(
        string $userType,
        int    $userId,
        string $title,
        string $message,
        string $type = 'info',
        ?array $data = null,
        ?string $link = null,
        string $icon = 'bx bx-bell',
        string $color = 'primary'
    ): void {
        DB::table('tbl_shop_notifications')->insert([
            'user_type'  => $userType,
            'user_id'    => $userId,
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'icon'       => $icon,
            'color'      => $color,
            'data'       => $data ? json_encode($data) : null,
            'link'       => $link,
            'is_read'    => 0,
            'created_at' => now(),
        ]);
    }
}

// ═══════════════════════════════════════════════════════
//  IMAGE HELPER
// ═══════════════════════════════════════════════════════

if (!function_exists('uploadShopImage')) {
    /**
     * Uploads an image to storage and returns the path.
     * @param  \Illuminate\Http\UploadedFile $file
     * @param  string $folder  Sub-folder under public/assets/shop/
     * @return string          Relative path stored in DB
     */
    function uploadShopImage(\Illuminate\Http\UploadedFile $file, string $folder = 'products'): string
    {
        $directory = public_path('assets/shop/' . $folder);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);
        return 'assets/shop/' . $folder . '/' . $filename;
    }
}

if (!function_exists('deleteShopImage')) {
    /** Deletes a shop image from public path. */
    function deleteShopImage(?string $imagePath): void
    {
        if ($imagePath && file_exists(public_path($imagePath))) {
            @unlink(public_path($imagePath));
        }
    }
}

// ═══════════════════════════════════════════════════════
//  ORDER STATUS HELPERS
// ═══════════════════════════════════════════════════════

if (!function_exists('getOrderStatusBadge')) {
    function getOrderStatusBadge(string $status): string
    {
        return match ($status) {
            'pending'          => '<span class="badge bg-warning text-dark">Pending</span>',
            'confirmed'        => '<span class="badge bg-info">Confirmed</span>',
            'processing'       => '<span class="badge bg-primary">Processing</span>',
            'ready_to_ship'    => '<span class="badge bg-secondary">Ready to Ship</span>',
            'shipped'          => '<span class="badge bg-indigo">Shipped</span>',
            'out_for_delivery' => '<span class="badge bg-cyan">Out for Delivery</span>',
            'delivered'        => '<span class="badge bg-success">Delivered</span>',
            'cancelled'        => '<span class="badge bg-danger">Cancelled</span>',
            'return_requested' => '<span class="badge bg-orange">Return Requested</span>',
            'returned'         => '<span class="badge bg-dark">Returned</span>',
            default            => '<span class="badge bg-secondary">' . ucfirst($status) . '</span>',
        };
    }
}

if (!function_exists('getPaymentStatusBadge')) {
    function getPaymentStatusBadge(string $status): string
    {
        return match ($status) {
            'pending'        => '<span class="badge bg-warning text-dark">Pending</span>',
            'paid'           => '<span class="badge bg-success">Paid</span>',
            'failed'         => '<span class="badge bg-danger">Failed</span>',
            'refunded'       => '<span class="badge bg-info">Refunded</span>',
            'partial_refund' => '<span class="badge bg-orange">Partial Refund</span>',
            default          => '<span class="badge bg-secondary">' . ucfirst($status) . '</span>',
        };
    }
}


// ═══════════════════════════════════════════════════════
//  CODE GENERATOR
// ═══════════════════════════════════════════════════════

if (!function_exists('generateShopCode')) {
    function generateShopCode(string $prefix = 'SHP', string $table = 'tbl_shop_suppliers', string $column = 'supplier_code', int $padding = 4): string
    {
        $year = date('Y');
        $base = strtoupper($prefix) . '-' . $year . '-';
        $last = \Illuminate\Support\Facades\DB::table($table)
            ->where($column, 'like', $base . '%')
            ->orderByRaw("LENGTH({$column}) DESC, {$column} DESC")
            ->value($column);
        $nextNum = 1;
        if ($last) {
            $parts   = explode('-', $last);
            $nextNum = ((int) end($parts)) + 1;
        }
        return $base . str_pad($nextNum, $padding, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('uploadShopImage')) {
    function uploadShopImage($file, string $folder = 'products'): string
    {
        $folderPath = 'shop/' . ltrim($folder, '/');
        $filename   = time() . '_' . uniqid() . '.' . strtolower($file->getClientOriginalExtension());
        $file->storeAs($folderPath, $filename, 'public');
        return 'storage/' . $folderPath . '/' . $filename;
    }
}

if (!function_exists('deleteShopImage')) {
    function deleteShopImage(?string $relativePath): void
    {
        if (!$relativePath) return;
        $storagePath = preg_replace('#^storage/#', '', $relativePath);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($storagePath);
    }
}
