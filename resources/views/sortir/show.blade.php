<x-app-layout>
    @php
        $upperOptions = $upperMaterials->map(function($m) {
            return [
                'id' => $m->id, 
                'name' => $m->name . ' (Sisa: ' . $m->stock . ' ' . $m->unit . ')', 
                'stock' => $m->stock,
                'disabled' => false
            ];
        })->values();

        $solOptions = $solMaterials->map(function($m) {
            $displayName = $m->name . ' - ' . ($m->size ?? 'All Size') . ' (' . $m->sub_category . ') (Sisa: ' . $m->stock . ')';
            return [
                'id' => $m->id, 
                'name' => $displayName,
                'sub_category' => $m->sub_category,
                'stock' => $m->stock,
                'disabled' => false
            ];
        })->values();

        $otherOptions = $otherMaterials->map(function($m) {
            return [
                'id' => $m->id, 
                'name' => $m->name . ' (Sisa: ' . $m->stock . ' ' . $m->unit . ')', 
                'sub_category' => $m->type,
                'stock' => $m->stock,
                'disabled' => false
            ];
        })->values();

        // Get Custom Service ID for Upsell Form
        $customServiceId = \App\Models\Service::where('name', 'like', '%Custom%')->value('id');
    @endphp

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">
                    {{ __('Material Validation') }}
                </h2>
                <div class="text-xs font-medium opacity-90 flex items-center gap-2">
                    <span class="bg-white/20 px-2 py-0.5 rounded text-white font-mono">
                        {{ $order->spk_number }}
                    </span>
                    <span>{{ $order->customer_name }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            
            {{-- Customer Information Card --}}
            <div class="dashboard-card overflow-hidden mb-6">
                <div class="dashboard-card-header">
                    <h3 class="dashboard-card-title flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Informasi Customer
                    </h3>
                </div>
                <div class="dashboard-card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Nama Customer</label>
                            <p class="text-lg font-bold text-gray-800">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">No. Telepon</label>
                            <p class="text-lg font-mono font-bold text-teal-600">{{ $order->customer_phone ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Email</label>
                            <p class="text-sm font-medium text-gray-700">{{ $order->customer_email ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Alamat</label>
                            <p class="text-sm text-gray-700">{{ $order->customer_address ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Sepatu</label>
                            <p class="text-sm font-bold text-gray-800">{{ $order->shoe_brand }} - {{ $order->shoe_type }}</p>
                            <p class="text-xs text-gray-500">Warna: {{ $order->shoe_color }} | Size: {{ $order->shoe_size ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Prioritas</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold 
                                {{ $order->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $order->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $order->priority === 'normal' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $order->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ strtoupper($order->priority ?? 'NORMAL') }}
                            </span>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2">Jenis Layanan</label>
                            <div class="flex flex-wrap gap-2">
                                @forelse($order->services as $service)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold border-2 
                                        {{ stripos($service->category, 'Sol') !== false ? 'bg-orange-50 text-orange-700 border-orange-200' : '' }}
                                        {{ stripos($service->category, 'Upper') !== false ? 'bg-purple-50 text-purple-700 border-purple-200' : '' }}
                                        {{ stripos($service->category, 'Repaint') !== false ? 'bg-pink-50 text-pink-700 border-pink-200' : '' }}
                                        {{ stripos($service->category, 'Cleaning') !== false || stripos($service->category, 'Treatment') !== false ? 'bg-teal-50 text-teal-700 border-teal-200' : '' }}
                                        {{ !stripos($service->category, 'Sol') && !stripos($service->category, 'Upper') && !stripos($service->category, 'Repaint') && !stripos($service->category, 'Cleaning') && !stripos($service->category, 'Treatment') ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $service->name }}
                                        <span class="ml-2 opacity-60 text-[10px]">({{ $service->category }})</span>
                                    </span>
                                @empty
                                    <span class="text-sm text-gray-400 italic">Tidak ada layanan</span>
                                @endforelse
                            </div>
                        </div>


                        {{-- Photos Section (Merged) --}}
                        @if($order->photos && $order->photos->count() > 0)
                        <div class="md:col-span-2 border-t border-gray-100 pt-4 mt-2">
                             <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Foto Kondisi Sepatu
                            </label>
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                @foreach($order->photos as $photo)
                                    <div class="group relative aspect-square rounded-lg overflow-hidden border border-gray-200 shadow-sm cursor-pointer"
                                         onclick="window.open('{{ asset('storage/' . $photo->file_path) }}', '_blank')">
                                        <img src="{{ asset('storage/' . $photo->file_path) }}" 
                                             alt="Foto Sepatu" 
                                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                            <span class="opacity-0 group-hover:opacity-100 bg-black/50 text-white text-[10px] px-2 py-1 rounded">Lihat</span>
                                        </div>
                                        <div class="absolute bottom-0 inset-x-0 bg-black/50 text-white text-[9px] p-0.5 truncate text-center">
                                            {{ $photo->step }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Photos Gallery Removed (Merged into Customer Info) --}}
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- LEFT COLUMN: Materials for this Order (AlpineJS Cart) -->
                <div class="md:col-span-2 space-y-6" x-data="sortirMaterialCart()">
                    
                    <form action="{{ route('sortir.update-materials', $order->id) }}" method="POST" id="materialForm">
                        @csrf
                        <div class="dashboard-card overflow-hidden">
                            <div class="dashboard-card-header flex justify-between items-center">
                                <h3 class="dashboard-card-title">
                                    📦 Daftar Material Sepatu Ini
                                </h3>
                                <span class="text-xs text-gray-400 font-mono">
                                    <span x-text="selectedMaterials.length"></span> Items
                                </span>
                            </div>
                            
                            <div class="dashboard-card-body p-0">
                                <div x-show="selectedMaterials.length === 0" class="p-8 text-center bg-gray-50/50">
                                    <div class="inline-flex items-center justify-center p-3 bg-white rounded-full text-gray-300 mb-3 shadow-sm">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div>
                                    <p class="text-gray-500 font-bold">Belum ada material yang ditambahkan.</p>
                                    <p class="text-xs text-gray-400 mt-1 mb-4">Silahkan pilih material di kolom kanan bawah atau buat request baru.</p>
                                    
                                    <a href="{{ route('materials.selection.create', $order->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-premium-green text-premium-green rounded-xl text-xs font-bold hover:bg-green-50 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Pesan / Request Material Baru
                                    </a>
                                </div>

                                <div class="overflow-x-auto" x-show="selectedMaterials.length > 0">
                                    <table class="w-full text-sm text-left text-gray-500">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-100">
                                            <tr>
                                                <th class="px-6 py-3 w-10">M</th>
                                                <th class="px-6 py-3">Material</th>
                                                <th class="px-6 py-3">Qty</th>
                                                <th class="px-6 py-3 w-20">Stok</th>
                                                <th class="px-6 py-3 text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 bg-white">
                                            <template x-for="(mat, index) in selectedMaterials" :key="mat.material_id">
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-6 py-4 font-mono text-xs text-gray-400" x-text="index + 1"></td>
                                                    <td class="px-6 py-4">
                                                        <div class="font-bold text-gray-800" x-text="mat.name"></div>
                                                        <div class="text-[10px] text-gray-400" x-text="mat.sub_category"></div>
                                                        <!-- Hidden Inputs -->
                                                        <input type="hidden" :name="'materials['+index+'][material_id]'" :value="mat.material_id">
                                                        <input type="hidden" :name="'materials['+index+'][quantity]'" :value="mat.quantity">
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" @click="decreaseQty(index)" class="w-6 h-6 rounded bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold flex items-center justify-center">-</button>
                                                            <input type="number" x-model="mat.quantity" class="w-12 text-center text-sm border-none p-0 focus:ring-0 font-bold text-gray-800 bg-transparent" readonly>
                                                            <button type="button" @click="increaseQty(index)" class="w-6 h-6 rounded bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold flex items-center justify-center">+</button>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-xs font-mono">
                                                        <span :class="mat.stock_available >= mat.quantity ? 'text-green-600' : 'text-red-600 font-bold'">
                                                            <span x-text="mat.stock_available"></span> Available
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-right">
                                                        <button type="button" @click="removeMaterial(index)" class="text-red-400 hover:text-red-600 transition-colors">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                             <!-- Save Button Section -->
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center" x-show="hasChanges || selectedMaterials.length > 0">
                                <span class="text-xs text-gray-500 italic">Pastikan data benar sebelum menyimpan.</span>
                                <button type="submit" 
                                        :disabled="!hasChanges"
                                        class="px-8 py-3 rounded-xl transition-all flex items-center gap-2 btn-premium-yellow disabled:opacity-50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                    SIMPAN PERUBAHAN
                                </button>
                            </div>
                        </div>
                    </form>

                <script>
                    function sortirMaterialCart() {
                        return {
                            selectedMaterials: [
                                @foreach($order->materials as $m)
                                {
                                    material_id: {{ $m->id }},
                                    name: '{{ $m->name }}',
                                    sub_category: '{{ $m->sub_category ?? $m->type }}',
                                    quantity: {{ $m->pivot->quantity }},
                                    stock_available: {{ $m->stock + $m->pivot->quantity }} // Original stock + reserved
                                },
                                @endforeach
                            ],
                            upperMaterials: @json($upperOptions),
                            solMaterials: @json($solOptions),
                            otherMaterials: @json($otherOptions),
                            searchUpper: '',
                            searchSol: '',
                            searchOther: '',
                            showModal: false,
                            searchModal: '',
                            activeModalType: 'upper', // upper, sol, other
                            activeModalTitle: 'Pilih Material Upper',
                            activeTab: '{{ $suggestedTab }}',
                            form: {
                                upperId: '',
                                upperQty: 1,
                                solId: '',
                                solQty: 1,
                                otherId: '',
                                otherQty: 1
                            },
                            hasChanges: false,
                            
                            init() {
                                this.$watch('selectedMaterials', () => {
                                    this.hasChanges = true;
                                });
                            },
                            
                            getFilteredUpper() {
                                if (!this.searchUpper) return this.upperMaterials;
                                return this.upperMaterials.filter(m => m.name.toLowerCase().includes(this.searchUpper.toLowerCase()));
                            },

                            getFilteredSol() {
                                if (!this.searchSol) return this.solMaterials;
                                return this.solMaterials.filter(m => m.name.toLowerCase().includes(this.searchSol.toLowerCase()));
                            },

                            getFilteredOther() {
                                if (!this.searchOther) return this.otherMaterials;
                                return this.otherMaterials.filter(m => m.name.toLowerCase().includes(this.searchOther.toLowerCase()));
                            },

                            // Modal Helpers
                            getModalItems() {
                                let list = [];
                                if (this.activeModalType === 'upper') list = this.upperMaterials;
                                if (this.activeModalType === 'sol') list = this.solMaterials;
                                if (this.activeModalType === 'other') list = this.otherMaterials;
                                
                                if (!this.searchModal) return list;
                                return list.filter(m => m.name.toLowerCase().includes(this.searchModal.toLowerCase()));
                            },

                            openModal(type) {
                                this.activeModalType = type;
                                this.searchModal = '';
                                
                                if (type === 'upper') this.activeModalTitle = 'Pilih Material Upper';
                                if (type === 'sol') this.activeModalTitle = 'Pilih Material Sol';
                                if (type === 'other') this.activeModalTitle = 'Pilih Material Lainnya';
                                
                                this.showModal = true;
                            },
                            
                            selectMaterial(materialId) {
                                if (this.activeModalType === 'upper') this.form.upperId = materialId;
                                if (this.activeModalType === 'sol') this.form.solId = materialId;
                                if (this.activeModalType === 'other') this.form.otherId = materialId;
                                
                                this.showModal = false;
                            },

                            getSelectedName(type) {
                                let id = '';
                                let list = [];
                                
                                if (type === 'upper') { id = this.form.upperId; list = this.upperMaterials; }
                                if (type === 'sol') { id = this.form.solId; list = this.solMaterials; }
                                if (type === 'other') { id = this.form.otherId; list = this.otherMaterials; }
                                
                                if (!id) return '';
                                let found = list.find(m => m.id == id);
                                return found ? found.name : 'Material tidak ditemukan';
                            },

                            addMaterial(id, qty, type) {
                                if (!id || qty < 1) return;
                                
                                // Find material info
                                let matInfo = null;
                                if (type === 'upper') {
                                    matInfo = this.upperMaterials.find(m => m.id == id);
                                } else if (type === 'sol') {
                                    matInfo = this.solMaterials.find(m => m.id == id);
                                } else if (type === 'other') {
                                    matInfo = this.otherMaterials.find(m => m.id == id);
                                }
                                
                                if (!matInfo) return;

                                // Check if exists
                                const existing = this.selectedMaterials.find(m => m.material_id == id);
                                if (existing) {
                                    existing.quantity += parseInt(qty);
                                } else {
                                    this.selectedMaterials.push({
                                        material_id: id,
                                        name: matInfo.name.split(' (')[0], // Remove (Sisa: ...) for display
                                        sub_category: matInfo.sub_category || type,
                                        quantity: parseInt(qty),
                                        stock_available: matInfo.stock
                                    });
                                }
                                
                                // Reset Form
                                if (type === 'upper') {
                                    this.form.upperId = '';
                                    this.form.upperQty = 1;
                                } else if (type === 'sol') {
                                    this.form.solId = '';
                                    this.form.solQty = 1;
                                } else if (type === 'other') {
                                    this.form.otherId = '';
                                    this.form.otherQty = 1;
                                }
                                this.hasChanges = true;
                            },
                            
                            removeMaterial(index) {
                                this.selectedMaterials.splice(index, 1);
                                this.hasChanges = true;
                            },
                            
                            increaseQty(index) {
                                this.selectedMaterials[index].quantity++;
                                this.hasChanges = true;
                            },
                            
                            decreaseQty(index) {
                                if (this.selectedMaterials[index].quantity > 1) {
                                    this.selectedMaterials[index].quantity--;
                                    this.hasChanges = true;
                                }
                            }
                        }
                    }
                </script>
                
                    <!-- Material Selection Section -->
                    <div class="bg-white overflow-hidden shadow-sm">
                        <!-- Section Header -->
                        <div class="px-6 py-4 bg-premium-green border-b-4 border-accent-yellow flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-white">Ambil Material dari Gudang</h4>
                                    <p class="text-xs text-white/80">Pilih material yang dibutuhkan untuk order ini</p>
                                </div>
                            </div>
                            <a href="{{ route('materials.selection.create', $order->id) }}" 
                               class="inline-flex items-center gap-2 px-6 py-2.5 btn-premium-yellow text-text-dark rounded-xl shadow-lg hover:opacity-90 transform hover:-translate-y-0.5 transition-all font-bold text-xs uppercase tracking-tight">
                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                               Request Barang (Advanced)
                            </a>
                        </div>

                        <div class="p-6">
                            <!-- Tabs Navigation -->
                            <div class="flex flex-wrap gap-2 mb-6 p-1 bg-gray-50 rounded-2xl border border-gray-100">
                                <button type="button" 
                                    @click="activeTab = 'upper'" 
                                    :class="activeTab === 'upper' ? 'bg-white text-premium-green shadow-sm border-gray-200' : 'text-gray-500 hover:bg-gray-100 border-transparent'"
                                    class="flex-1 min-w-[120px] py-3 px-4 rounded-xl text-sm font-bold transition-all border-2 flex items-center justify-center gap-2">
                                    Upper ({{ count($upperOptions) }})
                                </button>
                                <button type="button" 
                                    @click="activeTab = 'sol'" 
                                    :class="activeTab === 'sol' ? 'bg-white text-premium-green shadow-sm border-gray-200' : 'text-gray-500 hover:bg-gray-100 border-transparent'"
                                    class="flex-1 min-w-[120px] py-3 px-4 rounded-xl text-sm font-bold transition-all border-2 flex items-center justify-center gap-2">
                                    Sol ({{ count($solOptions) }})
                                </button>
                                <button type="button" 
                                    @click="activeTab = 'other'" 
                                    :class="activeTab === 'other' ? 'bg-white text-premium-green shadow-sm border-gray-200' : 'text-gray-500 hover:bg-gray-100 border-transparent'"
                                    class="flex-1 min-w-[120px] py-3 px-4 rounded-xl text-sm font-bold transition-all border-2 flex items-center justify-center gap-2">
                                    Lainnya ({{ count($otherOptions) }})
                                </button>
                            </div>

                            <!-- Tab Panels -->
                            <div class="tab-panels">
                                <!-- Form Upper (Alpine) -->
                                <div x-show="activeTab === 'upper'" class="space-y-4">
                                    <div class="bg-white rounded-xl shadow-sm border border-premium-green p-4">
                                        <h5 class="text-sm font-bold text-premium-green mb-3">Material Upper</h5>
                                        <div class="grid grid-cols-12 gap-3">
                                            <div class="col-span-12 sm:col-span-7">
                                                <div class="flex gap-2">
                                                    <input type="text" :value="getSelectedName('upper')" readonly class="block w-full text-sm border-2 border-gray-100 rounded-xl py-3 px-4 bg-gray-50 text-gray-700 cursor-not-allowed" placeholder="Belum ada material dipilih" />
                                                    <button type="button" @click="openModal('upper')" class="shrink-0 px-4 py-3 bg-green-50 text-premium-green rounded-xl font-bold hover:bg-green-100 transition-colors border border-green-100">
                                                        Pilih Material
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-span-6 sm:col-span-2">
                                                <input x-model.number="form.upperQty" type="number" min="1" class="block w-full text-sm border-2 border-gray-100 rounded-xl text-center font-bold py-3 focus:border-premium-green" />
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                <button type="button" @click="addMaterial(form.upperId, form.upperQty, 'upper')" :disabled="!form.upperId" class="w-full btn-premium-yellow py-3 rounded-xl shadow-sm disabled:opacity-50">
                                                    AMBIL
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Sol (Alpine) -->
                                <div x-show="activeTab === 'sol'" class="space-y-4">
                                    <div class="bg-white rounded-xl shadow-sm border border-premium-green p-4">
                                        <h5 class="text-sm font-bold text-premium-green mb-3">Material Sol</h5>
                                        <div class="grid grid-cols-12 gap-3">
                                            <div class="col-span-12 sm:col-span-7">
                                                <div class="flex gap-2">
                                                    <input type="text" :value="getSelectedName('sol')" readonly class="block w-full text-sm border-2 border-gray-100 rounded-xl py-3 px-4 bg-gray-50 text-gray-700 cursor-not-allowed" placeholder="Belum ada material dipilih" />
                                                    <button type="button" @click="openModal('sol')" class="shrink-0 px-4 py-3 bg-green-50 text-premium-green rounded-xl font-bold hover:bg-green-100 transition-colors border border-green-100">
                                                        Pilih Material
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-span-6 sm:col-span-2">
                                                <input x-model.number="form.solQty" type="number" min="1" class="block w-full text-sm border-2 border-gray-100 rounded-xl text-center font-bold py-3 focus:border-premium-green" />
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                <button type="button" @click="addMaterial(form.solId, form.solQty, 'sol')" :disabled="!form.solId" class="w-full btn-premium-yellow py-3 rounded-xl shadow-sm disabled:opacity-50">
                                                    AMBIL
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Other (Alpine) -->
                                <div x-show="activeTab === 'other'" class="space-y-4">
                                    <div class="bg-white rounded-xl shadow-sm border border-premium-green p-4">
                                        <h5 class="text-sm font-bold text-premium-green mb-3">Material Lainnya</h5>
                                        <div class="grid grid-cols-12 gap-3">
                                            <div class="col-span-12 sm:col-span-7">
                                                <div class="flex gap-2">
                                                    <input type="text" :value="getSelectedName('other')" readonly class="block w-full text-sm border-2 border-gray-100 rounded-xl py-3 px-4 bg-gray-50 text-gray-700 cursor-not-allowed" placeholder="Belum ada material dipilih" />
                                                    <button type="button" @click="openModal('other')" class="shrink-0 px-4 py-3 bg-green-50 text-premium-green rounded-xl font-bold hover:bg-green-100 transition-colors border border-green-100">
                                                        Pilih Material
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-span-6 sm:col-span-2">
                                                <input x-model.number="form.otherQty" type="number" min="1" class="block w-full text-sm border-2 border-gray-100 rounded-xl text-center font-bold py-3 focus:border-premium-green" />
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                <button type="button" @click="addMaterial(form.otherId, form.otherQty, 'other')" :disabled="!form.otherId" class="w-full btn-premium-yellow py-3 rounded-xl shadow-sm disabled:opacity-50">
                                                    AMBIL
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                             <!-- MODAL -->
                            <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title" x-text="activeModalTitle"></h3>
                                                    
                                                    <div class="mt-4">
                                                        <input type="text" x-model="searchModal" placeholder="Cari material..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500" autofocus>
                                                    </div>

                                                    <div class="mt-4 h-64 overflow-y-auto border border-gray-100 rounded-lg p-2 custom-scrollbar">
                                                        <template x-for="m in getModalItems()" :key="m.id">
                                                            <div class="flex justify-between items-center p-3 hover:bg-gray-50 rounded-lg border-b border-gray-50 last:border-0 transition-colors">
                                                                <div>
                                                                    <div class="font-bold text-gray-800" x-text="m.name.split(' (')[0]"></div>
                                                                    <div class="text-xs text-gray-500">
                                                                        Sisa: <span :class="m.stock > 0 ? 'text-green-600 font-bold' : 'text-red-600 font-bold'" x-text="m.stock"></span>
                                                                    </div>
                                                                </div>
                                                                <button 
                                                                    @click="selectMaterial(m.id)" 
                                                                    :disabled="m.disabled"
                                                                    class="px-4 py-2 text-xs font-bold rounded-xl btn-premium-yellow disabled:opacity-50">
                                                                    PILIH
                                                                </button>
                                                            </div>
                                                        </template>
                                                        <div x-show="getModalItems().length === 0" class="text-center py-8 text-gray-500 italic text-sm">
                                                            Tidak ada material ditemukan.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="showModal = false">
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Live Warehouse Stock -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden h-full">
                        <div class="px-6 py-4 bg-gray-900 text-white border-b-4 border-premium-green flex items-center gap-2">
                            <svg class="w-5 h-5 text-premium-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <h3 class="text-base font-bold text-white">Gudang Material</h3>
                        </div>
                        <div class="p-4 bg-gray-50 h-[400px] overflow-y-auto custom-scrollbar">
                            <div class="space-y-2">
                                <!-- Upper -->
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Upper</div>
                                @foreach($upperMaterials as $stock)
                                    <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-100 hover:border-teal-200 transition-colors shadow-sm group">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-700 group-hover:text-teal-700 transition-colors">{{ $stock->name }}</span>
                                        </div>
                                        <span class="text-xs font-mono font-bold px-2 py-1 rounded {{ $stock->stock > 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                            {{ $stock->stock }} {{ $stock->unit }}
                                        </span>
                                    </div>
                                @endforeach

                                <!-- Sol -->
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-4">Sol</div>
                                @foreach($solMaterials as $stock)
                                    <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-100 hover:border-teal-200 transition-colors shadow-sm group">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-700 group-hover:text-teal-700 transition-colors">{{ $stock->name }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $stock->size ? 'Size: '.$stock->size : $stock->sub_category }}</span>
                                        </div>
                                        <span class="text-xs font-mono font-bold px-2 py-1 rounded {{ $stock->stock > 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                            {{ $stock->stock }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="p-3 bg-white border-t border-gray-100 text-xs text-center text-gray-500 italic">
                            *Stok berkurang otomatis saat di-Ambil.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation Action -->
            <div class="dashboard-card p-6">
                @if($order->materials->where('pivot.status', 'REQUESTED')->count() > 0)
                    <div class="flex items-center gap-4 bg-red-50 border border-red-100 p-4 rounded-xl text-red-800">
                        <div class="p-3 bg-red-100 rounded-full shrink-0 animate-pulse">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">Validasi Tertunda</h4>
                            <p class="text-sm">Ada material dengan status <span class="font-bold bg-white px-1 rounded text-red-600 border border-red-200">REQUESTED</span> (Stok Kurang). Mohon lakukan pembelanjaan / restock terlebih dahulu.</p>
                        </div>
                    </div>
                @else
                    @php
                        $hasSol = $order->materials->contains(fn($m) => $m->type === 'Material Sol');
                        $hasUpper = $order->materials->contains(fn($m) => $m->type === 'Material Upper');
                        // If no materials or only general, maybe show both optional? 
                        // Or adhere strictly:
                        // "Untuk PIC muncul atau boleh diisi tergantung material yang dipakai"
                        // If no specific material, maybe don't show PIC? Or show both as optional?
                        // Let's assume if hasSol -> Show Sol PIC (Required?). 
                        // User said "boleh diisi", implies optional but visible.
                        // I will show them if relevant material exists.
                    @endphp

                    <form id="sortir-finish-form" action="{{ route('sortir.finish', $order->id) }}" method="POST" class="w-full">
                        @csrf
                        <div class="flex flex-col gap-6">
                            
                            @if($hasSol || $hasUpper)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                @if($hasSol)
                                <div class="w-full">
                                    <label class="block text-xs font-bold uppercase text-premium-green mb-2 tracking-wider">PIC Material Sol (Wajib)</label>
                                    <select name="pic_sortir_sol_id" class="block w-full text-sm border-gray-200 rounded-xl focus:border-premium-green focus:ring-0 py-3 shadow-sm bg-white" required>
                                        <option value="">-- Pilih PIC (Sol) --</option>
                                        @foreach($techSol as $tech)
                                            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                @if($hasUpper)
                                <div class="w-full">
                                    <label class="block text-xs font-bold uppercase text-premium-green mb-2 tracking-wider">PIC Material Upper (Wajib)</label>
                                    <select name="pic_sortir_upper_id" class="block w-full text-sm border-gray-200 rounded-xl focus:border-premium-green focus:ring-0 py-3 shadow-sm bg-white" required>
                                        <option value="">-- Pilih PIC (Upper) --</option>
                                        @foreach($techUpper as $tech)
                                            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                            @else
                                <div class="text-center p-8 bg-green-50 rounded-2xl border border-green-100 mb-2">
                                    <div class="inline-flex items-center justify-center p-3 bg-white rounded-full text-premium-green shadow-sm mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <p class="text-sm font-bold text-premium-green">Material Ready!</p>
                                    <p class="text-xs text-gray-500 mt-1 italic">Tidak ada material spesifik Sol/Upper. Anda bisa langsung melanjutkan ke Produksi.</p>
                                </div>
                            @endif
                            
                            <div class="flex justify-center md:justify-end">
                                <button type="button" onclick="confirmSortirFinish()" class="w-full md:w-auto md:min-w-[300px] inline-flex items-center justify-center px-10 py-5 btn-premium-yellow text-text-dark rounded-2xl shadow-xl transform hover:-translate-y-1 active:translate-y-0 transition-all text-sm uppercase tracking-widest">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    MATERIAL READY → PRODUCTION
                                </button>
                            </div>
                        </div>
                        <script>
                        function confirmSortirFinish() {
                            // Manual validation check since we are using type="button"
                            const form = document.getElementById('sortir-finish-form');
                            if (!form.checkValidity()) {
                                form.reportValidity();
                                return;
                            }

                            Swal.fire({
                                title: 'Selesai Sortir?',
                                text: "Pastikan material sudah sesuai. Lanjut ke Produksi?",
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#10B981',
                                cancelButtonColor: '#6B7280',
                                confirmButtonText: 'Ya, Lanjut!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    form.submit();
                                }
                            });
                        }
                        </script>
                    </form>
                @endif
            </div>

            <!-- Upsell Section -->
            <div class="flex justify-start border-t border-gray-200 pt-6 mt-8" x-data="{ openUpsell: false }">
                <button @click="openUpsell = true" class="text-sm text-premium-green hover:opacity-80 font-bold flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Tambah Layanan (Upsell)
                </button>

                <!-- Model Upsell -->
                <div x-show="openUpsell" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" style="display: none;">
                    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 relative" @click.away="openUpsell = false">
                         <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Tambah Layanan (Upsell)</h3>
                            <button @click="openUpsell = false" class="text-gray-400 hover:text-gray-600">✕</button>
                        </div>
                        
                        <p class="text-sm text-gray-500 mb-4 bg-blue-50 p-3 rounded-lg border border-blue-100">
                            Order akan dikembalikan ke status <strong>PREPARATION</strong> untuk pengerjaan ulang. <br>
                            Material lama tetap tersimpan. Input material baru nanti setelah order masuk lagi ke Sortir.
                        </p>

                        <form action="{{ route('sortir.add-service', $order->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-5" x-data="{ selectedServiceId: '', services: {{ \App\Models\Service::orderBy('name')->get()->map(fn($s) => ['id' => $s->id, 'price' => $s->price]) }} }">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Layanan Tambahan</label>
                                    <select x-model="selectedServiceId" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-premium-green focus:ring-0 py-2.5" required>
                                        <option value="">-- Cari Layanan --</option>
                                        @foreach(\App\Models\Service::orderBy('name')->get() as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->category }})</option>
                                        @endforeach
                                        <option value="custom">+ Input Manual (Custom)</option>
                                    </select>
                                    {{-- Hidden Input for Actual Service ID --}}
                                    <input type="hidden" name="service_id" :value="selectedServiceId === 'custom' ? '{{ $customServiceId }}' : selectedServiceId">

                                <!-- Manual Price Input for Custom Service (Price 0 or Custom Option) -->
                                <template x-if="selectedServiceId === 'custom' || (selectedServiceId && services.find(s => s.id == selectedServiceId)?.price === 0)">
                                    <div class="mt-3 space-y-3">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Layanan Custom (Opsional)</label>
                                            <input type="text" name="custom_name"
                                                class="block w-full px-3 py-2 border border-gray-100 rounded-xl focus:ring-0 focus:border-premium-green text-sm font-semibold text-gray-800"
                                                placeholder="Contoh: Repaint Patina">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Harga Custom (Fleksibel)</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                                </div>
                                                <input type="number" name="custom_price"
                                                    class="block w-full pl-10 pr-3 py-2 border border-gray-100 rounded-xl focus:ring-0 focus:border-premium-green text-sm font-bold text-gray-900 placeholder-gray-400"
                                                    placeholder="Masukkan harga..." required>
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-gray-500 italic">Layanan ini membutuhkan input harga manual.</p>
                                    </div>
                                </template>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Foto Kondisi (Opsional)</label>
                                <label class="block mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 transition-colors cursor-pointer bg-white">
                                    <div class="space-y-1 text-center w-full">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium text-blue-600 hover:text-blue-500">Upload Foto</span>
                                            <span class="pl-1">atau drag and drop</span>
                                            <input id="upsell-photo-sortir" name="upsell_photo" type="file" class="sr-only" accept="image/*" onchange="document.getElementById('file-chosen-sortir').textContent = this.files[0].name">
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                        <p id="file-chosen-sortir" class="text-xs font-bold text-teal-600 pt-2"></p>
                                    </div>
                                </label>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" @click="openUpsell = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-bold">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-premium-green text-white rounded-xl hover:opacity-90 font-bold shadow-md transition-all">Simpan & Proses</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
         </div>
    </div>
</x-app-layout>
