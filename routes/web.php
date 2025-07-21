<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\adminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\Pos\BrandController;
use App\Http\Controllers\Pos\SupplierController;
use App\Http\Controllers\Pos\CustomerController;
use App\Http\Controllers\Pos\UnitController;
use App\Http\Controllers\Pos\CategoryController;
use App\Http\Controllers\Pos\ProductController;
use App\Http\Controllers\Pos\PurchaseController;
use App\Http\Controllers\Pos\Purchase2Controller;
use App\Http\Controllers\Pos\DefaultController;
use App\Http\Controllers\Pos\InvoiceController;
use App\Http\Controllers\Pos\StockController;
use App\Http\Controllers\Pos\ReturnController;
use App\Http\Controllers\Pos\SalesRepController;
use App\Http\Controllers\Pos\DeliveryzoneController;
use App\Http\Controllers\pos\ProductPriceCodeController;
use App\Http\Controllers\Pos\PurchasePaymentController;
use App\Http\Controllers\Pos\TaxController;
use App\Http\Controllers\ProductLabelsPrintController;
use App\Http\Controllers\PurcheseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return view('auth.login');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard/{startDate?}/{endDate?}', [DashboardController::class, 'getDashboardData'])->name('getDashboardData');
    Route::get('/dashboard/print/invoice/{startDate?}/{endDate?}/{filterName?}/{total_amount?}/{total_profit?}/{total_paid?}/{total_due?}', [DashboardController::class, 'dashboardReportPrint'])->name('dashboardReport.print'); 
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Group Middleware for session expire 
Route::middleware('auth')->group(function () {
    
    // Dashboard controller
    Route::get('/dashboard-data/{startDate?}/{endDate?}', [DashboardController::class, 'getDashboardData'])->name('getDashboardData');
//Admin All Route
Route::controller(adminController::class)->group(function () {
    Route::get('/admin/logout', 'destroy')->name('admin.logout');
    Route::get('/admin/profile', 'profile')->name('admin.profile');
    Route::get('/edit/profile', 'editProfile')->name('edit.profile');
    Route::post('/store/profile', 'storeProfile')->name('store.profile');
    Route::get('/change/password', 'changePassword')->name('change.password');
    Route::post('/update/password', 'updatePassword')->name('update.password');
});
// Users all route
Route::resource('users',UserController::class);
// Role all route
Route::resource('roles',RoleController::class);

//Supplier All Route
Route::controller(SupplierController::class)->group(function () {
    Route::get('/supplier/all', 'SupplierAll')->name('supplier.all');
    Route::get('/supplier/add', 'SupplierAdd')->name('supplier.add');
    Route::post('/supplier/store', 'SupplierStore')->name('supplier.store');
    Route::get('/supplier/edit/{id}', 'SupplierEdit')->name('supplier.edit');
    Route::post('/supplier/update', 'SupplierUpdate')->name('supplier.update');
    Route::get('/supplier/delete/{id}', 'SupplierDelete')->name('supplier.delete'); 
    // all supplier report print
     Route::get('/supplier/all-report/pdf/{id?}', 'SupplierAllReportPdf')->name('supplier.all-report-pdf');
});

//Customer All Route
Route::controller(CustomerController::class)->group(function () {
    Route::get('/customer/all', 'CustomerAll')->name('customer.all');
    Route::get('/customer/add', 'CustomerAdd')->name('customer.add');
    Route::post('/customer/store', 'CustomerStore')->name('customer.store');
    Route::get('/customer/edit/{id}', 'CustomerEdit')->name('customer.edit');
    Route::post('/customer/update', 'CustomerUpdate')->name('customer.update');
    Route::get('/customer/delete/{id}', 'CustomerDelete')->name('customer.delete');
    Route::get('/credit/customer', 'CreditCustomer')->name('credit.customer');
    Route::get('/credit/customer/print/pdf', 'CreditCustomerPrintPdf')->name('credit.customer.print.pdf');
    Route::get('/customer/edit/invoice/{invoice_id}', 'CustomerEditInvoice')->name('customer.edit.invoice');
    Route::post('/customer/update/invoice/{invoice_id}', 'CustomerUpdateInvoice')->name('customer.update.invoice');
    Route::get('/customer/invoice/details/{invoice_id}', 'CustomerInvoiceDetails')->name('customer.invoice.details.pdf');
    Route::get('/paid/customer/', 'PaidCustomer')->name('paid.customer');
    Route::get('/paid/customer/print/pdf', 'PaidCustomerPrintPdf')->name('paid.customer.print.pdf');

    // pdf generate
    Route::get('/customer/invoices-report/pdf/{customerId}', 'CustomerInvoicesReportPdf')->name('customer.invoices-report-pdf');
    Route::get('/customer/transaction-report/pdf/{customerId}', 'CustomerTransactionsReportPdf')->name('customer.transaction-report-pdf');
    Route::get('/customer/all-report/pdf/{id?}', 'CustomerAllReportPdf')->name('customer.all-report-pdf');
    Route::get('/customer/wise/report', 'CustomerWiseReport')->name('customer.wise.report');
    //Customer Wise Report
    Route::get('/customer/report', 'CustomerReport')->name('customer.report');
    Route::get('/customer/invoices-report/pdf/{customerId}', 'CustomerInvoicesReportPdf')->name('customer.invoices-report-pdf');
    Route::get('/customer/transaction-report/pdf/{customerId}', 'CustomerTransactionsReportPdf')->name('customer.transaction-report-pdf');

    Route::get('/customer/wise/paid/report', 'CustomerWisePaidReport')->name('customer.wise.paid.report');
    // NEW
    Route::get('/customer/due_payment/all','CustomerDuePayment')->name('customer.due_payment.all');

    Route::post('customer/due_payment/make_payment', 'CustomerDueMakePayment')->name('customer.due_payment.make_payment');
    Route::get('customer/due_payment/make_payment/edit/{transactionId}', 'CustomerDueMakePaymentEdit')->name('customer.due_payment.make_payment.edit');
    Route::post('customer/due_payment/make_payment/update', 'CustomerDueMakePaymentUpdate')->name('customer.due_payment.make_payment.update');
    Route::get('customer/due_payment/make_payment//delete/{transactionId}', 'CustomerDueMakePaymentDelete')->name('customer.transaction.delete');
    Route::get('/customer/get-customer-invoices/{id}', 'getInvoicesByCustomer')->name('customer.get_invoices');
    //Customer Transaction Report
    Route::get('/customer/transaction/report/pdf/{customerId}', 'CustomerTransactionReportPdf')->name('customer.transaction-report-pdf');
    // All customer transaction report
    Route::get('/customer/transaction', 'CustomerAllTransaction')->name('customer.all.transaction');
    Route::get('/customer/all/transaction/report/pdf/{startDate?}/{endDate?}/{filter?}/{customer_filter?}', 'CustomerAllTransactionPdf')->name('customer.all.transaction-report-pdf');
   
    });


//Units All Route
Route::controller(UnitController::class)->group(function () {
    Route::get('/unit/all', 'UnitAll')->name('unit.all');
    Route::get('/unit/add', 'UnitAdd')->name('unit.add');
    Route::post('/unit/store', 'UnitStore')->name('unit.store');
    Route::get('/unit/edit/{id}', 'UnitEdit')->name('unit.edit');
    Route::post('/unit/update', 'UnitUpdate')->name('unit.update');
    Route::get('/unit/delete/{id}', 'UnitDelete')->name('unit.delete');
});

//Categroy All Route
Route::controller(CategoryController::class)->group(function () {
    Route::get('/category/add', 'CategoryAdd')->name('category.add');
    Route::post('/category/store', 'CategoryStore')->name('category.store');
    Route::get('/category/edit/{id}', 'CategoryEdit')->name('category.edit');
    Route::post('/category/update', 'CategoryUpdate')->name('category.update');
    Route::get('/category/delete/{id}', 'CategoryDelete')->name('category.delete');
});

//Tax All Route
Route::controller(TaxController::class)->group(function () {
    Route::get('/tax', 'index')->name('tax.index');
    Route::post('/tax/store', 'store')->name('tax.store');
    Route::get('/tax/edit/{id}', 'edit')->name('tax.edit');
    Route::post('/tax/update', 'update')->name('tax.update');
    Route::get('/tax/delete/{id}', 'delete')->name('tax.delete');
});
//Brand All Route
Route::controller(BrandController::class)->group(function () {
    Route::get('/brand/add', 'brandAdd')->name('brand.add');
    Route::post('/brand/store', 'brandStore')->name('brand.store');
    Route::get('/brand/edit/{id}', 'brandEdit')->name('brand.edit');
    Route::post('/brand/update', 'brandUpdate')->name('brand.update');
    Route::get('/brand/delete/{id}', 'brandDelete')->name('brand.delete');
});

//SR Controller
Route::controller(SalesRepController::class)->group(function () {
    Route::get('/sr/all', 'SrAll')->name('sr.all');
    Route::get('/sr/add', 'SrAdd')->name('sr.add');
    Route::post('/sr/store', 'SrStore')->name('sr.store');
    Route::get('/sr/edit/{id}', 'SrEdit')->name('sr.edit');
    Route::post('/sr/update', 'SrUpdate')->name('sr.update');
    Route::get('/sr/delete/{id}', 'SrDelete')->name('sr.delete');
});

//Delivery Zone All Route
Route::controller(DeliveryzoneController::class)->group(function () {
    Route::get('/deliveryzone/all', 'DeliveryzoneAll')->name('deliveryzone.all');
    Route::get('/deliveryzone/add', 'DeliveryzoneAdd')->name('deliveryzone.add');
    Route::post('/deliveryzone/store', 'DeliveryzoneStore')->name('deliveryzone.store');
    Route::get('/deliveryzone/edit/{id}', 'DeliveryzoneEdit')->name('deliveryzone.edit');
    Route::post('/deliveryzone/update', 'DeliveryzoneUpdate')->name('deliveryzone.update');
    Route::get('/deliveryzone/delete/{id}', 'DeliveryzoneDelete')->name('deliveryzone.delete');
});


//product All Route
Route::controller(ProductController::class)->group(function () {
    Route::get('/product/all', 'ProductAll')->name('product.all');
    Route::get('/product/add', 'ProductAdd')->name('product.add');
    Route::post('/product/store', 'ProductStore')->name('product.store');
    Route::get('/product/edit/{id}', 'ProductEdit')->name('product.edit');
    Route::post('/product/update', 'ProductUpdate')->name('product.update');
    Route::get('/product/delete/{id}', 'ProductDelete')->name('product.delete');   
});
// Product Price code All Route
Route::controller(ProductPriceCodeController::class)->group(function(){
    Route::get('/productpricecode/all', 'ProductPriceCodeAll')->name('productpricecode.all');
    Route::get('/productpricecode/create', 'ProductPriceCodeCreate')->name('productpricecode.create');
    Route::post('/productpricecode/store', 'ProductPriceCodeStore')->name('productpricecode.store');
    Route::get('/productpricecode/edit/{id}', 'ProductPriceCodeEdit')->name('productpricecode.edit');
    Route::post('/productpricecode/update', 'ProductPriceCodeUpdate')->name('productpricecode.update');
    Route::get('/productpricecode/delete/{id}', 'ProductPriceCodeDelete')->name('productpricecode.delete'); 
});

// Product Labels Print 
Route::controller(ProductLabelsPrintController::class)->group(function(){
    Route::get('/productLabelsprint/index', 'index')->name('productLabelsprint.index');
    Route::post('/productLabelsprint/print', 'labelPrint')->name('productLabelsprint.labelPrint');
});

//Purchase All Route
Route::controller(PurcheseController::class)->group(function () {
    Route::get('/purchese/all', 'PurchaseAll')->name('purchase.all');  
    Route::get('/purchese/add', 'PurcheseAdd')->name('purchase.add');  
    Route::get('/purchase/edit/{id}', 'PurchaseEdit')->name('purchase.edit');  
    Route::post('/purchese/store', 'PurchaseStore')->name('purchase.store');  
    Route::post('/purchase/update/', 'PurchaseUpdate')->name('purchase.update');    
    Route::get('/purchase/delete/{id}', 'PurchaseDelete')->name('purchase.destroy');  
    
    Route::get('/purchese/print/{id}', 'PurchasePosPrint')->name('purchase.print');   

    Route::get('/purchese/supplier-wise-report/all', 'PurcheseReport')->name('purchase.supplier_wise_purchese_report.all');  

    Route::get('/purchese/supplier-wise-payment/all', 'PurchesePayment')->name('purchase.supplier_wise_purchese_payment.all'); 

    Route::post('/purchese/supplier-wise-due-payment/make_payment', 'PurchasePaymentMakePayment')->name('purchase.supplier_wise_purchese_payment.make_payment');

    Route::get('/purchese/supplier-report', 'SupplierReport')->name('purchase.wise.due.report');

    Route::get('/purchese/supplier/due/report/{purchase_id}', 'SupplierPurchaseReport')->name('purchase.supplier.edit');
    Route::get('/purchese/supplier/due/report/data/{id}', 'supplier_purchase_data')->name('purchase.data');//sent data to supplier_purches_report.blade.php

    Route::post('/purchese/supplier/due/update/{purchase_id}', 'SupplierPurchaseUpdate')->name('purchase.supplier.update');

    Route::get('/purchese/supplier/all/report', 'SupplierWiseAllReport')->name('purchase.wise.all.report');
    // PDF
    Route::get('/purchese/print/pos_pdf/{id}', 'PrintPurchase')->name('purchase.print.pos_pdf'); 
    // purchase preview
    Route::post('/purchase/preview','PreView')->name('purchase.preview');
    // print PDF
    Route::get('/purchase/supplier/purchase-report/pdf/{supplierId}', 'SupplierPurchaseReportPdf')->name('purchase.supplier.purchase-report-pdf');
    Route::get('/purchase/supplier/transaction-report/pdf/{supplierId}', 'SupplierTransactionsReportPdf')->name('purchase.supplier.transaction-report-pdf');
    Route::get('/print/purchase-all-filer/{startDate?}/{endDate?}/{filter?}/{supplier_filter?}', 'PurchaseAllFilterPrint')
    ->name('print.purchase-all-filer'); 
    // get purchase by supplier
    Route::get('/supplier/get-supplier-purchases/{id}', 'getPurchasesBySupplier')->name('supplier.get_purchases');
    // transaction Edit
    Route::get('/purchase/supplier/transaction/edit/{transactionId}', 'SupplierTransactionEdit')->name('purchase.supplier.transaction.edit');
    // transaction update
    Route::post('/purchase/supplier/transaction/update/', 'SupplierTransactionUpdate')->name('purchase.supplier.transaction.update');
    // All Supplier transaction report
    Route::get('/purchase/supplier/transaction', 'SupplierAllTransaction')->name('purchase.supplier.all.transaction');
    Route::get('/purchase/supplier/all/transaction/report/pdf/{startDate?}/{endDate?}/{filter?}/{supplier_filter?}', 'SupplierAllTransactionPdf')->name('purchase.supplier.all.transaction-report-pdf');
});
//Invoice All Route
Route::controller(InvoiceController::class)->group(function () {
    Route::get('/invoice/all', 'InvoiceAll')->name('invoice.all');
    Route::get('/invoice/add', 'InvoiceAdd')->name('invoice.add');   
    Route::post('/invoice/sms-send', 'InvoiceSmsSend')->name('invoice.sms-send');   
    Route::post('/invoice/store', 'InvoiceStore')->name('invoice.store');
    Route::get('/invoice/pending/list', 'InvoicePending')->name('invoice.pending.list');   


    Route::get('/invoice/edit/{id}', 'InvoiceEdit')->name('invoice.edit');     
    Route::post('/invoice/update', 'InvoiceUpdate')->name('invoice.update');
        
    Route::get('/invoice/approve/{id}', 'InvoiceApprove')->name('invoice.approve');     
    Route::get('/invoice/delete/{id}', 'InvoiceDelete')->name('invoice.delete');     
    Route::post('/approval/store/{id}', 'ApprovalStore')->name('approval.store');     
    Route::get('/print/invoice/list', 'PrintInvoiceList')->name('print.invoice.list'); 
    // Print Invoice and challan fron invoice_all page
    Route::get('/print/report/{id}/{invoice_type}', 'ReportPrint')->name('print.report');
    Route::get('/print/invoice-all-filer/{startDate?}/{endDate?}/{filter?}/{invoice_type_filter?}/{customer_filter?}', 'InvoiceAllFilterPrint')
    ->name('print.invoice-all-filer'); 
    // Print direct from Add Invoice    
    Route::get('/print/invoiceFromAddInvoice/{id}', 'InvoicePosPrint')->name('print.invoice.fromAddInvoice');     
    Route::get('/print/invoice/{id}', 'PrintInvoice')->name('print.invoice');     
    Route::get('/daily/invoice/Report', 'DailyInvoiceReport')->name('daily.invoice.report');     
    Route::get('/daily/invoice/pdf', 'DailyInvoicePdf')->name('daily.invoice.pdf');     
    Route::get('/deliveryzone/invoice/details', 'DeliveryZoneInvoiceDetails')->name('deliveryzone.invoice.details');     
    Route::get('/deliveryzone/invoice/pdf', 'DeliveryZoneInvoicePdf')->name('deliveryzone.invoice.pdf'); 
    Route::get('/deliveryzone/invoice/summary/edit', 'DeliveryZoneInvoiceEdit')->name('deliveryzone.invoice.summary.edit');
    Route::get('/deliveryzone/invoice/summary', 'DeliveryZoneInvoiceSummary')->name('deliveryzone.invoice.summary'); 

     // invoice preview
     Route::post('/invoice/preview','PreView')->name('invoice.preview');
     Route::post('/invoice/all/preview','InvoiceAllPreView')->name('invoice.all.preview');

    // Route::get('/invoice/update/{id}', 'InvoiceUpdate')->name('invoice.invoice_update');     
    // Route::get('/invoice/edit_invoice/{id}', 'edit_invoice')->name('invoice.edit_invoice');     
    // Route::post('/invoice/invoice_update/{id}', 'InvoiceUpdate')->name('invoice.invoice_update');    
});

//Return All Route
Route::controller(ReturnController::class)->group(function () {
    Route::get('/return/add', 'ReturnAdd')->name('return.add'); 
    Route::post('/return/store', 'ReturnStore')->name('return.store');
    Route::get('/return/all', 'ReturnAll')->name('return.all');  
    Route::get('/return/pending/list', 'ReturnPending')->name('return.pending');     
    Route::get('/return/approve/{id}', 'ReturnApprove')->name('return.approve');     
    Route::get('/return/delete/{id}', 'ReturnDelete')->name('return.delete');     
    // Route::post('/approval/store/{id}', 'ApprovalStore')->name('approval.store');     
    // Route::get('/print/invoice/list', 'PrintInvoiceList')->name('print.invoice.list');     
    // Route::get('/print/invoice/{id}', 'PrintInvoice')->name('print.invoice');     
    Route::get('/daily/return/Report', 'DailyReturnReport')->name('daily.return.report');     
    Route::get('/daily/return/pdf', 'DailyReturnPdf')->name('daily.return.pdf');     
});

//Stock All Route
Route::controller(StockController::class)->group(function () {
    Route::get('/stock/report', 'StockReport')->name('stock.report');
    // pdf
    Route::get('/stock/report/pdf', 'StockReportPdf')->name('stock.report.pdf');
    Route::get('/stock/supplier/wise', 'StockSupplierWise')->name('stock.supplier.wise');
    Route::get('/supplier/wise/pdf', 'SupplierWisePdf')->name('supplier.wise.pdf');
    Route::get('/product/wise/pdf', 'ProductWisePdf')->name('product.wise.pdf');
        
});

Route::group(['prefix' => 'settings', 'as' => 'settings.','controller'=>SettingController::class],function () {
    Route::get('/','index')->name('index');
    Route::post('/sms-config','update_sms')->name('sms_config');
    Route::post('/sms-test','sms_test')->name('sms_test');
});

}); // End of Group Middleware for session expire 

//Default All Route
Route::controller(DefaultController::class)->group(function () {
    Route::get('/get-category-by-brand', 'GetCategoryByBrand')->name('get-category-by-brand');
    Route::get('/get-category', 'GetCategory')->name('get-category');
    Route::get('/get-product', 'GetProduct')->name('get-product');
    Route::get('/get-product-for-invoice', 'GetProductForInvoice')->name('get-product-for-invoice');
    // Barcode
    Route::get('/get-product-by-barcode', 'GetProductForInvoiceByBarcode')->name('get-product-by-barcode');
    Route::get('/get-stock', 'GetStock')->name('check-product-stock');
});
//All Download dashboardReportPrint
Route::get('dashboard-report-generate-pdf/{startDate?}/{endDate?}/{filterName?}/{total_amount?}/{total_profit?}/{total_paid?}/{total_due?}', [PDFController::class, 'dashboardReportPDF'])->name('dashboard-report-generate.pdf');


require __DIR__.'/auth.php';
Route::get("/inv/{id}",[InvoiceController::class,'PrintInvoice'])->name('PublicPrintInvoice');
Route::get('/test',function(){
dd(\App\Models\Invoice::withSum('payment','due_amount')->where('id','!=',94)->get()->sum('payment_sum_due_amount'));
});
