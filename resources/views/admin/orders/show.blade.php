<x-app-layout>
    <div class="min-h-screen bg-[#F3F4F6] pb-20 font-sans">
        
        {{-- Hero Header (Light Theme) --}}
        <div class="relative bg-white border-b border-gray-200 pb-24 overflow-hidden">
            {{-- Background Pattern --}}
            <div class="absolute inset-0">
                <div class="absolute inset-0 bg-gray-50/50"></div>
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-[#22B086] blur-3xl opacity-5 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-[#FFC232] blur-3xl opacity-5"></div>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-6">
                {{-- Breadcrumb / Back --}}
                <div class="flex items-center gap-4 mb-8 relative z-50">
                    @if($order->customer)
                        <a href="{{ route('admin.customers.show', $order->customer->id) }}" class="group flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium shadow-sm transition-all">
                            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali ke Customer
                        </a>
                    @else
                        <a href="{{ route('admin.customers.index') }}" class="group flex items-center gap-2 px-4 py-2 rounded-full bg-[#FFC232] hover:bg-[#FFB000] text-gray-900 text-sm font-bold shadow-lg shadow-orange-200 transition-all">
                            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali ke List Customer
                        </a>
                    @endif
                    <span class="text-gray-500">/</span>
                    <span class="text-gray-400 text-sm">Detail Work Order</span>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-[#22B086]/10 text-[#22B086] border border-[#22B086]/20">
                                Work Order
                            </span>
                            <span class="text-gray-500 text-xs font-medium flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $order->created_at->format('d F Y, H:i') }}
                            </span>
                        </div>
                        <h1 class="text-5xl font-black text-gray-900 tracking-tight leading-tight">
                            {{ $order->spk_number }}
                        </h1>
                        <div class="mt-4 flex items-center gap-4">
                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-100 border border-gray-200">
                                <span class="w-2 h-2 rounded-full bg-[#FFC232] animate-pulse"></span>
                                <span class="text-gray-700 text-sm font-bold">{{ str_replace('_', ' ', $order->status->value) }}</span>
                            </div>
                            <div class="h-4 w-px bg-gray-300"></div>
                            <div class="text-gray-500 text-sm flex items-center gap-2" 
                                 x-data="{ 
                                    editing: false, 
                                    date: '{{ $order->estimation_date ? $order->estimation_date->format('Y-m-d') : '' }}',
                                    displayDate: '{{ $order->estimation_date ? $order->estimation_date->format('d M Y') : 'Set Estimasi' }}',
                                    isLoading: false,
                                    async save() {
                                        this.isLoading = true;
                                        try {
                                            const res = await fetch('{{ route('admin.orders.update-estimation-date', $order->id) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ estimation_date: this.date })
                                            });
                                            const data = await res.json();
                                            if (data.success) {
                                                this.displayDate = data.estimation_date;
                                                this.editing = false;
                                                // Optional: Show success toast or reload to update status
                                                window.location.reload(); 
                                            }
                                        } catch (e) {
                                            alert('Gagal memperbarui tanggal');
                                        } finally {
                                            this.isLoading = false;
                                        }
                                    }
                                 }">
                                Estimasi: 
                                <template x-if="!editing">
                                    <button @click="editing = true" class="text-gray-900 font-bold hover:text-[#22B086] hover:underline decoration-dashed underline-offset-4 transition-colors">
                                        <span x-text="displayDate"></span>
                                    </button>
                                </template>
                                <template x-if="editing">
                                    <div class="flex items-center gap-2">
                                        <input type="date" x-model="date" 
                                               class="text-xs font-bold rounded-lg border-gray-200 focus:border-[#22B086] focus:ring-[#22B086] py-1 px-2">
                                        <button @click="save()" :disabled="isLoading" class="p-1 bg-[#22B086] text-white rounded hover:bg-[#1C8D6C] disabled:opacity-50">
                                            <svg x-show="!isLoading" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            <svg x-show="isLoading" class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        </button>
                                        <button @click="editing = false" class="p-1 bg-gray-200 text-gray-600 rounded hover:bg-gray-300">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="flex gap-3">
                        <a href="{{ route('admin.orders.shipping-label', $order->id) }}" target="_blank" class="flex items-center gap-2 px-6 py-3 bg-[#FFC232] text-gray-900 rounded-xl font-bold text-sm shadow-xl shadow-orange-200 hover:bg-[#FFB000] transition-all hover:-translate-y-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6"></path></svg>
                            Print Label
                        </a>
                        <a href="{{ route('assessment.print-spk', $order->id) }}" target="_blank" class="flex items-center gap-2 px-6 py-3 bg-[#22B086] text-white rounded-xl font-bold text-sm shadow-xl shadow-emerald-200 hover:bg-[#1C8D6C] transition-all hover:-translate-y-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print SPK
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Grid --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-10">
            
            {{-- Status Steps (Visual) --}}
            @php
                $flowSteps = [
                    \App\Enums\WorkOrderStatus::DITERIMA,
                    \App\Enums\WorkOrderStatus::ASSESSMENT,
                    \App\Enums\WorkOrderStatus::PREPARATION,
                    \App\Enums\WorkOrderStatus::PRODUCTION,
                    \App\Enums\WorkOrderStatus::QC,
                    \App\Enums\WorkOrderStatus::SELESAI,
                ];
                $statusOrderMap = array_flip(array_map(fn($s) => $s->value, $flowSteps));
                $currentIndex = $statusOrderMap[$order->status->value] ?? -1;
            @endphp
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8 overflow-x-auto">
                <div class="flex items-center justify-between min-w-[600px]">
                    @foreach($flowSteps as $index => $step)
                        @php
                            $stepIndex = $statusOrderMap[$step->value] ?? 99;
                            $isCompleted = $stepIndex < $currentIndex;
                            $isActive = $order->status == $step;
                        @endphp
                        <div class="flex flex-col items-center relative flex-1 group">
                            {{-- Connecting Line --}}
                            @if(!$loop->last)
                                <div class="absolute top-4 left-1/2 w-full h-1 transition-colors duration-500 -z-10
                                    {{ $isCompleted ? 'bg-[#22B086]' : 'bg-gray-100' }}"></div>
                            @endif
                            
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 mb-2 z-10 transition-all duration-500
                                {{ $isCompleted ? 'bg-[#22B086] border-[#22B086] text-white shadow-md shadow-emerald-500/20' : '' }}
                                {{ $isActive ? 'bg-[#22B086] border-[#22B086] text-white shadow-lg shadow-emerald-500/30 animate-pulse' : '' }}
                                {{ !$isCompleted && !$isActive ? 'bg-white border-gray-200 text-gray-400' : '' }}">
                                @if($isCompleted)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ ($isActive || $isCompleted) ? 'text-[#22B086]' : 'text-gray-400' }}">
                                {{ str_replace('_', ' ', $step->value) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- LEFT COLUMN: Customer & Address --}}
                <div class="space-y-8">
                    {{-- Customer Card --}}
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden group hover:shadow-xl transition-all duration-300"
                         x-data="customerEditor({
                            name: '{{ str_replace(["'"], ["\\'"], $order->customer_name) }}',
                            phone: '{{ $order->customer_phone }}',
                            email: '{{ $order->customer_email }}'
                         })" x-cloak>
                        <div class="bg-gray-50/50 p-6 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-black text-gray-800 text-base uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#22B086]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Customer
                            </h3>
                            <button @click="showCustomerModal = true" class="p-1.5 bg-white border border-gray-100 rounded-lg text-gray-400 hover:text-[#22B086] hover:border-[#22B086] transition-all" title="Edit Identitas Customer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                        </div>
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-[#22B086] to-[#1C8D6C] rounded-full flex items-center justify-center text-3xl font-black text-white shadow-lg mb-4">
                                    {{ substr($order->customer_name, 0, 1) }}
                                </div>
                                <h4 class="text-xl font-bold text-gray-900">{{ $order->customer_name }}</h4>
                                <p class="text-sm text-gray-500 font-medium">{{ $order->customer_phone }}</p>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                    <span class="text-xs font-bold text-gray-400 uppercase">Email</span>
                                    <span class="text-sm font-bold text-gray-700">{{ $order->customer_email ?? '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                    <span class="text-xs font-bold text-gray-400 uppercase">Member Sejak</span>
                                    <span class="text-sm font-bold text-gray-700">{{ $order->customer ? $order->customer->created_at->format('M Y') : '-' }}</span>
                                </div>
                            </div>

                            {{-- Customer Editor Modal --}}
                            <template x-teleport="body">
                                <div x-show="showCustomerModal" class="fixed inset-0 z-[999] overflow-y-auto" style="display: none;">
                                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showCustomerModal" 
                                             x-transition:enter="transition ease-out duration-300" 
                                             x-transition:enter-start="opacity-0" 
                                             x-transition:enter-end="opacity-100" 
                                             class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showCustomerModal = false">
                                            <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-md"></div>
                                        </div>

                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                        <div x-show="showCustomerModal" 
                                             x-transition:enter="transition ease-out duration-300" 
                                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-[0_35px_100px_-15px_rgba(0,0,0,0.5)] transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20 relative z-[1000]">
                                            
                                            <div class="bg-gradient-to-r from-[#22B086] to-[#1C8D6C] px-8 py-7 relative pb-8">
                                                {{-- Abstract background pattern --}}
                                                <div class="absolute inset-0 opacity-10 pointer-events-none overflow-hidden">
                                                    <svg class="absolute -right-4 -top-4 w-32 h-32" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="50" fill="white"/></svg>
                                                </div>

                                                <div class="flex justify-between items-center relative z-10">
                                                    <div>
                                                        <h3 class="text-2xl font-black text-white leading-tight">Edit Identitas Customer</h3>
                                                        <p class="text-white/90 text-[10px] font-bold mt-1 uppercase tracking-widest">Update Data Pemilik SPK Ini</p>
                                                    </div>
                                                    <button @click="showCustomerModal = false" class="p-2 bg-white/10 hover:bg-white/20 rounded-xl transition-all duration-300">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="p-8 space-y-6">
                                                <div>
                                                    <label for="customer_name" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                                                    <input type="text" id="customer_name" name="customer_name" x-model="name" class="w-full rounded-xl border-gray-200 focus:border-[#22B086] focus:ring-[#22B086] font-bold text-sm">
                                                </div>

                                                <div>
                                                    <label for="customer_phone" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5 flex items-center justify-between">
                                                        <span>Nomor WhatsApp / HP</span>
                                                        <span x-show="phone.length > 0 && phone.length < 3" class="text-[9px] text-amber-500 lowercase font-bold animate-pulse">Ketik min. 3 angka untuk mencari...</span>
                                                    </label>
                                                    <div class="relative group">
                                                        <!-- Search Icon -->
                                                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#22B086] transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                                        </div>

                                                        <input type="text" id="customer_phone" name="customer_phone" 
                                                               x-model="phone" 
                                                               @input.debounce.300ms="fetchSuggestions"
                                                               class="w-full pl-11 pr-11 py-3.5 rounded-2xl border-gray-100 bg-gray-50/50 focus:bg-white focus:border-[#22B086] focus:ring-4 focus:ring-emerald-500/10 font-bold text-sm transition-all placeholder:text-gray-300" 
                                                               placeholder="Cari Nama atau Nomor HP...">
                                                        
                                                        <!-- Loading Indicator -->
                                                        <div x-show="isSearching" class="absolute right-4 top-1/2 -translate-y-1/2" x-cloak>
                                                            <svg class="animate-spin h-5 w-5 text-[#22B086]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                        </div>

                                                        <!-- Suggestions Dropdown (Glassmorphism) -->
                                                        <div x-show="suggestions.length > 0" 
                                                             x-transition:enter="transition ease-out duration-200"
                                                             x-transition:enter-start="opacity-0 translate-y-2"
                                                             x-transition:enter-end="opacity-100 translate-y-0"
                                                             class="absolute z-[110] left-0 right-0 mt-3 bg-white/90 backdrop-blur-xl border border-white shadow-[0_20px_50px_rgba(0,0,0,0.15)] rounded-3xl overflow-hidden divide-y divide-gray-100/50"
                                                             @click.away="suggestions = []" x-cloak>
                                                            
                                                            <div class="px-4 py-2 bg-gray-50/50 border-b border-gray-100/50">
                                                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Hasil Pencarian Customer</span>
                                                            </div>

                                                            <template x-for="item in suggestions" :key="item.phone">
                                                                <button type="button" @click="selectSuggestion(item)" class="w-full text-left px-5 py-4 hover:bg-emerald-500/5 transition-all flex items-center justify-between group/item">
                                                                    <div class="flex flex-col">
                                                                        <span class="font-black text-sm text-gray-900 group-hover/item:text-[#22B086] transition-colors" x-text="item.name"></span>
                                                                        <div class="flex items-center gap-2 mt-0.5">
                                                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md" x-text="item.phone"></span>
                                                                            <span class="text-[10px] text-gray-400 font-medium" x-show="item.email" x-text="item.email"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="opacity-0 group-hover/item:opacity-100 transition-opacity">
                                                                        <svg class="w-5 h-5 text-[#22B086]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                                                    </div>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label for="customer_email" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Alamat Email</label>
                                                    <input type="email" id="customer_email" name="customer_email" x-model="email" class="w-full rounded-xl border-gray-200 focus:border-[#22B086] focus:ring-[#22B086] font-bold text-sm" placeholder="Opsional">
                                                </div>
                                            </div>

                                            <div class="bg-gray-50 px-8 py-6 flex gap-3">
                                                <button @click="submit()" :disabled="isLoading" class="flex-1 py-4 bg-[#22B086] hover:bg-[#1C8D6C] text-white font-black rounded-2xl shadow-xl shadow-emerald-100 transition-all flex items-center justify-center gap-2">
                                                    <template x-if="!isLoading">
                                                        <span class="flex items-center gap-2">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                            Simpan Perubahan
                                                        </span>
                                                    </template>
                                                    <template x-if="isLoading">
                                                        <span class="flex items-center gap-2">
                                                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                            Memproses...
                                                        </span>
                                                    </template>
                                                </button>
                                                <button @click="showCustomerModal = false" class="px-8 py-4 bg-white border border-gray-200 text-gray-500 font-black rounded-2xl transition-all">
                                                    Batal
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Address Card --}}
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden group hover:shadow-xl transition-all duration-300"
                         x-data="addressEditor({
                            address: '{{ str_replace(["\r", "\n", "'"], [' ', ' ', "\\'"], $order->customer?->address ?? $order->customer_address) }}',
                            city: '{{ $order->customer?->city }}',
                            cityId: '{{ $order->customer?->city_id }}',
                            district: '{{ $order->customer?->district }}',
                            districtId: '{{ $order->customer?->district_id }}',
                            village: '{{ $order->customer?->village }}',
                            villageId: '{{ $order->customer?->village_id }}',
                            province: '{{ $order->customer?->province }}',
                            provinceId: '{{ $order->customer?->province_id }}',
                            postalCode: '{{ $order->customer?->postal_code }}'
                         })" x-cloak x-init="init()">
                        <div class="bg-gray-50/50 p-6 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-black text-gray-800 text-base uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Alamat Pengiriman
                            </h3>
                            <button @click="showModal = true" class="p-1.5 bg-white border border-gray-100 rounded-lg text-gray-400 hover:text-[#FFC232] hover:border-[#FFC232] transition-all" title="Edit Alamat">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                        </div>
                        <div class="p-6 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-[#FFC232]/5 rounded-full blur-3xl -z-0"></div>
                            
                            <p class="text-gray-800 font-bold leading-relaxed relative z-10 mb-4">
                                "{{ $order->customer?->address ?? $order->customer_address ?? 'Alamat tidak tersedia' }}"
                            </p>
                            
                            <div class="grid grid-cols-2 gap-3 relative z-10">
                                <div class="p-3 bg-orange-50/50 border border-orange-100 rounded-xl">
                                    <span class="block text-[10px] font-black text-[#FFC232] uppercase">Kelurahan</span>
                                    <span class="font-bold text-gray-700 text-sm whitespace-nowrap overflow-hidden text-overflow-ellipsis">{{ $order->customer?->village ?? '-' }}</span>
                                </div>
                                <div class="p-3 bg-orange-50/50 border border-orange-100 rounded-xl">
                                    <span class="block text-[10px] font-black text-[#FFC232] uppercase">Kecamatan</span>
                                    <span class="font-bold text-gray-700 text-sm whitespace-nowrap overflow-hidden text-overflow-ellipsis">{{ $order->customer?->district ?? '-' }}</span>
                                </div>
                                <div class="p-3 bg-orange-50/50 border border-orange-100 rounded-xl">
                                    <span class="block text-[10px] font-black text-[#FFC232] uppercase">Kota</span>
                                    <span class="font-bold text-gray-700 text-sm whitespace-nowrap overflow-hidden text-overflow-ellipsis">{{ $order->customer?->city ?? '-' }}</span>
                                </div>
                                <div class="p-3 bg-orange-50/50 border border-orange-100 rounded-xl">
                                    <span class="block text-[10px] font-black text-[#FFC232] uppercase">Provinsi</span>
                                    <span class="font-bold text-gray-700 text-sm whitespace-nowrap overflow-hidden text-overflow-ellipsis">{{ $order->customer?->province ?? '-' }}</span>
                                </div>
                                <div class="p-3 bg-orange-50/50 border border-orange-100 rounded-xl col-span-2">
                                    <span class="block text-[10px] font-black text-[#FFC232] uppercase">Kode Pos</span>
                                    <span class="font-bold text-gray-700 text-sm">{{ $order->customer?->postal_code ?? '-' }}</span>
                                </div>
                            </div>

                            {{-- Address Editor Modal --}}
                            <template x-teleport="body">
                                <div x-show="showModal" class="fixed inset-0 z-[999] overflow-y-auto" style="display: none;">
                                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showModal = false">
                                            <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-md"></div>
                                        </div>

                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                        <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20 relative z-[1000]">
                                            
                                            <div class="bg-gradient-to-r from-[#FFC232] to-[#FFB000] px-8 py-6">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <h3 class="text-xl font-black text-gray-900 leading-tight">Edit Alamat Pengiriman</h3>
                                                        <p class="text-gray-800 text-xs font-bold mt-1 opacity-80 uppercase tracking-widest">Update Master Data Customer</p>
                                                    </div>
                                                    <button @click="showModal = false" class="text-gray-900 hover:rotate-90 transition-transform duration-300">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="p-8 space-y-6">
                                                <div>
                                                    <label for="shipping_address" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Alamat Lengkap</label>
                                                    <textarea id="shipping_address" name="shipping_address" x-model="address" rows="3" class="w-full rounded-xl border-gray-200 focus:border-[#FFC232] focus:ring-[#FFC232] font-bold text-sm" placeholder="Nama jalan, nomor rumah, RT/RW..."></textarea>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label for="shipping_province" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Provinsi</label>
                                                        <div class="relative">
                                                            <select id="shipping_province" name="shipping_province" x-model="provinceId" @change="onProvinceChange()" 
                                                                    class="w-full rounded-xl border-gray-200 focus:border-[#FFC232] focus:ring-[#FFC232] font-bold text-sm pr-10">
                                                                <option value="">-- Pilih Provinsi --</option>
                                                                <template x-for="p in provinces" :key="p.id">
                                                                    <option :value="p.id" x-text="p.name" :selected="p.id == provinceId"></option>
                                                                </template>
                                                            </select>
                                                            <template x-if="isLoadingProvinces">
                                                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                                                    <svg class="animate-spin h-4 w-4 text-[#FFC232]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="shipping_city" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Kota / Kabupaten</label>
                                                        <div class="relative">
                                                            <select id="shipping_city" name="shipping_city" x-model="cityId" @change="onCityChange()" :disabled="!provinceId"
                                                                    class="w-full rounded-xl border-gray-200 focus:border-[#FFC232] focus:ring-[#FFC232] font-bold text-sm disabled:opacity-50 pr-10">
                                                                <option value="">-- Pilih Kota --</option>
                                                                <template x-for="c in regencies" :key="c.id">
                                                                    <option :value="c.id" x-text="c.name" :selected="c.id == cityId"></option>
                                                                </template>
                                                            </select>
                                                            <template x-if="isLoadingRegencies">
                                                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                                                    <svg class="animate-spin h-4 w-4 text-[#FFC232]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label for="shipping_district" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Kecamatan</label>
                                                        <div class="relative">
                                                            <select id="shipping_district" name="shipping_district" x-model="districtId" @change="onDistrictChange()" :disabled="!cityId"
                                                                    class="w-full rounded-xl border-gray-200 focus:border-[#FFC232] focus:ring-[#FFC232] font-bold text-sm disabled:opacity-50 pr-10">
                                                                <option value="">-- Pilih Kecamatan --</option>
                                                                <template x-for="d in districts" :key="d.id">
                                                                    <option :value="d.id" x-text="d.name" :selected="d.id == districtId"></option>
                                                                </template>
                                                            </select>
                                                            <template x-if="isLoadingDistricts">
                                                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                                                    <svg class="animate-spin h-4 w-4 text-[#FFC232]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label for="shipping_village" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Kelurahan / Desa</label>
                                                        <div class="relative">
                                                            <select id="shipping_village" name="shipping_village" x-model="villageId" :disabled="!districtId"
                                                                    class="w-full rounded-xl border-gray-200 focus:border-[#FFC232] focus:ring-[#FFC232] font-bold text-sm disabled:opacity-50 pr-10">
                                                                <option value="">-- Pilih Kelurahan --</option>
                                                                <template x-for="v in villages" :key="v.id">
                                                                    <option :value="v.id" x-text="v.name" :selected="v.id == villageId"></option>
                                                                </template>
                                                            </select>
                                                            <template x-if="isLoadingVillages">
                                                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                                                    <svg class="animate-spin h-4 w-4 text-[#FFC232]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label for="shipping_postal_code" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Kode Pos</label>
                                                    <input id="shipping_postal_code" name="shipping_postal_code" type="text" x-model="postalCode" class="w-full rounded-xl border-gray-200 focus:border-[#FFC232] focus:ring-[#FFC232] font-bold text-sm" placeholder="Contoh: 12345">
                                                </div>
                                            </div>

                                            <div class="bg-gray-50 px-8 py-6 flex gap-3">
                                                <button @click="submit()" :disabled="isLoading" class="flex-1 py-4 bg-[#FFC232] hover:bg-[#FFB000] text-gray-900 font-black rounded-2xl shadow-xl shadow-orange-200 transition-all flex items-center justify-center gap-2">
                                                    <template x-if="!isLoading">
                                                        <span class="flex items-center gap-2">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                            Simpan Alamat
                                                        </span>
                                                    </template>
                                                    <template x-if="isLoading">
                                                        <span class="flex items-center gap-2">
                                                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                            Memproses...
                                                        </span>
                                                    </template>
                                                </button>
                                                <button @click="showModal = false" class="px-8 py-4 bg-white border border-gray-200 text-gray-500 font-black rounded-2xl transition-all">
                                                    Batal
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Items & Services (Span 2) --}}
                <div class="lg:col-span-2 space-y-8">
                    
                    {{-- Item Details --}}
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-100 p-8 relative overflow-hidden"
                         x-data="shoeInfoEditor({
                            brand: '{{ $order->shoe_brand }}',
                            size: '{{ $order->shoe_size }}',
                            color: '{{ $order->shoe_color }}',
                            category: '{{ $order->category ?? 'General' }}',
                            tali: '{{ $order->accessories_tali }}',
                            insole: '{{ $order->accessories_insole }}',
                            box: '{{ $order->accessories_box }}',
                            other: '{{ $order->accessories_other }}',
                            hk_days: {{ $order->hk_days ?? 0 }},
                            is_warranty: {{ $order->is_warranty ? 'true' : 'false' }}
                         })" x-cloak>
                        <div class="absolute right-0 top-0 w-64 h-64 bg-[#22B086]/5 rounded-full blur-3xl pointer-events-none"></div>
                        
                        <div class="flex justify-between items-center mb-8 relative z-10">
                            <div class="flex items-center gap-4">
                                 <span class="w-12 h-12 rounded-2xl bg-[#22B086] text-white flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </span>
                                <div>
                                    <h3 class="text-2xl font-black text-gray-900">Detail Sepatu</h3>
                                    <p class="text-[#22B086] font-medium text-sm">Informasi lengkap spesifikasi barang</p>
                                </div>
                            </div>
                            <button @click="showModal = true" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50 hover:text-[#22B086] hover:border-[#22B086] transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Edit Detail
                            </button>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 relative z-10 mb-8">
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Brand</p>
                                <p class="text-xl font-black text-gray-800">{{ $order->shoe_brand }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Size</p>
                                <p class="text-xl font-black text-gray-800">{{ $order->shoe_size }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Color</p>
                                <p class="text-xl font-black text-gray-800">{{ $order->shoe_color }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Category</p>
                                <p class="text-xl font-black text-gray-800">{{ $order->category ?? 'General' }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Target HK</p>
                                <div class="flex items-center gap-2">
                                    <p class="text-xl font-black text-gray-800">{{ $order->hk_days ?? '-' }}</p>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">Hari</span>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Garansi</p>
                                @if($order->is_warranty)
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-black rounded-lg uppercase tracking-wider border border-emerald-200">AKTIF</span>
                                @else
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-400 text-[10px] font-black rounded-lg uppercase tracking-wider border border-gray-200">NORMAL</span>
                                @endif
                            </div>
                        </div>

                        {{-- Accessories Checklist (Assessment Style) --}}
                        <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-100">
                            <div class="flex items-center gap-2 mb-6">
                                <span class="w-1.5 h-6 bg-[#22B086] rounded-full"></span>
                                <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest">Aksesoris Penyerta</h4>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach(['Tali' => $order->accessories_tali, 'Insole' => $order->accessories_insole, 'Box' => $order->accessories_box] as $label => $val)
                                    @php
                                        $isNempel = in_array(strtoupper($val), ['N', 'NEMPEL']);
                                        $isSimpan = in_array(strtoupper($val), ['S', 'SIMPAN']);
                                        $isEmpty = !$val || in_array(strtoupper($val), ['T', 'TIDAK ADA', 'NONE', '-']);
                                    @endphp
                                    <div class="flex items-center justify-between px-4 py-3 bg-white border border-gray-100 rounded-xl shadow-sm">
                                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-tight">{{ $label }}</span>
                                        <div class="flex gap-1.5">
                                            <span class="w-7 h-7 flex items-center justify-center rounded-lg text-[9px] font-black transition-all {{ $isEmpty ? 'bg-red-500 text-white shadow-lg shadow-red-100' : 'bg-gray-100 text-gray-300' }}">T</span>
                                            <span class="w-7 h-7 flex items-center justify-center rounded-lg text-[9px] font-black transition-all {{ $isNempel ? 'bg-[#22B086] text-white shadow-lg shadow-emerald-100' : 'bg-gray-100 text-gray-300' }}">N</span>
                                            <span class="w-7 h-7 flex items-center justify-center rounded-lg text-[9px] font-black transition-all {{ $isSimpan ? 'bg-[#FFC232] text-white shadow-lg shadow-yellow-100' : 'bg-gray-100 text-gray-300' }}">S</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($order->accessories_other && $order->accessories_other != 'Tidak Ada')
                                <div class="mt-4 p-4 bg-white border border-gray-100 rounded-xl">
                                    <p class="text-[9px] font-black text-[#22B086] uppercase tracking-widest mb-1">Aksesoris Lainnya:</p>
                                    <p class="text-sm font-bold text-gray-700 leading-relaxed">{{ $order->accessories_other }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Modal Edit Shoe Info -->
                        <div x-show="showModal" 
                             class="fixed inset-0 z-[100] overflow-y-auto" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0">
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div @click="showModal = false" class="fixed inset-0 transition-opacity bg-black/60 backdrop-blur-sm" aria-hidden="true"></div>

                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
                                     @click.away="showModal = false"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                    
                                    <div class="p-8">
                                        <div class="flex justify-between items-center mb-6">
                                            <h3 class="text-2xl font-black text-gray-900">Edit Detail Sepatu</h3>
                                            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>

                                        <div class="space-y-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label for="shoe_brand" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Brand Sepatu</label>
                                                    <input id="shoe_brand" name="shoe_brand" type="text" x-model="brand" class="w-full rounded-xl border-gray-200 focus:border-[#22B086] focus:ring-[#22B086] font-bold text-sm">
                                                </div>
                                                <div>
                                                    <label for="shoe_category" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Kategori Barang (Affects SPK Prefix)</label>
                                                    <select id="shoe_category" name="shoe_category" x-model="category" class="w-full rounded-xl border-gray-200 focus:border-[#22B086] focus:ring-[#22B086] font-bold text-sm">
                                                        <option value="Sepatu">Sepatu</option>
                                                        <option value="Tas">Tas</option>
                                                        <option value="Topi">Topi</option>
                                                        <option value="Apparel">Apparel</option>
                                                        <option value="Lainnya">Lainnya</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label for="shoe_color" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Varian Warna</label>
                                                    <input id="shoe_color" name="shoe_color" type="text" x-model="color" class="w-full rounded-xl border-gray-200 focus:border-[#22B086] focus:ring-[#22B086] font-bold text-sm">
                                                </div>
                                                <div>
                                                    <label for="shoe_size" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Ukuran (Size)</label>
                                                    <input id="shoe_size" name="shoe_size" type="text" x-model="size" class="w-full rounded-xl border-gray-200 focus:border-[#22B086] focus:ring-[#22B086] font-bold text-sm">
                                                </div>
                                            </div>

                                            <div class="pt-4 border-t border-gray-100">
                                                <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4">Estimasi & Garansi</h4>
                                                <div class="grid grid-cols-2 gap-4">
                                                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Hari Kerja (HK)</label>
                                                        <div class="flex items-center gap-3">
                                                            <input type="number" x-model="hk_days" class="w-20 rounded-xl border-gray-200 focus:border-[#22B086] focus:ring-[#22B086] font-black text-center text-lg">
                                                            <span class="text-xs font-bold text-gray-500">Hari</span>
                                                        </div>
                                                    </div>
                                                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex flex-col justify-center">
                                                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 text-center">Status Garansi</label>
                                                        <div class="flex justify-center">
                                                            <button @click="is_warranty = !is_warranty" 
                                                                    :class="is_warranty ? 'bg-emerald-500 border-emerald-600 shadow-emerald-200 shadow-lg' : 'bg-gray-200 border-gray-300'"
                                                                    class="relative inline-flex h-8 w-16 items-center rounded-full transition-all duration-300 border-2">
                                                                <span :class="is_warranty ? 'translate-x-9 bg-white' : 'translate-x-1 bg-white'"
                                                                      class="inline-block h-5 w-5 transform rounded-full transition-transform duration-300 shadow-sm"></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="pt-4 border-t border-gray-100">
                                                <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4">Aksesoris Penyerta (T/N/S)</h4>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <template x-for="item in ['tali', 'insole', 'box']" :key="item">
                                                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                                            <span class="block text-[10px] font-black text-gray-400 uppercase mb-3" x-text="item"></span>
                                                            <div class="flex gap-2">
                                                                <template x-for="opt in ['T', 'N', 'S']" :key="opt">
                                                                    <button @click="$data[item] = opt" 
                                                                            :class="$data[item] === opt ? 'bg-[#22B086] text-white shadow-lg shadow-emerald-100' : 'bg-white text-gray-400 border-gray-200 hover:border-[#22B086]'"
                                                                            class="flex-1 py-2 rounded-xl text-xs font-black transition-all border"
                                                                            x-text="opt">
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                            <div>
                                                <label for="shoe_other" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Aksesoris Lainnya</label>
                                                <textarea id="shoe_other" name="shoe_other" x-model="other" rows="3" class="w-full rounded-xl border-gray-200 focus:border-[#22B086] focus:ring-[#22B086] font-medium text-sm" placeholder="Contoh: Gantungan kunci, Lace lock, dll..."></textarea>
                                            </div>
                                        </div>

                                        <div class="mt-8 flex gap-3">
                                            <button @click="submit()" :disabled="isLoading" class="flex-1 py-4 bg-[#22B086] hover:bg-[#1C8D6C] text-white font-black rounded-2xl shadow-xl shadow-emerald-200 transition-all flex items-center justify-center gap-2">
                                                <template x-if="!isLoading">
                                                    <span class="flex items-center gap-2">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                        Simpan Perubahan
                                                    </span>
                                                </template>
                                                <template x-if="isLoading">
                                                    <span class="flex items-center gap-2">
                                                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                        Sedang Memproses...
                                                    </span>
                                                </template>
                                            </button>
                                            <button @click="showModal = false" class="px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-600 font-black rounded-2xl transition-all">
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Database Rack Information (Assessment Style) --}}
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden group hover:shadow-xl transition-all duration-300">
                        <div class="bg-gray-50/50 p-8 border-b border-gray-100">
                            <h3 class="text-2xl font-black text-gray-900 leading-none">Informasi Rak Penyimpanan</h3>
                            <p class="text-[#22B086] font-bold text-[10px] uppercase tracking-[0.2em] mt-2">Data Alokasi Slot Gudang Terpusat</p>
                        </div>

                        <div class="p-8">
                            @php
                                $activeAssignments = $order->storageAssignments->where('status', 'stored');
                                $inboundRack = $activeAssignments->filter(fn($a) => in_array(strtolower($a->category), ['before', 'inbound']))->first();
                                $shoeRack = $activeAssignments->filter(fn($a) => in_array(strtolower($a->category), ['shoes', 'finish', 'sepatu']))->first();
                                $accRack = $activeAssignments->filter(fn($a) => in_array(strtolower($a->category), ['accessories', 'accessory', 'aksesoris']))->first();
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {{-- Inbound Rack --}}
                                <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100 flex flex-col items-center justify-center text-center">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Rak Inbound</span>
                                    @if($inboundRack)
                                        <div class="w-20 h-20 bg-white rounded-2xl flex flex-col items-center justify-center shadow-lg border-b-4 border-orange-400 rotate-1 mb-2">
                                            <span class="text-3xl font-black text-gray-900 leading-none">{{ $inboundRack->rack_code }}</span>
                                        </div>
                                        <p class="text-[9px] font-black text-orange-500 uppercase">Sector: {{ $inboundRack->category }}</p>
                                    @else
                                        <div class="w-16 h-16 bg-white/50 rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-200 text-gray-300 text-xl font-black mb-2">T</div>
                                        <p class="text-[9px] font-black text-gray-300 uppercase italic">Not Stored</p>
                                    @endif
                                </div>

                                {{-- Shoe Rack --}}
                                <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100 flex flex-col items-center justify-center text-center">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Rak Sepatu</span>
                                    @if($shoeRack)
                                        <div class="w-20 h-20 bg-white rounded-2xl flex flex-col items-center justify-center shadow-lg border-b-4 border-[#FFC232] -rotate-1 mb-2">
                                            <span class="text-3xl font-black text-gray-900 leading-none">{{ $shoeRack->rack_code }}</span>
                                        </div>
                                        <p class="text-[9px] font-black text-[#D4A017] uppercase">Sector: {{ $shoeRack->category }}</p>
                                    @else
                                        <div class="w-16 h-16 bg-white/50 rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-200 text-gray-300 text-xl font-black mb-2">T</div>
                                        <p class="text-[9px] font-black text-gray-300 uppercase italic">Not Stored</p>
                                    @endif
                                </div>

                                {{-- Accessory Rack --}}
                                <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100 flex flex-col items-center justify-center text-center">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Rak Aksesoris</span>
                                    @if($accRack)
                                        <div class="w-20 h-20 bg-white rounded-2xl flex flex-col items-center justify-center shadow-lg border-b-4 border-[#22AF85] rotate-2 mb-2">
                                            <span class="text-3xl font-black text-gray-900 leading-none">{{ $accRack->rack_code }}</span>
                                        </div>
                                        <p class="text-[9px] font-black text-[#22AF85] uppercase">Sector: {{ $accRack->category }}</p>
                                    @else
                                        <div class="w-16 h-16 bg-white/50 rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-200 text-gray-300 text-xl font-black mb-2">T</div>
                                        <p class="text-[9px] font-black text-gray-300 uppercase italic">Not Stored</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Services Table (Interactive Editor) --}}
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden"
                         x-data="serviceEditor()" x-cloak>
                        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                            <div>
                                <h3 class="font-black text-gray-900 text-lg">Layanan & Harga</h3>
                                <p class="text-gray-500 text-xs mt-0.5">Klik biaya untuk mengedit • Kelola layanan order</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <button @click="showAddForm = !showAddForm" 
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-[#22B086] hover:bg-[#1C8D6C] text-white rounded-lg text-xs font-bold transition-all shadow-md shadow-emerald-200 hover:-translate-y-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    Tambah
                                </button>
                                <div class="px-4 py-2 bg-[#FFC232] text-gray-900 rounded-lg font-mono font-bold shadow-lg shadow-orange-200 transition-all">
                                    TOTAL: Rp <span x-text="formatRupiah(totalTransaksi)">{{ number_format($order->total_transaksi, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Add Service Form (Slide Down) --}}
                        <div x-show="showAddForm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                             class="px-8 py-5 bg-emerald-50/50 border-b border-emerald-100">
                            <div class="flex flex-col md:flex-row gap-3 items-end">
                                <div class="flex-1 min-w-[200px]">
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1 block">Kategori</label>
                                    <select x-model="selectedCategory" @change="onCategoryChange()" 
                                            class="w-full rounded-xl border-gray-200 text-sm font-medium focus:border-[#22B086] focus:ring-[#22B086] shadow-sm">
                                        <option value="">-- Pilih Kategori --</option>
                                        <template x-for="cat in uniqueCategories" :key="cat">
                                            <option :value="cat" x-text="cat"></option>
                                        </template>
                                        <option value="custom">✏️ Layanan Custom...</option>
                                    </select>
                                </div>
                                <div class="flex-1 min-w-[200px]" x-show="selectedCategory && selectedCategory !== 'custom'" x-transition>
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1 block">Pilih Layanan</label>
                                    <select x-model="newServiceId" @change="onServiceSelect()" 
                                            class="w-full rounded-xl border-gray-200 text-sm font-medium focus:border-[#22B086] focus:ring-[#22B086] shadow-sm">
                                        <option value="">-- Pilih Layanan --</option>
                                        <template x-for="svc in filteredServices" :key="svc.id">
                                            <option :value="svc.id" x-text="svc.name + ' (Rp ' + formatRupiah(svc.price) + ')'"></option>
                                        </template>
                                        <option value="custom">✏️ Layanan Custom...</option>
                                    </select>
                                </div>
                                <div x-show="selectedCategory === 'custom' || newServiceId === 'custom'" class="flex-1" x-transition>
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1 block">Nama Custom</label>
                                    <input type="text" x-model="newCustomName" placeholder="Nama layanan kustom"
                                           class="w-full rounded-xl border-gray-200 text-sm font-medium focus:border-[#22B086] focus:ring-[#22B086] shadow-sm">
                                </div>
                                <div class="w-40">
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1 block">Biaya (Rp)</label>
                                    <input type="number" x-model.number="newCost" min="0" step="1000" placeholder="0"
                                           class="w-full rounded-xl border-gray-200 text-sm font-mono font-bold focus:border-[#22B086] focus:ring-[#22B086] shadow-sm">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1 block">Detail Jasa (Opsional)</label>
                                <input type="text" x-model="newDetails" placeholder="Contoh: Warna Hitam, Ukuran 42, Ganti Insole Ori, dll..."
                                       class="w-full rounded-xl border-gray-200 text-sm font-medium focus:border-[#22B086] focus:ring-[#22B086] shadow-sm">
                            </div>
                            <div class="flex gap-2 mt-3">
                                <button @click="submitAdd()" :disabled="isLoading"
                                        class="px-4 py-2.5 bg-[#22B086] hover:bg-[#1C8D6C] text-white rounded-xl text-xs font-bold transition-all shadow-md disabled:opacity-50">
                                    <span x-show="!isLoading">Simpan</span>
                                    <span x-show="isLoading" class="flex items-center gap-1">
                                        <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        Proses...
                                    </span>
                                </button>
                                <button @click="showAddForm = false; resetAddForm()" class="px-3 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-xs font-bold transition-all">
                                    Batal
                                </button>
                            </div>
                        </div>

                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-8 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-wider">Service Name</th>
                                    <th class="px-8 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-wider">Biaya</th>
                                    <th class="px-4 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-wider w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(svc, idx) in services" :key="svc.id">
                                    <tr class="hover:bg-gray-50/50 transition-colors group">
                                        <td class="px-8 py-5">
                                            <div class="flex items-start gap-3">
                                                <div class="w-1 h-10 bg-gray-200 rounded-full group-hover:bg-[#22B086] transition-colors mt-0.5"></div>
                                                <div>
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded uppercase tracking-wider border border-emerald-100" x-text="svc.category || 'GENERAL'"></span>
                                                    </div>
                                                    <span class="font-bold text-gray-800 text-sm" x-text="svc.name"></span>
                                                    <template x-if="svc.details">
                                                        <p class="text-[11px] text-gray-400 mt-1 font-medium italic bg-gray-50 px-2 py-0.5 rounded-md inline-block border border-gray-100" x-text="svc.details"></p>
                                                    </template>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="font-mono font-bold text-gray-700">
                                                Rp <span x-text="formatRupiah(svc.cost)"></span>
                                            </span>
                                        </td>
                                        <td class="px-4 py-5 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <button @click="startEdit(svc)" :disabled="isLoading"
                                                        class="p-1.5 text-gray-400 hover:text-[#22B086] hover:bg-emerald-50 rounded-lg transition-all opacity-0 group-hover:opacity-100 disabled:opacity-50"
                                                        title="Edit detail jasa">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </button>
                                                <button @click="confirmRemove(svc)" :disabled="isLoading"
                                                        class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all opacity-0 group-hover:opacity-100 disabled:opacity-50"
                                                        title="Hapus layanan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                {{-- Extra Costs (Ongkir etc) --}}
                                @if($order->shipping_cost > 0)
                                    <tr class="bg-gray-50/30">
                                        <td class="px-8 py-4 text-xs font-bold text-gray-500 uppercase pl-12">Ongkos Kirim</td>
                                        <td class="px-8 py-4 text-right font-mono font-bold text-gray-700">
                                            Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                                        </td>
                                        <td></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>


                    {{-- Service Editor Modal --}}
                    <template x-teleport="body">
                        <div x-show="showEditModal" class="fixed inset-0 z-[1000] overflow-y-auto" style="display: none;">
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showEditModal = false">
                                    <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-md"></div>
                                </div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                     class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20 relative z-[1001]">
                                    
                                    <div class="bg-gradient-to-r from-[#22B086] to-[#1C8D6C] px-8 py-6">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <h3 class="text-xl font-black text-white leading-tight">Edit Detail Layanan</h3>
                                                <p class="text-white/80 text-[10px] font-bold mt-1 uppercase tracking-widest">Update Instruksi Pengerjaan & Biaya</p>
                                            </div>
                                            <button @click="showEditModal = false" class="text-white hover:rotate-90 transition-transform duration-300">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="p-8 space-y-5">
                                        {{-- Row 1: Category & Name --}}
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Kategori Layanan</label>
                                                <select x-model="editCategory" class="w-full rounded-xl border-gray-200 focus:border-[#22B086] focus:ring-[#22B086] font-bold text-sm bg-gray-50/50">
                                                    <template x-for="cat in uniqueCategories" :key="cat">
                                                        <option :value="cat" x-text="cat"></option>
                                                    </template>
                                                    <option value="custom">CUSTOM / LAINNYA</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Nama Layanan</label>
                                                <input type="text" x-model="editName" class="w-full rounded-xl border-gray-200 focus:border-[#22B086] focus:ring-[#22B086] font-bold text-sm bg-gray-50/50" placeholder="Nama layanan...">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Detail Instruksi (Penting untuk Workshop)</label>
                                            <textarea x-model="editDetails" rows="3" class="w-full rounded-xl border-gray-200 focus:border-[#22B086] focus:ring-[#22B086] font-medium text-sm" placeholder="Contoh: Warna Hitam, Ukuran 42, Ganti Insole Ori, dll..."></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5">Biaya Layanan (Rp)</label>
                                            <div class="relative">
                                                <div class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-400 text-sm">Rp</div>
                                                <input type="number" x-model.number="editCost" class="w-full pl-12 rounded-xl border-gray-200 focus:border-[#22B086] focus:ring-[#22B086] font-mono font-bold text-sm bg-gray-50/50">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 px-8 py-6 flex gap-3">
                                        <button @click="submitEdit()" :disabled="isLoading" class="flex-1 py-4 bg-[#22B086] hover:bg-[#1C8D6C] text-white font-black rounded-2xl shadow-xl shadow-emerald-200 transition-all flex items-center justify-center gap-2">
                                            <template x-if="!isLoading">
                                                <span class="flex items-center gap-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    Update Layanan
                                                </span>
                                            </template>
                                            <template x-if="isLoading">
                                                <span class="flex items-center gap-2">
                                                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                    Menyimpan...
                                                </span>
                                            </template>
                                        </button>
                                        <button @click="showEditModal = false" class="px-8 py-4 bg-white border border-gray-200 text-gray-500 font-black rounded-2xl transition-all">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    </div>

                    {{-- 4. Workshop Activity Timeline --}}
                    @php
                        // Phase Color & Icon Mapping
                        $phaseStyles = [
                            'LOGISTICS' => [
                                'label' => 'Logistik & Pengiriman',
                                'color' => '#3B82F6', // Blue
                                'bg' => 'bg-blue-50',
                                'text' => 'text-blue-600',
                                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
                                'dot' => 'border-blue-500'
                            ],
                            'ASSESSMENT' => [
                                'label' => 'Assessment & Approval',
                                'color' => '#F59E0B', // Amber
                                'bg' => 'bg-amber-50',
                                'text' => 'text-amber-600',
                                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                                'dot' => 'border-amber-500'
                            ],
                            'PRODUCTION' => [
                                'label' => 'Proses Produksi',
                                'color' => '#10B981', // Emerald
                                'bg' => 'bg-emerald-50',
                                'text' => 'text-emerald-600',
                                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.638.319a4 4 0 01-2.154.431l-2.141-.214a2 2 0 00-1.176.28l-1.428.857a2 2 0 00-.788 2.276l.428 1.712a2 2 0 001.941 1.514H19a2 2 0 002-2v-3.003a2 2 0 00-.572-1.414z"></path></svg>',
                                'dot' => 'border-emerald-500'
                            ],
                            'QC' => [
                                'label' => 'Quality Control',
                                'color' => '#6366F1', // Indigo
                                'bg' => 'bg-indigo-50',
                                'text' => 'text-indigo-600',
                                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
                                'dot' => 'border-indigo-500'
                            ],
                            'FINAL' => [
                                'label' => 'Penyelesaian & Delivery',
                                'color' => '#111827', // Gray-900
                                'bg' => 'bg-gray-100',
                                'text' => 'text-gray-900',
                                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                                'dot' => 'border-gray-900'
                            ],
                            'SYSTEM' => [
                                'label' => 'Administrasi & System',
                                'color' => '#6B7280', // Gray-500
                                'bg' => 'bg-gray-50',
                                'text' => 'text-gray-500',
                                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
                                'dot' => 'border-gray-400'
                            ],
                        ];

                        // Grouping Logic
                        $allLogs = $order->logs->sortBy('created_at');
                        $groupedLogs = [
                            'LOGISTICS' => [],
                            'ASSESSMENT' => [],
                            'PRODUCTION' => [],
                            'QC' => [],
                            'FINAL' => [],
                            'SYSTEM' => [],
                        ];

                        foreach ($allLogs as $log) {
                            $step = strtoupper($log->step);
                            $act = strtolower($log->action);

                            if (in_array($step, ['READY_TO_DISPATCH', 'OTW_WORKSHOP', 'DITERIMA', 'DIANTAR'])) {
                                $groupedLogs['LOGISTICS'][] = $log;
                            } elseif (in_array($step, ['ASSESSMENT', 'WAITING_PAYMENT', 'WAITING_VERIFICATION', 'CX_FOLLOWUP'])) {
                                $groupedLogs['ASSESSMENT'][] = $log;
                            } elseif (in_array($step, ['PREPARATION', 'SORTIR', 'PRODUCTION']) || str_contains($act, 'prep_') || str_contains($act, 'prod_')) {
                                $groupedLogs['PRODUCTION'][] = $log;
                            } elseif ($step === 'QC' || str_contains($act, 'qc_')) {
                                $groupedLogs['QC'][] = $log;
                            } elseif ($step === 'SELESAI') {
                                $groupedLogs['FINAL'][] = $log;
                            } else {
                                $groupedLogs['SYSTEM'][] = $log;
                            }
                        }

                        // Filter empty groups
                        $groupedLogs = array_filter($groupedLogs, fn($group) => count($group) > 0);
                    @endphp

                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mt-8" 
                         x-data="{ activePhase: '{{ array_key_last($groupedLogs) }}' }">
                        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/30 flex items-center justify-between">
                            <div>
                                <h3 class="font-black text-gray-900 text-lg">Workshop Activity Timeline</h3>
                                <p class="text-gray-500 text-xs mt-0.5">Audit trail pengerjaan teknisi & riwayat sistem</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-[#22B086]/10 text-[#22B086] text-[10px] font-black uppercase rounded-lg">Full History</span>
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black uppercase rounded-lg">{{ $allLogs->count() }} Events</span>
                            </div>
                        </div>
                        
                        <div class="p-0 flex flex-col md:flex-row min-h-[500px]">
                            @if(count($groupedLogs) > 0)
                                {{-- Sidebar Navigation --}}
                                <div class="w-full md:w-72 bg-gray-50/50 border-r border-gray-100 p-6 space-y-2">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 px-2">Work Phases</p>
                                    @foreach($groupedLogs as $phaseKey => $logs)
                                        @php $style = $phaseStyles[$phaseKey]; @endphp
                                        <button @click="activePhase = '{{ $phaseKey }}'" 
                                                class="w-full flex items-center gap-3 p-3 rounded-xl transition-all duration-200 text-left group"
                                                :class="activePhase === '{{ $phaseKey }}' ? 'bg-white shadow-md shadow-gray-200/50 ring-1 ring-gray-100' : 'hover:bg-gray-100/80'">
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors"
                                                 :class="activePhase === '{{ $phaseKey }}' ? '{{ $style['bg'] }} {{ $style['text'] }}' : 'bg-gray-200 text-gray-500 group-hover:bg-gray-300'">
                                                {!! $style['icon'] !!}
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-[11px] font-black uppercase tracking-tight"
                                                    :class="activePhase === '{{ $phaseKey }}' ? 'text-gray-900' : 'text-gray-500'">
                                                    {{ $style['label'] }}
                                                </h4>
                                                <p class="text-[9px] font-bold text-gray-400">{{ count($logs) }} Aktivitas</p>
                                            </div>
                                            <div x-show="activePhase === '{{ $phaseKey }}'" 
                                                 class="w-1.5 h-1.5 rounded-full {{ str_replace('border-', 'bg-', $style['dot']) }}"
                                                 x-show="activePhase === '{{ $phaseKey }}'"></div>
                                        </button>
                                    @endforeach
                                </div>

                                {{-- Timeline Content --}}
                                <div class="flex-1 p-8 bg-white overflow-y-auto max-h-[600px] custom-scrollbar">
                                    @foreach($groupedLogs as $phaseKey => $logs)
                                        <div x-show="activePhase === '{{ $phaseKey }}'" 
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 transform translate-x-4"
                                             x-transition:enter-end="opacity-100 transform translate-x-0"
                                             class="space-y-8 relative before:absolute before:inset-0 before:ml-0 before:-translate-x-px before:h-full before:w-0.5 before:bg-gray-100">
                                            
                                            @php $style = $phaseStyles[$phaseKey]; @endphp
                                            
                                            @foreach($logs as $log)
                                                @php
                                                    $duration = null;
                                                    if (str_contains($log->action, 'finish') || str_contains($log->action, 'complete')) {
                                                        $baseAction = str_replace(['_finish', '_complete', '_completed'], '', $log->action);
                                                        $startLog = $allLogs->filter(function($l) use ($baseAction, $log) {
                                                            return (str_contains($l->action, $baseAction) && 
                                                                   (str_contains($l->action, 'start') || str_contains($l->action, 'started'))) &&
                                                                   $l->created_at->lt($log->created_at);
                                                        })->first();

                                                        if ($startLog) {
                                                            $diff = $startLog->created_at->diff($log->created_at);
                                                            $durationParts = [];
                                                            if ($diff->d > 0) $durationParts[] = $diff->d . " Hari";
                                                            if ($diff->h > 0) $durationParts[] = $diff->h . " Jam";
                                                            if ($diff->i > 0 || empty($durationParts)) $durationParts[] = $diff->i . " Menit";
                                                            $duration = implode(' ', $durationParts);
                                                        }
                                                    }

                                                    $isStatusChange = str_contains(strtolower($log->description), 'status berubah') || str_contains(strtolower($log->action), 'status');
                                                    $techName = $log->user?->name ?? 'System';
                                                @endphp
                                                <div class="relative flex items-start gap-6 group pl-6">
                                                    {{-- Dot --}}
                                                    <div class="absolute left-0 w-0 h-10 flex items-center justify-center -ml-px">
                                                        <div class="w-2.5 h-2.5 rounded-full bg-white border-2 {{ $style['dot'] }} z-10 group-hover:scale-150 transition-all duration-300 shadow-sm"></div>
                                                    </div>
                                                    
                                                    <div class="flex-1 -mt-1">
                                                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 mb-1">
                                                            <h5 class="text-sm {{ $isStatusChange ? 'font-black text-gray-900' : 'font-bold text-gray-700' }} tracking-tight">
                                                                {{ $log->description }}
                                                                @if($duration)
                                                                    <span class="ml-2 text-[10px] lowercase font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                                                                        ⏱️ Selesai dalam {{ $duration }}
                                                                    </span>
                                                                @endif
                                                            </h5>
                                                            <span class="text-[10px] font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                                                                {{ $log->created_at->format('d M Y, H:i') }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center gap-3">
                                                            <div class="flex items-center gap-1.5">
                                                                <div class="w-4 h-4 rounded-full bg-gray-100 flex items-center justify-center text-[8px] font-black text-gray-500 uppercase">
                                                                    {{ substr($techName, 0, 1) }}
                                                                </div>
                                                                <span class="text-[11px] font-bold text-gray-500">{{ $techName }}</span>
                                                            </div>
                                                            <span class="text-gray-200">|</span>
                                                            <span class="text-[9px] font-black {{ $style['text'] }} uppercase tracking-widest">{{ str_replace('_', ' ', $log->step) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12 w-full">
                                    <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h5 class="text-gray-400 text-sm font-black uppercase tracking-widest">No Activity Log</h5>
                                    <p class="text-gray-300 text-xs mt-1">Belum ada riwayat aktivitas untuk order ini</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>



                {{-- 5. Gallery Foto --}}
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 lg:col-span-2" 
                     x-data="{ 
                        showLightbox: false, 
                        activeId: null,
                        activeImage: '', 
                        activeCaption: '', 
                        activeStep: '',
                        activeUploader: '',
                        activeSize: '',
                        isCover: false,
                        isRef: false,
                        openLightbox(id, url, caption, step, uploader, size, isCover, isRef) {
                            this.activeId = id;
                            this.activeImage = url;
                            this.activeCaption = caption;
                            this.activeStep = step;
                            this.activeUploader = uploader;
                            this.activeSize = size;
                            this.isCover = isCover;
                            this.isRef = isRef;
                            this.showLightbox = true;
                        },
                        async setAsCover() {
                            try {
                                const res = await fetch(`/photos/${this.activeId}/set-cover`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                });
                                const data = await res.json();
                                if(data.success) {
                                    this.isCover = true;
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message,
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                }
                            } catch(e) { console.error(e); }
                        },
                        async setAsReference() {
                            try {
                                const res = await fetch(`/photos/${this.activeId}/set-reference`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                });
                                const data = await res.json();
                                if(data.success) {
                                    this.isRef = true;
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message,
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                }
                            } catch(e) { console.error(e); }
                        },
                        downloadImage() {
                            const link = document.createElement('a');
                            link.href = this.activeImage;
                            link.download = 'photo_' + Date.now();
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }
                     }">
                    
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-black text-gray-900 text-lg flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-[#22B086]/10 text-[#22B086] flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </span>
                            Galeri Foto Lengkap
                        </h3>
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1 rounded-full uppercase tracking-wider">
                            {{ $order->photos->count() }} Foto
                        </span>
                    </div>
                    
                    <div class="p-6 bg-gray-50 min-h-[400px]">
                        @if($order->photos->count() > 0)
                            @php
                                $groupedPhotos = $order->photos->groupBy('step');
                                $stepLabels = [
                                    'RECEPTION' => '📩 Foto Referensi CS',
                                    'WAREHOUSE_BEFORE' => '📸 Foto Penerimaan (Gudang)',
                                    'ASSESSMENT' => '📋 Foto Assessment (Teknisi)',
                                    'WASHING' => 'washing', 
                                    'PREPARATION' => '🛠 Preparation',
                                    'PRODUCTION' => '🏭 Production / Cuci',
                                    'QC' => '✨ Quality Control',
                                    'FINISH' => '✅ Finishing & Packing',
                                    'CX_FOLLOWUP' => '📞 Foto Follow-up CX',
                                ];
                            @endphp

                            @foreach($groupedPhotos as $step => $photos)
                                <div class="mb-8 last:mb-0">
                                    <h4 class="text-[#22B086] font-bold uppercase tracking-widest text-xs mb-4 flex items-center gap-2 border-b border-gray-200 pb-2">
                                        <span class="w-2 h-2 rounded-full bg-[#22B086]"></span>
                                        {{ $stepLabels[$step] ?? $step }}
                                    </h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                        @foreach($photos as $photo)
                                            @php
                                                $size = 0;
                                                try {
                                                    if(\Illuminate\Support\Facades\Storage::disk('public')->exists($photo->file_path)) {
                                                        $size = \Illuminate\Support\Facades\Storage::disk('public')->size($photo->file_path);
                                                    }
                                                } catch(\Exception $e) {}
                                                $formattedSize = $size > 1048576 ? round($size/1048576, 1).' MB' : round($size/1024, 0).' KB';
                                                $uploaderName = $photo->uploader ? $photo->uploader->name : 'Admin';
                                            @endphp
                                            <div class="group relative aspect-square bg-white rounded-xl overflow-hidden border {{ $photo->is_spk_cover ? 'border-[#FFC232] ring-2 ring-[#FFC232]/50' : ($photo->is_primary_reference ? 'border-purple-500 ring-2 ring-purple-500/30' : 'border-gray-200') }} shadow-lg cursor-pointer transition-transform duration-300 hover:scale-105 hover:border-[#22B086]/50 hover:shadow-emerald-500/20"
                                                 @click="openLightbox('{{ $photo->id }}', '{{ $photo->photo_url }}', '{{ $photo->caption ?? 'Tanpa Caption' }}', '{{ $stepLabels[$step] ?? $step }}', '{{ $uploaderName }}', '{{ $formattedSize }}', {{ $photo->is_spk_cover ? 'true' : 'false' }}, {{ $photo->is_primary_reference ? 'true' : 'false' }})">
                                                <img src="{{ $photo->photo_url }}" 
                                                     class="w-full h-full object-cover">
                                                
                                                {{-- Overlay Info --}}
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-3">
                                                    <div class="absolute top-2 right-2 flex flex-col gap-1 items-end">
                                                        @if($photo->is_spk_cover)
                                                           <div class="px-1.5 py-0.5 bg-amber-500 text-white text-[8px] font-black rounded uppercase shadow-lg">Cover</div>
                                                        @endif
                                                        @if($photo->is_primary_reference)
                                                           <div class="px-1.5 py-0.5 bg-purple-600 text-white text-[8px] font-black rounded uppercase shadow-lg">Referansi</div>
                                                        @endif
                                                    </div>
                                                    <p class="text-white text-xs font-bold line-clamp-2">{{ $photo->caption ?? 'Tanpa Caption' }}</p>
                                                    <div class="flex justify-between items-center mt-1 text-[10px]">
                                                        <span class="text-gray-400">{{ $uploaderName }}</span>
                                                        <span class="text-gray-500">{{ $formattedSize }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="flex flex-col items-center justify-center py-20 text-gray-600">
                                <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-lg font-medium">Belum ada foto yang diupload</p>
                            </div>
                        @endif
                    </div>

                    {{-- Lightbox Modal --}}
                    <div x-show="showLightbox" 
                         style="display: none;"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/90 backdrop-blur-sm p-4"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-90"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-90">
                        
                        {{-- Close Button --}}
                        <button @click="showLightbox = false" class="absolute top-4 right-4 p-2 bg-white/10 hover:bg-white/20 rounded-full text-white transition-colors z-50">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <div class="max-w-7xl w-full max-h-screen flex flex-col md:flex-row gap-6 bg-gray-50 rounded-2xl overflow-hidden shadow-2xl border border-gray-200" @click.outside="showLightbox = false">
                            {{-- Image Area --}}
                            <div class="flex-1 bg-gray-200/50 flex items-center justify-center relative min-h-[400px]">
                                <img :src="activeImage" class="max-w-full max-h-[85vh] object-contain">
                            </div>

                            {{-- Sidebar Info --}}
                            <div class="w-full md:w-80 bg-white p-6 flex flex-col border-l border-gray-100">
                                <h3 class="text-[#22B086] font-bold uppercase tracking-widest text-xs mb-2" x-text="activeStep"></h3>
                                <p class="text-gray-900 font-bold text-lg mb-4 leading-relaxed" x-text="activeCaption || 'Tanpa Caption'"></p>

                                <div class="space-y-4 mb-8">
                                    <div>
                                        <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Diupload Oleh</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-600 uppercase font-bold" x-text="activeUploader.charAt(0)"></div>
                                            <p class="text-gray-600 text-sm" x-text="activeUploader"></p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Ukuran File</p>
                                        <p class="text-gray-600 text-sm mt-1" x-text="activeSize"></p>
                                    </div>
                                </div>

                                <div class="mt-auto space-y-3">
                                    <button @click="setAsReference()" 
                                            x-show="activeId"
                                            :disabled="isRef"
                                            :class="isRef ? 'bg-purple-600 text-white cursor-default' : 'bg-gray-100 hover:bg-gray-200 text-purple-600 border border-purple-500/50'"
                                            class="w-full py-3 px-4 font-bold rounded-xl flex items-center justify-center gap-2 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h2.2c.462 0 .694 0 .898-.053.204-.053.385-.143.748-.325l.443-.221c.643-.322.964-.482 1.275-.482.311 0 .632.16 1.275.482l.443.221c.363.182.544.272.748.325.204.053.436.053.898.053H18c1.105 0 2-.895 2-2V7c0-1.105-.895-2-2-2H8c-1.105 0-2 .895-2 2v13z"></path></svg>
                                        <span x-text="isRef ? 'Referensi Utama Aktif' : 'Atur Sebagai Referensi'"></span>
                                    </button>

                                    <button @click="setAsCover()" 
                                            x-show="activeId"
                                            :disabled="isCover"
                                            :class="isCover ? 'bg-[#FFC232] text-white cursor-default' : 'bg-gray-100 hover:bg-gray-200 text-[#FFC232] border border-[#FFC232]/50'"
                                            class="w-full py-3 px-4 font-bold rounded-xl flex items-center justify-center gap-2 transition-all">
                                        <svg class="w-5 h-5" :class="isCover ? 'fill-current' : 'fill-none'" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                        <span x-text="isCover ? 'SPK Cover Aktif' : 'Atur Sebagai Cover'"></span>
                                    </button>

                                    <button @click="downloadImage()" class="w-full py-3 px-4 bg-[#22B086] hover:bg-[#1C8D6C] text-white font-bold rounded-xl flex items-center justify-center gap-2 transition-colors shadow-lg shadow-emerald-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Download Foto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@push('scripts')
@php
    $servicesJson = $order->workOrderServices->map(function($s) {
        $details = '';
        if (!empty($s->service_details) && is_array($s->service_details)) {
            if (isset($s->service_details['manual_detail']) && !empty($s->service_details['manual_detail'])) {
                $details = $s->service_details['manual_detail'];
            } else {
                $parts = [];
                foreach ($s->service_details as $k => $v) {
                    if (!empty($v) && $k !== 'manual_detail') $parts[] = is_array($v) ? implode(', ', $v) : $v;
                }
                $details = implode(', ', $parts);
            }
        }
        return [
            'id' => $s->id,
            'name' => $s->custom_service_name ?? ($s->service ? $s->service->name : '-'),
            'category' => $s->category_name ?? ($s->service ? $s->service->category : 'GENERAL'),
            'cost' => $s->cost,
            'details' => $details,
        ];
    });
    $catalogJson = $allServices->map(function($s) {
        return [
            'id' => $s->id,
            'name' => $s->name, 
            'price' => $s->price, 
            'category' => $s->category
        ];
    })->values();
@endphp
<script>
function serviceEditor() {
    return {
        orderId: @json($order->id),
        services: @json($servicesJson),
        totalTransaksi: @json($order->total_transaksi),
        sisaTagihan: @json($order->sisa_tagihan),
        statusPembayaran: @json($order->status_pembayaran),
        serviceCatalog: @json($catalogJson),

        // UI State
        showAddForm: false,
        showEditModal: false,
        editingId: null,
        isLoading: false,

        // Editing fields
        editName: '',
        editCategory: '',
        editCost: 0,
        editDetails: '',

        // Add form fields
        selectedCategory: '',
        newServiceId: '',
        newCustomName: '',
        newCost: 0,
        newDetails: '',

        get uniqueCategories() {
            const cats = new Set();
            this.serviceCatalog.forEach(s => {
                if(s.category) cats.add(s.category);
            });
            return Array.from(cats).sort();
        },

        get filteredServices() {
            if (!this.selectedCategory || this.selectedCategory === 'custom') return [];
            return this.serviceCatalog.filter(s => s.category === this.selectedCategory);
        },

        // Edit fields
        editDetails: '',

        // Service catalog for lookup
        serviceCatalog: @json($catalogJson),

        formatRupiah(val) {
            return new Intl.NumberFormat('id-ID').format(val || 0);
        },

        onCategoryChange() {
            this.newServiceId = '';
            this.newDetails = '';
            if (this.selectedCategory === 'custom') {
                this.newCost = 0;
            }
        },

        onServiceSelect() {
            this.newDetails = '';
            if (this.newServiceId && this.newServiceId !== 'custom') {
                const svc = this.serviceCatalog.find(s => s.id == this.newServiceId);
                if (svc) {
                    this.newCost = svc.price;
                    this.newCustomName = '';
                }
            } else if (this.newServiceId === 'custom') {
                this.newCost = 0;
                this.newCustomName = '';
            }
        },

        resetAddForm() {
            this.selectedCategory = '';
            this.newServiceId = '';
            this.newCustomName = '';
            this.newCost = 0;
            this.newDetails = '';
        },

        async submitAdd() {
            if (!this.selectedCategory) {
                this.showToast('error', 'Pilih kategori terlebih dahulu');
                return;
            }
            if (this.selectedCategory !== 'custom' && !this.newServiceId) {
                this.showToast('error', 'Pilih layanan terlebih dahulu');
                return;
            }
            if (this.selectedCategory === 'custom' && !this.newCustomName.trim()) {
                this.showToast('error', 'Masukkan nama layanan custom');
                return;
            }
            if (this.newCost <= 0) {
                this.showToast('error', 'Biaya harus lebih dari 0');
                return;
            }

            this.isLoading = true;
            try {
                const body = { 
                    cost: this.newCost,
                    category_name: this.selectedCategory
                };

                if (this.selectedCategory === 'custom' || this.newServiceId === 'custom') {
                    body.custom_service_name = this.newCustomName;
                    body.service_id = null;
                } else {
                    body.service_id = this.newServiceId;
                }

                if (this.newDetails.trim()) {
                    body.service_details = this.newDetails.trim();
                }

                const res = await this.fetchApi(`/admin/orders/${this.orderId}/services`, 'POST', body);
                if (res.success) {
                    this.showToast('success', res.message);
                    setTimeout(() => location.reload(), 600);
                }
            } catch (e) {
                this.showToast('error', 'Gagal menambahkan layanan: ' + e.message);
            } finally {
                this.isLoading = false;
            }
        },

        startEdit(svc) {
            this.editingId = svc.id;
            this.editName = svc.name;
            this.editCategory = svc.category || 'GENERAL';
            this.editCost = svc.cost;
            this.editDetails = svc.details || '';
            this.showEditModal = true;
        },

        cancelEdit() {
            this.editingId = null;
            this.editCost = 0;
            this.editDetails = '';
            this.editName = '';
            this.editCategory = '';
            this.showEditModal = false;
        },

        async submitEdit() {
            if (!this.editingId) return;
            if (this.editCost < 0) {
                this.showToast('error', 'Biaya tidak boleh negatif');
                return;
            }

            this.isLoading = true;
            try {
                const res = await this.fetchApi(`/admin/orders/${this.orderId}/services/${this.editingId}`, 'PUT', {
                    cost: this.editCost,
                    category_name: this.editCategory,
                    custom_service_name: this.editName,
                    service_details: this.editDetails,
                });
                if (res.success) {
                    // Update local data
                    const svc = this.services.find(s => s.id === this.editingId);
                    if (svc) {
                        svc.name = this.editName;
                        svc.category = this.editCategory;
                        svc.details = this.editDetails;
                        svc.cost = this.editCost;
                    }
                    this.totalTransaksi = res.total_transaksi;
                    this.sisaTagihan = res.sisa_tagihan;
                    this.statusPembayaran = res.status_pembayaran;
                    this.showEditModal = false;
                    this.editingId = null;
                    this.showToast('success', res.message);
                }
            } catch (e) {
                this.showToast('error', 'Gagal memperbarui: ' + e.message);
            } finally {
                this.isLoading = false;
            }
        },

        async confirmRemove(svc) {
            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    title: 'Hapus Layanan?',
                    html: `<b>${svc.name}</b> akan dihapus dari order ini.<br>Total transaksi akan diperbarui otomatis.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                });
                if (!result.isConfirmed) return;
            } else if (!confirm(`Hapus layanan "${svc.name}"?`)) {
                return;
            }

            this.isLoading = true;
            try {
                const res = await this.fetchApi(`/admin/orders/${this.orderId}/services/${svc.id}`, 'DELETE');
                if (res.success) {
                    this.services = this.services.filter(s => s.id !== svc.id);
                    this.totalTransaksi = res.total_transaksi;
                    this.sisaTagihan = res.sisa_tagihan;
                    this.statusPembayaran = res.status_pembayaran;
                    this.showToast('success', res.message);
                }
            } catch (e) {
                this.showToast('error', 'Gagal menghapus: ' + e.message);
            } finally {
                this.isLoading = false;
            }
        },

        async fetchApi(url, method, body = null) {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            };
            if (body) options.body = JSON.stringify(body);

            const response = await fetch(url, options);
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Request failed');
            return data;
        },

        showToast(type, message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                });
            } else {
                alert(message);
            }
        },
    };
}

function shoeInfoEditor(initialData) {
    return {
        orderId: @json($order->id),
        showModal: false,
        isLoading: false,
        
        // Form Fields
        brand: initialData.brand,
        size: initialData.size,
        color: initialData.color,
        category: initialData.category,
        tali: initialData.tali,
        insole: initialData.insole,
        box: initialData.box,
        other: initialData.other,
        hk_days: initialData.hk_days,
        is_warranty: initialData.is_warranty,

        async submit() {
            this.isLoading = true;
            try {
                const res = await fetch(`/admin/orders/${this.orderId}/update-shoe-info`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        shoe_brand: this.brand,
                        shoe_size: this.size,
                        shoe_color: this.color,
                        category: this.category,
                        accessories_tali: this.tali,
                        accessories_insole: this.insole,
                        accessories_box: this.box,
                        accessories_other: this.other,
                        hk_days: this.hk_days,
                        is_warranty: this.is_warranty ? 1 : 0,
                    })
                });

                const data = await res.json();
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                    location.reload();
                } else {
                    throw new Error(data.message || 'Gagal memperbarui data');
                }
            } catch (e) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: e.message
                    });
                } else {
                    alert(e.message);
                }
            } finally {
                this.isLoading = false;
            }
        }
    };
}

function addressEditor(initialData) {
    return {
        orderId: @json($order->id),
        showModal: false,
        isLoading: false,
        
        // Form Fields
        address: initialData.address,
        province: initialData.province,
        provinceId: initialData.provinceId,
        city: initialData.city,
        cityId: initialData.cityId,
        district: initialData.district,
        districtId: initialData.districtId,
        village: initialData.village,
        villageId: initialData.villageId,
        postalCode: initialData.postalCode,

        // Lists
        provinces: [],
        regencies: [],
        districts: [],
        villages: [],

        // Loading states
        isLoadingProvinces: false,
        isLoadingRegencies: false,
        isLoadingDistricts: false,
        isLoadingVillages: false,

        async init() {
            await this.fetchProvinces();
            if (this.provinceId) await this.fetchRegencies();
            if (this.cityId) await this.fetchDistricts();
            if (this.districtId) await this.fetchVillages();
        },

        async fetchProvinces() {
            this.isLoadingProvinces = true;
            try {
                const res = await fetch('/regional/provinces');
                this.provinces = await res.json();
            } catch (e) {
                console.error('Failed to fetch provinces', e);
            } finally {
                this.isLoadingProvinces = false;
            }
        },

        async fetchRegencies() {
            if (!this.provinceId) return;
            this.isLoadingRegencies = true;
            try {
                const res = await fetch(`/regional/regencies/${this.provinceId}`);
                this.regencies = await res.json();
            } catch (e) {
                console.error('Failed to fetch regencies', e);
            } finally {
                this.isLoadingRegencies = false;
            }
        },

        async fetchDistricts() {
            if (!this.cityId) return;
            this.isLoadingDistricts = true;
            try {
                const res = await fetch(`/regional/districts/${this.cityId}`);
                this.districts = await res.json();
            } catch (e) {
                console.error('Failed to fetch districts', e);
            } finally {
                this.isLoadingDistricts = false;
            }
        },

        async fetchVillages() {
            if (!this.districtId) return;
            this.isLoadingVillages = true;
            try {
                const res = await fetch(`/regional/villages/${this.districtId}`);
                this.villages = await res.json();
            } catch (e) {
                console.error('Failed to fetch villages', e);
            } finally {
                this.isLoadingVillages = false;
            }
        },

        async onProvinceChange() {
            this.regencies = [];
            this.districts = [];
            this.villages = [];
            this.cityId = '';
            this.districtId = '';
            this.villageId = '';
            
            // Get Province Name
            const p = this.provinces.find(x => x.id == this.provinceId);
            this.province = p ? p.name : '';
            
            if (this.provinceId) await this.fetchRegencies();
        },

        async onCityChange() {
            this.districts = [];
            this.villages = [];
            this.districtId = '';
            this.villageId = '';
            
            // Get City Name
            const c = this.regencies.find(x => x.id == this.cityId);
            this.city = c ? c.name : '';
            
            if (this.cityId) await this.fetchDistricts();
        },

        async onDistrictChange() {
            this.villages = [];
            this.villageId = '';

            // Get District Name
            const d = this.districts.find(x => x.id == this.districtId);
            this.district = d ? d.name : '';

            if (this.districtId) await this.fetchVillages();
        },

        async submit() {
            // Get Final Village Name before submit
            const v = this.villages.find(x => x.id == this.villageId);
            this.village = v ? v.name : this.village;

            this.isLoading = true;
            try {
                const res = await fetch(`/admin/orders/${this.orderId}/update-shipping-address`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        address: this.address,
                        province: this.province,
                        province_id: this.provinceId,
                        city: this.city,
                        city_id: this.cityId,
                        district: this.district,
                        district_id: this.districtId,
                        village: this.village,
                        village_id: this.villageId,
                        postal_code: this.postalCode,
                    })
                });

                const data = await res.json();
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                    location.reload();
                } else {
                    throw new Error(data.message || 'Gagal memperbarui alamat');
                }
            } catch (e) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: e.message
                    });
                } else {
                    alert(e.message);
                }
            } finally {
                this.isLoading = false;
            }
        }
    };
}

function customerEditor(initialData) {
    return {
        orderId: @json($order->id),
        showCustomerModal: false,
        isLoading: false,
        
        // Form Fields
        name: initialData.name,
        phone: initialData.phone,
        email: initialData.email,

        suggestions: [],
        isSearching: false,

        async fetchSuggestions() {
            if (this.phone.length < 3) {
                this.suggestions = [];
                return;
            }

            this.isSearching = true;
            try {
                const res = await fetch(`/admin/customers/search-json?q=${encodeURIComponent(this.phone)}`);
                this.suggestions = await res.json();
            } catch (e) {
                console.error('Search failed', e);
            } finally {
                this.isSearching = false;
            }
        },

        selectSuggestion(customer) {
            this.name = customer.name;
            this.phone = customer.phone;
            this.email = customer.email;
            this.suggestions = [];
        },

        async submit() {
            this.isLoading = true;
            try {
                const res = await fetch(`/admin/orders/${this.orderId}/update-customer-info`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        name: this.name,
                        phone: this.phone,
                        email: this.email,
                    })
                });

                const data = await res.json();
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                    location.reload();
                } else {
                    throw new Error(data.message || 'Gagal memperbarui data customer');
                }
            } catch (e) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: e.message
                    });
                } else {
                    alert(e.message);
                }
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endpush

</x-app-layout>
