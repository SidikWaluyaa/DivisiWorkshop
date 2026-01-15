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
use App\Http\Controllers\Admin\ComplaintController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin / Master Data Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::delete('services/bulk-destroy', [App\Http\Controllers\Admin\ServiceController::class, 'bulkDestroy'])->name('services.bulk-destroy');
        Route::resource('services', App\Http\Controllers\Admin\ServiceController::class);

        Route::delete('materials/bulk-destroy', [App\Http\Controllers\Admin\MaterialController::class, 'bulkDestroy'])->name('materials.bulk-destroy');
        Route::get('materials/export-pdf', [App\Http\Controllers\Admin\MaterialController::class, 'exportPdf'])->name('materials.export-pdf');
        Route::get('materials/export-excel', [App\Http\Controllers\Admin\MaterialController::class, 'exportExcel'])->name('materials.export-excel');
        Route::get('materials/template', [App\Http\Controllers\Admin\MaterialController::class, 'downloadTemplate'])->name('materials.template');
        Route::post('materials/import', [App\Http\Controllers\Admin\MaterialController::class, 'import'])->name('materials.import');
        Route::resource('materials', App\Http\Controllers\Admin\MaterialController::class);

        Route::delete('users/bulk-destroy', [App\Http\Controllers\Admin\UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);

        Route::get('complaints/trash', [ComplaintController::class, 'trash'])->name('complaints.trash');
        Route::post('complaints/{id}/restore', [ComplaintController::class, 'restore'])->name('complaints.restore');
        Route::delete('complaints/{id}/force-delete', [ComplaintController::class, 'forceDelete'])->name('complaints.force-delete');
        Route::resource('complaints', ComplaintController::class);
        Route::post('complaints/{complaint}/api-reply', [ComplaintController::class, 'apiReply'])->name('complaints.api_reply');

        Route::get('purchases/export-pdf', [App\Http\Controllers\Admin\PurchaseController::class, 'exportPdf'])->name('purchases.export-pdf');
        Route::delete('purchases/bulk-destroy', [App\Http\Controllers\Admin\PurchaseController::class, 'bulkDestroy'])->name('purchases.bulk-destroy');
        Route::resource('purchases', App\Http\Controllers\Admin\PurchaseController::class);
        Route::post('purchases/{purchase}/payment', [App\Http\Controllers\Admin\PurchaseController::class, 'updatePayment'])->name('purchases.payment');
        
        // Performance
        Route::get('/performance', [App\Http\Controllers\Admin\PerformanceController::class, 'index'])->name('performance.index');

        // System Tools
        Route::get('/system', [App\Http\Controllers\Admin\SystemController::class, 'index'])->name('system.index');
        Route::post('/system/reset', [App\Http\Controllers\Admin\SystemController::class, 'reset'])->name('system.reset');


        // Reporting Module
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
            Route::get('/financial/export', [App\Http\Controllers\Admin\ReportController::class, 'exportFinancial'])->name('financial.export');
            Route::get('/productivity/export', [App\Http\Controllers\Admin\ReportController::class, 'exportProductivity'])->name('productivity.export');
        });
    });

    // Gudang / Reception
    Route::prefix('reception')->name('reception.')->group(function () {
        Route::get('/', [ReceptionController::class, 'index'])->name('index');
        Route::get('/template', [ReceptionController::class, 'downloadTemplate'])->name('template');
        Route::get('/export', [ReceptionController::class, 'exportExcel'])->name('export');
        Route::post('/import', [ReceptionController::class, 'import'])->name('import');
        Route::post('/store', [ReceptionController::class, 'store'])->name('store');
        Route::delete('/bulk-delete', [ReceptionController::class, 'bulkDelete'])->name('bulk-delete');
        Route::patch('/{id}/update-email', [ReceptionController::class, 'updateEmail'])->name('update-email');
        Route::patch('/{id}/update-shoe-info', [ReceptionController::class, 'updateShoeInfo'])->name('update-shoe-info');
        Route::get('/print-tag/{id}', [ReceptionController::class, 'printTag'])->name('print-tag');
        Route::post('/{id}/process', [ReceptionController::class, 'process'])->name('process');
        Route::post('/{id}/send-email', [ReceptionController::class, 'sendEmail'])->name('send-email');
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
        Route::post('/{id}/update-station', [PreparationController::class, 'updateStation'])->name('update-station');
        Route::post('/{id}/finish', [PreparationController::class, 'finish'])->name('finish');
        Route::post('/{id}/approve', [PreparationController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [PreparationController::class, 'reject'])->name('reject');
        Route::post('/bulk-update', [PreparationController::class, 'bulkUpdate'])->name('bulk-update');
    });

    // Sortir & Material
    Route::prefix('sortir')->name('sortir.')->group(function () {
        Route::get('/', [SortirController::class, 'index'])->name('index');
        Route::get('/{id}', [SortirController::class, 'show'])->name('show');
        Route::post('/{id}/add-material', [App\Http\Controllers\SortirController::class, 'addMaterial'])->name('add-material');
        Route::post('/{id}/add-service', [App\Http\Controllers\SortirController::class, 'addService'])->name('add-service');
        Route::delete('/{id}/material/{materialId}', [SortirController::class, 'destroyMaterial'])->name('destroy-material');
        Route::post('/{id}/finish', [SortirController::class, 'finish'])->name('finish');
        Route::post('/bulk-update', [SortirController::class, 'bulkUpdate'])->name('bulk-update');
    });

    // Production
    Route::prefix('production')->name('production.')->group(function () {
        Route::get('/', [ProductionController::class, 'index'])->name('index');
        Route::post('/{id}/update-station', [ProductionController::class, 'updateStation'])->name('update-station');
        Route::post('/{id}/finish', [ProductionController::class, 'finish'])->name('finish');
        Route::post('/{id}/approve', [ProductionController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [ProductionController::class, 'reject'])->name('reject');
        Route::post('/bulk-update', [ProductionController::class, 'bulkUpdate'])->name('bulk-update');
    });

    // QC
    Route::prefix('qc')->name('qc.')->group(function () {
        Route::get('/', [QCController::class, 'index'])->name('index');
        Route::get('/{id}', [QCController::class, 'show'])->name('show');
        Route::post('/{id}/update-station', [QCController::class, 'updateStation'])->name('update-station');
        Route::post('/{id}/update', [QCController::class, 'update'])->name('update');
        Route::post('/{id}/fail', [QCController::class, 'fail'])->name('fail');
        Route::post('/bulk-update', [QCController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/{id}/pass', [QCController::class, 'pass'])->name('pass');
        Route::post('/{id}/finish', [QCController::class, 'finish'])->name('finish');
        Route::post('/{id}/approve', [QCController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [QCController::class, 'reject'])->name('reject');
    });

    // Finish & Pickup
    Route::prefix('finish')->name('finish.')->group(function () {
        Route::get('/trash', [FinishController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [FinishController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [FinishController::class, 'forceDelete'])->name('force-delete');
        
        Route::delete('/bulk-destroy', [FinishController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::get('/', [FinishController::class, 'index'])->name('index');
        Route::get('/{id}', [FinishController::class, 'show'])->name('show');
        Route::delete('/{id}', [FinishController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/pickup', [FinishController::class, 'pickup'])->name('pickup');
        Route::post('/{id}/add-service', [FinishController::class, 'addService'])->name('add-service');
        Route::post('/{id}/send-email', [FinishController::class, 'sendEmail'])->name('send-email');
    });

    // Gallery / Documentation
    Route::prefix('gallery')->name('gallery.')->group(function () {
        Route::get('/', [App\Http\Controllers\GalleryController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\GalleryController::class, 'show'])->name('show');
    });

    // Photo Documentation Routes
    Route::post('/work-orders/{id}/photos', [App\Http\Controllers\WorkOrderPhotoController::class, 'store'])->name('photos.store');
    Route::delete('/photos/{id}', [App\Http\Controllers\WorkOrderPhotoController::class, 'destroy'])->name('photos.destroy');

    // Manual WhatsApp Trigger
    Route::post('/orders/{id}/whatsapp-send', [App\Http\Controllers\WhatsAppController::class, 'send'])->name('orders.whatsapp_send');
    Route::post('/orders/{id}/whatsapp-template-test', [App\Http\Controllers\WhatsAppController::class, 'sendTemplateTest'])->name('orders.whatsapp_template_test');
});

// Public Complaint Routes
Route::get('/complaints', [App\Http\Controllers\ComplaintController::class, 'index'])->name('complaints.index');
Route::post('/complaints', [App\Http\Controllers\ComplaintController::class, 'store'])->name('complaints.store');
Route::get('/complaints/success/{complaint}', [App\Http\Controllers\ComplaintController::class, 'success'])->name('complaints.success');

require __DIR__.'/auth.php';
