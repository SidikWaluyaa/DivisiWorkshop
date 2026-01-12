<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkOrder::whereHas('photos')
            ->with(['services', 'photos']) // Eager load
            ->orderBy('updated_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('shoe_brand', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(24);

        return view('gallery.index', compact('orders'));
    }

    public function show($id)
    {
        $order = WorkOrder::with(['photos' => function($q) {
            $q->orderBy('created_at', 'desc');
        }, 'services'])->findOrFail($id);

        // Group photos by logical phase for better display
        $groupedPhotos = $order->photos->groupBy(function($photo) {
            if (str_contains($photo->step, 'PREP') || str_contains($photo->step, 'WASH') || str_contains($photo->step, 'SOL') || str_contains($photo->step, 'UPPER')) return 'PREPARATION';
            if (str_contains($photo->step, 'SORTIR')) return 'SORTIR / MATERIAL';
            if (str_contains($photo->step, 'PROD')) return 'PRODUCTION';
            if (str_contains($photo->step, 'QC')) return 'QC CHECK';
            if (str_contains($photo->step, 'FINISH') || str_contains($photo->step, 'PACKING')) return 'FINISHING';
            if (str_contains($photo->step, 'UPSELL')) return 'UPSELL / TAMBAH JASA';
            return 'OTHER';
        });

        return view('gallery.show', compact('order', 'groupedPhotos'));
    }
}
