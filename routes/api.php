<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/dashboard-summary', 'App\Http\Controllers\Api\V1\DashboardApiController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);
    
    Route::get('/finance-sync', 'App\Http\Controllers\Api\V1\FinanceSyncController@index')
        ->middleware(\App\Http\Middleware\ApiKeyMiddleware::class);
});
