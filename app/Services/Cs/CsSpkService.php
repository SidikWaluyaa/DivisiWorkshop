<?php

namespace App\Services\Cs;

use App\Models\CsSpk;
use App\Models\CsLead;
use App\Models\CsActivity;
use App\Models\WorkOrder;
use App\Models\WorkOrderPhoto;
use App\Models\Promotion;
use App\Enums\WorkOrderStatus;
use App\Services\CustomerService;
use App\Services\PromoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CsSpkService
{
    protected $customerService;
    protected $promoService;

    public function __construct(CustomerService $customerService, PromoService $promoService)
    {
        $this->customerService = $customerService;
        $this->promoService = $promoService;
    }

    /**
     * Calculate discount for given promo and amount
     */
    public function calculateDiscount(Promotion $promo, float $totalAmount, array $itemPrices = []): float
    {
        return $promo->calculateDiscount($totalAmount, $itemPrices);
    }

    /**
     * Generate SPK
     */
    public function generateSpk(CsLead $lead, array $data, ?int $userId = null)
    {
        $userId = $userId ?? Auth::id();

        return DB::transaction(function () use ($lead, $data, $userId) {
            // 1. Helper: Validate Customer Data & Sync
            // Use existing CustomerService passed in constructor
            $this->customerService->syncCustomer([
                'phone' => $data['customer_phone'],
                'name' => $data['customer_name'],
                'email' => $data['customer_email'] ?? null,
                'address' => $data['customer_address'],
                'city' => $data['customer_city'],
                'province' => $data['customer_province'],
            ]);
            
            // 2. Normalize and process Items logic (extracted from Controller)
            // This logic is complex, usually better in a helper but sticking to Service for now
            $itemsData = $this->processSpkItems($data['items']);
            $totalPrice = collect($itemsData)->sum('item_total_price');

            // 2.5. Handle Promo if provided
            $appliedPromo = null;
            $totalDiscount = 0;
            
            if (!empty($data['promo_code'])) {
                $serviceIds = collect($itemsData)->pluck('services')->flatten()->pluck('id')->unique()->toArray();
                $itemPrices = collect($itemsData)->pluck('item_total_price')->toArray();
                
                $validation = $this->promoService->validatePromoCode($data['promo_code'], [
                    'service_ids' => $serviceIds,
                    'customer_phone' => $data['customer_phone'],
                    'total_amount' => $totalPrice,
                ]);

                if ($validation['valid']) {
                    $appliedPromo = $validation['promo'];
                    
                    // Calculate TOTAL discount for the order
                    $totalDiscount = $this->promoService->calculateDiscount($appliedPromo, $totalPrice, $itemPrices);
                    
                    // Distribute discount among items
                    if ($totalDiscount > 0) {
                        if ($appliedPromo->type === Promotion::TYPE_BOGO) {
                            // BOGO: Apply to the cheapest item
                            $cheapestIndex = null;
                            $minPrice = PHP_FLOAT_MAX;
                            foreach ($itemsData as $idx => $item) {
                                if ($item['item_total_price'] < $minPrice) {
                                    $minPrice = $item['item_total_price'];
                                    $cheapestIndex = $idx;
                                }
                            }
                            
                            if ($cheapestIndex !== null) {
                                $item = &$itemsData[$cheapestIndex];
                                $item['promotion_id'] = $appliedPromo->id;
                                $item['original_price'] = $item['item_total_price'];
                                $item['discount_amount'] = $totalDiscount;
                                $item['item_total_price'] = 0; // Free
                            }
                        } else {
                            // PERCENTAGE or FIXED: Distribute proportionally
                            $remainingDiscount = $totalDiscount;
                            foreach ($itemsData as $idx => &$itemData) {
                                $itemPrice = $itemData['item_total_price'];
                                // Proportion = itemPrice / totalPrice
                                $proportion = $totalPrice > 0 ? ($itemPrice / $totalPrice) : 0;
                                $itemDiscount = round($totalDiscount * $proportion, 2);
                                
                                // Adjust last item to avoid rounding errors
                                if ($idx === count($itemsData) - 1) {
                                    $itemDiscount = $remainingDiscount;
                                }
                                
                                $remainingDiscount -= $itemDiscount;
                                
                                $itemData['promotion_id'] = $appliedPromo->id;
                                $itemData['original_price'] = $itemPrice;
                                $itemData['discount_amount'] = $itemDiscount;
                                $itemData['item_total_price'] = $itemPrice - $itemDiscount;
                            }
                        }
                    }
                    
                    // Recalculate total after discount
                    $totalPrice = $totalPrice - $totalDiscount;
                }
            }

            // 3. Generate SPK Number
            $spkNum = $data['spk_number'] ?? CsSpk::generateSpkNumber($data['delivery_type'], $data['manual_cs_code']);

            // 4. Create SPK Header
            $spk = $lead->spk()->create([
                'spk_number' => $spkNum,

                 // Fix: Controller used $customer->id.
                 // Let's fetch it again.
                 'customer_id' => \App\Models\Customer::where('phone', \App\Helpers\PhoneHelper::normalize($data['customer_phone']))->first()->id,

                'services' => [],
                'total_price' => $totalPrice,
                'total_items' => count($itemsData),
                'dp_amount' => $data['dp_amount'],
                'dp_status' => $data['dp_amount'] > 0 ? CsSpk::DP_PENDING : CsSpk::DP_WAIVED,
                'expected_delivery_date' => $data['expected_delivery_date'],
                'special_instructions' => $data['special_instructions'],
                'priority' => $data['priority'],
                'delivery_type' => $data['delivery_type'],
                'cs_code' => strtoupper($data['manual_cs_code']),
                'status' => $data['dp_amount'] > 0 ? CsSpk::STATUS_WAITING_DP : CsSpk::STATUS_DP_PAID,
            ]);

            // 5. Create SPK Items
            foreach ($itemsData as $index => $itemData) {
                // Need to fetch quotation item details again or pass them through
                // Ideally processSpkItems should return full structure.
                
                $spk->items()->create([
                    'item_number' => $index + 1,
                    // Map other fields...
                    'quotation_item_id' => $itemData['quotation_item_id'],
                    'category' => $itemData['category'],
                    'shoe_type' => $itemData['shoe_type'],
                    'shoe_brand' => $itemData['shoe_brand'],
                    'shoe_size' => $itemData['shoe_size'],
                    'shoe_color' => $itemData['shoe_color'],
                    'services' => $itemData['services'],
                    'item_total_price' => $itemData['item_total_price'],
                    'item_notes' => $itemData['item_notes'] ?? null,
                    'promotion_id' => $itemData['promotion_id'] ?? null,
                    'original_price' => $itemData['original_price'] ?? null,
                    'discount_amount' => $itemData['discount_amount'] ?? 0,
                ]);
            }
            
            // 5.5. Log promo usage if applied
            if ($appliedPromo && $totalDiscount > 0) {
                $this->promoService->logPromoUsage($appliedPromo, [
                    'cs_lead_id' => $lead->id,
                    'customer_phone' => $data['customer_phone'],
                    'original_amount' => $totalPrice + $totalDiscount,
                    'discount_amount' => $totalDiscount,
                    'final_amount' => $totalPrice,
                    'applied_by' => $userId,
                ]);
            }

            // 6. Update Lead
            $lead->update([
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_address' => $data['customer_address'],
                // ... other fields
            ]);

            // 7. Log
             $lead->activities()->create([
                'user_id' => $userId,
                'type' => CsActivity::TYPE_NOTE,
                'content' => 'SPK #' . $spk->spk_number . ' berhasil dibuat. Total: Rp ' . number_format($totalPrice),
            ]);

            return $spk;
        });
    }

    /**
     * Handover to Workshop (Convert to WorkOrders)
     */
    public function handToWorkshop(CsSpk $spk, array $itemsInput, ?int $userId = null)
    {
        $userId = $userId ?? Auth::id();

        if (!$spk->canBeHandedToWorkshop()) {
            throw new \Exception('SPK belum siap diserahkan ke workshop!');
        }

        return DB::transaction(function () use ($spk, $itemsInput, $userId) {
            $workOrders = [];
            $workOrderNumbers = [];

            foreach ($itemsInput as $itemId => $itemInput) {
                $spkItem = \App\Models\CsSpkItem::findOrFail($itemInput['spk_item_id']);

                // Generate Work Order Number
                $newSpkNumber = WorkOrder::generateSpkNumber(
                    $itemInput['item_type'], 
                    $spk->cs_code ?? ($spk->lead->cs->cs_code ?? 'SW')
                );

                // Service Price Logic
                $servicePrice = $spkItem->item_total_price ?? collect($spkItem->services)->sum('price') ?? 0;

                // Create Work Order
                $workOrder = WorkOrder::create([
                    'spk_number' => $newSpkNumber,
                    'customer_name' => $spk->lead->customer_name,
                    'customer_phone' => $spk->lead->customer_phone,
                    'customer_email' => $spk->lead->customer_email,
                    'customer_address' => $spk->lead->customer_address,
                    'entry_date' => now(),
                    'estimation_date' => $spk->expected_delivery_date,
                    'status' => WorkOrderStatus::SPK_PENDING->value,
                    'total_service_price' => $servicePrice,
                    'total_transaksi' => $servicePrice,
                    'sisa_tagihan' => $servicePrice,
                    'dp_amount' => 0, // DP tracked at SPK level
                    'shoe_brand' => $itemInput['shoe_brand'] ?: $spkItem->shoe_brand,
                    'shoe_type' => $itemInput['shoe_type'] ?: $spkItem->shoe_type,
                    'shoe_color' => $itemInput['shoe_color'] ?: $spkItem->shoe_color,
                    'shoe_size' => $itemInput['shoe_size'] ?: $spkItem->shoe_size,
                    'category' => $spkItem->category,
                    'priority' => $spk->priority,
                    'shipping_type' => $spk->delivery_type,
                    'cs_code' => $spk->cs_code ?? ($spk->lead->cs->cs_code ?? 'SW'),
                    'current_location' => 'Gudang Penerimaan',
                    'notes' => $spkItem->item_notes ?: $spk->special_instructions,
                    'created_by' => $userId,
                ]);

                // Photo Handling (Multiple)
                $refPhotos = $itemInput['ref_photos'] ?? [];
                $coverIndex = $itemInput['cover_index'] ?? 0;
                $refIndex = $itemInput['ref_index'] ?? 0;

                foreach ($refPhotos as $idx => $file) {
                    if ($file instanceof \Illuminate\Http\UploadedFile) {
                        $filename = 'ref_' . time() . '_' . $workOrder->id . '_' . $idx . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('work-order-photos', $filename, 'public');

                        WorkOrderPhoto::create([
                            'work_order_id' => $workOrder->id,
                            'file_path' => asset('storage/' . $path),
                            'step' => 'RECEPTION',
                            'caption' => 'Foto Referensi Handover CS - Item #' . $spkItem->item_number . ' (' . ($idx + 1) . ')',
                            'is_spk_cover' => ($idx == $coverIndex),
                            'is_primary_reference' => ($idx == $refIndex),
                            'user_id' => $userId,
                        ]);
                    }
                }

                // Services
                foreach ($spkItem->services as $service) {
                    $workOrder->workOrderServices()->create([
                        'service_id' => $service['id'] ?? null,
                        'custom_service_name' => $service['id'] ? null : ($service['name'] ?? 'Custom'),
                        'category_name' => $service['category'] ?? '-',
                        'cost' => $service['price'],
                        'status' => 'PENDING',
                        'service_details' => ['manual_detail' => $service['manual_detail'] ?? null]
                    ]);
                }

                $workOrders[] = $workOrder;
                $workOrderNumbers[] = $newSpkNumber;
            }

            // Link SPK to WO (Pivot or simple relation)
            $spk->handToWorkshop($workOrders[0]->id, $userId);
            
            // Update Lead
            $spk->lead->update([
                'status' => CsLead::STATUS_CONVERTED,
                'converted_to_work_order_id' => $workOrders[0]->id,
            ]);

            // Log activity
            $spk->lead->activities()->create([
                'user_id' => $userId,
                'type' => CsActivity::TYPE_STATUS_CHANGE,
                'content' => 'Order converted to ' . count($workOrders) . ' Work Orders: ' . implode(', ', $workOrderNumbers),
            ]);

            return $workOrders;
        });
    }

    // Helper for processing items (simplified for brevity)
    protected function processSpkItems(array $itemsInput)
    {
        $results = [];
        foreach ($itemsInput as $quotationItemId => $itemInput) {
            $quotationItem = \App\Models\CsQuotationItem::findOrFail($itemInput['quotation_item_id']);
            
            // Logic to fetch Services & Calculate Price
            // Replicating Controller Logic...
            
            // 1. Regular Services
            $services = [];
            $subtotal = 0;
            
            if (!empty($itemInput['services'])) {
                $dbServices = \App\Models\Service::whereIn('id', $itemInput['services'])->get();
                foreach ($dbServices as $s) {
                    $services[] = [
                        'id' => $s->id,
                        'name' => $s->name,
                        'category' => $s->category,
                        'price' => $s->price,
                        'manual_detail' => $itemInput['service_details'][$s->id] ?? null,
                    ];
                    $subtotal += $s->price;
                }
            }

            // 2. Custom Services (Handle both legacy flat arrays and new nested structure)
            if (!empty($itemInput['custom_services'])) {
                foreach ($itemInput['custom_services'] as $customService) {
                    $name = $customService['name'] ?? null;
                    if (!$name) continue;
                    $price = (float) ($customService['price'] ?? 0);
                    $services[] = [
                        'id' => null,
                        'name' => $name,
                        'category' => 'Custom',
                        'price' => $price,
                        'manual_detail' => $customService['description'] ?? null,
                        'is_custom' => true
                    ];
                    $subtotal += $price;
                }
            }

            // Legacy Support (Flat Arrays)
            if (!empty($itemInput['custom_service_names'])) {
                foreach ($itemInput['custom_service_names'] as $idx => $name) {
                    $price = (float) ($itemInput['custom_service_prices'][$idx] ?? 0);
                    $services[] = [
                        'id' => null,
                        'name' => $name,
                        'category' => 'Custom',
                        'price' => $price,
                        'manual_detail' => $itemInput['custom_service_descriptions'][$idx] ?? null,
                        'is_custom' => true
                    ];
                    $subtotal += $price;
                }
            }

            $results[] = [
                'quotation_item_id' => $quotationItem->id,
                'category' => $quotationItem->category,
                'shoe_type' => $quotationItem->shoe_type,
                'shoe_brand' => $quotationItem->shoe_brand,
                'shoe_size' => $quotationItem->shoe_size,
                'shoe_color' => $quotationItem->shoe_color,
                'services' => $services,
                'item_total_price' => $subtotal,
                'item_notes' => $quotationItem->item_notes,
            ];
        }
        return $results;
    }
}
