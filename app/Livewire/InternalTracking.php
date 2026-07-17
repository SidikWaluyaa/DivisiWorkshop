<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WorkOrder;

class InternalTracking extends Component
{
    public $searchKeyword = '';

    public function render()
    {
        $results = collect();

        if (strlen(trim($this->searchKeyword)) > 0) {
            $keyword = trim($this->searchKeyword);
            
            $results = WorkOrder::query()
                ->where('status', '!=', \App\Enums\WorkOrderStatus::SPK_PENDING)
                ->where(function($q) use ($keyword) {
                    $q->where('spk_number', 'like', '%' . $keyword . '%')
                      ->orWhere('customer_name', 'like', '%' . $keyword . '%')
                      ->orWhereHas('customer', function($sub) use ($keyword) {
                          $sub->where('name', 'like', '%' . $keyword . '%')
                              ->orWhere('phone', 'like', '%' . $keyword . '%');
                      })
                      ->orWhereHas('invoice', function($sub) use ($keyword) {
                          $sub->where('invoice_number', 'like', '%' . $keyword . '%');
                      });
                })
                ->with(['customer', 'invoice', 'photos', 'logs', 'storageAssignments.rack'])
                // Prioritaskan hasil pencarian jika namanya SAMA PERSIS, atau SPK-nya SAMA PERSIS
                ->orderByRaw("
                    CASE 
                        WHEN spk_number = ? THEN 1 
                        WHEN customer_name = ? THEN 2
                        WHEN customer_name LIKE ? THEN 3
                        ELSE 4 
                    END
                ", [$keyword, $keyword, $keyword.'%'])
                ->latest()
                ->take(24) // Tampilkan lebih banyak hasil (kelipatan 4, misal 24)
                ->get();
        }

        return view('livewire.internal-tracking', [
            'results' => $results
        ])->layout('layouts.app');
    }

    public function getRedirectUrl(WorkOrder $spk)
    {
        return $spk->getStationUrl();
    }
}
