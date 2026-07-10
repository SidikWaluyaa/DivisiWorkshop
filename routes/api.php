<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Tracking Endpoint (CORS-restricted, rate-limited, no API key required)
    Route::get('/public/track', [\App\Http\Controllers\Api\V1\PublicTrackingApiController::class, 'track'])
        ->middleware('throttle:60,1');

    Route::get('/public/track-shoes', [\App\Http\Controllers\Api\V1\PublicShoeTrackingApiController::class, 'track'])
        ->middleware('throttle:60,1');

    // Public Warranty Claim Endpoints (CORS-restricted, rate-limited, no API key required)
    Route::post('/public/warranty-claims/check', [\App\Http\Controllers\Api\V1\PublicWarrantyClaimApiController::class, 'check'])
        ->middleware('throttle:30,1');
    Route::post('/public/warranty-claims/submit', [\App\Http\Controllers\Api\V1\PublicWarrantyClaimApiController::class, 'submit'])
        ->middleware('throttle:10,1');

    Route::get('/dashboard-summary', 'App\Http\Controllers\Api\V1\DashboardApiController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);
    
    Route::get('/cx-summary', 'App\Http\Controllers\Api\V1\CxDashboardApiController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);
    
    Route::get('/finance/dashboard', [\App\Http\Controllers\Api\V1\FinanceDashboardApiController::class, 'index'])
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/finance-sync', 'App\Http\Controllers\Api\V1\FinanceSyncController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/payment-sync', 'App\Http\Controllers\Api\V1\PaymentSyncController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    // Warehouse Sync Suite
    Route::get('/warehouse-inventory-sync', 'App\Http\Controllers\Api\V1\WarehouseSyncController@inventoryIndex')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);
    
    Route::get('/warehouse-request-sync', 'App\Http\Controllers\Api\V1\WarehouseSyncController@requestIndex')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);
    
    Route::get('/warehouse-transaction-sync', 'App\Http\Controllers\Api\V1\WarehouseSyncController@transactionIndex')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/warehouse-sortir-sync', 'App\Http\Controllers\Api\V1\WarehouseSyncController@sortirIndex')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);
        
    Route::get('/warehouse-forecast-sync', 'App\Http\Controllers\Api\V1\WarehouseSyncController@forecastIndex')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);
        
    Route::get('/cx-after-confirmation', 'App\Http\Controllers\Api\V1\CxAfterConfirmationApiController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/cx-overdue', [\App\Http\Controllers\Api\V1\CxOverdueApiController::class, 'index'])
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/warehouse-summary', 'App\Http\Controllers\Api\V1\WarehouseDashboardApiController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/warehouse-manifest-summary', 'App\Http\Controllers\Api\V1\WarehouseManifestDashboardApiController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/warehouse-sortir-summary', 'App\Http\Controllers\Api\V1\WarehouseSortirDashboardApiController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/warehouse-production-summary', 'App\Http\Controllers\Api\V1\WarehouseProductionDashboardApiController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/warehouse-qc-summary', 'App\Http\Controllers\Api\V1\WarehouseQcDashboardApiController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/warehouse-piutang-sync', 'App\Http\Controllers\Api\V1\WarehouseSyncController@piutangIndex')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/warehouse-piutang-before-sync', 'App\Http\Controllers\Api\V1\WarehouseSyncController@piutangBeforeIndex')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/warehouse-shoerack-sync', 'App\Http\Controllers\Api\V1\WarehouseSyncController@shoeRackIndex')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/service-tracking-sync', 'App\Http\Controllers\Api\V1\WarehouseSyncController@serviceTrackingIndex')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/workshop-sync', 'App\Http\Controllers\Api\V1\WorkshopSyncController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    // Customer Portal Integration (Secure client API with throttling)
    Route::get('/customer-portal/orders', [\App\Http\Controllers\Api\V1\CustomerPortalApiController::class, 'getOrdersByPhone'])
        ->middleware([\App\Http\Middleware\ApiKeyMiddleware::class, 'throttle:60,1']);
    Route::get('/customer-portal/customers', [\App\Http\Controllers\Api\V1\CustomerPortalApiController::class, 'getCustomers'])
        ->middleware([\App\Http\Middleware\ApiKeyMiddleware::class, 'throttle:60,1']);

    // CS Module APIs
    Route::get('/cs-forecasting', [\App\Http\Controllers\Api\V1\CsForecastingApiController::class, 'index'])
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);

    Route::get('/cs-kpi-leaderboard', [\App\Http\Controllers\Api\V1\CsKpiLeaderboardApiController::class, 'index'])
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);
});
