<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/dashboard-summary', 'App\Http\Controllers\Api\V1\DashboardApiController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);
    
    Route::get('/cx-summary', 'App\Http\Controllers\Api\V1\CxDashboardApiController@index')
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

    Route::get('/warehouse-summary', 'App\Http\Controllers\Api\V1\WarehouseDashboardApiController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);
});
