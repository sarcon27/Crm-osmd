<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceAccountController;
use App\Http\Controllers\FinanceTransactionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MeasureController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TariffController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard');
});

Route::middleware('auth')->group(function () {
    Route::post('user/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('user/login', [AuthController::class, 'auth'])->name('auth');

});

Route::group([
    'prefix' => 'dashboard',
    'as' => 'dashboard.',
    'middleware' => ['auth'],
], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::resource('/buildings', BuildingController::class);
    Route::get('/apartments/{apartment}/meter-services', [ApartmentController::class, 'getMeterServices'])->name('apartments.meter-services');
    Route::put('/apartments/create-invoice', [ApartmentController::class, 'createFromInvoice'])->name('apartments.create_invoice');
    Route::resource('/apartments', ApartmentController::class);
    Route::get('/owners/search', [OwnerController::class, 'search'])->name('owners.search');
    Route::resource('/owners', OwnerController::class);
    Route::resource('/services', ServiceController::class);
    Route::resource('/tariffs', TariffController::class)->only('store', 'update');
    Route::resource('/measures', MeasureController::class)->only('store', 'update');

    Route::get('/finance-accounts/search', [FinanceAccountController::class, 'search'])->name('finance-accounts.search');
    Route::resource('/finance-accounts', FinanceAccountController::class)->only('index', 'store');

    Route::post('/finance-transactions/{transaction}/post', [FinanceTransactionController::class, 'post'])->name('finance.transactions.post');
    Route::post('/finance-transactions/{transaction}/reject', [FinanceTransactionController::class, 'reject'])->name('finance.transactions.reject');
    Route::resource('/finance-transactions', FinanceTransactionController::class)->only('index', 'store');

    Route::resource('/meters', MeterController::class)->only('index', 'store');

    Route::get('/invoices/calculate/apartment/{apartment}', [InvoiceController::class, 'calculate'])->name('invoices.calculate');

    Route::resource('/invoices', InvoiceController::class)->only('index', 'store');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'save'])->name('settings.save');

});
