<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkshopManifest;
use App\Enums\WorkOrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutboundController extends Controller
{
    /**
     * Display dedicated Show page for Outbound Manifest (Surat Jalan Outbound)
     */
    public function show($id)
    {
        $manifest = WorkshopManifest::with([
            'workOrders.customer', 
            'workOrders.workOrderServices.service', 
            'dispatcher', 
            'receiver'
        ])->findOrFail($id);

        return view('qc.outbound.show', compact('manifest'));
    }

    /**
     * Display dedicated Print page for Outbound Manifest (Surat Jalan Outbound)
     */
    public function print($id)
    {
        $manifest = WorkshopManifest::with([
            'workOrders.customer', 
            'workOrders.workOrderServices.service', 
            'dispatcher', 
            'receiver'
        ])->findOrFail($id);

        return view('qc.outbound.print', compact('manifest'));
    }

    /**
     * Display dedicated Create page for Outbound Manifest
     */
    public function create()
    {
        $stagingOrders = WorkOrder::where('status', WorkOrderStatus::STAGING_OUTBOUND)
            ->whereNull('workshop_manifest_id')
            ->with(['customer', 'workOrderServices.service'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('qc.outbound.create', compact('stagingOrders'));
    }
}
