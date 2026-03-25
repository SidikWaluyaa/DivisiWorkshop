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
                ->where(function($q) use ($keyword) {
                    $q->where('spk_number', 'like', '%' . $keyword . '%')
                      ->orWhere('customer_name', 'like', '%' . $keyword . '%')
                      ->orWhereHas('customer', function($sub) use ($keyword) {
                          $sub->where('name', 'like', '%' . $keyword . '%')
                              ->orWhere('phone', 'like', '%' . $keyword . '%');
                      });
                })
                ->with(['customer'])
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
        $status = $spk->status->value ?? $spk->status;

        switch ($status) {
            case 'SPK_PENDING':
            case 'DITERIMA':
            case 'READY_TO_DISPATCH':
            case 'OTW_WORKSHOP':
                return route('reception.index', ['search' => $spk->spk_number, 'highlight' => $spk->spk_number]);
            case 'ASSESSMENT':
                return route('assessment.create', $spk->id) . '?highlight=' . $spk->spk_number;
            case 'PREPARATION':
                return route('preparation.index', ['search' => $spk->spk_number, 'highlight' => $spk->spk_number]);
            case 'SORTIR':
                return route('sortir.index', ['search' => $spk->spk_number, 'highlight' => $spk->spk_number]);
            case 'PRODUCTION':
                // Deteksi tab produksi spesifik (Sol, Upper, Treatment) berdasarkan tanggungan pekerjaan
                if ($spk->needs_sol && is_null($spk->prod_sol_completed_at)) {
                    $prodTab = 'sol';
                } elseif ($spk->needs_upper && is_null($spk->prod_upper_completed_at)) {
                    $prodTab = 'upper';
                } elseif (is_null($spk->prod_cleaning_completed_at)) {
                    $prodTab = 'treatment';
                } else {
                    $prodTab = 'all';
                }
                return route('production.index', ['tab' => $prodTab, 'search' => $spk->spk_number, 'highlight' => $spk->spk_number]);
            
            case 'QC':
                // Deteksi tab QC spesifik berdasarkan service category & progress
                $needsJahit = $spk->hasServiceCategory(['Sol', 'Upper', 'Repaint', 'Jahit']);
                if ($needsJahit && is_null($spk->qc_jahit_completed_at)) {
                    $qcTab = 'jahit';
                } elseif (is_null($spk->qc_cleanup_completed_at)) {
                    $qcTab = 'cleanup';
                } elseif (is_null($spk->qc_final_completed_at)) {
                    $qcTab = 'final';
                } else {
                    $qcTab = 'all';
                }
                return route('qc.index', ['search' => $spk->spk_number, 'tab' => $qcTab, 'highlight' => $spk->spk_number]);
            case 'SELESAI':
            case 'DIANTAR':
                return route('finish.index', ['search' => $spk->spk_number, 'highlight' => $spk->spk_number]);
            case 'WAITING_PAYMENT':
            case 'WAITING_VERIFICATION':
                // For finance, we'll keep show if index lacks search support, but let's assume index exists for consistency if possible.
                // Reverting to show to avoid routing errors, as finance.show is standard. Highlighting may be ignored if no index.
                return route('finance.show', $spk->id) . '?highlight=' . $spk->spk_number;
            case 'CX_FOLLOWUP':
            case 'HOLD_FOR_CX':
                return route('cx.index', ['search' => $spk->spk_number, 'highlight' => $spk->spk_number]);
            default:
                return route('admin.orders.show', $spk->id) . '?highlight=' . $spk->spk_number;
        }
    }
}
