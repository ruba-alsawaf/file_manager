<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FileController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('folders')->name('folders.')->group(function () {
    Route::get('/', [FolderController::class, 'index'])->name('index');
    Route::post('/', [FolderController::class, 'create'])->name('create');
    Route::get('/tree', [FolderController::class, 'tree'])->name('tree');

    Route::prefix('{folder}')->group(function () {
        Route::get('/', [FolderController::class, 'show'])->name('show');
        Route::put('/', [FolderController::class, 'update'])->name('update');
        Route::delete('/', [FolderController::class, 'destroy'])->name('destroy');
    });
});


Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('files')->group(function () {
        Route::post('/create', [FileController::class, 'create']);
        Route::get('/{id}', [FileController::class, 'show']);
        Route::put('/{id}', [FileController::class, 'update']);
        Route::delete('/{id}', [FileController::class, 'destroy']);
        Route::get('/{id}/download', [FileController::class, 'download']);
    });
});
