<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\WarehouseApiService;
use App\Http\Resources\V1\WarehouseInventoryResource;
use App\Http\Resources\V1\WarehouseRequestResource;
use App\Http\Resources\V1\WarehouseTransactionResource;
use Illuminate\Http\Request;

class WarehouseSyncController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseApiService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    /**
     * Get inventory stock levels.
     */
    public function inventoryIndex(Request $request)
    {
        $data = $this->warehouseService->getInventoryData();
        return WarehouseInventoryResource::collection($data);
    }

    /**
     * Get material requests.
     */
    public function requestIndex(Request $request)
    {
        $data = $this->warehouseService->getRequestData($request->start_date, $request->end_date);
        return WarehouseRequestResource::collection($data);
    }

    /**
     * Get inventory transactions.
     */
    public function transactionIndex(Request $request)
    {
        $data = $this->warehouseService->getTransactionData($request->start_date, $request->end_date);
        return WarehouseTransactionResource::collection($data);
    }
}
