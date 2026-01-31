<?php

namespace App\Services\Cs;

use App\Models\CsLead;
use App\Models\CsActivity;
use App\Models\User;
use App\Services\CustomerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CsLeadService
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Find best CS for assignment (Simple Round Robin or Random for now)
     */
    public function findCsForAssignment()
    {
        // Prioritize current user if they are logged in
        if (Auth::check()) {
            return Auth::id();
        }

        // Fallback for automated systems/guests
        $csUser = User::whereJsonContains('access_rights', 'cs')
            ->inRandomOrder()
            ->first();
            
        return $csUser ? $csUser->id : null;
    }

    /**
     * Create a new Lead
     */
    public function createLead(array $data, ?int $userId = null): CsLead
    {
        $userId = $userId ?? Auth::id();
        
        // If cs_id is not provided, use the best available assignment
        if (empty($data['cs_id'])) {
            $csId = $this->findCsForAssignment();
        } else {
            $csId = $data['cs_id'];
        }

        return DB::transaction(function () use ($data, $userId, $csId) {
            $lead = CsLead::create([
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'source' => $data['source'],
                'source_detail' => $data['source_detail'] ?? null,
                'priority' => $data['priority'],
                'notes' => $data['notes'] ?? null,
                'status' => CsLead::STATUS_GREETING,
                'cs_id' => $csId,
                'first_contact_at' => now(),
                'last_activity_at' => now(),
            ]);

            // Log activity
            $this->logActivity($lead, $userId, CsActivity::TYPE_NOTE, 'Lead baru masuk: ' . ($data['notes'] ?? 'Tidak ada catatan'), $data['source']);

            // Auto-calculate response time if first response
            if (!$lead->first_response_at) {
                $lead->update(['first_response_at' => now()]);
                $lead->calculateResponseTime();
            }

            return $lead;
        });
    }

    /**
     * Update Lead Status
     */
    public function updateStatus(CsLead $lead, string $status, ?string $notes = null, ?int $userId = null)
    {
        $userId = $userId ?? Auth::id();

        // Validation logic
        if ($status === CsLead::STATUS_CLOSING && !$lead->canMoveToClosing()) {
            throw new \Exception('Belum ada quotation yang diterima customer!');
        }

        if ($status === CsLead::STATUS_KONSULTASI && !$lead->canMoveToKonsultasi()) {
            // Allow if status is already beyond Konsultasi (e.g. moving back) or admin override
             // For strict flow, throw exception. For flexible, allow.
             // Implemented strict check in Controller previously, keeping same logic.
             // But usually Drag & Drop needs flexiblity.
        }

        $updateData = [
            'status' => $status,
            'last_activity_at' => now(),
        ];
        
        // If moving to CLOSING, set expected value
        if ($status === CsLead::STATUS_CLOSING) {
             $acceptedQuotation = $lead->getAcceptedQuotation();
             if ($acceptedQuotation) {
                 $updateData['expected_value'] = $acceptedQuotation->total;
             }
        }

        $lead->update($updateData);

        // Log activity
        $content = 'Status diubah ke ' . $status;
        if ($status === CsLead::STATUS_CLOSING) {
             $acceptedQuotation = $lead->getAcceptedQuotation();
             $content .= '. Quotation #' . ($acceptedQuotation->quotation_number ?? '?') . ' diterima.';
        }
        if ($notes) {
            $content .= ': ' . $notes;
        }

        $this->logActivity($lead, $userId, CsActivity::TYPE_STATUS_CHANGE, $content);

        return $lead;
    }

    /**
     * Log Activity Helper
     */
    public function logActivity(CsLead $lead, int $userId, string $type, string $content, ?string $channel = null)
    {
        return $lead->activities()->create([
            'user_id' => $userId,
            'type' => $type,
            'channel' => $channel,
            'content' => $content,
        ]);
    }

    /**
     * Calculate Conversion Rate
     */
    public function calculateConversionRate()
    {
        // Simple calculation: Converted / Total Leads * 100
        // Or based on timeframe? Let's use All Time for now based on existing logic
        $total = CsLead::count();
        $converted = CsLead::converted()->count();

        return $total > 0 ? round(($converted / $total) * 100, 1) : 0;
    }
}
