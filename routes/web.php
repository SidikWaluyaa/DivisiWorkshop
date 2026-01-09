<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\WashingController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\PreparationController;
use App\Http\Controllers\SortirController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\QCController;
use App\Http\Controllers\FinishController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrackingController;

Route::get('/', function () {
    return redirect()->route('tracking.index');
});

// Public Tracking Routes (No Auth Required)
Route::get('/track', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/track', [TrackingController::class, 'track'])->name('tracking.track');


Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\UserController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin / Master Data Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::delete('services/bulk-destroy', [App\Http\Controllers\Admin\ServiceController::class, 'bulkDestroy'])->name('services.bulk-destroy');
        Route::resource('services', App\Http\Controllers\Admin\ServiceController::class);

        Route::delete('materials/bulk-destroy', [App\Http\Controllers\Admin\MaterialController::class, 'bulkDestroy'])->name('materials.bulk-destroy');
        Route::resource('materials', App\Http\Controllers\Admin\MaterialController::class);

        Route::delete('users/bulk-destroy', [App\Http\Controllers\Admin\UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);

        Route::get('purchases/export-pdf', [App\Http\Controllers\Admin\PurchaseController::class, 'exportPdf'])->name('purchases.export-pdf');
        Route::delete('purchases/bulk-destroy', [App\Http\Controllers\Admin\PurchaseController::class, 'bulkDestroy'])->name('purchases.bulk-destroy');
        Route::resource('purchases', App\Http\Controllers\Admin\PurchaseController::class);
        Route::post('purchases/{purchase}/payment', [App\Http\Controllers\Admin\PurchaseController::class, 'updatePayment'])->name('purchases.payment');
        
        // Performance
        Route::get('/performance', [App\Http\Controllers\Admin\PerformanceController::class, 'index'])->name('performance.index');
    });

    // Gudang / Reception
    Route::prefix('reception')->name('reception.')->group(function () {
        Route::get('/', [ReceptionController::class, 'index'])->name('index');
        Route::post('/import', [ReceptionController::class, 'import'])->name('import');
        Route::delete('/reset', [ReceptionController::class, 'reset'])->name('reset');
        Route::get('/print-tag/{id}', [ReceptionController::class, 'printTag'])->name('print-tag');
        Route::post('/{id}/process', [ReceptionController::class, 'process'])->name('process');
    });



    // Assessment
    Route::prefix('assessment')->name('assessment.')->group(function () {
        Route::get('/', [AssessmentController::class, 'index'])->name('index');
        Route::get('/{id}/create', [AssessmentController::class, 'create'])->name('create');
        Route::post('/{id}/store', [AssessmentController::class, 'store'])->name('store');
    });

    // Preparation
    Route::prefix('preparation')->name('preparation.')->group(function () {
        Route::get('/', [PreparationController::class, 'index'])->name('index');
        Route::get('/{id}', [PreparationController::class, 'show'])->name('show');
        Route::post('/{id}/update', [PreparationController::class, 'update'])->name('update');
        Route::post('/{id}/finish', [PreparationController::class, 'finish'])->name('finish');
    });

    // Sortir & Material
    Route::prefix('sortir')->name('sortir.')->group(function () {
        Route::get('/', [SortirController::class, 'index'])->name('index');
        Route::get('/{id}', [SortirController::class, 'show'])->name('show');
        Route::post('/{id}/add', [SortirController::class, 'addMaterial'])->name('add-material');
        Route::delete('/{id}/material/{materialId}', [SortirController::class, 'destroyMaterial'])->name('destroy-material');
        Route::post('/{id}/finish', [SortirController::class, 'finish'])->name('finish');
    });

    // Producion
    Route::prefix('production')->name('production.')->group(function () {
        Route::get('/', [ProductionController::class, 'index'])->name('index');
        Route::post('/{id}/start', [ProductionController::class, 'start'])->name('start');
        Route::post('/{id}/finish', [ProductionController::class, 'finish'])->name('finish');
    });

    // QC
    Route::prefix('qc')->name('qc.')->group(function () {
        Route::get('/', [QCController::class, 'index'])->name('index');
        Route::get('/{id}', [QCController::class, 'show'])->name('show');
        Route::post('/{id}/update', [QCController::class, 'update'])->name('update');
        Route::post('/{id}/fail', [QCController::class, 'fail'])->name('fail');
        Route::post('/{id}/pass', [QCController::class, 'pass'])->name('pass');
    });

    // Finish & Pickup
    Route::prefix('finish')->name('finish.')->group(function () {
        Route::get('/', [FinishController::class, 'index'])->name('index');
        Route::get('/{id}', [FinishController::class, 'show'])->name('show');
        Route::post('/{id}/pickup', [FinishController::class, 'pickup'])->name('pickup');
    });
});

require __DIR__.'/auth.php';
