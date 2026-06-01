<x-app-layout>
<div class="min-h-screen bg-[#F8FAFC]">

    {{-- ===== PREMIUM DARK HEADER ===== --}}
    <div class="bg-gray-900 pt-12 pb-24 relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/10 to-transparent mix-blend-overlay"></div>
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-[#FFC232]/10 rounded-full blur-[100px]"></div>
        </div>

        <div class="max-w-4xl mx-auto px-6 relative z-10">
            <div class="flex items-center gap-6">
                <a href="{{ route('admin.promotions.index') }}" class="group flex items-center justify-center w-14 h-14 bg-white/5 rounded-[1.5rem] border border-white/10 text-white hover:bg-white/10 transition-all hover:-translate-x-1 active:scale-90">
                    <svg class="w-6 h-6 text-white/50 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <h1 class="text-4xl font-black text-white italic tracking-tighter leading-none uppercase">{{ $isEdit ? 'Edit Promo' : 'Buat Promo Baru' }}</h1>
                    <p class="text-white/40 font-black text-xs uppercase tracking-[0.4em] italic mt-2 flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-[#FFC232]"></span>
                        {{ $isEdit ? 'Update konfigurasi promo' : 'Buat promo baru untuk menarik customer' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FORM CARD ===== --}}
    <div class="max-w-4xl mx-auto px-6 -mt-12 relative z-20 pb-16">

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 mb-6 flex items-start gap-4">
                <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center text-rose-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                </div>
                <div>
                    <span class="text-xs font-black text-rose-700 uppercase tracking-wider italic block mb-1">Terdapat Error</span>
                    <ul class="text-xs text-rose-600 italic space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ $isEdit ? route('admin.promotions.update', $promotion) : route('admin.promotions.store') }}" 
              method="POST" 
              x-data="promoForm({{ $isEdit ? $promotion->toJson() : 'null' }})"
              class="space-y-8">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            {{-- Section: Informasi Dasar --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-2xl border border-gray-100">
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.5em] italic flex items-center gap-4 mb-8">
                    <div class="w-2 h-2 rounded-full bg-[#FFC232]"></div>
                    Informasi Dasar
                    <div class="h-px flex-1 bg-gray-100"></div>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Kode Promo *</label>
                        <input type="text" name="code" value="{{ old('code', $promotion->code) }}" 
                               class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-900 italic uppercase focus:ring-2 focus:ring-[#FFC232]/50 focus:border-[#FFC232] transition-all placeholder:text-gray-300"
                               placeholder="RESIZE20" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Nama Promo *</label>
                        <input type="text" name="name" value="{{ old('name', $promotion->name) }}" 
                               class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-900 italic focus:ring-2 focus:ring-[#FFC232]/50 focus:border-[#FFC232] transition-all placeholder:text-gray-300"
                               placeholder="Diskon 20% untuk Resize" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Deskripsi</label>
                        <textarea name="description" rows="2" 
                                  class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 italic focus:ring-2 focus:ring-[#FFC232]/50 focus:border-[#FFC232] transition-all placeholder:text-gray-300"
                                  placeholder="Dapatkan diskon 20% untuk layanan Resize">{{ old('description', $promotion->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Section: Tipe & Diskon --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-2xl border border-gray-100">
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.5em] italic flex items-center gap-4 mb-8">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    Tipe & Diskon
                    <div class="h-px flex-1 bg-gray-100"></div>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Tipe Promo *</label>
                        <select name="type" x-model="type" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 italic focus:ring-2 focus:ring-[#FFC232]/50 transition-all" required>
                            <option value="PERCENTAGE">Persentase (%)</option>
                            <option value="FIXED">Fixed Amount (Rp)</option>
                            <option value="BUNDLE">Bundle</option>
                            <option value="BOGO">BOGO</option>
                        </select>
                    </div>
                    <div x-show="type === 'PERCENTAGE' || type === 'BUNDLE'">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Diskon (%)</label>
                        <input type="number" name="discount_percentage" value="{{ old('discount_percentage', $promotion->discount_percentage) }}" step="0.01" min="0" max="100" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 italic focus:ring-2 focus:ring-[#FFC232]/50 transition-all placeholder:text-gray-300" placeholder="20">
                    </div>
                    <div x-show="type === 'FIXED' || type === 'BOGO'">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Diskon (Rp)</label>
                        <input type="number" name="discount_amount" value="{{ old('discount_amount', $promotion->discount_amount) }}" step="1000" min="0" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 italic focus:ring-2 focus:ring-[#FFC232]/50 transition-all placeholder:text-gray-300" placeholder="50000">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Max Diskon (Rp)</label>
                        <input type="number" name="max_discount_amount" value="{{ old('max_discount_amount', $promotion->max_discount_amount) }}" step="1000" min="0" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 italic focus:ring-2 focus:ring-[#FFC232]/50 transition-all placeholder:text-gray-300" placeholder="500000">
                        <p class="text-[10px] text-gray-400 italic mt-1">Opsional — cap maksimal diskon</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Min Pembelian (Rp)</label>
                        <input type="number" name="min_purchase_amount" value="{{ old('min_purchase_amount', $promotion->min_purchase_amount) }}" step="1000" min="0" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 italic focus:ring-2 focus:ring-[#FFC232]/50 transition-all placeholder:text-gray-300" placeholder="200000">
                        <p class="text-[10px] text-gray-400 italic mt-1">Opsional — minimum pembelian</p>
                    </div>
                </div>
            </div>

            {{-- Section: Periode Berlaku --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-2xl border border-gray-100">
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.5em] italic flex items-center gap-4 mb-8">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    Periode Berlaku
                    <div class="h-px flex-1 bg-gray-100"></div>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Mulai *</label>
                        <input type="datetime-local" name="valid_from" value="{{ old('valid_from', $promotion->valid_from?->format('Y-m-d\TH:i')) }}" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 italic focus:ring-2 focus:ring-[#FFC232]/50 transition-all" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Sampai *</label>
                        <input type="datetime-local" name="valid_until" value="{{ old('valid_until', $promotion->valid_until?->format('Y-m-d\TH:i')) }}" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 italic focus:ring-2 focus:ring-[#FFC232]/50 transition-all" required>
                    </div>
                </div>
            </div>

            {{-- Section: Berlaku Untuk --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-2xl border border-gray-100">
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.5em] italic flex items-center gap-4 mb-8">
                    <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                    Berlaku Untuk
                    <div class="h-px flex-1 bg-gray-100"></div>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Layanan *</label>
                        <select name="applicable_to" x-model="applicableTo" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 italic focus:ring-2 focus:ring-[#FFC232]/50 transition-all" required>
                            <option value="ALL_SERVICES">Semua Layanan</option>
                            <option value="SPECIFIC_SERVICES">Layanan Tertentu</option>
                            <option value="CATEGORIES">Kategori Tertentu</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Customer Tier *</label>
                        <select name="customer_tier" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 italic focus:ring-2 focus:ring-[#FFC232]/50 transition-all" required>
                            <option value="ALL" {{ old('customer_tier', $promotion->customer_tier) === 'ALL' ? 'selected' : '' }}>Semua Customer</option>
                            <option value="VIP" {{ old('customer_tier', $promotion->customer_tier) === 'VIP' ? 'selected' : '' }}>VIP Only</option>
                            <option value="REGULAR" {{ old('customer_tier', $promotion->customer_tier) === 'REGULAR' ? 'selected' : '' }}>Regular Only</option>
                            <option value="NEW" {{ old('customer_tier', $promotion->customer_tier) === 'NEW' ? 'selected' : '' }}>New Customer Only</option>
                        </select>
                    </div>

                    <div x-show="applicableTo === 'SPECIFIC_SERVICES'" class="md:col-span-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Pilih Layanan</label>
                        <div class="border border-gray-100 bg-gray-50 rounded-xl p-4 max-h-60 overflow-y-auto space-y-1">
                            @foreach($services as $service)
                                <label class="flex items-center gap-3 py-2 px-3 hover:bg-white rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" name="service_ids[]" value="{{ $service->id }}"
                                           {{ $isEdit && $promotion->services->contains($service->id) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-[#FFC232] focus:ring-[#FFC232]/50">
                                    <span class="text-sm font-bold text-gray-700 italic">{{ $service->name }}</span>
                                    <span class="text-[10px] text-gray-400 italic ml-auto tabular-nums">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div x-show="type === 'BUNDLE'" class="md:col-span-2">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Layanan Bundle (harus semua dipilih)</label>
                        <div class="border border-gray-100 bg-gray-50 rounded-xl p-4 max-h-60 overflow-y-auto space-y-1">
                            @foreach($services as $service)
                                <label class="flex items-center gap-3 py-2 px-3 hover:bg-white rounded-lg cursor-pointer transition-colors">
                                    <input type="checkbox" name="bundle_services[]" value="{{ $service->id }}"
                                           {{ $isEdit && $promotion->bundles->first() && in_array($service->id, $promotion->bundles->first()->required_services ?? []) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500/50">
                                    <span class="text-sm font-bold text-gray-700 italic">{{ $service->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section: Batas Penggunaan & Pengaturan --}}
            <div class="bg-white rounded-[2rem] p-8 shadow-2xl border border-gray-100">
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.5em] italic flex items-center gap-4 mb-8">
                    <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                    Batas Penggunaan & Pengaturan
                    <div class="h-px flex-1 bg-gray-100"></div>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Max Total Penggunaan</label>
                        <input type="number" name="max_usage_total" value="{{ old('max_usage_total', $promotion->max_usage_total) }}" min="1" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 italic focus:ring-2 focus:ring-[#FFC232]/50 transition-all placeholder:text-gray-300" placeholder="100">
                        <p class="text-[10px] text-gray-400 italic mt-1">Kosongkan untuk unlimited</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Max Per Customer</label>
                        <input type="number" name="max_usage_per_customer" value="{{ old('max_usage_per_customer', $promotion->max_usage_per_customer ?? 1) }}" min="1" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 italic focus:ring-2 focus:ring-[#FFC232]/50 transition-all placeholder:text-gray-300" placeholder="1">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] italic block mb-2">Priority</label>
                        <input type="number" name="priority" value="{{ old('priority', $promotion->priority ?? 0) }}" min="0" class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 italic focus:ring-2 focus:ring-[#FFC232]/50 transition-all placeholder:text-gray-300" placeholder="0">
                        <p class="text-[10px] text-gray-400 italic mt-1">Angka lebih tinggi = prioritas lebih tinggi</p>
                    </div>
                    <div class="flex flex-col gap-4 pt-6">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="is_active" {{ old('is_active', $promotion->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500/50">
                            <span class="text-sm font-bold text-gray-700 italic group-hover:text-gray-900 transition-colors">Aktif</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="is_stackable" {{ old('is_stackable', $promotion->is_stackable) ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500/50">
                            <span class="text-sm font-bold text-gray-700 italic group-hover:text-gray-900 transition-colors">Bisa Ditumpuk dengan Promo Lain</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-4 justify-end">
                <a href="{{ route('admin.promotions.index') }}" class="px-8 py-4 bg-white border border-gray-200 rounded-2xl text-sm font-black text-gray-600 italic uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm active:scale-95">
                    Batal
                </a>
                <button type="submit" class="px-10 py-4 bg-[#FFC232] text-gray-900 rounded-2xl text-sm font-black italic uppercase tracking-widest shadow-[0_20px_40px_-10px_rgba(255,194,50,0.5)] hover:scale-105 transition-all active:scale-95 border-4 border-white/20">
                    {{ $isEdit ? 'Update Promo' : 'Buat Promo' }}
                </button>
            </div>
        </form>
    </div>
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
