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
        
        // Services
        Route::middleware('access:admin.services')->group(function () {
            Route::delete('services/bulk-destroy', [App\Http\Controllers\Admin\ServiceController::class, 'bulkDestroy'])->name('services.bulk-destroy');
            Route::get('services/export-excel', [App\Http\Controllers\Admin\ServiceController::class, 'exportExcel'])->name('services.export-excel');
            Route::get('services/template', [App\Http\Controllers\Admin\ServiceController::class, 'downloadTemplate'])->name('services.template');
            Route::post('services/import', [App\Http\Controllers\Admin\ServiceController::class, 'import'])->name('services.import');
            Route::resource('services', App\Http\Controllers\Admin\ServiceController::class);
        });

        // Materials
        Route::middleware('access:admin.materials')->group(function () {
            Route::delete('materials/bulk-destroy', [App\Http\Controllers\Admin\MaterialController::class, 'bulkDestroy'])->name('materials.bulk-destroy');
            Route::get('materials/export-pdf', [App\Http\Controllers\Admin\MaterialController::class, 'exportPdf'])->name('materials.export-pdf');
            Route::get('materials/export-excel', [App\Http\Controllers\Admin\MaterialController::class, 'exportExcel'])->name('materials.export-excel');
            Route::get('materials/template', [App\Http\Controllers\Admin\MaterialController::class, 'downloadTemplate'])->name('materials.template');
            Route::post('materials/import', [App\Http\Controllers\Admin\MaterialController::class, 'import'])->name('materials.import');
            Route::resource('materials', App\Http\Controllers\Admin\MaterialController::class);
        });

        // Users
        Route::middleware('access:admin.users')->group(function () {
            Route::delete('users/bulk-destroy', [App\Http\Controllers\Admin\UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
            Route::resource('users', App\Http\Controllers\Admin\UserController::class);
        });

        // Complaints
        Route::middleware('access:admin.complaints')->group(function () {
            Route::get('complaints/trash', [ComplaintController::class, 'trash'])->name('complaints.trash');
            Route::post('complaints/{id}/restore', [ComplaintController::class, 'restore'])->name('complaints.restore');
            Route::delete('complaints/{id}/force-delete', [ComplaintController::class, 'forceDelete'])->name('complaints.force-delete');
            Route::resource('complaints', ComplaintController::class);
            Route::post('complaints/{complaint}/api-reply', [ComplaintController::class, 'apiReply'])->name('complaints.api_reply');
        });

        // Purchases
        Route::middleware('access:admin.purchases')->group(function () {
            Route::get('purchases/export-pdf', [App\Http\Controllers\Admin\PurchaseController::class, 'exportPdf'])->name('purchases.export-pdf');
            Route::delete('purchases/bulk-destroy', [App\Http\Controllers\Admin\PurchaseController::class, 'bulkDestroy'])->name('purchases.bulk-destroy');
            Route::resource('purchases', App\Http\Controllers\Admin\PurchaseController::class);
            Route::post('purchases/{purchase}/payment', [App\Http\Controllers\Admin\PurchaseController::class, 'updatePayment'])->name('purchases.payment');
        });

        // Performance
        Route::middleware('access:admin.performance')->group(function () {
            Route::get('/performance', [App\Http\Controllers\Admin\PerformanceController::class, 'index'])->name('performance.index');
        });

        // System Tools
        Route::middleware('access:admin.system')->group(function () {
            Route::get('/system', [App\Http\Controllers\Admin\SystemController::class, 'index'])->name('system.index');
            Route::post('/system/reset', [App\Http\Controllers\Admin\SystemController::class, 'reset'])->name('system.reset');
        });

        // Reporting Module
        Route::prefix('reports')->name('reports.')->middleware('access:admin.reports')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
            Route::get('/financial/export', [App\Http\Controllers\Admin\ReportController::class, 'exportFinancial'])->name('financial.export');
            Route::get('/productivity/export', [App\Http\Controllers\Admin\ReportController::class, 'exportProductivity'])->name('productivity.export');
        });

        // Customer Master Data
        Route::middleware('access:admin.customers')->group(function () {
            Route::resource('customers', App\Http\Controllers\Admin\CustomerController::class);
            Route::post('customers/{id}/upload-photo', [App\Http\Controllers\Admin\CustomerController::class, 'uploadPhoto'])->name('customers.upload-photo');
        });
    });

    // Gudang / Reception
    // Customer Experience (CX) logic
    Route::prefix('cx')->name('cx.')->middleware('access:cx')->group(function () {
        Route::get('/', [App\Http\Controllers\CustomerExperienceController::class, 'index'])->name('index');
        Route::post('/{id}/process', [App\Http\Controllers\CustomerExperienceController::class, 'process'])->name('process');
        Route::delete('/{id}', [App\Http\Controllers\CustomerExperienceController::class, 'destroy'])->name('destroy'); // Quick Delete
    });

    Route::prefix('reception')->name('reception.')->middleware('access:gudang')->group(function () {
        Route::get('/', [ReceptionController::class, 'index'])->name('index');
        Route::get('/template', [ReceptionController::class, 'downloadTemplate'])->name('template');
        Route::get('/export', [ReceptionController::class, 'exportExcel'])->name('export');
        Route::post('/import', [ReceptionController::class, 'import'])->name('import');
        Route::post('/store', [ReceptionController::class, 'store'])->name('store');
        Route::delete('/bulk-delete', [ReceptionController::class, 'bulkDelete'])->name('bulk-delete');
        Route::patch('/{id}/update-email', [ReceptionController::class, 'updateEmail'])->name('update-email');
        Route::patch('/{id}/update-shoe-info', [ReceptionController::class, 'updateShoeInfo'])->name('update-shoe-info');
        Route::get('/print-tag/{id}', [ReceptionController::class, 'printTag'])->name('print-tag');
        Route::get('/print-spk/{id}', [ReceptionController::class, 'printSpk'])->name('print-spk');
        Route::post('/{id}/process', [ReceptionController::class, 'process'])->name('process');
        Route::post('/{id}/receive', [ReceptionController::class, 'receive'])->name('receive'); // New Step 1
        Route::post('/{id}/confirm', [ReceptionController::class, 'confirm'])->name('confirm');
        Route::post('/{id}/send-email', [ReceptionController::class, 'sendEmail'])->name('send-email');
        Route::post('/{id}/skip-assessment', [ReceptionController::class, 'skipAssessment'])->name('skip-assessment'); // Directly to Preparation
        
        // NEW: Reception Detail & Processing
        Route::get('/{id}', [ReceptionController::class, 'show'])->name('show');
        Route::post('/{id}/process-reception', [ReceptionController::class, 'processReception'])->name('process-reception');
        Route::delete('/{id}', [ReceptionController::class, 'destroy'])->name('destroy');
    });



    // Assessment
    Route::prefix('assessment')->name('assessment.')->middleware('access:assessment')->group(function () {
        Route::get('/', [AssessmentController::class, 'index'])->name('index');
        Route::get('/{id}/create', [AssessmentController::class, 'create'])->name('create');
        Route::post('/{id}/store', [AssessmentController::class, 'store'])->name('store');
        Route::get('/{id}/print-spk', [AssessmentController::class, 'printSpk'])->name('print-spk'); // New Route for Detailed Print
        Route::delete('/{id}', [AssessmentController::class, 'destroy'])->name('destroy');
    });

    // Finance Routes (Consolidated below)

    // Preparation
    Route::prefix('preparation')->name('preparation.')->middleware('access:preparation')->group(function () {
        Route::get('/', [PreparationController::class, 'index'])->name('index');
        Route::get('/{id}', [PreparationController::class, 'show'])->name('show');
        Route::post('/{id}/update', [PreparationController::class, 'update'])->name('update');
        Route::post('/{id}/update-station', [PreparationController::class, 'updateStation'])->name('update-station');
        Route::post('/{id}/finish', [PreparationController::class, 'finish'])->name('finish');
        Route::post('/{id}/approve', [PreparationController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [PreparationController::class, 'reject'])->name('reject');
        Route::post('/bulk-update', [PreparationController::class, 'bulkUpdate'])->name('bulk-update');
    });

    // Sortir & Material (Sortir Access)
    Route::prefix('sortir')->name('sortir.')->middleware('access:sortir')->group(function () {
        Route::get('/', [SortirController::class, 'index'])->name('index');
        Route::get('/{id}', [SortirController::class, 'show'])->name('show');
        Route::post('/{id}/add-material', [App\Http\Controllers\SortirController::class, 'addMaterial'])->name('add-material');
        Route::post('/{id}/update-materials', [App\Http\Controllers\SortirController::class, 'updateMaterials'])->name('update-materials');
        Route::post('/{id}/add-service', [App\Http\Controllers\SortirController::class, 'addService'])->name('add-service');
        Route::delete('/{id}/material/{materialId}', [SortirController::class, 'destroyMaterial'])->name('destroy-material');
        Route::post('/{id}/finish', [SortirController::class, 'finish'])->name('finish');
        Route::post('/{id}/skip-to-production', [SortirController::class, 'skipToProduction'])->name('skip-production'); // Direct Button
        Route::post('/bulk-update', [SortirController::class, 'bulkUpdate'])->name('bulk-update');
    });

    // Production
    Route::prefix('production')->name('production.')->middleware('access:production')->group(function () {
        Route::get('/', [ProductionController::class, 'index'])->name('index');
        Route::post('/{id}/update-station', [ProductionController::class, 'updateStation'])->name('update-station');
        Route::post('/{id}/finish', [ProductionController::class, 'finish'])->name('finish');
        Route::post('/{id}/approve', [ProductionController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [ProductionController::class, 'reject'])->name('reject');
        Route::post('/bulk-update', [ProductionController::class, 'bulkUpdate'])->name('bulk-update');
    });

    // QC
    Route::prefix('qc')->name('qc.')->middleware('access:qc')->group(function () {
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
    Route::prefix('finish')->name('finish.')->middleware('access:finish')->group(function () {
        Route::get('/trash', [FinishController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [FinishController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [FinishController::class, 'forceDelete'])->name('force-delete');
        
        Route::post('/bulk-restore', [FinishController::class, 'bulkRestore'])->name('bulk-restore');
        Route::delete('/bulk-force-delete', [FinishController::class, 'bulkForceDelete'])->name('bulk-force-delete');

        Route::delete('/bulk-destroy', [FinishController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::get('/', [FinishController::class, 'index'])->name('index');
        Route::get('/{id}', [FinishController::class, 'show'])->name('show');
        Route::delete('/{id}', [FinishController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/pickup', [FinishController::class, 'pickup'])->name('pickup');
        Route::post('/{id}/add-service', [FinishController::class, 'addService'])->name('add-service');
        Route::post('/{id}/create-oto', [FinishController::class, 'createOTO'])->name('create-oto');
        Route::post('/{id}/send-email', [FinishController::class, 'sendEmail'])->name('send-email');
    });

    // Customer Experience (CX) Followup & OTO
    Route::prefix('cx')->name('cx.')->group(function () {
        Route::get('/', [App\Http\Controllers\CustomerExperienceController::class, 'index'])->name('index');
        Route::get('/cancelled', [App\Http\Controllers\CustomerExperienceController::class, 'cancelled'])->name('cancelled');
        Route::post('/{id}/process', [App\Http\Controllers\CustomerExperienceController::class, 'process'])->name('process');
        
        // CX OTO Routes
        Route::get('/oto', [App\Http\Controllers\CXOTOController::class, 'index'])->name('oto.index');
        Route::post('/oto/{id}/contact', [App\Http\Controllers\CXOTOController::class, 'markContacted'])->name('oto.contact');
        Route::post('/oto/{id}/accept', [App\Http\Controllers\CXOTOController::class, 'customerAccept'])->name('oto.accept');
        Route::post('/oto/{id}/reject', [App\Http\Controllers\CXOTOController::class, 'customerReject'])->name('oto.reject');
        Route::post('/oto/{id}/cancel', [App\Http\Controllers\CXOTOController::class, 'cancel'])->name('oto.cancel');
    });

    // CS Dashboard (Lead Management - New Module)
    Route::prefix('cs')->name('cs.')->middleware('auth')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\CsLeadController::class, 'index'])->name('dashboard');
        Route::post('/leads', [App\Http\Controllers\CsLeadController::class, 'store'])->name('leads.store');
        Route::post('/leads/{id}/update-status', [App\Http\Controllers\CsLeadController::class, 'updateStatus'])->name('leads.update-status');
        Route::get('/leads/{id}', [App\Http\Controllers\CsLeadController::class, 'show'])->name('leads.show');
        Route::post('/leads/{id}/spk', [App\Http\Controllers\CsLeadController::class, 'storeSpk'])->name('leads.spk.store'); // Create SPK Route
        Route::delete('/leads/{id}', [App\Http\Controllers\CsLeadController::class, 'destroy'])->name('leads.destroy'); // New Delete Route
    });
    
    Route::post('/cx-issues', [App\Http\Controllers\CxIssueController::class, 'store'])->name('cx-issues.store');

    // Gallery / Documentation
    Route::prefix('gallery')->name('gallery.')->group(function () {
        Route::get('/', [App\Http\Controllers\GalleryController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\GalleryController::class, 'show'])->name('show');
    });

    // Finance Routes
    Route::get('finance', [App\Http\Controllers\FinanceController::class, 'index'])->name('finance.index');
    Route::get('finance/{workOrder}', [App\Http\Controllers\FinanceController::class, 'show'])->name('finance.show'); // New Route
    Route::post('finance/{workOrder}/payment', [App\Http\Controllers\FinanceController::class, 'storePayment'])->name('finance.payment.store');
    Route::post('finance/{workOrder}/update-status', [App\Http\Controllers\FinanceController::class, 'updateStatus'])->name('finance.status.update');
    Route::get('finance/{workOrder}/export-payment-history', [App\Http\Controllers\FinanceController::class, 'exportPaymentHistory'])->name('finance.export-payment-history');
    Route::delete('finance/{workOrder}', [App\Http\Controllers\FinanceController::class, 'destroy'])->name('finance.destroy');

    // Work Order Photos
    // Route::resource('work-order-photos', App\Http\Controllers\WorkOrderPhotoController::class);
    Route::post('orders/{order}/photos', [App\Http\Controllers\WorkOrderPhotoController::class, 'store'])->name('work-order-photos.store');
    Route::delete('/photos/{id}', [App\Http\Controllers\WorkOrderPhotoController::class, 'destroy'])->name('photos.destroy');

    // Manual WhatsApp Trigger
    Route::post('/orders/{id}/whatsapp-send', [App\Http\Controllers\WhatsAppController::class, 'send'])->name('orders.whatsapp_send');
    Route::post('/orders/{id}/whatsapp-template-test', [App\Http\Controllers\WhatsAppController::class, 'sendTemplateTest'])->name('orders.whatsapp_template_test');
});

// Public Complaint Routes
Route::get('/complaints', [App\Http\Controllers\ComplaintController::class, 'index'])->name('complaints.index');
Route::post('/complaints', [App\Http\Controllers\ComplaintController::class, 'store'])->name('complaints.store');
Route::get('/complaints/success/{complaint}', [App\Http\Controllers\ComplaintController::class, 'success'])->name('complaints.success');

// Public CS Form (Signed URL)
Route::get('/c/form/{lead}', [App\Http\Controllers\CsLeadController::class, 'guestForm'])->name('cs.guest.form')->middleware('signed');
Route::post('/c/form/{lead}', [App\Http\Controllers\CsLeadController::class, 'guestUpdate'])->name('cs.guest.update')->middleware('signed');

require __DIR__.'/auth.php';
