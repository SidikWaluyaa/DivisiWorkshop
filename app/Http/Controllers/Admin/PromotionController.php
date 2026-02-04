<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\PromotionActivity;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    /**
     * Display a listing of promotions
     */
    public function index(Request $request)
    {
        $query = Promotion::with('services', 'creator');

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search by code or name
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'LIKE', "%{$request->search}%")
                  ->orWhere('name', 'LIKE', "%{$request->search}%");
            });
        }

        $promotions = $query->orderBy('priority', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(20);

        return view('admin.promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new promotion
     */
    public function create()
    {
        $services = Service::orderBy('name')->get();
        return view('admin.promotions.form', [
            'promotion' => new Promotion(),
            'services' => $services,
            'isEdit' => false,
        ]);
    }

    /**
     * Store a newly created promotion
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promotions,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:PERCENTAGE,FIXED,BUNDLE,BOGO',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
            'applicable_to' => 'required|in:ALL_SERVICES,SPECIFIC_SERVICES,CATEGORIES',
            'customer_tier' => 'required|in:ALL,VIP,REGULAR,NEW',
            'max_usage_total' => 'nullable|integer|min:1',
            'max_usage_per_customer' => 'nullable|integer|min:1',
            'is_stackable' => 'boolean',
            'priority' => 'nullable|integer|min:0',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
            'bundle_services' => 'nullable|array',
            'bundle_services.*' => 'exists:services,id',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');
        $validated['is_stackable'] = $request->has('is_stackable');

        $promotion = Promotion::create($validated);

        // Attach services if applicable
        if ($validated['applicable_to'] === 'SPECIFIC_SERVICES' && !empty($validated['service_ids'])) {
            $promotion->services()->attach($validated['service_ids']);
        }

        // Create bundle if type is BUNDLE
        if ($validated['type'] === 'BUNDLE' && !empty($validated['bundle_services'])) {
            $promotion->bundles()->create([
                'required_services' => $validated['bundle_services'],
            ]);
        }

        // Log Activity
        PromotionActivity::create([
            'promotion_id' => $promotion->id,
            'user_id' => Auth::id(),
            'type' => 'CREATED',
            'content' => "Promo '{$promotion->name}' ({$promotion->code}) berhasil dibuat.",
            'metadata' => $promotion->toArray(),
        ]);

        return redirect()->route('admin.promotions.index')
                        ->with('success', 'Promo berhasil dibuat!');
    }

    /**
     * Show the form for editing the specified promotion
     */
    public function edit(Promotion $promotion)
    {
        $services = Service::orderBy('name')->get();
        $promotion->load('services', 'bundles');

        return view('admin.promotions.form', [
            'promotion' => $promotion,
            'services' => $services,
            'isEdit' => true,
        ]);
    }

    /**
     * Update the specified promotion
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promotions,code,' . $promotion->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:PERCENTAGE,FIXED,BUNDLE,BOGO',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
            'applicable_to' => 'required|in:ALL_SERVICES,SPECIFIC_SERVICES,CATEGORIES',
            'customer_tier' => 'required|in:ALL,VIP,REGULAR,NEW',
            'max_usage_total' => 'nullable|integer|min:1',
            'max_usage_per_customer' => 'nullable|integer|min:1',
            'is_stackable' => 'boolean',
            'priority' => 'nullable|integer|min:0',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
            'bundle_services' => 'nullable|array',
            'bundle_services.*' => 'exists:services,id',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_stackable'] = $request->has('is_stackable');

        $promotion->update($validated);

        // Sync services
        if ($validated['applicable_to'] === 'SPECIFIC_SERVICES' && !empty($validated['service_ids'])) {
            $promotion->services()->sync($validated['service_ids']);
        } else {
            $promotion->services()->detach();
        }

        // Update bundle
        if ($validated['type'] === 'BUNDLE' && !empty($validated['bundle_services'])) {
            $promotion->bundles()->delete();
            $promotion->bundles()->create([
                'required_services' => $validated['bundle_services'],
            ]);
        } else {
            $promotion->bundles()->delete();
        }

        // Log Activity
        PromotionActivity::create([
            'promotion_id' => $promotion->id,
            'user_id' => Auth::id(),
            'type' => 'UPDATED',
            'content' => "Konfigurasi promo '{$promotion->name}' diperbarui.",
            'metadata' => [
                'changes' => array_intersect_key($validated, $promotion->getChanges()),
                'full_state' => $promotion->toArray(),
            ],
        ]);

        return redirect()->route('admin.promotions.index')
                        ->with('success', 'Promo berhasil diupdate!');
    }

    /**
     * Remove the specified promotion
     */
    public function destroy(Promotion $promotion)
    {
        // Log Activity before deletion (since it's not soft delete-aware in this model yet)
        PromotionActivity::create([
            'promotion_id' => $promotion->id,
            'user_id' => Auth::id(),
            'type' => 'DELETED',
            'content' => "Promo '{$promotion->name}' ({$promotion->code}) dihapus.",
        ]);

        $promotion->delete();

        return redirect()->route('admin.promotions.index')
                        ->with('success', 'Promo berhasil dihapus!');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Promotion $promotion)
    {
        $promotion->update([
            'is_active' => !$promotion->is_active,
        ]);

        $status = $promotion->is_active ? 'diaktifkan' : 'dinonaktifkan';

        // Log Activity
        PromotionActivity::create([
            'promotion_id' => $promotion->id,
            'user_id' => Auth::id(),
            'type' => 'TOGGLED',
            'content' => "Status promo '{$promotion->name}' diubah menjadi " . ($promotion->is_active ? 'Aktif' : 'Non-aktif'),
        ]);

        return back()->with('success', "Promo berhasil {$status}!");
    }
}
