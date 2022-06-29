<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\Custom\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientScriptController;
use App\Http\Controllers\ScriptController;
use App\Services\ScriptService;

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

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('users/select-ids', [UserController::class, 'queryIds'])->name('users.selectIds');
    Route::get('users/download', [UserController::class, 'download'])->name('users.download');
    Route::resource('users', UserController::class);


    Route::get('clients/get-list', [ClientController::class, 'list'])->name('clients.list');
    Route::get('clients/select-ids', [ClientController::class, 'queryIds'])->name('clients.selectIds');
    Route::get('clients/download', [ClientController::class, 'download'])
        ->name('clients.download');
    Route::get('clients/{id}/select-ids', [ClientController::class, 'queryShowIds'])->name('clients.show.selectIds');
    Route::get('clients/{id}/download', [ClientController::class, 'showDownload'])->name('clients.show.download');
    Route::resource('clients', ClientController::class);

    Route::get('scripts/select-ids', [ScriptController::class, 'queryIds'])->name('scripts.selectIds');
    Route::get('scripts/download', [ScriptController::class, 'download'])->name('scripts.download');
    Route::get('scripts/{id}/select-ids', [ScriptController::class, 'queryShowIds'])->name('scripts.show.selectIds');
    Route::get('scripts/{id}/download', [ScriptController::class, 'showDownload'])->name('scripts.show.download');
    Route::get('scripts/get-list', [ScriptController::class, 'list'])->name('scripts.list');
    Route::resource('scripts', ScriptController::class);

    Route::get('client_scripts/select-ids', [ClientScriptController::class, 'queryIds'])->name('client_scripts.selectIds');
    Route::get('client_scripts/download', [ClientScriptController::class, 'download'])->name('client_scripts.download');
    Route::get('client_scripts/{id}/select-ids', [ClientScriptController::class, 'queryShowIds'])->name('client_scripts.show.selectIds');
    Route::get('client_scripts/{id}/download', [ClientScriptController::class, 'showDownload'])->name('client_scripts.show.download');
    Route::resource('client_scripts', ClientScriptController::class);
});
