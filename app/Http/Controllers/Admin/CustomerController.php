<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->withCount(['photos', 'workOrders'])
                           ->orderBy('created_at', 'desc')
                           ->paginate(20)
                           ->appends($request->except('page'));

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'city_id' => 'nullable|string',
            'province' => 'nullable|string|max:100',
            'province_id' => 'nullable|string',
            'postal_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);

        return redirect()->route('admin.customers.show', $customer)
                        ->with('success', 'Customer berhasil ditambahkan');
    }

    /**
     * Display the specified customer
     */
    public function show($id)
    {
        $customer = Customer::with([
            'photos.uploader', 
            'workOrders' => function($q) {
                $q->with(['services', 'photos'])
                  ->latest('entry_date');
            }
        ])->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'city_id' => 'nullable|string', // RajaOngkir ID
            'province' => 'nullable|string|max:100',
            'province_id' => 'nullable|string', // RajaOngkir ID
            'postal_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.show', $customer)
                        ->with('success', 'Customer berhasil diupdate');
    }

    /**
     * Remove the specified customer
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        
        // Delete all photos
        foreach ($customer->photos as $photo) {
            Storage::disk('public')->delete($photo->file_path);
        }
        
        $customer->delete();

        return redirect()->route('admin.customers.index')
                        ->with('success', 'Customer berhasil dihapus');
    }

    /**
     * Upload photo for customer (with compress + watermark)
     */
    public function uploadPhoto(Request $request, $id)
    {
        $request->validate([
            'photos.*' => 'required|image|mimes:jpeg,jpg,png|max:10240',
            'caption' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:general,before,after',
        ]);

        $customer = Customer::findOrFail($id);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $this->processAndSavePhoto($customer, $photo, $request->caption, $request->type ?? 'general');
            }
        }

        return redirect()->back()->with('success', 'Foto berhasil diupload');
    }

    /**
     * Process photo: compress + watermark
     */
    protected function processAndSavePhoto($customer, $photo, $caption = null, $type = 'general')
    {
        // Create manager instance with desired driver
        $manager = new ImageManager(new Driver());

        // Load image
        $image = $manager->read($photo);
        
        // Resize if too large
        if ($image->width() > 1920) {
            $image->scaleDown(width: 1920);
        }
        
        // Add watermark
        try {
            $logoPath = public_path('images/logo-watermark.png');
            if (file_exists($logoPath)) {
                $logo = $manager->read($logoPath);
                $logo->scale(width: 400);

                $image->place($logo, 'bottom-right', 20, 80);
            }
            
            // Add customer name text
            $image->text($customer->name, $image->width() - 20, $image->height() - 20, function($font) {
                $font->size(36);
                $font->color('rgba(255, 255, 255, 0.9)');
                $font->align('right');
                $font->valign('bottom');
            });
        } catch (\Exception $e) {
            Log::warning('Watermark failed: ' . $e->getMessage());
        }
        
        // Save
        $filename = 'customer_' . $customer->id . '_' . time() . '_' . uniqid() . '.jpg';
        $path = 'photos/customers/' . $filename;
        
        Storage::disk('public')->put($path, $image->toJpeg(85)->toString());
        
        // Create record
        CustomerPhoto::create([
            'customer_id' => $customer->id,
            'file_path' => $path,
            'caption' => $caption,
            'type' => $type,
            'uploaded_by' => Auth::id(),
        ]);
    }
    /**
     * Delete customer photo
     */
    public function destroyPhoto($id)
    {
        try {
            $photo = CustomerPhoto::findOrFail($id);
            
            // Delete file
            if (Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }
            
            $photo->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto: ' . $e->getMessage()
            ], 500);
        }
    }
}
