<?php
use App\Http\Controllers\Company;
use App\Http\Controllers\Company_setting;
use App\Http\Controllers\CrmActivityController;
use App\Http\Controllers\LeadTimelineController;
use App\Http\Controllers\Login;
use App\Http\Controllers\Role_permission;
use App\Http\Controllers\Staff;
use App\Http\Controllers\Superadmin;
use App\Http\Controllers\Super_menu_submenu;
use App\Http\Controllers\IndustryTypeController;
use App\Http\Controllers\CampaignTypeController;
use App\Http\Controllers\EmailComposeController;
use App\Http\Controllers\CrmReportController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\NoteTitleController;
use App\Http\Controllers\TooltipController;
use App\Http\Controllers\TermsController;
use App\Http\Controllers\GstController;
use App\Http\Controllers\VatController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\GetDataController;
use App\Http\Controllers\DealReportController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\AlternateUnitController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\MailConfig;
use App\Http\Controllers\EnquirySource; // prit
use App\Http\Controllers\EnquiryStatus;
use App\Http\Controllers\DealStageController;
use App\Http\Controllers\HsnGstMapController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\City;
use App\Http\Controllers\Country;
use App\Http\Controllers\CrmAttachmentController;
use App\Http\Controllers\CrmCallController;
use App\Http\Controllers\CrmNoteController;
use App\Http\Controllers\State;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\StockGroupController;
use App\Http\Controllers\StockCategoryController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadDetailsController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DealDetailsController;
use App\Http\Controllers\LeadReportController; // prit 
use App\Http\Controllers\PerformaInvoiceController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\CampaignReportController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\MainInvoiceController;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckStaffPermission;
use App\Http\Middleware\CompanyIsValid;
use App\Http\Middleware\CompanyORSuperadmin;
use App\Http\Middleware\SuperAdminIsValid;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $isLoggedIn = session('login_type');

    if ($isLoggedIn) {
        return redirect()->route(
            $isLoggedIn === 'Super Admin'
            ? 'main.dashboard'
            : 'dashboard'
        );
    }

    return view('login');
})->name('login');

Route::get('quick/search', [Login::class, 'quick_search'])->name('quick.search');
Route::post('/logout', [Login::class, 'logout'])->name('logout');

Route::post('/login/process', [Login::class, 'login_process'])->name('login.process');
Route::post('/forgot-password/process', [Login::class, 'forgot_password_process'])->name('forgot.password.process');
Route::get('/reset-password/{token}', [Login::class, 'show_reset_password'])->name('password.reset');
Route::post('/reset-password', [Login::class, 'reset_password_process'])->name('password.reset.process');
  Route::get('crm/activity/notes/export/excel/{reference_type}/{reference_id}', [CrmNoteController::class, 'exportExcel'])->name('crm.activity.notes.export.excel');
    Route::get('crm/activity/notes/export/pdf/{reference_type}/{reference_id}', [CrmNoteController::class, 'exportPdf'])->name('crm.activity.notes.export.pdf');
Route::middleware([CompanyIsValid::class])->controller(Company::class)->group(function () {
    Route::post('/commondelete/{id}', [Company::class, 'destroy'])->name('commondelete')->middleware(CheckPermission::class . ':delete');
    Route::get('/dashboard', [Company::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/data', [Company::class, 'getDashboardData'])->name('dashboard.data');
  
   
    //crm report
     Route::get('report/crm', [CrmReportController::class, 'crmReport'])->name('crm_report.index')->middleware(CheckPermission::class . ':view');
    Route::get('report/crm/list', [CrmReportController::class, 'crmReportList'])->name('crm_report.list');

    //company documents module
    Route::get('documents', [DocumentController::class, 'index'])->name('documents.index')->middleware(CheckPermission::class . ':view');
    Route::post('document/category/store', [DocumentController::class, 'category_store'])->name('document.category.store')->middleware(CheckPermission::class . ':create');
    Route::get('document/category/get', [DocumentController::class, 'get_document_category'])->name('document.category.get')->middleware(CheckPermission::class . ':view');
    Route::get('document/category/update', [DocumentController::class, 'category_update'])->name('document.category.update')->middleware(CheckPermission::class . ':update');
    Route::post('document/category/delete', [DocumentController::class, 'category_delete'])->name('document.category.delete')->middleware(CheckPermission::class . ':delete');

    Route::get('/documents/category/{encrypted_id}', [DocumentController::class, 'show'])->name('document.category.show');
    Route::post('/documents/mngt/store', [DocumentController::class, 'document_store'])->name('document.mngt.store');
    Route::get('/documents/mngt/list/{encrypted_id}', [DocumentController::class, 'document_list'])->name('document.mngt.list');
    Route::post('/documents/mngt/delete', [DocumentController::class, 'document_delete'])->name('document.mngt.delete');
    Route::post('/documents/mngt/file-delete', [DocumentController::class, 'document_file_delete'])->name('document.mngt.file.delete');
    Route::post('/documents/mngt/update', [DocumentController::class, 'document_update'])->name('document.mngt.update');

    //industry type master
    Route::get('industry-type', [IndustryTypeController::class, 'index'])->name('industry_type.index')->middleware(CheckPermission::class . ':view');
    Route::post('industry-type/save', [IndustryTypeController::class, 'storeOrUpdate'])->name('industry_type.store')->middleware(CheckPermission::class . ':create');
    Route::get('industry-type/list', [IndustryTypeController::class, 'getList'])->name('industry_type.list')->middleware(CheckPermission::class . ':view');
    Route::get('industry-type/edit/{id}', [IndustryTypeController::class, 'edit'])->name('industry_type.edit')->middleware(CheckPermission::class . ':update');
    Route::post('industry-type/delete', [IndustryTypeController::class, 'destroy'])->name('industry_type.destroy')->middleware(CheckPermission::class . ':delete');

    //campaign -type master
    Route::get('campaign-type', [CampaignTypeController::class, 'index'])->name('campaign_type.index')->middleware(CheckPermission::class . ':view');
    Route::post('campaign-type/save', [CampaignTypeController::class, 'storeOrUpdate'])->name('campaign_type.store')->middleware(CheckPermission::class . ':create');
    Route::get('campaign-type/list', [CampaignTypeController::class, 'getList'])->name('campaign_type.list')->middleware(CheckPermission::class . ':view');
    Route::get('campaign-type/edit/{id}', [CampaignTypeController::class, 'edit'])->name('campaign_type.edit')->middleware(CheckPermission::class . ':update');
    Route::post('campaign-type/delete', [CampaignTypeController::class, 'destroy'])->name('campaign_type.destroy')->middleware(CheckPermission::class . ':delete');

    //campaign master
    Route::get('campaign', [CampaignController::class, 'index'])->name('campaign.index')->middleware(CheckPermission::class . ':view');
    Route::post('campaign/save', [CampaignController::class, 'storeOrUpdate'])->name('campaign.store')->middleware(CheckPermission::class . ':create');
    Route::get('campaign/list', [CampaignController::class, 'getList'])->name('campaign.list')->middleware(CheckPermission::class . ':view');
    Route::get('campaign/edit/{id}', [CampaignController::class, 'edit'])->name('campaign.edit')->middleware(CheckPermission::class . ':update');
    Route::post('campaign/delete', [CampaignController::class, 'destroy'])->name('campaign.destroy')->middleware(CheckPermission::class . ':delete');
    Route::get('campaign/history/{id}', [CampaignController::class, 'getHistory'])->name('campaign.history');
    Route::get('dashboard/campaign-overview', [CampaignController::class, 'dashboardOverview'])->name('dashboard.campaign-overview');
    Route::get('campaign/view/{id}', [CampaignController::class, 'show'])->name('campaign.view')->middleware(CheckPermission::class . ':view');

    //product master
    Route::get('products', [ProductController::class, 'index'])->name('products.index')->middleware(CheckPermission::class . ':view');
    Route::post('products/save', [ProductController::class, 'storeOrUpdate'])->name('products.store')->middleware(CheckPermission::class . ':create');
    Route::get('products/list', [ProductController::class, 'getList'])->name('products.list')->middleware(CheckPermission::class . ':view');
    Route::get('products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit')->middleware(CheckPermission::class . ':update');
    Route::post('products/delete', [ProductController::class, 'destroy'])->name('products.destroy')->middleware(CheckPermission::class . ':delete');
     Route::get('product/latest-options', [ProductController::class, 'getLatestOptions'])->name('product.latest_options');
      Route::post('ProductStore', [ProductController::class, 'store'])->name('ProductStore');
      Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    // product category
    Route::get('productcategory', [ProductCategoryController::class, 'index'])->name('productcategory.index')->middleware(CheckPermission::class . ':view');
    Route::post('productcategory/save', [ProductCategoryController::class, 'storeOrUpdate'])->name('productcategory.store');
    Route::get('productcategory/list', [ProductCategoryController::class, 'getList'])->name('productcategory.list');
    Route::get('productcategory/edit/{id}', [ProductCategoryController::class, 'edit'])->name('productcategory.edit');
    Route::post('productcategory/delete', [ProductCategoryController::class, 'destroy'])->name('productcategory.destroy');

    // *---- Deal Routes ----*
    Route::get('deal', [DealController::class, 'index'])->name('deal.index')->middleware(CheckPermission::class . ':view');
    Route::get('deal/kanban-data', [DealController::class, 'getKanbanData'])->name('deal.kanban.data');
    Route::post('deal/update-stage/{deal}', [DealController::class, 'updateStage'])->name('deal.update-stage');
    Route::get('deal/edit/{id}', [DealController::class, 'editDeal'])->name('deal.edit');
    Route::post('deal/update/{id}', [DealController::class, 'updateDeal'])->name('deal.update');
    Route::post('/deals/closed-lost', [DealController::class, 'closedLost'])->name('deal.closed-lost');
    Route::get('deals/get-lead-details/{lead}', [DealController::class, 'getDetailsForClosedWon'])->name('deal.details.for.closed-won');
    Route::post('/deals/closed-won', [DealController::class, 'closedWon'])->name('deal.closed-won');
    Route::post('/deals/create-direct', [DealController::class, 'createDirect'])->name('deal.create-direct');
    Route::get('/deals/customers-list', [DealController::class, 'getCustomersList'])->name('deal.customers-list');

    // *---- Deal Details / Schedule Call / Notes / Attachments Routes ----*
    Route::get('deal/details/{id}', [DealDetailsController::class, 'details'])->name('deal.details')->middleware(CheckPermission::class . ':view');
    Route::post('deal/details/update/{id}', [DealDetailsController::class, 'updateDetails'])->name('deal.details.update');
    Route::post('deal/details/schedule-call', [DealController::class, 'scheduleCall'])->name('deal.details.schedule-call');
    Route::post('deal/details/update-call', [DealController::class, 'updateCall'])->name('deal.details.update-call');
    Route::post('deal/details/add-note', [DealController::class, 'addNote'])->name('deal.details.add-note');
    Route::post('deal/details/update-note', [DealController::class, 'updateNote'])->name('deal.details.update-note');
    Route::post('deal/details/upload-attachment', [DealController::class, 'uploadAttachment'])->name('deal.details.upload-attachment');
    Route::get('deal/details/calls/{deal_id}', [DealController::class, 'getCalls'])->name('deal.details.get-calls');
    Route::get('deal/details/notes/{deal_id}', [DealController::class, 'getNotes'])->name('deal.details.get-notes');
    Route::get('deal/details/attachments/{deal_id}', [DealController::class, 'getAttachments'])->name('deal.details.get-attachments');
    Route::get('deal/details/logs/{deal_id}', [DealController::class, 'getLogs'])->name('deal.details.get-logs');
    Route::get('dashboard/scheduled-calls', [DealController::class, 'getDashboardCalls'])->name('dashboard.scheduled-calls');
    Route::post('deal/details/delete-call', [DealController::class, 'deleteCall'])->name('deal.details.delete-call');
    Route::post('deal/details/update-call-status', [DealController::class, 'updateCallStatus'])->name('deal.details.update-call-status');
    Route::post('deal/details/delete-note', [DealController::class, 'deleteNote'])->name('deal.details.delete-note');
    Route::post('deal/details/delete-attachment', [DealController::class, 'deleteAttachment'])->name('deal.details.delete-attachment');
//dummy add 
     Route::get('hsn-gst-map', [HsnGstMapController::class, 'index'])->name('hsn_gst.index')->middleware(CheckPermission::class . ':view');
    Route::post('hsn-gst-map/save', [HsnGstMapController::class, 'storeOrUpdate'])->name('hsn_gst.store')->middleware(CheckPermission::class . ':create');
    Route::get('hsn-gst-map/list', [HsnGstMapController::class, 'getList'])->name('hsn_gst.list')->middleware(CheckPermission::class . ':view');
    Route::get('hsn-gst-map/edit/{id}', [HsnGstMapController::class, 'edit'])->name('hsn_gst.edit')->middleware(CheckPermission::class . ':update');
    Route::post('hsn-gst-map/delete', [HsnGstMapController::class, 'destroy'])->name('hsn_gst.destroy')->middleware(CheckPermission::class . ':delete');
    Route::get('/customer/data', [CustomerController::class, 'getAllData'])->name('customer.data');

    Route::get('unit', [UnitController::class, 'index'])->name('unit.index')->middleware(CheckPermission::class . ':view');
    Route::post('unit/save', [UnitController::class, 'storeOrUpdate'])->name('unit.store')->middleware(CheckPermission::class . ':create');
    Route::get('unit/list', [UnitController::class, 'getList'])->name('unit.list')->middleware(CheckPermission::class . ':view');
    Route::get('unit/edit/{id}', [UnitController::class, 'edit'])->name('unit.edit')->middleware(CheckPermission::class . ':update');
    Route::post('unit/delete', [UnitController::class, 'destroy'])->name('unit.destroy')->middleware(CheckPermission::class . ':delete');

    //note title master module 
     Route::get('note-title', [NoteTitleController::class, 'index'])->name('note-titles.index')->middleware(CheckPermission::class . ':view');
    Route::post('note-title/save', [NoteTitleController::class, 'storeOrUpdate'])->name('note-titles.store')->middleware(CheckPermission::class . ':create');
    Route::get('note-title/list', [NoteTitleController::class, 'getList'])->name('note-titles.list')->middleware(CheckPermission::class . ':view');
    Route::get('note-title/edit/{id}', [NoteTitleController::class, 'edit'])->name('note-titles.edit')->middleware(CheckPermission::class . ':update');
    Route::post('note-title/delete', [NoteTitleController::class, 'destroy'])->name('note-titles.destroy')->middleware(CheckPermission::class . ':delete');

    //tooltip master module
    Route::get('tooltip', [TooltipController::class, 'index'])->name('tooltips.index')->middleware(CheckPermission::class . ':view');
    Route::post('tooltip/save', [TooltipController::class, 'storeOrUpdate'])->name('tooltips.store')->middleware(CheckPermission::class . ':create');
    Route::get('tooltip/list', [TooltipController::class, 'getList'])->name('tooltips.list')->middleware(CheckPermission::class . ':view');
    Route::get('tooltip/edit/{id}', [TooltipController::class, 'edit'])->name('tooltips.edit')->middleware(CheckPermission::class . ':update');
    Route::post('tooltip/delete', [TooltipController::class, 'destroy'])->name('tooltips.destroy')->middleware(CheckPermission::class . ':delete');
    Route::get('tooltip/get/{key}', [TooltipController::class, 'getTooltipByKey'])->name('tooltips.get');


     Route::get('gst', [GstController::class, 'index'])->name('gst.index')->middleware(CheckPermission::class . ':view');
    Route::post('gst/save', [GstController::class, 'storeOrUpdate'])->name('gst.store')->middleware(CheckPermission::class . ':create');
    Route::get('gst/list', [GstController::class, 'getList'])->name('gst.list')->middleware(CheckPermission::class . ':view');
    Route::get('gst/edit/{id}', [GstController::class, 'edit'])->name('gst.edit')->middleware(CheckPermission::class . ':update');
    Route::post('gst/delete', [GstController::class, 'destroy'])->name('gst.destroy')->middleware(CheckPermission::class . ':delete');

    //vat
     Route::get('vat', [VatController::class, 'index'])->name('vat.index')->middleware(CheckPermission::class . ':view');
    Route::post('vat/save', [VatController::class, 'storeOrUpdate'])->name('vat.store')->middleware(CheckPermission::class . ':create');
    Route::get('vat/list', [VatController::class, 'getList'])->name('vat.list')->middleware(CheckPermission::class . ':view');
    Route::get('vat/edit/{id}', [VatController::class, 'edit'])->name('vat.edit')->middleware(CheckPermission::class . ':update');
    Route::post('vat/delete', [VatController::class, 'destroy'])->name('vat.destroy')->middleware(CheckPermission::class . ':delete');

     Route::get('stockgroup', [StockGroupController::class, 'index'])->name('stockgroup.index')->middleware(CheckPermission::class . ':view');
    Route::post('stockgroup/save', [StockGroupController::class, 'storeOrUpdate'])->name('stockgroup.store');
    Route::get('stockgroup/list', [StockGroupController::class, 'getList'])->name('stockgroup.list');
    Route::get('stockgroup/edit/{id}', [StockGroupController::class, 'edit'])->name('stockgroup.edit');
    Route::post('stockgroup/delete', [StockGroupController::class, 'destroy'])->name('stockgroup.destroy');

    Route::get('stockcategory', [StockCategoryController::class, 'index'])->name('stockcategory.index')->middleware(CheckPermission::class . ':view');
    Route::post('stockcategory/save', [StockCategoryController::class, 'storeOrUpdate'])->name('stockcategory.store');
    Route::get('stockcategory/list', [StockCategoryController::class, 'getList'])->name('stockcategory.list');
    Route::get('stockcategory/edit/{id}', [StockCategoryController::class, 'edit'])->name('stockcategory.edit');
    Route::post('stockcategory/delete', [StockCategoryController::class, 'destroy'])->name('stockcategory.destroy');

     Route::get('altunit', [AlternateUnitController::class, 'index'])->name('altunit.index')->middleware(CheckPermission::class . ':view');
    Route::post('altunit/save', [AlternateUnitController::class, 'storeOrUpdate'])->name('altunit.store');
    Route::get('altunit/list', [AlternateUnitController::class, 'getList'])->name('altunit.list');
    Route::get('altunit/edit/{id}', [AlternateUnitController::class, 'edit'])->name('altunit.edit');
    Route::post('altunit/delete', [AlternateUnitController::class, 'destroy'])->name('altunit.destroy');
     // raw material
    Route::get('rawmaterial', [RawMaterialController::class, 'index'])->name('rawmaterial.index')->middleware(CheckPermission::class . ':view');
    Route::get('rawmaterial/categories', [RawMaterialController::class, 'getCategoriesByGroup'])->name('rawmaterial.categories');
    Route::post('rawmaterial/save', [RawMaterialController::class, 'storeOrUpdate'])->name('rawmaterial.store');
    Route::get('rawmaterial/list', [RawMaterialController::class, 'getList'])->name('rawmaterial.list');
    Route::get('rawmaterial/edit/{id}', [RawMaterialController::class, 'edit'])->name('rawmaterial.edit');
    Route::post('rawmaterial/delete', [RawMaterialController::class, 'destroy'])->name('rawmaterial.destroy');

    Route::get('terms', [TermsController::class, 'index'])->name('terms.index');
    Route::get('TermsList', [TermsController::class, 'getList'])->name('terms.list');
    Route::post('TermsStore', [TermsController::class, 'store'])->name('terms.store');
    Route::post('terms/delete', [TermsController::class, 'destroy'])->name('terms.destroy');
    Route::get('terms/edit/{id}', [TermsController::class, 'edit'])->name('terms.edit');

    //customer 
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index')->middleware(CheckPermission::class . ':view');
    Route::get('/customer/city-search', [CustomerController::class, 'searchCity'])->name('customer.city.search');
    Route::get('/customer/city-details/{id}', [CustomerController::class, 'getCityDetails'])->name('customer.city.details');

    // Route::get('/customer/location-list', [CustomerController::class, 'getLocationLists'])->name('customer.location.list');
    Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/customer/list', [CustomerController::class, 'list'])->name('customer.list');
    Route::get('/customer/{id}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::post('/customer/delete', [CustomerController::class, 'delete'])->name('customer.delete');

    // AJAX customer creation
    Route::post('/ajax/customer/store', [CustomerController::class, 'ajaxStore'])->name('ajax.customer.store');
    // *---- CRM Activity Routes (shared for Deal & Lead via reference_type + reference_id) ----*
    // -- Calls --
    Route::post('crm/activity/schedule-call', [CrmCallController::class, 'scheduleCall'])->name('crm.activity.schedule-call');
    Route::post('crm/activity/update-call', [CrmCallController::class, 'updateCall'])->name('crm.activity.update-call');
    Route::get('crm/activity/calls/{reference_type}/{reference_id}', [CrmCallController::class, 'getCalls'])->name('crm.activity.get-calls');
    Route::post('crm/activity/update-call-status', [CrmCallController::class, 'updateCallStatus'])->name('crm.activity.update-call-status');
    Route::post('crm/activity/delete-call', [CrmCallController::class, 'deleteCall'])->name('crm.activity.delete-call');

    // -- Notes --
    Route::post('crm/activity/add-note', [CrmNoteController::class, 'addNote'])->name('crm.activity.add-note');
    Route::post('crm/activity/update-note', [CrmNoteController::class, 'updateNote'])->name('crm.activity.update-note');
    Route::post('crm/activity/update-note-status', [CrmNoteController::class, 'updateNoteStatus'])->name('crm.activity.update-note-status');
    Route::get('crm/activity/notes/{reference_type}/{reference_id}', [CrmNoteController::class, 'getNotes'])->name('crm.activity.get-notes');
    Route::post('crm/activity/delete-note', [CrmNoteController::class, 'deleteNote'])->name('crm.activity.delete-note');
  

    // -- Attachments --
        Route::post('crm/activity/upload-attachment', [CrmAttachmentController::class, 'uploadAttachment'])->name('crm.activity.upload-attachment');
        Route::get('crm/activity/attachments/{reference_type}/{reference_id}', [CrmAttachmentController::class, 'getAttachments'])->name('crm.activity.get-attachments');
        Route::post('crm/activity/delete-attachment', [CrmAttachmentController::class, 'deleteAttachment'])->name('crm.activity.delete-attachment');
    // deal report 
    Route::get('report/deal', [DealReportController::class, 'dealReport'])->name('deal_report.index')->middleware(CheckPermission::class . ':view');
    Route::get('report/deal/list', [DealReportController::class, 'dealReportList'])->name('deal_report.list');
    Route::get('report/deal/export/excel', [DealReportController::class, 'exportDealExcel'])->name('deal_report.export.excel');
    Route::get('report/deal/export/pdf', [DealReportController::class, 'exportDealPdf'])->name('deal_report.export.pdf');

    // *---- Quotation Routes ----*
    Route::get('/quotation/create/{dealId}', [QuotationController::class, 'create'])->name('quotation.create');
    Route::post('/quotation/store', [QuotationController::class, 'store'])->name('quotation.store');
    Route::get('/quotation/revision-data/{id}', [QuotationController::class, 'getRevisionData'])->name('quotation.revision.data');
    Route::get('/quotation', [QuotationController::class, 'index'])->name('quotation.index');
    Route::post('/quotation/send-email', [QuotationController::class, 'sendQuotationEmail'])->name('quotation.send.email');
    Route::get('/quotation/list', [QuotationController::class, 'list'])->name('quotation.list');
    Route::get('/quotation/history', [QuotationController::class, 'getQuotHistory'])->name('quotation.history');
    Route::get('/quotation/direct/{Id?}', [QuotationController::class, 'direct'])->name('direct_quotation.index');
    //compose module 
      Route::post('/compose-email/send', [EmailComposeController::class, 'send'])->name('compose.email.send');

     // Global helper APIs for product modal
    Route::get('/get-units-global', [GetDataController::class, 'getUnits'])->name('get.units.global');
    Route::get('/get-gst-global', [GetDataController::class, 'getGst'])->name('get.gst.global');
    Route::get('/get-categories-global', [GetDataController::class, 'getCategories'])->name('get.categories.global');
    Route::get('/get-hsn-global', [GetDataController::class, 'getHsn'])->name('get.hsn.global');
    Route::get('/get-rawmaterials-global', [GetDataController::class, 'getRawMaterials'])->name('get.rawmaterials.global');
    Route::get('get-data/quotation/products', [GetDataController::class, 'getProductsForQuotation'])->name('getdata.quotation.products');
    Route::get('get-data/terms-conditions', [GetDataController::class, 'getTermsCondition'])->name('getdata.terms-conditions');
    Route::get('get-data/customers', [GetDataController::class, 'getCustomersData'])->name('getdata.customers');
    Route::get('get-data/customer/{id}/details', [GetDataController::class, 'getCustomerDetails'])->name('getdata.customer.details');
    Route::get('change-password', [Login::class, 'change_password_view'])->name('password.change');
    Route::post('update-password', [Login::class, 'update_password'])->name('password.update');

    // Lead Source
    Route::get('enquiry/source', [EnquirySource::class, 'index'])->name('leadsource.index')->middleware(CheckPermission::class . ':view');
    Route::post('source/store', [EnquirySource::class, 'storeOrUpdate'])->name('leadsource.store')->middleware(CheckPermission::class . ':create');
    Route::get('lead-source/list', [EnquirySource::class, 'getList'])->name('leadsource.list')->middleware(CheckPermission::class . ':view');
    Route::get('lead-source/edit/{id}', [EnquirySource::class, 'edit'])->name('leadsource.edit')->middleware(CheckPermission::class . ':update');
    Route::post('lead-source/delete', [EnquirySource::class, 'destroy'])->name('leadsource.destroy')->middleware(CheckPermission::class . ':delete');

    // Lead status
    Route::get('lead/status', [EnquiryStatus::class, 'index'])->name('leadstatus.index')->middleware(CheckPermission::class . ':view');
    Route::post('lead-status/save', [EnquiryStatus::class, 'storeOrUpdate'])->name('leadstatus.store')->middleware(CheckPermission::class . ':create');
    Route::get('lead-status/list', [EnquiryStatus::class, 'getList'])->name('leadstatus.list')->middleware(CheckPermission::class . ':view');
    Route::get('lead-status/edit/{id}', [EnquiryStatus::class, 'edit'])->name('leadstatus.edit')->middleware(CheckPermission::class . ':update');
    Route::post('lead-status/delete', [EnquiryStatus::class, 'destroy'])->name('leadstatus.destroy')->middleware(CheckPermission::class . ':delete');
    Route::post('lead-status/update-order', [EnquiryStatus::class, 'updateOrder'])->name('leadstatus.updateOrder')->middleware(CheckPermission::class . ':update');

    // *---- Deal Stage Routes ----*
    Route::get('dealstage', [DealStageController::class, 'index'])->name('dealstage.index')->middleware(CheckPermission::class . ':view');
    Route::post('dealstage/save', [DealStageController::class, 'storeOrUpdate'])->name('dealstage.store')->middleware(CheckPermission::class . ':create');
    Route::get('dealstage/list', [DealStageController::class, 'getList'])->name('dealstage.list')->middleware(CheckPermission::class . ':view');
    Route::get('dealstage/edit/{id}', [DealStageController::class, 'edit'])->name('dealstage.edit')->middleware(CheckPermission::class . ':update');
    Route::post('dealstage/delete', [DealStageController::class, 'destroy'])->name('dealstage.destroy')->middleware(CheckPermission::class . ':delete');
    Route::post('/dealstage/update-order', [DealStageController::class, 'updateOrder'])->name('dealstage.updateOrder');
    Route::post('dealstage/update-name', [DealStageController::class, 'updateName'])->name('dealstage.updateName')->middleware(CheckPermission::class . ':update');
    Route::post('dealstage/set-won', [DealstageController::class, 'setWonStage'])->name('dealstage.set-won');
    Route::post('dealstage/set-lost', [DealstageController::class, 'setLostStage'])->name('dealstage.set-lost');

    //  *---- Location Routes ----*
    Route::get('location/cities', [LocationController::class, 'cities'])->name('location.cities');
    Route::get('location/city/{id}', [LocationController::class, 'getLocationByCityId'])->name('location.by_city');

    //  country
    Route::get('country', [Country::class, 'index'])->name('country.index')->middleware(CheckPermission::class . ':view');
    Route::post('countryStore', [Country::class, 'store'])->name('countryStore');
    Route::get('CountryList', [Country::class, 'list'])->name('CountryList');
    Route::get('country/edit/{id}', [Country::class, 'edit'])->name('country.edit');
    Route::post('country/delete', [Country::class, 'destroy'])->name('countryDelete');
    Route::post('country/update', [Country::class, 'update'])->name('country.update');
    // state
    Route::get('state', [State::class, 'index'])->name('state.index')->middleware(CheckPermission::class . ':view');
    Route::post('state/store', [State::class, 'storeOrUpdateStates'])->name('state.store');
    Route::get('state.list', [State::class, 'getStates'])->name('state.list');
    Route::get('state/edit/{id}', [State::class, 'edit'])->name('state.edit');
    Route::post('state/destroy/{id}', [State::class, 'destroy'])->name('state.destroy');
    Route::post('state/deleteByCountry/{id}', [State::class, 'deleteByCountry'])->name('state.deleteByCountry');
    // city
    Route::get('city', [City::class, 'index'])->name('city.index')->middleware(CheckPermission::class . ':view');
    Route::get('city/list', [City::class, 'getCities'])->name('city.list');
    Route::post('/city/store', [City::class, 'storeOrUpdateCities'])->name('city.store');
    Route::get('city/edit', [City::class, 'edit'])->name('city.edit');
    Route::post('city/destroy/{id}', [City::class, 'destroy'])->name('city.destroy');
    Route::post('city/deleteByState/{id}', [City::class, 'deleteByState'])->name('city.deleteByState');
    Route::post('city/deleteByCountryAndState', [City::class, 'deleteByCountryAndState'])->name('city.deleteByCountryAndState');
    Route::get('/ajax/cities', [City::class, 'ajaxCities'])->name('ajax.cities');
    Route::get('/ajax/city-details/{city_id}', [City::class, 'getCityDetails'])->name('ajax.city.details');
    Route::get('/get-location-by-city', [LocationController::class, 'getLocationByCity'])->name('get.location.by.city');
    Route::get('/countries/all', [LocationController::class, 'getCountries'])->name('countries.all');
    Route::get('/states/{countryId}', [LocationController::class, 'getStates'])->name('states.by_country');
    Route::get('/cities/{stateId}', [LocationController::class, 'getCities'])->name('cities.by_state');
    Route::get('get/all-cities', [LocationController::class, 'getAllCities'])->name('get.all.cities');


    //enquiry lead
    Route::get('lead', [LeadController::class, 'index'])->name('lead.index')->middleware(CheckPermission::class . ':view');
    Route::get('/customer/search', [LeadController::class, 'search'])->name('customer.search');
    Route::get('/customer/city-search', [LeadController::class, 'searchCity'])->name('customer.city.search');
    Route::get('/customer/city-details/{id}', [LeadController::class, 'getCityDetails'])->name('customer.city.details');
    Route::get('/customer/{id}/details', [LeadController::class, 'getCustomerDetails'])->name('customer.details');
    Route::post('/ajax/customer/store', [LeadController::class, 'ajaxStore'])->name('ajax.customer.store');
    Route::post('/enquiry/store', [LeadController::class, 'store'])->name('enquiry.store');
    Route::get('/enquiry/{id}/edit', [LeadController::class, 'edit'])->name('enquiry.edit');
    Route::post('/enquiry/convert-to-deal', [LeadController::class, 'convertToDeal'])->name('enquiry.convert');
    Route::get('/enquiry/list', [LeadController::class, 'enquiryList'])->name('enquiry.list');
    Route::get('/enquiry/export/excel', [LeadController::class, 'exportExcel'])->name('enquiry.export.excel');
    Route::get('/enquiry/export/pdf', [LeadController::class, 'exportPdf'])->name('enquiry.export.pdf');
    Route::post('/lead/delete/{id}', [LeadController::class, 'delete'])->name('lead.delete')->middleware(CheckPermission::class . ':delete');
    Route::get('/lead/details/{id}', [LeadDetailsController::class, 'details'])->name('lead.details')->middleware(CheckPermission::class . ':view');
    Route::post('/lead/details/update/{id}', [LeadDetailsController::class, 'updateDetails'])->name('lead.details.update');
    Route::get('/lead/details/logs/{lead_id}', [LeadDetailsController::class, 'getLogs'])->name('lead.details.get-logs');
    Route::post('/lead/details/products/save/{id}', [LeadDetailsController::class, 'saveProducts'])->name('lead.details.save-products');
    Route::get('/enquiry/staff-by-type', [LeadController::class, 'getStaffByType'])->name('enquiry.staff.by.type');
    Route::post('/lead/product/ajax-store', [LeadController::class, 'ajaxStoreProduct'])->name('lead.product.ajax-store');
    Route::post('/lead/industry/ajax-store', [LeadController::class, 'ajaxStoreIndustry'])->name('lead.industry.ajax-store');


    // *---- Reports Routes ----*
    Route::get('report/lead', [LeadReportController::class, 'leadReport'])->name('lead_report.index')->middleware(CheckPermission::class . ':view');
    Route::get('report/lead/list', [LeadReportController::class, 'leadReportList'])->name('lead_report.list');
    Route::get('report/lead/export/excel', [LeadReportController::class, 'exportLeadExcel'])->name('lead_report.export.excel');
    Route::get('report/lead/export/pdf', [LeadReportController::class, 'exportLeadPdf'])->name('lead_report.export.pdf');

    //campaign report
    // Campaign Report Routes
    Route::get('report/campaign', [CampaignReportController::class, 'campaignReport'])->name('campaign_report.index')->middleware(CheckPermission::class . ':view');
    Route::get('report/campaign/list', [CampaignReportController::class, 'campaignReportList'])->name('campaign_report.list');
    Route::get('report/campaign/export/excel', [CampaignReportController::class, 'exportCampaignExcel'])->name('campaign_report.export.excel');
    Route::get('report/campaign/export/pdf', [CampaignReportController::class, 'exportCampaignPdf'])->name('campaign_report.export.pdf');

    Route::get('/wa-status', [WhatsappController::class, 'getStatus'])->name('wa.status');
    Route::get('/wa-create', [WhatsappController::class, 'createSession'])->name('wa.create');
    // dashboard 
    Route::get('/main/dashboard', [Superadmin::class, 'dashboard'])->name('main.dashboard');
    Route::get('/dashboard', [Company::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/data', [Company::class, 'getDashboardData'])->name('dashboard.data');

    // Lead Timeline Route
    Route::get('/lead-timeline/get', [LeadTimelineController::class, 'getTimeline'])->name('lead.timeline.get');
    Route::get('/lead-timeline/download', [LeadTimelineController::class, 'downloadTimeline'])->name('lead.timeline.download');

    // CRM Activity Routes
    Route::get('/crm/activity/calls/lead/{leadId}', [CrmActivityController::class, 'getCalls'])->name('crm.activity.calls');
    Route::post('/crm/activity/schedule-call', [CrmActivityController::class, 'scheduleCall'])->name('crm.activity.schedule-call');
    Route::post('/crm/activity/update-call', [CrmActivityController::class, 'updateCall'])->name('crm.activity.update-call');
    Route::post('/crm/activity/update-call-status', [CrmActivityController::class, 'updateCallStatus'])->name('crm.activity.update-call-status');
    Route::post('/crm/activity/delete-call', [CrmActivityController::class, 'deleteCall'])->name('crm.activity.delete-call');

    Route::get('/crm/activity/notes/lead/{leadId}', [CrmActivityController::class, 'getNotes'])->name('crm.activity.notes');
    Route::post('/crm/activity/add-note', [CrmActivityController::class, 'addNote'])->name('crm.activity.add-note');
    Route::post('/crm/activity/update-note', [CrmActivityController::class, 'updateNote'])->name('crm.activity.update-note');
    Route::post('/crm/activity/delete-note', [CrmActivityController::class, 'deleteNote'])->name('crm.activity.delete-note');
    Route::post('/crm/activity/update-note-status', [CrmActivityController::class, 'updateNoteStatus'])->name('crm.activity.update-note-status');

    Route::get('/crm/activity/attachments/lead/{leadId}', [CrmActivityController::class, 'getAttachments'])->name('crm.activity.attachments');
    Route::post('/crm/activity/upload-attachment', [CrmActivityController::class, 'uploadAttachment'])->name('crm.activity.upload-attachment');
    Route::post('/crm/activity/delete-attachment', [CrmActivityController::class, 'deleteAttachment'])->name('crm.activity.delete-attachment');

    // Staff management routes
    Route::get('/staff', [Staff::class, 'index'])->name('staff.index');
    Route::post('/staff/store', [Staff::class, 'store'])->name('staff.store');
    Route::post('/staff/update', [Staff::class, 'update'])->name('staff.update');
    Route::post('/staff/destroy', [Staff::class, 'destroy'])->name('staff.destroy');
    Route::get('/staff/list', [Staff::class, 'list'])->name('staff.list');
    Route::get('/staff/get-data', [Staff::class, 'get_staff'])->name('staff.get.data');
    Route::post('/staff/reset-password', [Staff::class, 'resetPassword'])->name('staff.reset.password');
    Route::get('/staff/export/excel', [Staff::class, 'export_excel'])->name('staff.export.excel');
    Route::get('/staff/logs', [Staff::class, 'getLogs'])->name('staff.logs');

     // *---- Performa Invoice Routes ----*
    Route::get('/performa-invoice', [PerformaInvoiceController::class, 'index'])->name('performa_invoice.index');
    Route::get('/performa-invoice/list', [PerformaInvoiceController::class, 'list'])->name('performa_invoice.list');
    Route::post('/performa-invoice/store', [PerformaInvoiceController::class, 'store'])->name('performa_invoice.store');
    Route::get('/performa-invoice/{id}/edit', [PerformaInvoiceController::class, 'edit'])->name('performa_invoice.edit');
    Route::post('/performa-invoice/{id}/update', [PerformaInvoiceController::class, 'update'])->name('performa_invoice.update');
    Route::get('/performa-invoice/quotations', [PerformaInvoiceController::class, 'getQuotations'])->name('performa_invoice.quotations');
    Route::get('/performa-invoice/quotation-items/{id}', [PerformaInvoiceController::class, 'getQuotationItems'])->name('performa_invoice.quotation.items');
    Route::post('/performa-invoice/{id}/send-email', [PerformaInvoiceController::class, 'sendEmail'])->name('performa_invoice.send_email');
    Route::get('/performa-invoice/pdf/{id}', [PerformaInvoiceController::class, 'performaInvoicePdf'])->name('performa_invoice.pdf');
    Route::get('/performa-invoice/{id}/history', [PerformaInvoiceController::class, 'history'])->name('performa_invoice.history');

    // *---- Main Invoice Routes ----*
    Route::get('/main-invoice', [MainInvoiceController::class, 'index'])->name('main_invoice.index');
    Route::get('/main-invoice/list', [MainInvoiceController::class, 'list'])->name('main_invoice.list');
    Route::post('/main-invoice/store', [MainInvoiceController::class, 'store'])->name('main_invoice.store');
    Route::get('/main-invoice/{id}/edit', [MainInvoiceController::class, 'edit'])->name('main_invoice.edit');
    Route::post('/main-invoice/{id}/update', [MainInvoiceController::class, 'update'])->name('main_invoice.update');
    Route::get('/main-invoice/performa-invoices', [MainInvoiceController::class, 'getPerformaInvoices'])->name('main_invoice.performa_invoices');
    Route::get('/main-invoice/performa-invoice-items/{id}', [MainInvoiceController::class, 'getPerformaInvoiceItems'])->name('main_invoice.performa_invoice_items');
    Route::get('/main-invoice/pdf/{id}', [MainInvoiceController::class, 'pdf'])->name('main_invoice.pdf');
    Route::post('/main-invoice/{id}/send-email', [MainInvoiceController::class, 'sendEmail'])->name('main_invoice.send_email');

    
    // Staff exit management routes
    Route::get('/staff/exit/data/{id}', [Staff::class, 'exitData'])->name('staff.exit.data');
    Route::get('/staff/exit/list', [Staff::class, 'exitList'])->name('staff.exit.list');
    Route::post('/staff/exit', [Staff::class, 'exitStore'])->name('staff.exit');
});
Route::post('/logout', [Login::class, 'logout'])->name('logout');

Route::middleware([SuperAdminIsValid::class])->controller(Superadmin::class)->group(function () {
    Route::get('/main/dashboard', [Superadmin::class, 'dashboard'])->name('main.dashboard');

    //Company menu assign
    Route::get('company/role', [Super_menu_submenu::class, 'index'])->name('company.role');
    Route::post('menustore', [Super_menu_submenu::class, 'menu_store'])->name('menustore');
    Route::get('menudatafetch', [Super_menu_submenu::class, 'menu_data_fetch'])->name('menudatafetch');
    Route::post('getmenudata/{menu_id}', [Super_menu_submenu::class, 'get_menu_data'])->name('getmenudata');
    Route::post('submenudelete', [Super_menu_submenu::class, 'delete'])->name('submenudelete');
    Route::post('deleteMenu', [Super_menu_submenu::class, 'deleteMenu'])->name('deleteMenu');
    Route::post('dashboard-settings', [Super_menu_submenu::class, 'saveSettings'])->name('dashboardsave');

    //Menu Order
    Route::get('menuorder', [Super_menu_submenu::class, 'menu_order'])->name('menuorder');
    Route::post('updatemenuorder', [Super_menu_submenu::class, 'updateMenuOrder'])->name('updatemenuorder');
    Route::post('updatesubmenuorder', [Super_menu_submenu::class, 'updateSubmenuOrder'])->name('updatesubmenuorder');
    Route::get('get-menu-data', [Super_menu_submenu::class, 'getMenuData'])->name('getMenuData');
    Route::get('menusorderingfetch', [Super_menu_submenu::class, 'fetchOrdering'])->name('menusorderingfetch');
    Route::post('menusorderingupdate', [Super_menu_submenu::class, 'updateOrdering'])->name('menusorderingupdate');
    Route::post('submenus/ordering/update', [Super_menu_submenu::class, 'updateSubmenuOrdering'])->name('submenus/ordering/update');
    Route::post('submenu/delete/{id}', [Super_menu_submenu::class, 'destroy'])->name('submenu.delete');

    //Menu Types
    Route::get('menutypes/ordering/fetch', [Super_menu_submenu::class, 'fetchMenuTypes'])->name('menutypesorderingfetch');
    Route::post('menutypes/ordering/update', [Super_menu_submenu::class, 'updateMenuTypeOrdering'])->name('menutypesorderingupdate');
    Route::post('menutypes/rename', [Super_menu_submenu::class, 'updateMenuTypeName'])->name('menutypesrename');

    Route::get('company/register', [Company::class, 'company_register_view'])->name('company.register');


});

Route::middleware([CompanyORSuperadmin::class])->controller(Company::class)->group(function () {
    Route::get('profile', [Company_setting::class, 'index'])->name('profile');

    // Company Edit and store Superadmin and Company
    Route::get('companies_edit/{id}', [Company::class, 'edit'])->name('companies.edit');
    Route::post('company/store', [Company::class, 'store'])->name('companies.store');
    Route::get('companies/list', [Company::class, 'list'])->name('companies.list');
    Route::post('/company/delete-upi-qr', [Company::class, 'deleteUpiQr'])->name('companies.delete_upi_qr');
    Route::post('/company/delete-signature', [Company::class, 'deleteSignature'])->name('companies.delete_signature');

    Route::get('get/country', [Company::class, 'getCountries'])->name('get.country');
    Route::get('get/state', [Company::class, 'getStates'])->name('get.state');
    Route::get('get/city', [Company::class, 'getCities'])->name('get.city');

    // Menu & Sub Menu
    Route::get('menu/submenu', [Role_permission::class, 'index'])->name('menu.submenu')->middleware([CheckStaffPermission::class]);
    Route::post('save/permissions', [Role_permission::class, 'save_permissions'])->name('save.permissions');
    Route::get('get-permissions/{roleId}', [Role_permission::class, 'get_permissions'])->name('get.permissions');
    Route::post('role/create', [Role_permission::class, 'create_role'])->name('role.create');
    Route::get('permissions/list', [Role_permission::class, 'list'])->name('permissions.list');
    Route::post('permissions/delete', [Role_permission::class, 'delete'])->name('permissions.delete');
    Route::get('/roles/by-type', [Role_permission::class, 'getRolesByType'])->name('get.roles.by.type');

    //mail-config
    Route::get('mail-config', [MailConfig::class, 'index'])->name('mail_config');
    Route::post('mail-config/update', [MailConfig::class, 'store'])->name('mail.config.update');
    Route::post('pusher-config/update', [MailConfig::class, 'pusherstore'])->name('pusher.config.update');
});
Route::get('/quotation/pdf/{id}', [QuotationController::class, 'quotationPdf'])->name('quotation.pdf');

Route::match(['get', 'post'], '/indiamart/webhook', function () {
    return response()->json(['status' => 'success']);
})->name('indiamart.webhook');

/*
|--------------------------------------------------------------------------
| PERMISSION SYSTEM ROUTE RULE (IMPORTANT)
|--------------------------------------------------------------------------
| For this module, always follow the route naming pattern:
|
|   <module>.index  → Main page (ONLY THIS is stored in DB)
|   <module>.store  → Create action
|   <module>.update → Update action
|   <module>.delete → Delete action
|   <module>.edit   → Edit action
|
| NOTE:
| - All CRUD routes MUST start with the same prefix as the index route.
|   Example: menu.index → menu.store / menu.update / menu.delete / menu.edit
|
| - The system automatically maps all CRUD routes back to <module>.index
|   for permission checking.
|
| - DO NOT add CRUD routes to permission table.
|   Only <module>.index should exist in DB.
|
| This ensures consistent and reliable permission checks.
*/

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
    Route::post('/review/delete',  [\App\Http\Controllers\ShopReviewController::class, 'destroy'])->name('shop.review.delete');

    Route::get('/coupon',           [\App\Http\Controllers\ShopCouponController::class, 'index'])->name('shop.coupon.index');
    Route::get('/coupon/list',      [\App\Http\Controllers\ShopCouponController::class, 'getList'])->name('shop.coupon.list');
    Route::post('/coupon/store',    [\App\Http\Controllers\ShopCouponController::class, 'store'])->name('shop.coupon.store');
    Route::get('/coupon/edit/{id}', [\App\Http\Controllers\ShopCouponController::class, 'edit'])->name('shop.coupon.edit');
    Route::post('/coupon/delete',   [\App\Http\Controllers\ShopCouponController::class, 'destroy'])->name('shop.coupon.delete');
    Route::post('/coupon/validate', [\App\Http\Controllers\ShopCouponController::class, 'validateCoupon'])->name('shop.coupon.validate');

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
    Route::get('/activity-log',        [\App\Http\Controllers\ShopActivityLogController::class, 'index'])->name('shop.activity_log.index');
    Route::get('/activity-log/list',   [\App\Http\Controllers\ShopActivityLogController::class, 'getList'])->name('shop.activity_log.list');
});

// ── Vendor Panel Routes ──────────────────────────────────────────────
Route::prefix('vendor')->middleware([VendorIsValid::class])->group(function () {
    Route::get('/dashboard', [ShopDashboardController::class, 'index'])->name('vendor.dashboard');
    Route::get('/product',           [\App\Http\Controllers\ShopProductController::class, 'vendorIndex'])->name('vendor.product.index');
    Route::post('/product/store',    [\App\Http\Controllers\ShopProductController::class, 'vendorStore'])->name('vendor.product.store');
    Route::get('/product/edit/{id}', [\App\Http\Controllers\ShopProductController::class, 'vendorEdit'])->name('vendor.product.edit');
    Route::post('/product/delete',   [\App\Http\Controllers\ShopProductController::class, 'vendorDestroy'])->name('vendor.product.delete');
    Route::get('/inventory',         [\App\Http\Controllers\ShopInventoryController::class, 'vendorIndex'])->name('vendor.inventory.index');
    Route::get('/order',             [\App\Http\Controllers\ShopOrderController::class, 'vendorIndex'])->name('vendor.order.index');
    Route::get('/order/list',        [\App\Http\Controllers\ShopOrderController::class, 'vendorList'])->name('vendor.order.list');
    Route::get('/order/show/{id}',   [\App\Http\Controllers\ShopOrderController::class, 'vendorShow'])->name('vendor.order.show');
    Route::get('/earning',           [\App\Http\Controllers\ShopVendorEarningController::class, 'vendorIndex'])->name('vendor.earning.index');
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
