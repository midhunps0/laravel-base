<?php

use App\Http\Controllers\AggregatesController;
use App\Services\ScriptService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ScriptController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientScriptController;
use App\Http\Controllers\Auth\Custom\LoginController;
use App\Http\Controllers\Auth\Custom\PasswordController;


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

Route::get('/', [LoginController::class, 'create']);

// Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/master-data', [DashboardController::class, 'masterData'])->middleware(['auth'])->name('dashboard.masterdata');
    Route::get('users/select-ids', [UserController::class, 'queryIds'])->name('users.selectIds');
    Route::get('users/download', [UserController::class, 'download'])->name('users.download');
    Route::resource('users', UserController::class)->middleware('access.control:user.create_any');


    Route::get('clients/get-list', [ClientController::class, 'list'])->name('clients.list');
    Route::get('clients/select-ids', [ClientController::class, 'queryIds'])->name('clients.selectIds');
    Route::get('clients/download', [ClientController::class, 'download'])
        ->name('clients.download');
    Route::get('clients/{id}/select-ids', [ClientController::class, 'queryShowIds'])->name('clients.show.selectIds');
    Route::get('clients/{id}/download', [ClientController::class, 'showDownload'])->name('clients.show.download');
    Route::get('clients/{id}/download-order', [ClientController::class, 'downloadOrder'])
        ->name('clients.order.download');
    Route::get('import/clients', [ClientController::class, 'bulkImportCreate'])->name('clients.import.create');
    Route::post('import/clients', [ClientController::class, 'bulkImportStore'])->name('clients.import.store');
    Route::get('import/client-portfolio', [ClientController::class, 'portfolioImportCreate'])->name('clientsportfolio.import.create');
    Route::post('import/client-portfolio', [ClientController::class, 'portfolioImportStore'])->name('clientsportfolio.import.store');
    Route::post('clients/{id}/create-script', [ClientController::class, 'createScript'])->name('clients.script.create');
    Route::post('clients/{id}/update-script', [ClientController::class, 'updateScript'])->name('clients.script.update');
    Route::post('clients/{id}/delete-script', [ClientController::class, 'deleteScript'])->name('clients.script.delete');

    Route::resource('clients', ClientController::class);

    Route::get('scripts/select-ids', [ScriptController::class, 'queryIds'])->name('scripts.selectIds');
    Route::get('scripts/download', [ScriptController::class, 'download'])->name('scripts.download');
    Route::get('scripts/{id}/select-ids', [ScriptController::class, 'queryShowIds'])->name('scripts.show.selectIds');
    Route::get('scripts/{id}/download', [ScriptController::class, 'showDownload'])->name('scripts.show.download');
    Route::get('scripts/get-list', [ScriptController::class, 'list'])->name('scripts.list');
    Route::get('sripts/{id}/download-order', [ScriptController::class, 'downloadOrder'])
        ->name('scripts.order.download');
    Route::get('import/scripts', [ScriptController::class, 'bulkImportCreate'])->name('scripts.import.create');
    Route::post('import/scripts', [ScriptController::class, 'bulkImportStore'])->name('scripts.import.store');
    Route::resource('scripts', ScriptController::class);

    Route::get('import/trade-backup', [ImportController::class, 'tradeBackupForm'])->name('get.import.trade_backup');
    Route::post('import/trade-backup', [ImportController::class, 'tradeBackupImport'])->name('post.import.trade_backup');

    Route::get('clientsripts/get-list', [ClientScriptController::class, 'list'])->name('clientsripts.list');
    Route::get('clientsripts/verify-order', [ClientScriptController::class, 'verifySellOrder'])->name('clientsripts.sellorder.verify');
    Route::get('clientsripts/analyse-order', [ClientScriptController::class, 'analyseSellOrder'])->name('clientsripts.sellorder.analyse');
    Route::get('clientsripts/select-ids', [ClientScriptController::class, 'queryIds'])->name('clientscripts.selectIds');
    Route::get('clientsripts/download', [ClientScriptController::class, 'download'])
        ->name('clientscripts.download');
    Route::get('clientsripts/download-order', [ClientScriptController::class, 'downloadOrder'])
        ->name('clientscripts.order.download');
    Route::resource('clientscripts', ClientScriptController::class)->only('index');

    Route::post('pass-reset', [PasswordController::class, 'store'])->name('custom.password.reset');

    Route::get('dashboard', [AggregatesController::class, 'adminIndex'])->name('dashboard');
    Route::get('rms-summary-download', [AggregatesController::class, 'adminDownload'])->name('aggregates.admin.download');
    Route::get('rms-summary-select-ids', [AggregatesController::class, 'adminSelectIds'])->name('aggregates.admin.selectIds');

    Route::get('clients-summary', [AggregatesController::class, 'rmIndex'])->name('aggregates.rm');
    Route::get('clients-summary-download', [AggregatesController::class, 'rmDownload'])->name('aggregates.rm.download');
    Route::get('clients-summary-select-ids', [AggregatesController::class, 'rmSelectIds'])->name('aggregates.rm.selectIds');
});
