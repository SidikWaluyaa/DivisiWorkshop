<?php

namespace App\Livewire\Cs;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use App\Models\CsLead;
use App\Models\CsQuotation;
use App\Models\CsQuotationItem;
use App\Models\CsSpk;
use App\Models\Service;
use App\Models\Material;
use App\Models\CsActivity;
use App\Services\Cs\CsLeadService;
use App\Services\Cs\CsSpkService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeadDetailManager extends Component
{
    use WithFileUploads;

    // Data
    public CsLead $lead;
    
    // UI State
    public $activeTab = 'all'; // all, draft, spk
    public $showDraftModal = false;
    public $showEditItemModal = false;
    public $showSpkModal = false;
    public $showHandoverModal = false;
    public $showFollowUpModal = false;
    public $showLostModal = false;
    public $handoverItems = [];
    public $showEditProfileModal = false;
    public $profileData = [];

    // Quotation Draft State
    public $draftItems = [];
    public $draftNotes = '';

    // Edit Item State
    public $editingItem = null;
    public $editingData = [];

    // SPK Generation State
    public $spkData = [
        'customer_name' => '',
        'customer_phone' => '',
        'customer_email' => '',
        'customer_address' => '',
        'customer_city' => '',
        'customer_province' => '',
        'spk_number' => '',
        'priority' => 'NORMAL',
        'delivery_type' => 'Offline',
        'manual_cs_code' => '',
        'expected_delivery_date' => '',
        'dp_amount' => 0,
        'promo_code' => '',
        'promotion_id' => null,
        'discount_amount' => 0,
        'special_instructions' => ''
    ];

    // Activity State
    public $activityType = 'CHAT';
    public $activityContent = '';

    // Services cache for HK lookup
    protected $servicesCache;

    public function mount(CsLead $lead)
    {
        if (!$lead->exists) {
            return redirect()->route('cs.dashboard')->with('error', 'Data Lead tidak ditemukan atau sudah dihapus.');
        }

        $this->lead = $lead->load(['cs', 'activities.user', 'quotations.quotationItems', 'spk.customer', 'spk.items']);
        
        // Initialize Spk Data from Lead
        $this->spkData['customer_name'] = $this->lead->customer_name;
        $this->spkData['customer_phone'] = $this->lead->customer_phone;
        $this->spkData['customer_email'] = $this->lead->customer_email;
        $this->spkData['customer_address'] = $this->lead->customer_address;
        $this->spkData['customer_city'] = $this->lead->customer_city;
        $this->spkData['customer_province'] = $this->lead->customer_province;
        $this->spkData['manual_cs_code'] = $this->lead->cs->cs_code ?? 'SW';
        
        $this->addDraftItem();
    }

    public function openEditProfile()
    {
        $this->profileData = [
            'customer_name' => $this->lead->customer_name,
            'customer_phone' => $this->lead->customer_phone,
            'customer_email' => $this->lead->customer_email,
            'channel' => $this->lead->channel,
            'priority' => $this->lead->priority,
            'notes' => $this->lead->notes,
        ];
        $this->showEditProfileModal = true;
    }

    public function updateProfile()
    {
        $this->validate([
            'profileData.customer_name' => 'required|string|max:255',
            'profileData.customer_phone' => 'required|string|max:20',
            'profileData.customer_email' => 'nullable|email|max:255',
            'profileData.channel' => 'nullable|string',
            'profileData.priority' => 'required|in:HOT,WARM,COLD',
            'profileData.notes' => 'nullable|string',
        ], [
            'profileData.customer_name.required' => 'Nama wajib diisi.',
            'profileData.customer_phone.required' => 'Nomor HP wajib diisi.',
            'profileData.priority.required' => 'Prioritas wajib dipilih.',
        ]);

        $this->lead->update($this->profileData);
        
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Profil lead berhasil diperbarui!']);
        $this->showEditProfileModal = false;
        $this->lead->refresh();
    }

    #[Computed]
    public function services()
    {
        return Service::orderBy('category')->orderBy('name')->get();
    }

    #[Computed]
    public function materials()
    {
        return Material::orderBy('name')->get();
    }

    /**
     * Get filtered quotation items (only from active/latest version)
     * This fixes the 'perhitungan tidak sesuai' by excluding old revisions
     */
    #[Computed]
    public function quotationItems()
    {
        // 1. If we have an accepted quotation, only show items from that one
        $accepted = $this->lead->getAcceptedQuotation();
        if ($accepted) {
            return $accepted->quotationItems;
        }

        // 2. Otherwise, show items from the latest version
        $latest = $this->lead->getLatestQuotation();
        if ($latest) {
            return $latest->quotationItems;
        }

        return collect();
    }


    /**
     * Quotation Draft Methods
     */
    public function addDraftItem()
    {
        $this->draftItems[] = [
            'category' => 'Sepatu',
            'shoe_brand' => '',
            'shoe_type' => '',
            'shoe_size' => '',
            'shoe_color' => '',
            'condition_notes' => '',
            'selected_services' => [],
            'custom_service_prices' => [],
            'service_details' => [], 
            'service_category_filter' => '',
            'service_search' => '',
            'extra_notes' => '',
            'custom_services' => [], // Array of ['category' => '', 'name' => '', 'price' => 0, 'manual_detail' => '']
            'item_notes' => '0 HK - Non-Garansi',
            'hk_days' => 0,
            'is_warranty' => false,
            'warranty_label' => 'Non-Garansi',
            'requested_materials' => []
        ];
    }

    public function removeDraftItem($index)
    {
        unset($this->draftItems[$index]);
        $this->draftItems = array_values($this->draftItems);
    }

    public function addCustomService($idx)
    {
        $this->draftItems[$idx]['custom_services'][] = [
            'category' => 'Lainnya',
            'name' => '',
            'price' => 0,
            'manual_detail' => '',
            'hk_days' => 0
        ];
    }

    public function removeCustomService($idx, $cIdx)
    {
        unset($this->draftItems[$idx]['custom_services'][$cIdx]);
        $this->draftItems[$idx]['custom_services'] = array_values($this->draftItems[$idx]['custom_services']);
        
        $this->syncItemHkFromServices($idx);
        $this->updateDraftItemNotes($idx);
    }

    protected function updateDraftItemNotes($idx)
    {
        // Always take the current HK value from the input field
        $totalHk = $this->draftItems[$idx]['hk_days'] ?? 0;
        $isWarranty = (bool)($this->draftItems[$idx]['is_warranty'] ?? false);
        $extraNotes = $this->draftItems[$idx]['extra_notes'] ?? '';
        
        $warrantyText = $isWarranty ? 'Bergaransi' : 'Non-Garansi';
        $note = "{$totalHk} HK - {$warrantyText}";
        
        if (!empty($extraNotes)) {
            $note .= " - {$extraNotes}";
        }

        $this->draftItems[$idx]['item_notes'] = $note;
    }

    public function updatedDraftItems($value, $key)
    {
        // Parts will be: [index, field_name, ...]
        $parts = explode('.', $key);
        $itemIdx = $parts[0] ?? null;

        if ($itemIdx !== null && is_numeric($itemIdx)) {
            // 1. If custom services changed, we need to sync the HK
            if (str_contains($key, 'custom_services')) {
                $this->syncItemHkFromServices($itemIdx);
            }

            // 2. Always update the textual notes/summary if it's a field that affects it
            if (str_contains($key, 'condition_notes') || str_contains($key, 'extra_notes') || str_contains($key, 'custom_services') || str_contains($key, 'hk_days')) {
                $this->updateDraftItemNotes($itemIdx);
            }
        }
    }

    public function updatedEditingData($value, $key)
    {
        if (str_contains($key, 'custom_services')) {
            $this->syncEditingHk();
        }
    }

    protected function syncItemHkFromServices($idx)
    {
        if (!isset($this->draftItems[$idx])) return;
        
        // Always calculate the total sum from all selected services as a baseline
        $catalogHk = $this->services()->whereIn('id', $this->draftItems[$idx]['selected_services'] ?? [])->sum('hk_days');
        $customHk = collect($this->draftItems[$idx]['custom_services'] ?? [])->sum('hk_days');
        
        // Set the total first. If > 1 service, the UI will allow manual editing later.
        $this->draftItems[$idx]['hk_days'] = $catalogHk + $customHk;
    }

    public function resetItemNotes($idx)
    {
        $this->updateDraftItemNotes($idx);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Catatan dikembalikan ke rumus standar.']);
    }

    public function toggleService($itemIdx, $serviceId)
    {
        $services = $this->draftItems[$itemIdx]['selected_services'];
        
        if (in_array($serviceId, $services)) {
            $this->draftItems[$itemIdx]['selected_services'] = array_values(array_diff($services, [$serviceId]));
            unset($this->draftItems[$itemIdx]['custom_service_prices'][$serviceId]);
        } else {
            $this->draftItems[$itemIdx]['selected_services'][] = $serviceId;
            $service = $this->services()->find($serviceId);
            $this->draftItems[$itemIdx]['custom_service_prices'][$serviceId] = $service->price;
        }

        // Use the new sync logic to enforce SOP for 1 service
        $this->syncItemHkFromServices($itemIdx);
        $this->updateDraftItemNotes($itemIdx);
    }

    public function saveQuotation()
    {
        $this->validate([
            'draftItems.*.category' => 'required',
        ], [
            'draftItems.*.category.required' => 'Kategori wajib diisi.',
        ]);

        $leadService = app(CsLeadService::class);
        $totalValue = $this->totalQuotationValue();

        try {
            DB::transaction(function () use ($leadService, $totalValue) {
                $lastQuotation = $this->lead->getLatestQuotation();
                $version = $lastQuotation ? $lastQuotation->version + 1 : 1;

                $quotation = $this->lead->quotations()->create([
                    'quotation_number' => CsQuotation::generateQuotationNumber(),
                    'version' => $version,
                    'status' => CsQuotation::STATUS_ACCEPTED,
                    'notes' => $this->draftNotes,
                    'subtotal' => $totalValue,
                    'total' => $totalValue,
                ]);

                foreach ($this->draftItems as $index => $itemData) {
                    $quotationItem = $quotation->quotationItems()->create([
                        'item_number' => $index + 1,
                        'category' => $itemData['category'],
                        'shoe_type' => $itemData['shoe_type'] ?? '',
                        'shoe_brand' => $itemData['shoe_brand'] ?? '',
                        'shoe_size' => $itemData['shoe_size'] ?? '',
                        'shoe_color' => $itemData['shoe_color'] ?? '',
                        'condition_notes' => $itemData['condition_notes'],
                        'item_notes' => $this->calculateDraftSummary($index),
                        'is_warranty' => (bool)$itemData['is_warranty'],
                        'hk_days' => (int)($itemData['hk_days'] ?? 0),
                        'item_total_price' => collect($itemData['custom_service_prices'] ?? [])->sum() + collect($itemData['custom_services'] ?? [])->sum('price'),
                    ]);

                    // Prepare consolidated services array
                    $servicesArray = [];
                    
                    // 1. Regular Services from Catalog
                    if (!empty($itemData['selected_services'])) {
                        $selectedDbServices = $this->services()->whereIn('id', $itemData['selected_services']);
                        foreach ($selectedDbServices as $s) {
                            $servicesArray[] = [
                                'id' => $s->id,
                                'name' => $s->name,
                                'category' => $s->category,
                                'price' => (float)($itemData['custom_service_prices'][$s->id] ?? $s->price),
                                'manual_detail' => $itemData['service_details'][$s->id] ?? '',
                            ];
                        }
                    }

                    // 2. Custom Services
                    if (!empty($itemData['custom_services'])) {
                        foreach ($itemData['custom_services'] as $cs) {
                            if (empty($cs['name'])) continue;
                            $servicesArray[] = [
                                'id' => null,
                                'name' => $cs['name'],
                                'category' => $cs['category'] ?? 'Custom',
                                'price' => (float)($cs['price'] ?? 0),
                                'manual_detail' => $cs['manual_detail'] ?? '',
                                'hk_days' => (int)($cs['hk_days'] ?? 0),
                                'is_custom' => true
                            ];
                        }
                    }
                    
                    // Save the final services array (even if empty)
                    $quotationItem->update([
                        'services' => $servicesArray,
                    ]);
                }

                $this->lead->activities()->create([
                    'user_id' => Auth::id(),
                    'type' => CsActivity::TYPE_QUOTATION_ACCEPTED,
                    'content' => 'Quotation #' . $quotation->quotation_number . ' (v' . $version . ') dibuat otomatis.',
                ]);
            });

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Quotation berhasil dibuat!']);
            $this->reset(['draftItems', 'draftNotes', 'showDraftModal']);
            $this->addDraftItem();
            $this->lead->refresh();
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    protected function calculateHkFromServices($serviceIds)
    {
        return $this->services->whereIn('id', $serviceIds)->sum('hk_days');
    }

    /**
     * Edit Item Methods
     */
    public function openEditItem($itemId)
    {
        $item = CsQuotationItem::find($itemId);
        
        if (!$item) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Data barang tidak ditemukan atau sudah dihapus.']);
            return;
        }

        $this->editingItem = $item;
        
        $services = $item->services ?? [];
        $selectedServices = [];
        $customServicePrices = [];
        $serviceDetails = [];
        $customServices = [];

        foreach ($services as $svc) {
            $isCustom = (isset($svc['is_custom']) && $svc['is_custom']) || empty($svc['id']);
            
            if ($isCustom) {
                $customServices[] = [
                    'category' => $svc['category'] ?? 'Lainnya',
                    'name' => $svc['name'] ?? '',
                    'price' => $svc['price'] ?? 0,
                    'manual_detail' => $svc['manual_detail'] ?? '',
                    'hk_days' => $svc['hk_days'] ?? 0
                ];
            } else {
                $svcId = $svc['id'];
                $selectedServices[] = $svcId;
                $customServicePrices[$svcId] = $svc['price'];
                $serviceDetails[$svcId] = $svc['manual_detail'] ?? '';
            }
        }

        // Extract extra_notes from existing item_notes if it exists
        // Format is usually: "X HK - [Warranty Label] - Extra Note"
        $extraNotes = '';
        $warrantyLabel = $item->is_warranty ? 'Bergaransi' : 'Non-Garansi';
        
        if (!empty($item->item_notes)) {
            // Support both " - " and "-" separators
            $rawNotes = str_replace(' - ', '|', $item->item_notes);
            $parts = explode('|', $rawNotes);
            
            // Expected Format: [HK] | [Warranty] | [Notes...]
            if (count($parts) >= 2) {
                $warrantyLabel = trim($parts[1]);
            }
            if (count($parts) >= 3) {
                // Combine everything from the 3rd part onwards
                $extraNotes = trim(implode(' - ', array_slice($parts, 2)));
            }
        }

        $this->editingData = [
            'category' => $item->category,
            'shoe_brand' => $item->shoe_brand,
            'shoe_type' => $item->shoe_type,
            'shoe_color' => $item->shoe_color,
            'shoe_size' => $item->shoe_size,
            'condition_notes' => $item->condition_notes,
            'selected_services' => $selectedServices,
            'custom_service_prices' => $customServicePrices,
            'service_details' => $serviceDetails,
            'custom_services' => $customServices,
            'service_category_filter' => '',
            'service_search' => '',
            'extra_notes' => $extraNotes, 
            'warranty_label' => $warrantyLabel,
            'hk_days' => $item->hk_days,
            'is_warranty' => (bool)$item->is_warranty,
            'requested_materials' => $item->materials ?? []
        ];

        $this->showEditItemModal = true;
    }

    public function calculateEditingSummary()
    {
        if (empty($this->editingData)) return '';

        // 1. HK from Standard Services + Custom Inputs
        $totalHk = (int)($this->editingData['hk_days'] ?? 0);

        // 3. Warranty Status & Text
        $isWarranty = (bool)($this->editingData['is_warranty'] ?? false);
        $warrantyText = $this->editingData['warranty_label'] ?? ($isWarranty ? 'Bergaransi' : 'Non-Garansi');
        
        $extraNotes = $this->editingData['extra_notes'] ?? '';
        
        $summary = "{$totalHk} HK - {$warrantyText}";
        
        if (!empty($extraNotes)) {
            $summary .= " - {$extraNotes}";
        }

        return $summary;
    }

    public function toggleDraftWarranty($idx)
    {
        $this->draftItems[$idx]['is_warranty'] = !($this->draftItems[$idx]['is_warranty'] ?? false);
        $this->draftItems[$idx]['warranty_label'] = $this->draftItems[$idx]['is_warranty'] ? 'Bergaransi' : 'Non-Garansi';
    }

    public function toggleEditingWarranty()
    {
        $this->editingData['is_warranty'] = !($this->editingData['is_warranty'] ?? false);
        $this->editingData['warranty_label'] = $this->editingData['is_warranty'] ? 'Bergaransi' : 'Non-Garansi';
    }

    public function calculateDraftSummary($idx)
    {
        if (!isset($this->draftItems[$idx])) return '';

        $item = $this->draftItems[$idx];

        // 1. HK is tracked in $item['hk_days']
        $totalHk = (int)($item['hk_days'] ?? 0);

        // 3. Warranty Status & Text
        $isWarranty = (bool)$item['is_warranty'];
        $warrantyText = !empty($item['warranty_label']) ? $item['warranty_label'] : ($isWarranty ? 'Bergaransi' : 'Non-Garansi');
        
        $extraNotes = $item['extra_notes'] ?? '';
        
        $summary = "{$totalHk} HK - {$warrantyText}";
        
        if (!empty($extraNotes)) {
            $summary .= " - {$extraNotes}";
        }

        return $summary;
    }

    public function toggleEditingService($serviceId)
    {
        if (in_array($serviceId, $this->editingData['selected_services'])) {
            $this->editingData['selected_services'] = array_values(array_diff($this->editingData['selected_services'], [$serviceId]));
            unset($this->editingData['custom_service_prices'][$serviceId]);
            unset($this->editingData['service_details'][$serviceId]);
        } else {
            $this->editingData['selected_services'][] = $serviceId;
            $service = $this->services()->find($serviceId);
            $this->editingData['custom_service_prices'][$serviceId] = $service->price;
            $this->editingData['service_details'][$serviceId] = '';
        }

        // Always sync HK first before allowing manual edit
        $this->syncEditingHk();
    }

    protected function syncEditingHk()
    {
        if (empty($this->editingData)) return;

        $selectedCount = count($this->editingData['selected_services'] ?? []);
        $customCount = count($this->editingData['custom_services'] ?? []);
        $totalServices = $selectedCount + $customCount;

        // If only 1 service, ALWAYS force standard HK
        if ($totalServices <= 1) {
            $catalogHk = $this->services()->whereIn('id', $this->editingData['selected_services'] ?? [])->sum('hk_days');
            $customHk = collect($this->editingData['custom_services'] ?? [])->sum('hk_days');
            $this->editingData['hk_days'] = $catalogHk + $customHk;
        }
    }

    public function addEditingCustomService()
    {
        $this->editingData['custom_services'][] = [
            'category' => 'Lainnya',
            'name' => '',
            'price' => 0,
            'manual_detail' => '',
            'hk_days' => 0
        ];
    }

    public function removeEditingCustomService($cIdx)
    {
        unset($this->editingData['custom_services'][$cIdx]);
        $this->editingData['custom_services'] = array_values($this->editingData['custom_services']);
        $this->syncEditingHk();
    }

    public function updateItem()
    {
        $leadService = app(CsLeadService::class);
        try {
            // Prepare the services array merging catalog and custom services
            $servicesArray = [];
            
            // 1. Regular services
            if (!empty($this->editingData['selected_services'])) {
                $selectedDbServices = $this->services()->whereIn('id', $this->editingData['selected_services']);
                foreach ($selectedDbServices as $s) {
                    $servicesArray[] = [
                        'id' => $s->id,
                        'name' => $s->name,
                        'category' => $s->category,
                        'price' => (float)($this->editingData['custom_service_prices'][$s->id] ?? $s->price),
                        'manual_detail' => $this->editingData['service_details'][$s->id] ?? '',
                    ];
                }
            }

            // 2. Custom services
            if (!empty($this->editingData['custom_services'])) {
                foreach ($this->editingData['custom_services'] as $cs) {
                    if (empty($cs['name'])) continue;
                    $servicesArray[] = [
                        'id' => null,
                        'name' => $cs['name'],
                        'category' => $cs['category'] ?? 'Custom',
                        'price' => (float)($cs['price'] ?? 0),
                        'manual_detail' => $cs['manual_detail'] ?? '',
                        'hk_days' => (int)($cs['hk_days'] ?? 0),
                        'is_custom' => true
                    ];
                }
            }

            // Calculate total HK
            $catalogHk = $this->services()->whereIn('id', $this->editingData['selected_services'] ?? [])->sum('hk_days');
            $customHk = collect($this->editingData['custom_services'] ?? [])->sum('hk_days');
            
            // Prepare full data for service
            $updateData = $this->editingData;
            $updateData['services'] = $servicesArray;
            $updateData['item_total_price'] = collect($servicesArray)->sum('price');
            $updateData['item_notes'] = $this->calculateEditingSummary();
            // Use the HK from the screen (could be manual or auto-sum)
            $updateData['hk_days'] = $this->editingData['hk_days'];
            
            // Explicitly ensure new fields are included if not already
            $updateData['shoe_type'] = $this->editingData['shoe_type'] ?? '';
            $updateData['shoe_color'] = $this->editingData['shoe_color'] ?? '';
            $updateData['shoe_size'] = $this->editingData['shoe_size'] ?? '';
            $updateData['shoe_brand'] = $this->editingData['shoe_brand'] ?? '';

            $leadService->updateQuotationItem($this->editingItem, $updateData);
            
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Barang berhasil diupdate!']);
            $this->showEditItemModal = false;
            
            // Critical: Refresh lead and ALL nested relations to ensure totals are recalculated
            $this->lead = $this->lead->fresh(['cs', 'activities.user', 'quotations.quotationItems', 'spk.customer', 'spk.items']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * SPK Generation Methods
     */
    public function openSpkModal()
    {
        $acceptedQuotation = $this->lead->quotations()->where('status', CsQuotation::STATUS_ACCEPTED)->latest()->first();
        if (!$acceptedQuotation) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Harus ada Quotation yang diterima terlebih dahulu.']);
            return;
        }

        $this->spkData['customer_address'] = $this->lead->customer_address;
        $this->spkData['customer_city'] = $this->lead->customer_city;
        $this->spkData['customer_province'] = $this->lead->customer_province;
        $this->spkData['manual_cs_code'] = $this->lead->cs->cs_code ?? 'SW';
        $this->spkData['spk_number'] = 'SPK-' . date('Ymd') . '-' . rand(100, 999);
        
        $this->showSpkModal = true;
    }

    public function updateLeadAddress()
    {
        $this->validate([
            'lead.customer_address' => 'nullable|string',
            'lead.customer_city' => 'nullable|string',
            'lead.customer_province' => 'nullable|string',
        ]);

        $this->lead->save();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Informasi pengiriman berhasil diperbarui!']);
    }

    public function applyPromo()
    {
        if (empty($this->spkData['promo_code'])) {
            $this->spkData['promotion_id'] = null;
            $this->spkData['discount_amount'] = 0;
            return;
        }

        $promo = \App\Models\Promotion::where('code', $this->spkData['promo_code'])->active()->valid()->first();

        if (!$promo) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Kode promo tidak valid atau sudah kadaluarsa.']);
            $this->spkData['promotion_id'] = null;
            $this->spkData['discount_amount'] = 0;
            return;
        }

        $totalQuotation = $this->totalQuotationValue;
        $this->spkData['promotion_id'] = $promo->id;
        $this->spkData['discount_amount'] = $promo->calculateDiscount($totalQuotation);
        
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Kode promo berhasil digunakan!']);
    }

    public function generateSpk()
    {
        $this->validate([
            'spkData.delivery_type' => 'required',
            'spkData.priority' => 'required',
            'spkData.expected_delivery_date' => 'nullable|date',
            'spkData.manual_cs_code' => 'required|min:2|max:5',
        ]);

        $spkService = app(CsSpkService::class);
        $customerService = app(\App\Services\CustomerService::class);

        try {
            DB::transaction(function () use ($spkService, $customerService) {
                $quotation = $this->lead->getLatestQuotation();
                if (!$quotation) throw new \Exception("Belum ada penawaran yang dibuat.");

                // 0. Ensure Customer exists in the main table - Use data from spkData (which can be edited in modal)
                $customer = $customerService->syncCustomer([
                    'name' => $this->spkData['customer_name'] ?: $this->lead->customer_name,
                    'phone' => $this->spkData['customer_phone'] ?: $this->lead->customer_phone,
                    'email' => $this->spkData['customer_email'] ?: $this->lead->customer_email,
                    'address' => $this->spkData['customer_address'] ?: $this->lead->customer_address,
                    'city' => $this->spkData['customer_city'] ?: $this->lead->customer_city,
                    'province' => $this->spkData['customer_province'] ?: $this->lead->customer_province,
                ]);

                // Also update the lead data to match the finalized SPK data
                $this->lead->update([
                    'customer_name' => $this->spkData['customer_name'] ?: $this->lead->customer_name,
                    'customer_address' => $this->spkData['customer_address'] ?: $this->lead->customer_address,
                    'customer_city' => $this->spkData['customer_city'] ?: $this->lead->customer_city,
                    'customer_province' => $this->spkData['customer_province'] ?: $this->lead->customer_province,
                ]);

                // Get first item for legacy root columns
                $firstItem = $quotation->quotationItems->first();
                
                // Prioritize manual input from the UI
                $csCode = strtoupper($this->spkData['manual_cs_code'] ?? auth()->user()->cs_code ?? $this->lead->cs->cs_code ?? 'CS');

                // 1. Create CsSpk record
                $spkNumber = CsSpk::generateSpkNumber($this->spkData['delivery_type'], $csCode);
                
                $spk = CsSpk::create([
                    'cs_lead_id' => $this->lead->id,
                    'spk_number' => $spkNumber,
                    'customer_id' => $customer->id,
                    'total_price' => $this->totalQuotationValue - $this->spkData['discount_amount'],
                    'dp_amount' => $this->spkData['dp_amount'] ?: 0,
                    'dp_status' => $this->spkData['dp_amount'] > 0 ? CsSpk::DP_PENDING : CsSpk::DP_WAIVED,
                    'delivery_type' => $this->spkData['delivery_type'],
                    'priority' => $this->spkData['priority'],
                    'expected_delivery_date' => $this->spkData['expected_delivery_date'],
                    'special_instructions' => $this->spkData['special_instructions'],
                    'cs_code' => $csCode,
                    'status' => $this->spkData['dp_amount'] > 0 ? CsSpk::STATUS_WAITING_DP : CsSpk::STATUS_DP_PAID,
                    // Legacy Root Columns
                    'shoe_brand' => $firstItem?->shoe_brand,
                    'shoe_type' => $firstItem?->shoe_type,
                    'shoe_size' => $firstItem?->shoe_size,
                    'shoe_color' => $firstItem?->shoe_color,
                    'category' => $firstItem?->category,
                ]);

                // 2. Create CsSpkItems
                foreach ($quotation->quotationItems as $qItem) {
                    $spk->items()->create([
                        'quotation_item_id' => $qItem->id,
                        'category' => $qItem->category,
                        'shoe_brand' => $qItem->shoe_brand,
                        'shoe_type' => $qItem->shoe_type,
                        'shoe_size' => $qItem->shoe_size,
                        'shoe_color' => $qItem->shoe_color,
                        'services' => $qItem->services,
                        'requested_materials' => $qItem->requested_materials,
                        'item_total_price' => $qItem->item_total_price,
                        'original_price' => $qItem->item_total_price,
                        'hk_days' => $qItem->hk_days,
                        'is_warranty' => $qItem->is_warranty,
                        'status' => 'PENDING',
                        'item_notes' => $qItem->item_notes,
                        'item_number' => $qItem->item_number ?? 1
                    ]);
                }

                // 3. Increment Promo Usage if applied
                if ($this->spkData['promotion_id']) {
                    $promo = \App\Models\Promotion::find($this->spkData['promotion_id']);
                    if ($promo) $promo->incrementUsage();
                }

                // Status remains in CLOSING as per legacy flow
                
                // Ensure the relation is reloaded for the UI
                $this->lead = $this->lead->fresh(['spk.items']);
                $this->activeTab = 'spk';
                
                $this->dispatch('notify', ['type' => 'success', 'message' => 'SPK Berhasil Diterbitkan!']);
                $this->showSpkModal = false;
            });
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Activity & Status Methods
     */
    public function logActivity()
    {
        if (empty($this->activityContent)) return;

        $this->lead->activities()->create([
            'user_id' => Auth::id(),
            'type' => $this->activityType,
            'content' => $this->activityContent,
        ]);

        $this->lead->update(['last_activity_at' => now()]);
        $this->activityContent = '';
        $this->lead->refresh();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Aktivitas dicatat.']);
    }

    public function updateStatus($status, $notes = null)
    {
        try {
            app(CsLeadService::class)->updateStatus($this->lead, $status, $notes);
            $this->lead->refresh();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Status diupdate ke ' . $status]);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function moveToStatus($status)
    {
        $this->updateStatus($status);
    }

    public function takeOverLead()
    {
        try {
            $updateData = ['cs_id' => Auth::id()];
            
            // Auto upgrade status if it's still in Greeting
            if ($this->lead->status === CsLead::STATUS_GREETING) {
                $updateData['status'] = CsLead::STATUS_KONSULTASI;
            }

            $this->lead->update($updateData);
            
            $this->lead->activities()->create([
                'user_id' => Auth::id(),
                'type' => 'NOTE',
                'content' => 'Lead diambil alih oleh ' . Auth::user()->name . ($updateData['status'] ?? '' === CsLead::STATUS_KONSULTASI ? ' dan dipindah ke Konsultasi' : ''),
            ]);

            $this->lead->refresh();
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Lead berhasil Anda ambil alih!']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal mengambil alih: ' . $e->getMessage()]);
        }
    }

    #[Computed]
    public function categories()
    {
        return Service::pluck('category')->unique()->values()->toArray();
    }

    #[Computed]
    public function currentSpkNumber()
    {
        if (empty($this->spkData['delivery_type'])) return 'DRAF-SPK-XXXX';
        return CsSpk::generateSpkNumber(
            $this->spkData['delivery_type'], 
            $this->spkData['manual_cs_code'] ?: 'CS'
        );
    }

    #[Computed]
    public function totalQuotationValue()
    {
        // 1. If we are in the middle of creating a NEW quotation (Draft Modal), calculate from draftItems
        if ($this->showDraftModal && !empty($this->draftItems)) {
            $grandTotal = 0;
            foreach ($this->draftItems as $item) {
                // Regular catalog services
                if (!empty($item['selected_services'])) {
                    $selectedDbServices = $this->services()->whereIn('id', $item['selected_services']);
                    foreach ($selectedDbServices as $s) {
                        $grandTotal += (float)($item['custom_service_prices'][$s->id] ?? $s->price);
                    }
                }
                
                // Custom services added manually
                if (!empty($item['custom_services'])) {
                    foreach ($item['custom_services'] as $customSvc) {
                        $grandTotal += (float) ($customSvc['price'] ?? 0);
                    }
                }
            }
            return $grandTotal;
        }

        // 2. Otherwise, use the same items as displayed in the list for consistency (Accepted or Latest Version)
        return $this->quotationItems()->sum('item_total_price');
    }

    public function openHandover()
    {
        if (!$this->lead->spk) return;

        $this->handoverItems = [];
        foreach ($this->lead->spk->items as $item) {
            $this->handoverItems[$item->id] = [
                'spk_item_id' => $item->id,
                'shoe_brand' => $item->shoe_brand,
                'shoe_type' => $item->shoe_type,
                'shoe_color' => $item->shoe_color,
                'shoe_size' => $item->shoe_size,
                'category' => $item->category,
                'item_type' => $item->category === 'Sepatu' ? 'Sepatu' : 'Lainnya',
                'hk_days' => $item->hk_days, // HK is now carried over!
                'ref_photos' => [],
                'cover_index' => 0,
                'ref_index' => 0,
            ];
        }

        $this->showHandoverModal = true;
    }

    public function submitHandover(\App\Services\Cs\CsSpkService $spkService)
    {
        $this->validate([
            'handoverItems.*.shoe_brand' => 'required',
            'handoverItems.*.shoe_type' => 'required',
            'handoverItems.*.ref_photos' => 'nullable|array',
        ]);

        try {
            DB::transaction(function () use ($spkService) {
                $spkService->handToWorkshop($this->lead->spk, $this->handoverItems);
                $this->lead->refresh();
                $this->showHandoverModal = false;
                $this->dispatch('notify', ['type' => 'success', 'message' => 'Order berhasil diserahkan ke Workshop!']);
            });
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal handover: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.cs.lead-detail-manager');
    }
}
