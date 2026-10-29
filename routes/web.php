<?php

use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════════════════
//  HEXASHOP E-COMMERCE ROUTES
//  All CRM routes above remain UNCHANGED.
//  New routes are prefixed with /shop/, /store/, /vendor/, /delivery/
// ═══════════════════════════════════════════════════════════════

use App\Http\Controllers\ShopAdminLoginController;
use App\Http\Controllers\ShopDashboardController;
use App\Http\Middleware\ShopAdminIsValid;
use App\Http\Middleware\CustomerIsValid;
use App\Http\Middleware\VendorIsValid;
use App\Http\Middleware\DeliveryStaffIsValid;

// ── Customer Storefront Routes (Phase 4) ─────────────────────────────
Route::get('/', [\App\Http\Controllers\ShopStoreController::class, 'home'])->name('store.home');
Route::get('/shop', [\App\Http\Controllers\ShopStoreController::class, 'shop'])->name('store.shop');
Route::get('/category/{slug}', [\App\Http\Controllers\ShopStoreController::class, 'category'])->name('store.category');
Route::get('/product/{slug}', [\App\Http\Controllers\ShopStoreController::class, 'product'])->name('store.product.detail');

// Wishlist
Route::get('/wishlist', [\App\Http\Controllers\ShopCartController::class, 'wishlist'])->name('store.wishlist');
Route::post('/wishlist/add', [\App\Http\Controllers\ShopCartController::class, 'addToWishlist'])->name('store.wishlist.add');
Route::post('/wishlist/remove', [\App\Http\Controllers\ShopCartController::class, 'removeFromWishlist'])->name('store.wishlist.remove');

// Cart
Route::get('/cart', [\App\Http\Controllers\ShopCartController::class, 'cart'])->name('store.cart');
Route::post('/cart/add', [\App\Http\Controllers\ShopCartController::class, 'addToCart'])->name('store.cart.add');
Route::post('/cart/update', [\App\Http\Controllers\ShopCartController::class, 'updateCart'])->name('store.cart.update');
Route::post('/cart/remove', [\App\Http\Controllers\ShopCartController::class, 'removeFromCart'])->name('store.cart.remove');
Route::post('/cart/apply-coupon', [\App\Http\Controllers\ShopCartController::class, 'applyCoupon'])->name('store.cart.apply_coupon');
Route::post('/cart/remove-coupon', [\App\Http\Controllers\ShopCartController::class, 'removeCoupon'])->name('store.cart.remove_coupon');

// Checkout (Requires login via CustomerIsValid or similar logic handled in controller)
Route::middleware([CustomerIsValid::class])->group(function() {
    Route::get('/checkout', [\App\Http\Controllers\ShopCheckoutController::class, 'index'])->name('store.checkout');
    Route::post('/checkout/process', [\App\Http\Controllers\ShopCheckoutController::class, 'process'])->name('store.checkout.process');
    Route::get('/checkout/success/{order_id}', [\App\Http\Controllers\ShopCheckoutController::class, 'success'])->name('store.checkout.success');
});


// ── Auth Routes (Public) ─────────────────────────────────────────────

Route::get('/shop/login',  [ShopAdminLoginController::class, 'showAdminLogin'])->name('shop.login');
Route::post('/shop/login', [ShopAdminLoginController::class, 'processAdminLogin'])->name('shop.admin.login.process');

Route::get('/vendor/login',     [ShopAdminLoginController::class, 'showVendorLogin'])->name('vendor.login');
Route::post('/vendor/login',    [ShopAdminLoginController::class, 'processVendorLogin'])->name('shop.vendor.login.process');
Route::get('/vendor/register',  [ShopAdminLoginController::class, 'showVendorRegister'])->name('vendor.register');
Route::post('/vendor/register', [ShopAdminLoginController::class, 'processVendorRegister'])->name('shop.vendor.register.process');

Route::get('/delivery/login',  [ShopAdminLoginController::class, 'showDeliveryLogin'])->name('delivery.login');
Route::post('/delivery/login', [ShopAdminLoginController::class, 'processDeliveryLogin'])->name('shop.delivery.login.process');

Route::get('/store/login',      [ShopAdminLoginController::class, 'showCustomerLogin'])->name('store.login');
Route::post('/store/login',     [ShopAdminLoginController::class, 'processCustomerLogin'])->name('store.customer.login.process');
Route::get('/store/register',   [ShopAdminLoginController::class, 'showCustomerRegister'])->name('store.register');
Route::post('/store/register',  [ShopAdminLoginController::class, 'processCustomerRegister'])->name('store.customer.register.process');

Route::post('/shop/logout', [ShopAdminLoginController::class, 'shopLogout'])->name('shop.logout');

// ── Shop Admin Panel Routes ──────────────────────────────────────────
Route::prefix('shop')->middleware([ShopAdminIsValid::class])->group(function () {

    Route::get('/dashboard',      [ShopDashboardController::class, 'index'])->name('shop.dashboard');
    Route::get('/dashboard/data', [ShopDashboardController::class, 'getDashboardData'])->name('shop.dashboard.data');
    Route::get('/global-search',  [ShopDashboardController::class, 'globalSearch'])->name('shop.global_search');

    Route::get('/notifications/list',      [\App\Http\Controllers\ShopNotificationController::class, 'getList'])->name('shop.notification.list');
    Route::post('/notifications/read',     [\App\Http\Controllers\ShopNotificationController::class, 'markRead'])->name('shop.notification.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\ShopNotificationController::class, 'markAllRead'])->name('shop.notification.read_all');

    Route::get('/brand',           [\App\Http\Controllers\ShopBrandController::class, 'index'])->name('shop.brand.index');
    Route::get('/brand/list',      [\App\Http\Controllers\ShopBrandController::class, 'getList'])->name('shop.brand.list');
    Route::post('/brand/store',    [\App\Http\Controllers\ShopBrandController::class, 'store'])->name('shop.brand.store');
    Route::get('/brand/edit/{id}', [\App\Http\Controllers\ShopBrandController::class, 'edit'])->name('shop.brand.edit');
    Route::post('/brand/delete',   [\App\Http\Controllers\ShopBrandController::class, 'destroy'])->name('shop.brand.delete');
    Route::get('/brand/ajax',      [\App\Http\Controllers\ShopBrandController::class, 'ajaxList'])->name('shop.ajax.brands');

    Route::get('/category',                    [\App\Http\Controllers\ShopCategoryController::class, 'index'])->name('shop.category.index');
    Route::get('/category/list',               [\App\Http\Controllers\ShopCategoryController::class, 'getList'])->name('shop.category.list');
    Route::post('/category/store',             [\App\Http\Controllers\ShopCategoryController::class, 'store'])->name('shop.category.store');
    Route::get('/category/edit/{id}',          [\App\Http\Controllers\ShopCategoryController::class, 'edit'])->name('shop.category.edit');
    Route::post('/category/delete',            [\App\Http\Controllers\ShopCategoryController::class, 'destroy'])->name('shop.category.delete');
    Route::get('/category/ajax',               [\App\Http\Controllers\ShopCategoryController::class, 'ajaxList'])->name('shop.ajax.categories');
    Route::get('/category/subcategories/{id}', [\App\Http\Controllers\ShopCategoryController::class, 'getSubcategories'])->name('shop.category.subcategories');

    Route::get('/unit',           [\App\Http\Controllers\ShopUnitController::class, 'index'])->name('shop.unit.index');
    Route::get('/unit/list',      [\App\Http\Controllers\ShopUnitController::class, 'getList'])->name('shop.unit.list');
    Route::post('/unit/store',    [\App\Http\Controllers\ShopUnitController::class, 'store'])->name('shop.unit.store');
    Route::get('/unit/edit/{id}', [\App\Http\Controllers\ShopUnitController::class, 'edit'])->name('shop.unit.edit');
    Route::post('/unit/delete',   [\App\Http\Controllers\ShopUnitController::class, 'destroy'])->name('shop.unit.delete');

    Route::get('/variant',                [\App\Http\Controllers\ShopVariantController::class, 'index'])->name('shop.variant.index');
    Route::get('/variant/list',           [\App\Http\Controllers\ShopVariantController::class, 'getList'])->name('shop.variant.list');
    Route::post('/variant/store',         [\App\Http\Controllers\ShopVariantController::class, 'store'])->name('shop.variant.store');
    Route::get('/variant/edit/{id}',      [\App\Http\Controllers\ShopVariantController::class, 'edit'])->name('shop.variant.edit');
    Route::post('/variant/delete',        [\App\Http\Controllers\ShopVariantController::class, 'destroy'])->name('shop.variant.delete');
    Route::get('/variant/{id}/values',    [\App\Http\Controllers\ShopVariantController::class, 'getValues'])->name('shop.variant.values');
    Route::post('/variant/value/store',   [\App\Http\Controllers\ShopVariantController::class, 'storeValue'])->name('shop.variant.value.store');
    Route::post('/variant/value/delete',  [\App\Http\Controllers\ShopVariantController::class, 'destroyValue'])->name('shop.variant.value.delete');

    Route::get('/supplier',           [\App\Http\Controllers\ShopSupplierController::class, 'index'])->name('shop.supplier.index');
    Route::get('/supplier/list',      [\App\Http\Controllers\ShopSupplierController::class, 'getList'])->name('shop.supplier.list');
    Route::post('/supplier/store',    [\App\Http\Controllers\ShopSupplierController::class, 'store'])->name('shop.supplier.store');
    Route::get('/supplier/edit/{id}', [\App\Http\Controllers\ShopSupplierController::class, 'edit'])->name('shop.supplier.edit');
    Route::post('/supplier/delete',   [\App\Http\Controllers\ShopSupplierController::class, 'destroy'])->name('shop.supplier.delete');
    Route::get('/supplier/show/{id}', [\App\Http\Controllers\ShopSupplierController::class, 'show'])->name('shop.supplier.show');
    Route::get('/supplier/ajax',      [\App\Http\Controllers\ShopSupplierController::class, 'ajaxList'])->name('shop.ajax.suppliers');

    Route::get('/product',                 [\App\Http\Controllers\ShopProductController::class, 'index'])->name('shop.product.index');
    Route::get('/product/list',            [\App\Http\Controllers\ShopProductController::class, 'getList'])->name('shop.product.list');
    Route::post('/product/store',          [\App\Http\Controllers\ShopProductController::class, 'store'])->name('shop.product.store');
    Route::get('/product/edit/{id}',       [\App\Http\Controllers\ShopProductController::class, 'edit'])->name('shop.product.edit');
    Route::get('/product/show/{id}',       [\App\Http\Controllers\ShopProductController::class, 'show'])->name('shop.product.show');
    Route::post('/product/delete',         [\App\Http\Controllers\ShopProductController::class, 'destroy'])->name('shop.product.delete');
    Route::post('/product/image/delete',   [\App\Http\Controllers\ShopProductController::class, 'deleteImage'])->name('shop.product.image.delete');
    Route::post('/product/image/primary',  [\App\Http\Controllers\ShopProductController::class, 'setPrimaryImage'])->name('shop.product.image.primary');
    Route::post('/product/variant/store',  [\App\Http\Controllers\ShopProductController::class, 'storeVariant'])->name('shop.product.variant.store');
    Route::post('/product/variant/delete', [\App\Http\Controllers\ShopProductController::class, 'deleteVariant'])->name('shop.product.variant.delete');
    Route::get('/product/ajax',            [\App\Http\Controllers\ShopProductController::class, 'ajaxList'])->name('shop.ajax.products');

    Route::get('/inventory',              [\App\Http\Controllers\ShopInventoryController::class, 'index'])->name('shop.inventory.index');
    Route::get('/inventory/list',         [\App\Http\Controllers\ShopInventoryController::class, 'getList'])->name('shop.inventory.list');
    Route::post('/inventory/adjust',      [\App\Http\Controllers\ShopInventoryController::class, 'adjust'])->name('shop.inventory.adjust');
    Route::get('/inventory/product/{id}', [\App\Http\Controllers\ShopInventoryController::class, 'productLedger'])->name('shop.inventory.product');

    Route::get('/rawmaterial',           [\App\Http\Controllers\ShopRawMaterialController::class, 'index'])->name('shop.rawmaterial.index');
    Route::get('/rawmaterial/list',      [\App\Http\Controllers\ShopRawMaterialController::class, 'getList'])->name('shop.rawmaterial.list');
    Route::post('/rawmaterial/store',    [\App\Http\Controllers\ShopRawMaterialController::class, 'store'])->name('shop.rawmaterial.store');
    Route::get('/rawmaterial/edit/{id}', [\App\Http\Controllers\ShopRawMaterialController::class, 'edit'])->name('shop.rawmaterial.edit');
    Route::post('/rawmaterial/delete',   [\App\Http\Controllers\ShopRawMaterialController::class, 'destroy'])->name('shop.rawmaterial.delete');

    Route::get('/purchase',           [\App\Http\Controllers\ShopPurchaseController::class, 'index'])->name('shop.purchase.index');
    Route::get('/purchase/list',      [\App\Http\Controllers\ShopPurchaseController::class, 'getList'])->name('shop.purchase.list');
    Route::post('/purchase/store',    [\App\Http\Controllers\ShopPurchaseController::class, 'store'])->name('shop.purchase.store');
    Route::get('/purchase/show/{id}', [\App\Http\Controllers\ShopPurchaseController::class, 'show'])->name('shop.purchase.show');
    Route::get('/purchase/edit/{id}', [\App\Http\Controllers\ShopPurchaseController::class, 'edit'])->name('shop.purchase.edit');
    Route::post('/purchase/delete',   [\App\Http\Controllers\ShopPurchaseController::class, 'destroy'])->name('shop.purchase.delete');
    Route::post('/purchase/receive',  [\App\Http\Controllers\ShopPurchaseController::class, 'markReceived'])->name('shop.purchase.receive');

    Route::get('/customer',           [\App\Http\Controllers\ShopCustomerManageController::class, 'index'])->name('shop.customer.index');
    Route::get('/customer/list',      [\App\Http\Controllers\ShopCustomerManageController::class, 'getList'])->name('shop.customer.list');
    Route::get('/customer/show/{id}', [\App\Http\Controllers\ShopCustomerManageController::class, 'show'])->name('shop.customer.show');
    Route::post('/customer/status',   [\App\Http\Controllers\ShopCustomerManageController::class, 'toggleStatus'])->name('shop.customer.status');

    Route::get('/vendor',             [\App\Http\Controllers\ShopVendorManageController::class, 'index'])->name('shop.vendor.index');
    Route::get('/vendor/list',        [\App\Http\Controllers\ShopVendorManageController::class, 'getList'])->name('shop.vendor.list');
    Route::get('/vendor/show/{id}',   [\App\Http\Controllers\ShopVendorManageController::class, 'show'])->name('shop.vendor.show');
    Route::post('/vendor/approve',    [\App\Http\Controllers\ShopVendorManageController::class, 'approve'])->name('shop.vendor.approve');
    Route::post('/vendor/reject',     [\App\Http\Controllers\ShopVendorManageController::class, 'reject'])->name('shop.vendor.reject');
    Route::post('/vendor/status',     [\App\Http\Controllers\ShopVendorManageController::class, 'toggleStatus'])->name('shop.vendor.status');
    Route::get('/vendor-earning',     [\App\Http\Controllers\ShopVendorEarningController::class, 'index'])->name('shop.vendor_earning.index');
    Route::get('/vendor-earning/list',[\App\Http\Controllers\ShopVendorEarningController::class, 'getList'])->name('shop.vendor_earning.list');

    Route::get('/delivery-staff',           [\App\Http\Controllers\ShopDeliveryStaffController::class, 'index'])->name('shop.delivery.index');
    Route::get('/delivery-staff/list',      [\App\Http\Controllers\ShopDeliveryStaffController::class, 'getList'])->name('shop.delivery.list');
    Route::post('/delivery-staff/store',    [\App\Http\Controllers\ShopDeliveryStaffController::class, 'store'])->name('shop.delivery.store');
    Route::get('/delivery-staff/edit/{id}', [\App\Http\Controllers\ShopDeliveryStaffController::class, 'edit'])->name('shop.delivery.edit');
    Route::post('/delivery-staff/delete',   [\App\Http\Controllers\ShopDeliveryStaffController::class, 'destroy'])->name('shop.delivery.delete');
    Route::get('/delivery-staff/ajax',      [\App\Http\Controllers\ShopDeliveryStaffController::class, 'ajaxList'])->name('shop.ajax.delivery_staff');

    Route::get('/order',              [\App\Http\Controllers\ShopOrderController::class, 'index'])->name('shop.order.index');
    Route::get('/order/list',         [\App\Http\Controllers\ShopOrderController::class, 'getList'])->name('shop.order.list');
    Route::get('/order/show/{id}',    [\App\Http\Controllers\ShopOrderController::class, 'show'])->name('shop.order.show');
    Route::post('/order/status',      [\App\Http\Controllers\ShopOrderController::class, 'updateStatus'])->name('shop.order.status');
    Route::get('/order/invoice/{id}', [\App\Http\Controllers\ShopOrderController::class, 'generateInvoice'])->name('shop.order.invoice');
    Route::post('/order/assign',      [\App\Http\Controllers\ShopOrderController::class, 'assignDelivery'])->name('shop.order.assign');

    Route::get('/delivery-assign',      [\App\Http\Controllers\ShopDeliveryAssignmentController::class, 'index'])->name('shop.delivery_assign.index');
    Route::get('/delivery-assign/list', [\App\Http\Controllers\ShopDeliveryAssignmentController::class, 'getList'])->name('shop.delivery_assign.list');

    Route::get('/return',             [\App\Http\Controllers\ShopReturnController::class, 'index'])->name('shop.return.index');
    Route::get('/return/list',        [\App\Http\Controllers\ShopReturnController::class, 'getList'])->name('shop.return.list');
    Route::get('/return/show/{id}',   [\App\Http\Controllers\ShopReturnController::class, 'show'])->name('shop.return.show');
    Route::post('/return/approve',    [\App\Http\Controllers\ShopReturnController::class, 'approve'])->name('shop.return.approve');
    Route::post('/return/reject',     [\App\Http\Controllers\ShopReturnController::class, 'reject'])->name('shop.return.reject');

    Route::get('/review',          [\App\Http\Controllers\ShopReviewController::class, 'index'])->name('shop.review.index');
    Route::get('/review/list',     [\App\Http\Controllers\ShopReviewController::class, 'getList'])->name('shop.review.list');
    Route::post('/review/approve', [\App\Http\Controllers\ShopReviewController::class, 'approve'])->name('shop.review.approve');
    Route::post('/review/reply',   [\App\Http\Controllers\ShopReviewController::class, 'reply'])->name('shop.review.reply');
    Route::post('/review/delete',  [\App\Http\Controllers\ShopReviewController::class, 'destroy'])->name('shop.review.delete');

    Route::get('/coupon',           [\App\Http\Controllers\ShopCouponController::class, 'index'])->name('shop.coupon.index');
    Route::post('/coupon/store',    [\App\Http\Controllers\ShopCouponController::class, 'store'])->name('shop.coupons.store');
    Route::post('/coupon/update/{id}', [\App\Http\Controllers\ShopCouponController::class, 'update'])->name('shop.coupons.update');
    Route::post('/coupon/delete/{id}', [\App\Http\Controllers\ShopCouponController::class, 'destroy'])->name('shop.coupons.delete');

    Route::get('/banner',           [\App\Http\Controllers\ShopBannerController::class, 'index'])->name('shop.banner.index');
    Route::get('/banner/list',      [\App\Http\Controllers\ShopBannerController::class, 'getList'])->name('shop.banner.list');
    Route::post('/banner/store',    [\App\Http\Controllers\ShopBannerController::class, 'store'])->name('shop.banner.store');
    Route::get('/banner/edit/{id}', [\App\Http\Controllers\ShopBannerController::class, 'edit'])->name('shop.banner.edit');
    Route::post('/banner/delete',   [\App\Http\Controllers\ShopBannerController::class, 'destroy'])->name('shop.banner.delete');

    Route::get('/cms',           [\App\Http\Controllers\ShopCmsController::class, 'index'])->name('shop.cms.index');
    Route::get('/cms/list',      [\App\Http\Controllers\ShopCmsController::class, 'getList'])->name('shop.cms.list');
    Route::post('/cms/store',    [\App\Http\Controllers\ShopCmsController::class, 'store'])->name('shop.cms.store');
    Route::get('/cms/edit/{id}', [\App\Http\Controllers\ShopCmsController::class, 'edit'])->name('shop.cms.edit');
    Route::post('/cms/delete',   [\App\Http\Controllers\ShopCmsController::class, 'destroy'])->name('shop.cms.delete');

    Route::get('/blog',           [\App\Http\Controllers\ShopBlogController::class, 'index'])->name('shop.blog.index');
    Route::get('/blog/list',      [\App\Http\Controllers\ShopBlogController::class, 'getList'])->name('shop.blog.list');
    Route::post('/blog/store',    [\App\Http\Controllers\ShopBlogController::class, 'store'])->name('shop.blog.store');
    Route::get('/blog/edit/{id}', [\App\Http\Controllers\ShopBlogController::class, 'edit'])->name('shop.blog.edit');
    Route::post('/blog/delete',   [\App\Http\Controllers\ShopBlogController::class, 'destroy'])->name('shop.blog.delete');

    Route::get('/report/sales',     [\App\Http\Controllers\ShopReportController::class, 'salesReport'])->name('shop.report.sales');
    Route::get('/report/inventory', [\App\Http\Controllers\ShopReportController::class, 'inventoryReport'])->name('shop.report.inventory');
    Route::get('/report/vendor',    [\App\Http\Controllers\ShopReportController::class, 'vendorReport'])->name('shop.report.vendor');
    Route::get('/report/customer',  [\App\Http\Controllers\ShopReportController::class, 'customerReport'])->name('shop.report.customer');

    Route::get('/settings',       [\App\Http\Controllers\ShopSettingController::class, 'index'])->name('shop.setting.index');
    Route::post('/settings/save', [\App\Http\Controllers\ShopSettingController::class, 'save'])->name('shop.setting.save');

    // Role & Permission Management
    Route::get('/roles/permissions',              [\App\Http\Controllers\ShopRolePermissionController::class, 'index'])->name('shop.roles.permissions.index');
    Route::get('/roles/permissions/{roleId}',     [\App\Http\Controllers\ShopRolePermissionController::class, 'getPermissions'])->name('shop.roles.permissions.get');
    Route::post('/roles/permissions/save',        [\App\Http\Controllers\ShopRolePermissionController::class, 'savePermissions'])->name('shop.roles.permissions.save');
    Route::post('/roles/create',                  [\App\Http\Controllers\ShopRolePermissionController::class, 'createRole'])->name('shop.roles.create');
    Route::post('/roles/delete',                  [\App\Http\Controllers\ShopRolePermissionController::class, 'deleteRole'])->name('shop.roles.delete');

    Route::get('/activity-log',        [\App\Http\Controllers\ShopActivityLogController::class, 'index'])->name('shop.activity_log.index');
    Route::get('/activity-log/list',   [\App\Http\Controllers\ShopActivityLogController::class, 'getList'])->name('shop.activity_log.list');

    Route::get('/profile',                   [\App\Http\Controllers\ShopProfileController::class, 'index'])->name('shop.profile');
    Route::post('/profile/update',           [\App\Http\Controllers\ShopProfileController::class, 'updateProfile'])->name('shop.profile.update');
    Route::get('/change-password',           [\App\Http\Controllers\ShopProfileController::class, 'showChangePassword'])->name('shop.change_password');
    Route::post('/change-password/update',   [\App\Http\Controllers\ShopProfileController::class, 'updatePassword'])->name('shop.change_password.update');
});

// ── Vendor Panel Routes ──────────────────────────────────────────────
Route::prefix('vendor')->middleware([VendorIsValid::class])->group(function () {
    Route::get('/dashboard', [ShopDashboardController::class, 'index'])->name('vendor.dashboard');
    Route::get('/product',           [\App\Http\Controllers\ShopProductController::class, 'vendorIndex'])->name('vendor.product.index');
    Route::get('/product/list',      [\App\Http\Controllers\ShopProductController::class, 'getList'])->name('vendor.product.list');
    Route::post('/product/store',    [\App\Http\Controllers\ShopProductController::class, 'vendorStore'])->name('vendor.product.store');
    Route::get('/product/edit/{id}', [\App\Http\Controllers\ShopProductController::class, 'vendorEdit'])->name('vendor.product.edit');
    Route::post('/product/delete',   [\App\Http\Controllers\ShopProductController::class, 'vendorDestroy'])->name('vendor.product.delete');
    Route::get('/inventory',         [\App\Http\Controllers\ShopInventoryController::class, 'vendorIndex'])->name('vendor.inventory.index');
    Route::get('/order',             [\App\Http\Controllers\ShopOrderController::class, 'vendorIndex'])->name('vendor.order.index');
    Route::get('/order/list',        [\App\Http\Controllers\ShopOrderController::class, 'vendorList'])->name('vendor.order.list');
    Route::get('/order/show/{id}',   [\App\Http\Controllers\ShopOrderController::class, 'vendorShow'])->name('vendor.order.show');
    Route::get('/earning',           [\App\Http\Controllers\ShopVendorEarningController::class, 'vendorIndex'])->name('vendor.earning.index');
    Route::get('/earning/list',      [\App\Http\Controllers\ShopVendorEarningController::class, 'vendorEarningList'])->name('vendor.earning.list');
    Route::get('/report',            [\App\Http\Controllers\ShopReportController::class, 'vendorDashboard'])->name('vendor.report.index');
    Route::post('/logout',           [ShopAdminLoginController::class, 'shopLogout'])->name('vendor.logout');
});

// ── Delivery Staff Routes ────────────────────────────────────────────
Route::prefix('delivery')->middleware([DeliveryStaffIsValid::class])->group(function () {
    Route::get('/dashboard',          [ShopDashboardController::class, 'index'])->name('delivery.dashboard');
    Route::get('/orders',             [\App\Http\Controllers\ShopDeliveryStaffController::class, 'myOrders'])->name('delivery.order.index');
    Route::get('/orders/show/{id}',   [\App\Http\Controllers\ShopDeliveryStaffController::class, 'showOrder'])->name('delivery.order.show');
    Route::post('/orders/status',     [\App\Http\Controllers\ShopDeliveryStaffController::class, 'updateStatus'])->name('delivery.order.status');
    Route::post('/orders/otp-verify', [\App\Http\Controllers\ShopDeliveryStaffController::class, 'verifyOtp'])->name('delivery.order.otp_verify');
    Route::get('/history',            [\App\Http\Controllers\ShopDeliveryStaffController::class, 'history'])->name('delivery.history.index');
    Route::post('/logout',            [ShopAdminLoginController::class, 'shopLogout'])->name('delivery.logout');
});

// ── Customer Storefront Routes (Auth-required) ───────────────────────
Route::prefix('store/account')->middleware([CustomerIsValid::class])->group(function () {
    Route::get('/profile',          [\App\Http\Controllers\ShopStorefrontController::class, 'profile'])->name('store.profile');
    Route::post('/profile/update',  [\App\Http\Controllers\ShopStorefrontController::class, 'updateProfile'])->name('store.profile.update');
    Route::get('/orders',           [\App\Http\Controllers\ShopStorefrontController::class, 'orders'])->name('store.orders');
    Route::get('/orders/{id}',      [\App\Http\Controllers\ShopStorefrontController::class, 'orderDetail'])->name('store.order.detail');
    Route::get('/orders/{id}/invoice', [\App\Http\Controllers\ShopStorefrontController::class, 'downloadInvoice'])->name('store.order.invoice');
    Route::post('/orders/{id}/cancel', [\App\Http\Controllers\ShopStorefrontController::class, 'cancelOrder'])->name('store.order.cancel');
    Route::post('/orders/{id}/return', [\App\Http\Controllers\ShopStorefrontController::class, 'requestReturn'])->name('store.order.return');
    Route::get('/wishlist',         [\App\Http\Controllers\ShopWishlistController::class, 'index'])->name('store.wishlist');
    Route::get('/address',          [\App\Http\Controllers\ShopAddressController::class, 'index'])->name('store.address');
    Route::post('/address/store',   [\App\Http\Controllers\ShopAddressController::class, 'store'])->name('store.address.store');
    Route::post('/address/delete',  [\App\Http\Controllers\ShopAddressController::class, 'destroy'])->name('store.address.delete');
    Route::post('/address/default', [\App\Http\Controllers\ShopAddressController::class, 'setDefault'])->name('store.address.default');
    Route::post('/logout',          [ShopAdminLoginController::class, 'shopLogout'])->name('store.logout');
});

// ── Public Storefront Routes (No Auth Required) ──────────────────────
Route::prefix('store')->name('store.')->group(function () {
    Route::get('/',               [\App\Http\Controllers\ShopStorefrontController::class, 'home'])->name('home');
    Route::get('/products',       [\App\Http\Controllers\ShopStorefrontController::class, 'productList'])->name('products');
    Route::get('/product/{slug}', [\App\Http\Controllers\ShopStorefrontController::class, 'productDetail'])->name('product.detail');
    Route::get('/category/{slug}',[\App\Http\Controllers\ShopStorefrontController::class, 'categoryPage'])->name('category');
    Route::get('/brand/{slug}',   [\App\Http\Controllers\ShopStorefrontController::class, 'brandPage'])->name('brand');
    Route::get('/search',         [\App\Http\Controllers\ShopStorefrontController::class, 'search'])->name('search');
    Route::get('/blog',           [\App\Http\Controllers\ShopBlogController::class, 'publicIndex'])->name('blog');
    Route::get('/blog/{slug}',    [\App\Http\Controllers\ShopBlogController::class, 'publicShow'])->name('blog.show');
    Route::get('/page/{slug}',    [\App\Http\Controllers\ShopCmsController::class, 'publicShow'])->name('page');
    Route::get('/cart',           [\App\Http\Controllers\ShopCartController::class, 'index'])->name('cart');
    Route::post('/cart/add',      [\App\Http\Controllers\ShopCartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update',   [\App\Http\Controllers\ShopCartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove',   [\App\Http\Controllers\ShopCartController::class, 'remove'])->name('cart.remove');
    Route::post('/wishlist/toggle',[\App\Http\Controllers\ShopWishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/checkout',       [\App\Http\Controllers\ShopCheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/place',[\App\Http\Controllers\ShopCheckoutController::class, 'placeOrder'])->name('checkout.place');
    Route::post('/review/submit', [\App\Http\Controllers\ShopReviewController::class, 'customerSubmit'])->name('review.submit');
    Route::post('/return/submit', [\App\Http\Controllers\ShopReturnController::class, 'customerSubmit'])->name('return.submit');
    Route::get('/track/{order}',  [\App\Http\Controllers\ShopStorefrontController::class, 'trackOrder'])->name('track');
});
