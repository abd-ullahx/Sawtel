<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\InvoiceController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\QuotationController;
use App\Http\Controllers\Client\SupportTicketController;
use App\Http\Controllers\Client\TransactionController;

$ev = env('APP_INSTALLED', true) == true ? get_option('email_verification', 0) : 0;

Route::get('/', function () {
    return redirect()->route('client.login');
});

Auth::routes(['verify' => $ev == 1 ? true : false, 'register' => false]);

Route::group(['middleware' => $ev == 1 ? ['auth:client', 'verified:client.verification.notice'] : ['auth:client']], function () {

    Route::get('logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    //Profile Controller
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/change_password', [ProfileController::class, 'change_password'])->name('profile.change_password');
    Route::post('profile/update_password', [ProfileController::class, 'update_password'])->name('profile.update_password');
    Route::get('profile/notification_mark_as_read/{id}', [ProfileController::class, 'notification_mark_as_read'])->name('profile.notification_mark_as_read');
    Route::get('profile/show_notification/{id}', [ProfileController::class, 'show_notification'])->name('profile.show_notification');

    //Invoice Controller
    Route::post('invoices/get_table_data', [InvoiceController::class, 'get_table_data']);
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');

    //Quotation Controller
    Route::post('quotations/get_table_data', [QuotationController::class, 'get_table_data']);
    Route::get('quotations', [QuotationController::class, 'index'])->name('quotations.index');

    //Transaction Controller
    Route::get('transactions/{id}/show', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('transactions/get_table_data', [TransactionController::class, 'get_table_data']);
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');

    //Support Ticket Controller
    Route::get('support_tickets/mark_as_resolved/{uuid}', [SupportTicketController::class, 'mark_as_resolved'])->name('support_tickets.mark_as_resolved');
    Route::get('support_tickets/mark_as_closed/{uuid}', [SupportTicketController::class, 'mark_as_closed'])->name('support_tickets.mark_as_closed');
    Route::post('support_tickets/reply/{uuid}', [SupportTicketController::class, 'reply'])->name('support_tickets.reply');
    Route::get('support_tickets/get_table_data', [SupportTicketController::class, 'get_table_data']);
    Route::resource('support_tickets', SupportTicketController::class)->except(['edit', 'update', 'destroy']);

});