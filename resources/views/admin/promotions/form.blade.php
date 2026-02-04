<x-app-layout>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.promotions.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">{{ $isEdit ? 'Edit Promo' : 'Buat Promo Baru' }}</h1>
        </div>
        <p class="text-gray-600 text-sm ml-9">{{ $isEdit ? 'Update informasi promo' : 'Buat promo baru untuk menarik customer' }}</p>
    </div>

    <!-- Form -->
    <form action="{{ $isEdit ? route('admin.promotions.update', $promotion) : route('admin.promotions.store') }}" 
          method="POST" 
          x-data="promoForm({{ $isEdit ? $promotion->toJson() : 'null' }})"
          class="bg-white rounded-lg shadow-sm p-6">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <!-- Basic Info -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Promo *</label>
                    <input type="text" name="code" value="{{ old('code', $promotion->code) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 uppercase"
                           placeholder="RESIZE20" required>
                    @error('code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Promo *</label>
                    <input type="text" name="name" value="{{ old('name', $promotion->name) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Diskon 20% untuk Resize" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="2" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Dapatkan diskon 20% untuk layanan Resize">{{ old('description', $promotion->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Promo Type & Discount -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tipe & Diskon</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Promo *</label>
                    <select name="type" x-model="type" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <option value="PERCENTAGE">Persentase (%)</option>
                        <option value="FIXED">Fixed Amount (Rp)</option>
                        <option value="BUNDLE">Bundle</option>
                        <option value="BOGO">BOGO</option>
                    </select>
                </div>

                <!-- Discount Percentage -->
                <div x-show="type === 'PERCENTAGE' || type === 'BUNDLE'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Diskon (%)</label>
                    <input type="number" name="discount_percentage" value="{{ old('discount_percentage', $promotion->discount_percentage) }}" 
                           step="0.01" min="0" max="100"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="20">
                </div>

                <!-- Discount Amount -->
                <div x-show="type === 'FIXED' || type === 'BOGO'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Diskon (Rp)</label>
                    <input type="number" name="discount_amount" value="{{ old('discount_amount', $promotion->discount_amount) }}" 
                           step="1000" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="50000">
                </div>

                <!-- Max Discount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Diskon (Rp)</label>
                    <input type="number" name="max_discount_amount" value="{{ old('max_discount_amount', $promotion->max_discount_amount) }}" 
                           step="1000" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="500000">
                    <p class="text-xs text-gray-500 mt-1">Opsional - cap maksimal diskon</p>
                </div>

                <!-- Min Purchase -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Pembelian (Rp)</label>
                    <input type="number" name="min_purchase_amount" value="{{ old('min_purchase_amount', $promotion->min_purchase_amount) }}" 
                           step="1000" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="200000">
                    <p class="text-xs text-gray-500 mt-1">Opsional - minimum pembelian</p>
                </div>
            </div>
        </div>

        <!-- Validity Period -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Periode Berlaku</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mulai *</label>
                    <input type="datetime-local" name="valid_from" 
                           value="{{ old('valid_from', $promotion->valid_from?->format('Y-m-d\TH:i')) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampai *</label>
                    <input type="datetime-local" name="valid_until" 
                           value="{{ old('valid_until', $promotion->valid_until?->format('Y-m-d\TH:i')) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>
        </div>

        <!-- Applicability -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Berlaku Untuk</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Applicable To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Layanan *</label>
                    <select name="applicable_to" x-model="applicableTo" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <option value="ALL_SERVICES">Semua Layanan</option>
                        <option value="SPECIFIC_SERVICES">Layanan Tertentu</option>
                        <option value="CATEGORIES">Kategori Tertentu</option>
                    </select>
                </div>

                <!-- Customer Tier -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer Tier *</label>
                    <select name="customer_tier" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <option value="ALL" {{ old('customer_tier', $promotion->customer_tier) === 'ALL' ? 'selected' : '' }}>Semua Customer</option>
                        <option value="VIP" {{ old('customer_tier', $promotion->customer_tier) === 'VIP' ? 'selected' : '' }}>VIP Only</option>
                        <option value="REGULAR" {{ old('customer_tier', $promotion->customer_tier) === 'REGULAR' ? 'selected' : '' }}>Regular Only</option>
                        <option value="NEW" {{ old('customer_tier', $promotion->customer_tier) === 'NEW' ? 'selected' : '' }}>New Customer Only</option>
                    </select>
                </div>

                <!-- Service Selection -->
                <div x-show="applicableTo === 'SPECIFIC_SERVICES'" class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Layanan</label>
                    <div class="border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto">
                        @foreach($services as $service)
                            <label class="flex items-center gap-2 py-2 hover:bg-gray-50 px-2 rounded cursor-pointer">
                                <input type="checkbox" name="service_ids[]" value="{{ $service->id }}"
                                       {{ $isEdit && $promotion->services->contains($service->id) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm">{{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Bundle Services -->
                <div x-show="type === 'BUNDLE'" class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Layanan Bundle (harus semua dipilih)</label>
                    <div class="border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto">
                        @foreach($services as $service)
                            <label class="flex items-center gap-2 py-2 hover:bg-gray-50 px-2 rounded cursor-pointer">
                                <input type="checkbox" name="bundle_services[]" value="{{ $service->id }}"
                                       {{ $isEdit && $promotion->bundles->first() && in_array($service->id, $promotion->bundles->first()->required_services ?? []) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm">{{ $service->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Limits -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Batas Penggunaan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Total Penggunaan</label>
                    <input type="number" name="max_usage_total" value="{{ old('max_usage_total', $promotion->max_usage_total) }}" 
                           min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="100">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan untuk unlimited</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Per Customer</label>
                    <input type="number" name="max_usage_per_customer" value="{{ old('max_usage_per_customer', $promotion->max_usage_per_customer ?? 1) }}" 
                           min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="1">
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <input type="number" name="priority" value="{{ old('priority', $promotion->priority ?? 0) }}" 
                           min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">Higher number = higher priority</p>
                </div>
                <div class="flex flex-col gap-3 pt-8">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Aktif</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_stackable" {{ old('is_stackable', $promotion->is_stackable) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Bisa Ditumpuk dengan Promo Lain</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 justify-end pt-4 border-t">
            <a href="{{ route('admin.promotions.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                {{ $isEdit ? 'Update Promo' : 'Buat Promo' }}
            </button>
        </div>
    </form>
</div>

<script>
function promoForm(existingPromo) {
    return {
        type: existingPromo?.type || 'PERCENTAGE',
        applicableTo: existingPromo?.applicable_to || 'ALL_SERVICES',
    }
}
</script>
</x-app-layout>
