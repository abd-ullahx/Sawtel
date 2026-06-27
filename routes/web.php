<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceTemplateController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\OnlinePaymentController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RecurringInvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalaryScaleController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffDocumentController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionMethodController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::group(['middleware' => ['install']], function () {

    Route::get('/', function () {
        return redirect('login');
    });

    Auth::routes(['verify' => false, 'register' => false]);
    Route::get('/logout', [LoginController::class, 'logout']);

    Route::group(['middleware' => ['auth', 'multi_business']], function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        //Profile Controller
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update')->middleware('demo');
        Route::get('/profile/change_password', [ProfileController::class, 'change_password'])->name('profile.change_password');
        Route::post('/profile/update_password', [ProfileController::class, 'update_password'])->name('profile.update_password')->middleware('demo');
        Route::get('/profile/notification_mark_as_read/{id}', [ProfileController::class, 'notification_mark_as_read'])->name('profile.notification_mark_as_read');
        Route::get('/profile/show_notification/{id}', [ProfileController::class, 'show_notification'])->name('profile.show_notification');

        /** Admin Only Route **/
        Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function () {
            //Business Controller
            //Route::get('/business/{id}/users', [BusinessController::class, 'users'])->name('business.users');
            Route::resource('/business', BusinessController::class)->except('show')->middleware("demo:DELETE");

//            //User Management
//            Route::get('/users/get_table_data', [UserController::class, 'get_table_data']);
//            Route::resource('/users', UserController::class)->middleware("demo:PUT|PATCH|DELETE");

            //Business User Controller
            Route::match(['get', 'post'], '/system_users/{userId}/{businessId}/change_role', [BusinessController::class, 'change_role'])->name('system_users.change_role');
            Route::delete('/system_users/{id}/destroy_user', [BusinessController::class, 'destroy_user'])->name('system_users.destroy_user');
            Route::post('/system_users/send_invitation', [BusinessController::class, 'send_invitation'])->name('system_users.send_invitation');
            Route::get('/system_users/invite/{businessId}', [BusinessController::class, 'invite'])->name('system_users.invite');

            //User Roles
            Route::resource('/roles', RoleController::class);

            //Currency List
            // Route::resource('currency', CurrencyController::class)->middleware("demo:PUT|PATCH|DELETE");

            //Taxes
            Route::resource('/taxes', TaxController::class);

            //Permission Controller
            Route::get('/permission/access_control', [PermissionController::class, 'index'])->name('permission.index');
            Route::get('/permission/access_control/{user_id?}', [PermissionController::class, 'show'])->name('permission.show');
            Route::post('/permission/store', [PermissionController::class, 'store'])->name('permission.store');

            //Language Controller
            Route::get('/languages/{lang}/edit_website_language', [LanguageController::class, 'edit_website_language'])->name('languages.edit_website_language');
            // Route::resource('/languages', LanguageController::class)->middleware("demo");

            //Business Settings Controller
            Route::post('/business/{id}/send_test_email', [BusinessSettingsController::class, 'send_test_email'])->name('business.send_test_email');
            Route::post('/business/{id}/store_email_settings', [BusinessSettingsController::class, 'store_email_settings'])->name('business.store_email_settings');
            Route::post('/business/{id}/store_payment_gateway_settings', [BusinessSettingsController::class, 'store_payment_gateway_settings'])->name('business.store_payment_gateway_settings');
            Route::post('/business/{id}/store_invoice_settings', [BusinessSettingsController::class, 'store_invoice_settings'])->name('business.store_invoice_settings');
            Route::post('/business/{id}/store_currency_settings', [BusinessSettingsController::class, 'store_currency_settings'])->name('business.store_currency_settings');
            Route::post('/business/{id}/store_general_settings', [BusinessSettingsController::class, 'store_general_settings'])->name('business.store_general_settings');
            Route::get('/business/{id}/settings', [BusinessSettingsController::class, 'settings'])->name('business.settings');

            //Utility Controller
            Route::match(['get', 'post'], '/administration/general_settings/{store?}', [UtilityController::class, 'settings'])->name('settings.update_settings')->middleware("demo");
            Route::post('/administration/upload_logo', [UtilityController::class, 'upload_logo'])->name('settings.uplaod_logo')->middleware("demo");
            Route::get('/administration/database_backup_list', [UtilityController::class, 'database_backup_list'])->name('database_backups.list');
            Route::get('/administration/create_database_backup', [UtilityController::class, 'create_database_backup'])->name('database_backups.create')->middleware("demo:GET");
            Route::delete('/administration/destroy_database_backup/{id}', [UtilityController::class, 'destroy_database_backup'])->name('database_backups.destroy')->middleware("demo:GET");
            Route::get('/administration/download_database_backup/{id}', [UtilityController::class, 'download_database_backup'])->name('database_backups.download')->middleware("demo:GET");
            Route::post('/administration/remove_cache', [UtilityController::class, 'remove_cache'])->name('settings.remove_cache')->middleware("demo");
            Route::post('/administration/send_test_email', [UtilityController::class, 'send_test_email'])->name('settings.send_test_email')->middleware("demo");

            //            //Notification Template
            //            Route::resource('notification_templates', NotificationTemplateController::class)->only([
            //                'index', 'edit', 'update',
            //            ])->middleware("demo");

        });

        /** Dynamic Permission **/
        Route::group(['middleware' => ['permission'], 'prefix' => 'admin'], function () {
            //Dashboard Widget
            Route::get('/dashboard/current_month_income_widget', 'DashboardController@current_month_income_widget')->name('dashboard.current_month_income_widget');
            Route::get('/dashboard/current_month_expense_widget', 'DashboardController@current_month_expense_widget')->name('dashboard.current_month_expense_widget');
            Route::get('/dashboard/due_invoice_amount_widget', 'DashboardController@due_invoice_amount_widget')->name('dashboard.due_invoice_amount_widget');
            Route::get('/dashboard/due_purchase_amount_widget', 'DashboardController@due_purchase_amount_widget')->name('dashboard.due_purchase_amount_widget');
            Route::get('/dashboard/cashflow_widget', 'DashboardController@cashflow_widget')->name('dashboard.cashflow_widget');
            Route::get('/dashboard/account_balance_widget', 'DashboardController@account_balance_widget')->name('dashboard.account_balance_widget');
            Route::get('/dashboard/income_by_category_widget', 'DashboardController@income_by_category_widget')->name('dashboard.income_by_category_widget');
            Route::get('/dashboard/expense_by_category_widget', 'DashboardController@expense_by_category_widget')->name('dashboard.expense_by_category_widget');
            Route::get('/dashboard/recent_transaction_widget', 'DashboardController@recent_transaction_widget')->name('dashboard.recent_transaction_widget');

            //Customers
            Route::get('/clients/get_table_data', [CustomerController::class, 'get_table_data']);
            Route::resource('/clients', CustomerController::class);
            Route::get('/clients/{id}/add-product-prices', [CustomerController::class, 'addProductPrices'])->name('client.add-product-prices');
            Route::post('/clients/{id}/store-product-prices', [CustomerController::class, 'storeProductPrices'])->name('client.store-product-prices');
            Route::post('/clients/{clientId}/store-single-product-price/{priceId}', [CustomerController::class, 'storeSingleProductPrice'])->name('client.store-single-product-price');
            Route::delete('/clients/{clientId}/delete-single-product-price/{priceId}', [CustomerController::class, 'deleteSingleProductPrice'])->name('client.delete-single-product-price');

            //Vendors
            Route::get('/vendors/get_table_data', [VendorController::class, 'get_table_data']);
            Route::resource('/vendors', VendorController::class);

            //Product Controller
            Route::resource('/product_units', ProductUnitController::class)->except('show');
            Route::get('/products/getProducts/{type}', [ProductController::class, 'getProducts']);
            Route::get('/products/getProduct/{id}', [ProductController::class, 'getProduct']);
            Route::get('/products/get_table_data', [ProductController::class, 'get_table_data']);
            Route::resource('/products', ProductController::class);

            //Invoices
            Route::match(['get', 'post'], '/invoices/{id}/send_email', [InvoiceController::class, 'send_email'])->name('invoices.send_email');
            Route::match(['get', 'post'], '/invoices/{id}/add_payment', [InvoiceController::class, 'add_payment'])->name('invoices.add_payment');
            Route::get('/invoices/{id}/mark_as_cancelled', [InvoiceController::class, 'mark_as_cancelled'])->name('invoices.mark_as_cancelled');
            Route::get('/invoices/{id}/duplicate', [InvoiceController::class, 'duplicate'])->name('invoices.duplicate');
            Route::get('/invoices/{id}/get_invoice_link', [InvoiceController::class, 'get_invoice_link'])->name('invoices.get_invoice_link');
            Route::get('/invoices/{id}/export_pdf', [InvoiceController::class, 'export_pdf'])->name('invoices.export_pdf');
            Route::get('/invoices/{id}/approve', [InvoiceController::class, 'approve'])->name('invoices.approve');
            Route::post('/invoices/get_table_data', [InvoiceController::class, 'get_table_data']);
            Route::resource('/invoices', InvoiceController::class);

            //Recurring Invoice
            Route::get('/recurring_invoices/{id}/convert_recurring', [RecurringInvoiceController::class, 'convert_recurring'])->name('recurring_invoices.convert_recurring');
            Route::get('/recurring_invoices/{id}/end_recurring', [RecurringInvoiceController::class, 'end_recurring'])->name('recurring_invoices.end_recurring');
            Route::get('/recurring_invoices/{id}/duplicate', [RecurringInvoiceController::class, 'duplicate'])->name('recurring_invoices.duplicate');
            Route::get('/recurring_invoices/{id}/approve', [RecurringInvoiceController::class, 'approve'])->name('recurring_invoices.approve');
            Route::post('/recurring_invoices/get_table_data', [RecurringInvoiceController::class, 'get_table_data']);
            Route::resource('/recurring_invoices', RecurringInvoiceController::class);

            //Quotation
            Route::match(['get', 'post'], '/quotations/{id}/send_email', [QuotationController::class, 'send_email'])->name('quotations.send_email');
            Route::get('/quotations/{id}/convert_to_invoice', [QuotationController::class, 'convert_to_invoice'])->name('quotations.convert_to_invoice');
            Route::get('/quotations/{id}/duplicate', [QuotationController::class, 'duplicate'])->name('quotations.duplicate');
            Route::get('/quotations/{id}/get_quotation_link', [QuotationController::class, 'get_quotation_link'])->name('quotations.get_quotation_link');
            Route::get('/quotations/{id}/export_pdf', [QuotationController::class, 'export_pdf'])->name('quotations.export_pdf');
            Route::post('/quotations/get_table_data', [QuotationController::class, 'get_table_data']);
            Route::resource('/quotations', QuotationController::class);

            //Purchase
            Route::match(['get', 'post'], '/purchases/{id}/add_payment', [PurchaseController::class, 'add_payment'])->name('purchases.add_payment');
            Route::get('/purchases/{id}/duplicate', [PurchaseController::class, 'duplicate'])->name('purchases.duplicate');
            Route::post('/purchases/get_table_data', [PurchaseController::class, 'get_table_data']);
            Route::resource('/purchases', PurchaseController::class);
            Route::post('/purchases/{id}/order-received', [PurchaseController::class, 'orderReceived'])->name('purchase.order-received');

            Route::post('/purchases/get-csv-data', [PurchaseController::class, 'get_csv_data'])->name('purchase.get-csv-data');

//            //Custom Invoice Template
            Route::get('/invoice_templates/{id}/clone', [InvoiceTemplateController::class, 'clone'])->name('invoice_templates.clone');
            Route::get('/invoice_templates/element/{element}', [InvoiceTemplateController::class, 'get_element']);
            Route::resource('/invoice_templates', InvoiceTemplateController::class);

            //Accounts
            Route::get('/accounts/{accountId}/{amount}/convert_due_amount', [AccountController::class, 'convert_due_amount']);
            Route::resource('/accounts', AccountController::class);

            //Transaction Categories
            Route::resource('/transaction_categories', TransactionCategoryController::class)->except('show');

            //Transaction
            Route::match(['get', 'post'], '/transactions/transfer', [TransactionController::class, 'transfer'])->name('transactions.transfer');
            Route::get('/transactions/get_table_data', [TransactionController::class, 'get_table_data']);
            Route::resource('/transactions', TransactionController::class);

            //Transaction Methods
            Route::resource('/transaction_methods', TransactionMethodController::class)->except('view');

            //HR Module
            Route::resource('/departments', DepartmentController::class);
            Route::get('/designations/get_designations/{deaprtment_id}', [DesignationController::class, 'get_designations']);
            Route::resource('/designations', DesignationController::class)->except('show');
            Route::get('/salary_scales/get_salary_scales/{designation_id}', [SalaryScaleController::class, 'get_salary_scales']);
            Route::get('/salary_scales/filter_by_department/{department_id}', [SalaryScaleController::class, 'index'])->name('salary_scales.filter_by_department');
            Route::resource('/salary_scales', SalaryScaleController::class);

            //Staff Controller
            Route::get('/staffs/get_table_data', [StaffController::class, 'get_table_data']);
            Route::resource('/staffs', StaffController::class);
//
//            //Staff Documents
            Route::get('/staff_documents/{employee_id}', [StaffDocumentController::class, 'index'])->name('staff_documents.index');
            Route::get('/staff_documents/create/{employee_id}', [StaffDocumentController::class, 'create'])->name('staff_documents.create');
            Route::resource('/staff_documents', StaffDocumentController::class)->except(['index', 'create', 'show']);
//
//            //Holiday Controller
            Route::get('/holidays/get_table_data', [HolidayController::class, 'get_table_data']);
            Route::match(['get', 'post'], '/holidays/weekends', [HolidayController::class, 'weekends'])->name('holidays.weekends');
            Route::resource('/holidays', HolidayController::class)->except('show');
//
//            //Leave Application
            Route::resource('/leave_types', LeaveTypeController::class)->except('show');
            Route::get('/leaves/get_table_data', [LeaveController::class, 'get_table_data']);
            Route::resource('/leaves', LeaveController::class);
//
//            //Attendance Controller
            Route::get('/attendance/get_table_data', [AttendanceController::class, 'get_table_data']);
            Route::post('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
            Route::resource('/attendance', AttendanceController::class)->except('show', 'edit', 'update', 'destroy');

//            //Award Controller
//            Route::get('/awards/get_table_data', [AwardController::class, 'get_table_data']);
//            Route::resource('/awards', AwardController::class);

//            //Payslip Controller
            Route::post('/payslips/store_payment', [PayrollController::class, 'store_payment'])->name('payslips.store_payment');
            Route::match(['get', 'post'], '/payslips/make_payment', [PayrollController::class, 'make_payment'])->name('payslips.make_payment');
            Route::get('/payslips/get_table_data', [PayrollController::class, 'get_table_data']);
            Route::resource('/payslips', PayrollController::class);

//            //Support Ticket Controller
//            Route::get('/support_tickets/mark_as_resolved/{uuid}', [SupportTicketController::class, 'mark_as_resolved'])->name('support_tickets.mark_as_resolved');
//            Route::get('/support_tickets/mark_as_closed/{uuid}', [SupportTicketController::class, 'mark_as_closed'])->name('support_tickets.mark_as_closed');
//            Route::get('/support_tickets/accept/{uuid}', [SupportTicketController::class, 'accept'])->name('support_tickets.accept');
//            Route::post('/support_tickets/reply/{uuid}', [SupportTicketController::class, 'reply'])->name('support_tickets.reply');
//            Route::get('/support_tickets/get_table_data/{status}', [SupportTicketController::class, 'get_table_data']);
//            Route::resource('/support_tickets', SupportTicketController::class)->except(['edit', 'update', 'destroy']);

            //Report Controller
            Route::get('/reports/account_balances', [ReportController::class, 'account_balances'])->name('reports.account_balances');
            Route::match(['get', 'post'], '/reports/account_statement', [ReportController::class, 'account_statement'])->name('reports.account_statement');
            Route::match(['get', 'post'], '/reports/profit_and_loss', [ReportController::class, 'profit_and_loss'])->name('reports.profit_and_loss');
            Route::match(['get', 'post'], '/reports/transactions_report', [ReportController::class, 'transactions_report'])->name('reports.transactions_report');
            Route::match(['get', 'post'], '/reports/income_by_customer', [ReportController::class, 'income_by_customer'])->name('reports.income_by_customer');
            Route::match(['get', 'post'], '/reports/purchase_by_vendor', [ReportController::class, 'purchase_by_vendor'])->name('reports.purchase_by_vendor');
            Route::match(['get', 'post'], '/reports/attendance_report', [ReportController::class, 'attendance_report'])->name('reports.attendance_report');
            Route::match(['get', 'post'], '/reports/payroll_report', [ReportController::class, 'payroll_report'])->name('reports.payroll_report');
            Route::match(['get', 'post'], '/reports/tax_report', [ReportController::class, 'tax_report'])->name('reports.tax_report');
            Route::get( '/reports/sale-products-report', [ReportController::class, 'saleProductsReport'])->name('reports.sale-products-report');
        });

        Route::get('/business/switch_business/{id}', [BusinessController::class, 'switch_business'])->name('business.switch_business');
        Route::get('/ajax/get_table_data', 'Select2Controller@get_table_data');
    });

    Route::get('/switch_language/', function () {
        if (isset($_GET['language'])) {
            session(['language' => $_GET['language']]);
            return back();
        }

        return back();
    })->name('switch_language');

    Route::get('/413', function () {
        $sizeUploadMax = ini_get("upload_max_filesize");
        return back()->with('error', "You can’t upload more than $sizeUploadMax each file !")->withInput();
    })->name('413');
});

Route::group(['prefix' => 'callback', 'namespace' => 'Gateway'], function () {
    Route::get('/paypal', 'PayPal\ProcessController@callback')->name('callback.PayPal');
    Route::post('/stripe', 'Stripe\ProcessController@callback')->name('callback.Stripe');
    Route::post('/razorpay', 'Razorpay\ProcessController@callback')->name('callback.Razorpay');
    Route::get('/paystack', 'Paystack\ProcessController@callback')->name('callback.Paystack');
    Route::get('/flutterwave', 'Flutterwave\ProcessController@callback')->name('callback.Flutterwave');
    Route::get('/mollie', 'Mollie\ProcessController@callback')->name('callback.Mollie');
    Route::match(['get', 'post'], '/instamojo', 'Instamojo\ProcessController@callback')->name('callback.Instamojo');
});

//Public Invoice VIew
Route::get('/invoice/make_payment/{short_code}/{gateway}', [OnlinePaymentController::class, 'make_payment'])->name('invoices.make_payment');
Route::get('/invoice/payment_methods/{short_code}', [OnlinePaymentController::class, 'payment_methods'])->name('invoices.payment_methods');
Route::get('/invoice/{short_code}/{export?}', [InvoiceController::class, 'show_public_invoice'])->name('invoices.show_public_invoice');

//Public Quotation VIew
Route::get('/quotations/{short_code}/{export?}', [QuotationController::class, 'show_public_quotation'])->name('quotations.show_public_quotation');

Route::get('/dashboard/json_income_by_category', 'DashboardController@json_income_by_category')->middleware('auth');
Route::get('/dashboard/json_expense_by_category', 'DashboardController@json_expense_by_category')->middleware('auth');
Route::get('/dashboard/json_cashflow', 'DashboardController@json_cashflow')->middleware('auth');

//Social Login
Route::get('/login/{provider}', 'Auth\SocialController@redirect');
Route::get('/login/{provider}/callback', 'Auth\SocialController@callback');

//Route::get('/installation', 'Install\InstallController@index');
//Route::get('install/database', 'Install\InstallController@database');
//Route::post('install/process_install', 'Install\InstallController@process_install');
//Route::get('install/create_user', 'Install\InstallController@create_user');
//Route::post('install/store_user', 'Install\InstallController@store_user');
//Route::get('install/system_settings', 'Install\InstallController@system_settings');
//Route::post('install/finish', 'Install\InstallController@final_touch');
