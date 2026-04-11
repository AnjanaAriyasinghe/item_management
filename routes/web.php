<?php

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ChequeBookController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpensApprovalController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseSubCategoryController;
use App\Http\Controllers\ProfileChangeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesReportController;

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


Auth::routes();


// Define a group of routes with 'auth' middleware applied
Route::middleware(['auth'])->group(function () {
    // Define a GET route for the root URL ('/')
    // Define a GET route with dynamic placeholders for route parameters
    // Route::get('{routeName}/{name?}', [HomeController::class, 'pageView']);
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard_data', [HomeController::class, 'dashboard_data'])->name('dashboard_data');
    Route::get('/dashboard/expenses_chart', [HomeController::class, 'expenses_chart'])->name('expenses_chart');
    Route::get('/dashboard/payment_chart', [HomeController::class, 'payment_chart'])->name('payment_chart');
    Route::resource('reset_password', ProfileChangeController::class)->names('user.reset');
    Route::prefix('admin')->group(callback: function () {
        Route::resource('roles', RoleController::class)->names('admin.roles');
        Route::post('update_default_company/{user}', [UserController::class, 'update_default_company'])->name('admin.users.update_default_company');
        Route::post('update_password', [UserController::class, 'update_password'])->name('admin.users.update_password');
        Route::resource('users', UserController::class)->names('admin.users');

        // Route::post('/reset_password_email', [ProfileChangeController::class, 'resetPassword'])->name('user.reset');
        Route::get('getVendors', [BankAccountController::class, 'getVendors'])->name('admin.getVendors');
        Route::get('get_vendor_accounts/{id}', [VendorController::class,'get_vendor_accounts'])->name('admin.get_vendor_accounts');
        Route::post('vendors/approval', [VendorController::class, 'approved'])->name('admin.vendor.approved');
        Route::post('vendors/rejected', [VendorController::class, 'rejected'])->name('admin.vendor.rejected');
        Route::resource('vendors', VendorController::class)->names('admin.vendors');
        Route::get('getBanks', [BankAccountController::class, 'getBanks'])->name('admin.getBanks');
        Route::get('getBranches/{id}', [BankAccountController::class, 'getBranches'])->name('admin.getBranches');
        Route::get('getAccounts/{id}', [BankAccountController::class, 'getAccountsByBanks'])->name('admin.getAccountsByBanks');
        Route::resource('bank_accounts', BankAccountController::class)->names('admin.bank_accounts');
        Route::get('getAccounts', [ChequeBookController::class, 'getAccounts'])->name('admin.getAccounts');
        Route::get('getChequeBooks/{id}', [ChequeBookController::class, 'getChequeBooks'])->name('admin.getChequeBooks');
        Route::get('getChequeBooksAll', [ChequeBookController::class, 'getChequeBooksAll'])->name('admin.getChequeBooksAll');
        Route::get('getCheque/{id}', [ChequeBookController::class, 'getCheque'])->name('admin.getCheque');
        Route::post('cheque_books/approval', [ChequeBookController::class, 'approval'])->name('admin.cheque_books.approval');
        Route::get('getAccountNo/{id}', [ChequeBookController::class, 'getAccountNo'])->name('admin.getAccountNo');
        Route::post('cheque_books/reject', [ChequeBookController::class, 'reject'])->name('admin.cheque_books.reject');
        Route::get('cheque_books/details', [ChequeBookController::class, 'details'])->name('admin.cheque_books.details');
        Route::get('cheque_books/getDetails/{id}', [ChequeBookController::class, 'getDetailsById'])->name('admin.cheque_books.getDetailsById');
        Route::post('cheque_books/cheque/cleared', [ChequeBookController::class, 'cleared'])->name('admin.cheque.cleared');
        Route::post('cheque_books/cheque/cancelled', [ChequeBookController::class, 'cancelled'])->name('admin.cheque.cancelled');
        Route::get('cheque_books/cheques', [ChequeBookController::class, 'cheques'])->name('admin.cheque_books.cheques');
        Route::get('authorized_signatories', [ChequeBookController::class, 'authorized_signatories'])->name('admin.cheque_books.authorized_signatories');
        Route::resource('cheque_books', ChequeBookController::class)->names('admin.cheque_books');
        Route::resource('expense_categories', ExpenseCategoryController::class)->names('admin.expense_categories');
        Route::get('getcategory', [ExpenseSubCategoryController::class, 'getcategory'])->name('admin.cheque_books.getcategory');
        Route::resource('expense_sub_categories', ExpenseSubCategoryController::class)->names('admin.expense_sub_categories');
        Route::resource('company',CompanyController::class)->names('admin.company');
        Route::resource('items', ItemController::class)->names('admin.items');
        Route::get('stocks/search-items', [StockController::class, 'searchItems'])->name('admin.stocks.search');
        Route::post('stocks/import', [StockController::class, 'importExcel'])->name('admin.stocks.import');
        Route::get('stocks/template', [StockController::class, 'downloadTemplate'])->name('admin.stocks.template');
        Route::resource('stocks', StockController::class)->names('admin.stocks');
        Route::get('stock-report', [StockReportController::class, 'index'])->name('admin.stock_report.index');
        Route::get('stock-report/{item}/history', [StockReportController::class, 'history'])->name('admin.stock_report.history');
        Route::resource('customers', CustomerController::class)->names('admin.customers');
        // Sales / Receipts
        Route::get('sales/search-items', [SaleController::class, 'searchItems'])->name('admin.sales.search');
        Route::get('sales/{sale}/detail', [SaleController::class, 'getDetail'])->name('admin.sales.detail');
        Route::resource('sales', SaleController::class)->names('admin.sales');
        
        // Sales Report
        Route::get('sales-report', [SalesReportController::class, 'index'])->name('admin.sales_report.index');
    });
    Route::prefix('finance')->group(callback: function () {
        Route::get('get_sub_category/{id}', [ExpenseController::class, 'get_sub_category'])->name('finance.get_sub_category');
        Route::resource('expenses', ExpenseController::class)->names('finance.expenses');
        Route::Post('expense/approval/cancel/{id}', [ExpensApprovalController::class, 'cancel'])->name('finance.expense.approval.cancel');
        Route::Post('expense/approval/approved', [ExpensApprovalController::class, 'approved'])->name('finance.expense.approval.approved');
        Route::Post('expense/approval/rejected', [ExpensApprovalController::class, 'rejected'])->name('finance.expense.approval.rejected');
        Route::get('expense/approval/approved/list', [ExpensApprovalController::class, 'approved_list'])->name('finance.expense.approval.approved_list');
        Route::get('expense/approval/rejected/list', [ExpensApprovalController::class, 'rejected_list'])->name('finance.expense.approval.rejected_list');
        Route::resource('expense/approval', ExpensApprovalController::class)->names('finance.expense.approval');
        Route::get('expense/payment/amountOnWords', [PaymentController::class, 'amountOnWords'])->name('finance.expense.approval.amountOnWords');
        // Route::get('printCheque', [PaymentController::class, 'printCheque'])->name('printCheque');
        Route::resource('expense/payment', PaymentController::class)->names('finance.expense.payment');
        Route::get('/generate-pdf', [PaymentController::class, 'generatePdf']);
    });
    Route::prefix('reports')->group(callback: function () {
        Route::get('payments', [ReportController::class, 'payments'])->name('report.payments');
    });
});
