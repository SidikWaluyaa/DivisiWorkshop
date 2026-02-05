<?php

namespace App\Services\Cs;

use App\Models\CsLead;
use App\Models\CsActivity;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\CsQuotationItem;
use App\Models\CsSpkItem;
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
     * Find best CS for assignment (Least Active Logic)
     */
    public function findCsForAssignment()
    {
        // 1. Get all eligible users (Role admin/owner or has explicit CS access)
        $eligibleUsers = User::where(function($q) {
                $q->where('access_rights', 'LIKE', '%"cs"%')
                  ->orWhereIn('role', ['admin', 'owner']);
            })
            ->get();

        if ($eligibleUsers->isEmpty()) {
            return null;
        }

        // 2. Count active leads for each eligible user
        // Active = Greeting, Konsultasi, or Closing
        $assignment = $eligibleUsers->map(function($user) {
            return [
                'id' => $user->id,
                'lead_count' => CsLead::where('cs_id', $user->id)
                    ->whereIn('status', [CsLead::STATUS_GREETING, CsLead::STATUS_KONSULTASI, CsLead::STATUS_CLOSING])
                    ->count()
            ];
        })->sortBy('lead_count')->first();

        return $assignment['id'];
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
                'channel' => $data['channel'] ?? ($data['source'] === CsLead::SOURCE_WALKIN ? CsLead::CHANNEL_OFFLINE : CsLead::CHANNEL_ONLINE),
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
    public function logActivity(CsLead $lead, int $userId, string $type, string $content, ?string $channel = null, ?array $metadata = null)
    {
        return $lead->activities()->create([
            'user_id' => $userId,
            'type' => $type,
            'channel' => $channel,
            'content' => $content,
            'metadata' => $metadata
        ]);
    }

    /**
     * Update Lead Data with Governance & Audit Trail
     */
    public function updateLead(CsLead $lead, array $data, ?int $userId = null): CsLead
    {
        $userId = $userId ?? Auth::id();
        $user = User::find($userId);
        
        // 1. Governance: Check if Lead is Locked (CONVERTED or LOST)
        $isLocked = in_array($lead->status, [CsLead::STATUS_CONVERTED, CsLead::STATUS_LOST]);
        
        if ($isLocked && \Illuminate\Support\Facades\Gate::denies('cs.override-locked')) {
            throw new \Exception('Data sudah terkunci (CONVERTED/LOST). Hanya Admin yang dapat mengubah data ini.');
        }

        // 2. Identify Changes for Audit Trail
        $changes = [];
        $fillable = $lead->getFillable();
        
        foreach ($data as $key => $value) {
            if (in_array($key, $fillable) && $lead->{$key} != $value) {
                $changes[$key] = [
                    'old' => $lead->{$key},
                    'new' => $value
                ];
            }
        }

        if (empty($changes)) {
            return $lead; // No changes detected
        }

        return DB::transaction(function () use ($lead, $data, $userId, $changes) {
            // 3. Update the Lead
            $lead->update($data);
            $lead->last_activity_at = now();
            $lead->save();

            // 4. Data Propagation (Single Source of Truth)
            // If customer data changed, sync to related models
            $propagateFields = ['customer_name', 'customer_phone', 'customer_email', 'customer_address'];
            $needsPropagation = !empty(array_intersect(array_keys($changes), $propagateFields));

            if ($needsPropagation) {
                // Propagate to WorkOrder if exist
                if ($lead->converted_to_work_order_id) {
                    $workOrder = WorkOrder::find($lead->converted_to_work_order_id);
                    if ($workOrder) {
                        $workOrder->update([
                            'customer_name' => $lead->customer_name,
                            'customer_phone' => $lead->customer_phone,
                            // Add other fields as necessary based on WorkOrder schema
                        ]);
                    }
                }

                // Propagate to CsSpk if exist
                if ($lead->spk) {
                    $lead->spk->update([
                        'customer_name' => $lead->customer_name,
                        'customer_phone' => $lead->customer_phone,
                    ]);
                }
            }

            // 5. Generate Audit Log
            $changeSummary = [];
            foreach ($changes as $field => $values) {
                $fieldName = ucwords(str_replace('_', ' ', $field));
                $changeSummary[] = "{$fieldName}: \"{$values['old']}\" ➡️ \"{$values['new']}\"";
            }

            $content = "Revisi data oleh " . Auth::user()->name;
            if (isset($data['revision_reason'])) {
                $content .= " (Alasan: {$data['revision_reason']})";
            }
            $content .= "\n" . implode("\n", $changeSummary);

            $this->logActivity(
                $lead, 
                $userId, 
                CsActivity::TYPE_NOTE, 
                $content, 
                null, 
                ['revision' => true, 'fields_changed' => array_keys($changes)]
            );

            return $lead;
        });
    }

    /**
     * Update Quotation Item & Services with Governance
     */
    public function updateQuotationItem(CsQuotationItem $item, array $data, ?int $userId = null): CsQuotationItem
    {
        $userId = $userId ?? Auth::id();
        $lead = $item->quotation->lead;
        
        // 1. Governance check
        $isLocked = in_array($lead->status, [CsLead::STATUS_CONVERTED, CsLead::STATUS_LOST]);
        if ($isLocked && \Illuminate\Support\Facades\Gate::denies('cs.override-locked')) {
             throw new \Exception('Item terkunci. Hanya Admin yang dapat merevisi item pada lead yang sudah DEAL/LOST.');
        }

        // 2. Identify Changes
        $changes = [];
        $fillable = $item->getFillable();
        foreach ($data as $key => $value) {
            if (in_array($key, $fillable) && $item->{$key} != $value) {
                // Special handling for decimal/float comparison
                if ($key === 'item_total_price') {
                    if (abs((float)$item->item_total_price - (float)$value) > 0.01) {
                         $changes[$key] = ['old' => $item->item_total_price, 'new' => $value];
                    }
                } else {
                     $changes[$key] = ['old' => $item->{$key}, 'new' => $value];
                }
            }
        }

        if (empty($changes)) return $item;

        return DB::transaction(function () use ($item, $data, $lead, $userId, $changes) {
            // 2.5 Handle Service Syncing if service_ids or custom_services provided
            if (isset($data['service_ids']) || !empty($data['custom_service_names'])) {
                $serviceIds = isset($data['service_ids']) ? (is_array($data['service_ids']) ? $data['service_ids'] : explode(',', $data['service_ids'])) : [];
                $dbServices = !empty($serviceIds) ? \App\Models\Service::whereIn('id', $serviceIds)->get() : collect();
                
                $newServices = [];
                $newSubtotal = 0;
                
                foreach ($dbServices as $s) {
                    $newServices[] = [
                        'id' => $s->id,
                        'name' => $s->name,
                        'category' => $s->category,
                        'price' => $s->price,
                        'manual_detail' => $data['service_details'][$s->id] ?? null,
                    ];
                    $newSubtotal += $s->price;
                }
                
                // If custom services are provided
                if (!empty($data['custom_service_names'])) {
                    foreach ($data['custom_service_names'] as $idx => $name) {
                        if (empty($name)) continue;
                        $price = (float)($data['custom_service_prices'][$idx] ?? 0);
                        $newServices[] = [
                            'id' => null,
                            'name' => $name,
                            'category' => 'Custom',
                            'price' => $price,
                            'manual_detail' => $data['custom_service_descriptions'][$idx] ?? null,
                            'is_custom' => true
                        ];
                        $newSubtotal += $price;
                    }
                }

                // Check for service changes for logs
                $oldServices = $item->services ?? [];
                $oldServiceIds = collect($oldServices)->pluck('id')->filter()->toArray();
                sort($serviceIds);
                sort($oldServiceIds);
                
                if ($serviceIds !== $oldServiceIds || count($newServices) !== count($oldServices)) {
                    $changes['services'] = [
                        'old' => collect($oldServices)->pluck('name')->implode(', ') ?: 'None',
                        'new' => collect($newServices)->pluck('name')->implode(', ') ?: 'None'
                    ];
                }

                $data['services'] = $newServices;
                $data['item_total_price'] = $newSubtotal; // Automatic recalculation
            }

            // 3. Update Quotation Item
            $item->update($data);

            // 4. Propagate to SPK Item (if exists)
            $spkItem = CsSpkItem::where('quotation_item_id', $item->id)->first();
            if ($spkItem) {
                $spkItem->update([
                    'category' => $item->category,
                    'shoe_brand' => $item->shoe_brand,
                    'shoe_type' => $item->shoe_type,
                    'shoe_size' => $item->shoe_size,
                    'shoe_color' => $item->shoe_color,
                    'services' => $item->services,
                    'item_total_price' => $item->item_total_price,
                    'item_notes' => $item->item_notes,
                ]);

                // Propagate to WorkOrder (if exists)
                if ($spkItem->work_order_id) {
                    $workOrder = WorkOrder::find($spkItem->work_order_id);
                    if ($workOrder) {
                        $workOrder->update([
                            'category' => $item->category,
                            'shoe_brand' => $item->shoe_brand,
                            'shoe_type' => $item->shoe_type,
                            'shoe_color' => $item->shoe_color,
                            'shoe_size' => $item->shoe_size,
                        ]);
                    }
                }
            }

            // 5. Recalculate Quotation Total
            $quotation = $item->quotation;
            $newTotal = $quotation->quotationItems()->sum('item_total_price');
            $quotation->update(['total' => $newTotal, 'subtotal' => $newTotal]);

            // 6. Sync SPK Total Price
            if ($lead->spk) {
                $lead->spk->update(['total_price' => $newTotal]);
            }

            // 7. Log Activity
            $changeLines = [];
            foreach ($changes as $field => $vals) {
                $fieldName = ucwords(str_replace('_', ' ', $field));
                $changeLines[] = "{$fieldName}: \"{$vals['old']}\" ➡️ \"{$vals['new']}\"";
            }

            $summary = "Revisi Item #{$item->item_number} ({$item->label}) oleh " . Auth::user()->name;
            if (isset($data['revision_reason'])) {
                $summary .= "\nAlasan: " . $data['revision_reason'];
            }
            $summary .= "\n" . implode("\n", $changeLines);
            
            $this->logActivity($lead, $userId, CsActivity::TYPE_NOTE, $summary, null, [
                'item_revision' => true, 
                'item_id' => $item->id,
                'fields_changed' => array_keys($changes)
            ]);

            return $item;
        });
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
