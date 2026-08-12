<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class SuratJalanController extends Controller
{
    /**
     * Display a print-friendly Surat Jalan for a work order.
     */
    public function show($id)
    {
        $order = WorkOrder::with(['customer', 'services', 'materials'])->findOrFail($id);

        return view('workshop.surat-jalan', compact('order'));
    }
}
