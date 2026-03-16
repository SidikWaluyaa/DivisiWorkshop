<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC] pb-32">
        {{-- Elite Sticky Header --}}
        <div class="bg-white/80 shadow-2xl border-b border-gray-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 sm:gap-6">
                        <a href="{{ route('finance.invoices.index') }}" class="group flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 hover:border-[#1B8A68]/30 hover:shadow-emerald-100 transition-all active:scale-95">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400 group-hover:text-[#1B8A68] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="bg-[#1B8A68] text-white text-[8px] sm:text-[10px] font-black px-1.5 sm:px-2 py-0.5 rounded-md uppercase tracking-widest shadow-lg shadow-emerald-100 italic">TRANSAKSI</span>
                                <h1 class="text-lg sm:text-2xl font-black text-gray-900 tracking-tight italic">Buat Invoice Gabungan</h1>
                            </div>
                            <p class="text-gray-400 text-[9px] sm:text-[10px] font-black uppercase tracking-[0.15em] sm:tracking-[0.2em] flex items-center gap-2 hidden sm:flex">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                Inventarisasi Tagihan Belum Terbit
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 mt-6 sm:mt-10">
            {{-- PENCARIAN PELANGGAN --}}
            <div class="bg-white rounded-2xl sm:rounded-[2.5rem] shadow-2xl p-5 sm:p-10 border border-gray-100 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#1B8A68]/5 rounded-bl-[5rem] -mr-8 -mt-8 transition-transform group-hover:scale-110 duration-700"></div>
                
                <h3 class="text-[10px] sm:text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] sm:tracking-[0.3em] mb-5 sm:mb-8 flex items-center gap-3 italic">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 bg-emerald-50 rounded-lg sm:rounded-xl flex items-center justify-center border border-emerald-100 shadow-inner text-sm sm:text-base">
                        🔍
                    </div>
                    Cari Data Pelanggan & Cek Tagihan
                </h3>
                
                <form action="{{ route('finance.invoices.create') }}" method="GET" class="relative z-10">
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-6">
                        <div class="flex-1 relative group/input">
                            <span class="absolute left-4 sm:left-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within/input:text-[#1B8A68] transition-colors text-lg sm:text-xl">👤</span>
                            <input type="text" name="search" id="search" value="{{ $search ?? '' }}" 
                                   class="w-full pl-12 sm:pl-16 pr-4 sm:pr-6 py-4 sm:py-6 bg-[#F8FAFC] border-2 border-transparent rounded-xl sm:rounded-[2rem] focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-base sm:text-lg font-black italic tracking-tight placeholder-gray-300 transition-all shadow-inner" 
                                   placeholder="Nama atau Nomor HP..." required>
                        </div>
                        <button type="submit" class="bg-[#FFC232] hover:bg-[#FFD666] text-gray-900 px-6 sm:px-10 py-4 sm:py-6 rounded-xl sm:rounded-[2rem] font-black text-xs uppercase tracking-[0.15em] sm:tracking-[0.2em] transition-all shadow-xl shadow-amber-100 hover:shadow-amber-200 hover:-translate-y-1 active:scale-95 italic flex items-center justify-center gap-3">
                            Cari Sekarang
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- HASIL PENCARIAN --}}
            @if($search)
                @if(!empty($groupedOrders) && count($groupedOrders) > 0)
                    <div class="mt-8 sm:mt-12 mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div>
                            <h2 class="text-2xl sm:text-4xl font-black text-gray-900 tracking-tighter italic leading-none mb-2 sm:mb-3">
                                Rincian <span class="text-[#1B8A68]">Tagihan</span> Ditemukan
                            </h2>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                                <div class="px-3 sm:px-4 py-1 sm:py-1.5 bg-emerald-50 border border-emerald-100 rounded-full">
                                    <span class="text-[10px] sm:text-[11px] font-black text-[#1B8A68] uppercase tracking-widest italic tracking-tight">{{ $customer->customer_name }}</span>
                                </div>
                                <span class="text-gray-400 text-[10px] sm:text-[11px] font-black uppercase tracking-widest opacity-60 italic">{{ $customer->customer_phone }}</span>
                            </div>
                        </div>
                        <div class="hidden md:flex flex-col items-end">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 opacity-60">Update Terakhir</span>
                            <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                <div class="w-2 h-2 rounded-full bg-[#22AF85] animate-pulse"></div>
                                <span class="text-[10px] font-black text-gray-700 uppercase tracking-widest italic">Sinkronisasi Data Real-time</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('finance.invoices.store') }}" method="POST" id="invoiceForm">
                        @csrf
                        <input type="hidden" name="customer_name" value="{{ $customer->customer_name }}">
                        <input type="hidden" name="customer_phone" value="{{ $customer->customer_phone }}">

                        <div class="space-y-5 sm:space-y-8">
                            @foreach($groupedOrders as $groupKey => $orders)
                                @php
                                    $keyParts = explode('|', $groupKey);
                                    $spkNumber = $keyParts[0];
                                    $dropOffDate = \Carbon\Carbon::parse($keyParts[1])->translatedFormat('d F Y, H:i');
                                @endphp

                                <div class="bg-white rounded-2xl sm:rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden group/card transition-all duration-500 hover:shadow-[#1B8A68]/5">
                                    {{-- Group Header --}}
                                    <div class="bg-gray-50/50 px-5 sm:px-10 py-4 sm:py-6 border-b border-gray-100 flex flex-col sm:flex-row gap-3 sm:gap-0 sm:justify-between sm:items-center relative overflow-hidden">
                                        <div class="absolute inset-y-0 left-0 w-1.5 bg-[#1B8A68]"></div>
                                        <div class="flex items-center gap-3 sm:gap-6">
                                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-xl sm:rounded-2xl flex items-center justify-center shadow-lg border border-gray-100 text-lg sm:text-xl flex-shrink-0">📦</div>
                                            <div class="min-w-0">
                                                <h3 class="font-black text-gray-900 text-sm sm:text-lg italic tracking-tight uppercase leading-none mb-1 sm:mb-1.5 truncate">Kedatangan: {{ $spkNumber }}</h3>
                                                <p class="text-[9px] sm:text-[10px] text-gray-400 font-black uppercase tracking-widest italic">Tanggal Masuk: {{ $dropOffDate }}</p>
                                            </div>
                                        </div>
                                        <label class="flex items-center gap-2 sm:gap-3 cursor-pointer group/toggle pl-14 sm:pl-0">
                                            <input type="checkbox" class="form-checkbox h-5 w-5 sm:h-6 sm:w-6 text-[#1B8A68] rounded-lg border-2 border-gray-200 focus:ring-[#1B8A68]/20 group-checkbox transition-all" data-target=".group-{{ Str::slug($spkNumber) }}">
                                            <span class="text-[10px] sm:text-[11px] font-black text-[#1B8A68] uppercase tracking-widest italic group-hover/toggle:underline transition-all whitespace-nowrap">Pilih Semua</span>
                                        </label>
                                    </div>

                                    {{-- Group Body --}}
                                    <div class="divide-y divide-gray-50">
                                        @foreach($orders as $order)
                                            <div class="group/item relative transition-all duration-300 hover:bg-[#F8FAFC]">
                                                <label class="flex items-start sm:items-center p-5 sm:p-10 cursor-pointer w-full">
                                                    <div class="flex-shrink-0 mr-4 sm:mr-8 relative pt-1 sm:pt-0">
                                                        <input type="checkbox" name="work_order_ids[]" value="{{ $order->id }}" 
                                                               class="form-checkbox h-6 w-6 sm:h-8 sm:w-8 text-[#1B8A68] rounded-lg sm:rounded-xl border-2 border-gray-200 focus:ring-[#1B8A68]/20 order-checkbox group-{{ Str::slug($spkNumber) }} transition-all"
                                                               data-price="{{ $order->total_transaksi }}">
                                                    </div>
                                                    
                                                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-4 gap-3 sm:gap-8 items-start sm:items-center">
                                                        {{-- SPK Info --}}
                                                        <div class="flex items-center gap-3 sm:gap-6">
                                                            <div class="flex flex-col min-w-0">
                                                                <div class="flex items-center gap-2 sm:gap-3 mb-0.5 sm:mb-1">
                                                                    <span class="px-1.5 sm:px-2 py-0.5 bg-emerald-50 text-[#1B8A68] text-[8px] sm:text-[9px] font-black rounded-md sm:rounded-lg border border-emerald-100 uppercase tracking-widest italic flex-shrink-0">ID SPK</span>
                                                                    <p class="font-black text-gray-900 italic tracking-tight truncate text-sm sm:text-base">{{ $order->spk_number }}</p>
                                                                </div>
                                                                <p class="text-[9px] sm:text-[10px] text-gray-400 font-black uppercase tracking-widest italic leading-none opacity-60">{{ $order->category }}</p>
                                                            </div>
                                                            {{-- Label Button --}}
                                                            <div class="flex flex-col items-center flex-shrink-0">
                                                                <span class="text-[7px] sm:text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] italic mb-0.5 sm:mb-1">LABEL</span>
                                                                <a href="{{ route('reception.print-tag', $order->id) }}" target="_blank" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white flex items-center justify-center text-[#1B8A68] shadow-lg border border-emerald-100 hover:border-[#1B8A68]/30 hover:bg-emerald-50 transition-all active:scale-95 group/btn" onclick="event.stopPropagation();">
                                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                                </a>
                                                            </div>
                                                        </div>

                                                        {{-- Shoe Details --}}
                                                        <div class="sm:col-span-2">
                                                            <div class="flex items-center gap-2 sm:gap-3 mb-1 sm:mb-1.5">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-[#1B8A68] flex-shrink-0"></span>
                                                                <p class="font-black text-gray-900 italic tracking-tight text-sm sm:text-lg leading-none uppercase truncate">{{ $order->shoe_brand }} {{ $order->shoe_type }}</p>
                                                            </div>
                                                            <p class="text-[9px] sm:text-[10px] text-gray-400 font-black uppercase tracking-widest italic opacity-80 pl-4 truncate">
                                                                @foreach($order->workOrderServices as $svc)
                                                                    {{ $svc->custom_service_name ?? ($svc->service ? $svc->service->name : 'Layanan') }}
                                                                    @if(!$loop->last) <span class="mx-1 sm:mx-1.5">•</span> @endif
                                                                @endforeach
                                                            </p>
                                                        </div>

                                                        {{-- Price + Status --}}
                                                        <div class="text-left sm:text-right flex sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-2">
                                                            <p class="text-lg sm:text-2xl font-black text-gray-900 tracking-tighter italic tabular-nums leading-none">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</p>
                                                            @if($order->status === \App\Enums\WorkOrderStatus::SELESAI)
                                                                <span class="inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 bg-emerald-50 text-[#1B8A68] text-[8px] sm:text-[9px] font-black rounded-lg sm:rounded-xl border border-emerald-100 uppercase tracking-[0.1em] sm:tracking-[0.2em] italic shadow-sm">Siap Tagih</span>
                                                            @else
                                                                <span class="inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 bg-amber-50 text-[#FFC232] text-[8px] sm:text-[9px] font-black rounded-lg sm:rounded-xl border border-amber-100 uppercase tracking-[0.1em] sm:tracking-[0.2em] italic shadow-sm">{{ $order->status }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- ELITE ACTION BAR (STICKY) - RESPONSIVE VERSION --}}
                        <div class="fixed bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] sm:w-full max-w-3xl sm:px-6 z-50">
                            <div class="bg-gray-900 rounded-2xl sm:rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.5)] p-1 border border-white/10 backdrop-blur-3xl overflow-hidden flex items-center justify-between group/action transition-all hover:scale-[1.01]">
                                <div class="absolute inset-y-0 left-0 w-1.5 bg-[#1B8A68]"></div>
                                
                                <div class="px-3 sm:px-6 py-2.5 sm:py-3 flex items-center gap-3 sm:gap-6 overflow-hidden">
                                    <div class="flex flex-col flex-shrink-0">
                                        <span class="text-[7px] sm:text-[8px] font-black text-white/40 uppercase tracking-[0.15em] sm:tracking-[0.2em] mb-0.5 italic">Item</span>
                                        <div class="flex items-center gap-1 sm:gap-1.5">
                                            <span id="selectedCount" class="text-lg sm:text-xl font-black text-white italic tracking-tighter leading-none">0</span>
                                            <span class="text-[8px] sm:text-[9px] text-white/30 font-black italic hidden sm:inline">Terpilih</span>
                                        </div>
                                    </div>
                                    <div class="h-6 w-px bg-white/10 flex-shrink-0 hidden sm:block"></div>
                                    <div class="flex flex-col relative group/ongkir cursor-text w-20 sm:w-32 flex-shrink-0 hidden sm:flex">
                                        <label for="shipping_cost" class="text-[7px] sm:text-[8px] font-black text-white/40 uppercase tracking-[0.15em] sm:tracking-[0.2em] mb-0.5 italic cursor-text">Ongkir (Rp)</label>
                                        <input type="number" name="shipping_cost" id="shipping_cost" 
                                               class="w-full bg-transparent border-b-2 border-white/10 focus:border-[#1B8A68] text-white font-black italic tracking-tighter text-base sm:text-lg leading-none p-0 focus:ring-0 transition-colors appearance-none" 
                                               placeholder="0" min="0" step="1000">
                                    </div>
                                    <div class="h-6 w-px bg-white/10 flex-shrink-0"></div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-[7px] sm:text-[8px] font-black text-white/40 uppercase tracking-[0.15em] sm:tracking-[0.2em] mb-0.5 italic">Total</span>
                                        <span id="totalSelectionPrice" class="text-base sm:text-xl font-black text-[#1B8A68] italic tracking-tighter tabular-nums leading-none truncate">Rp 0</span>
                                    </div>
                                </div>

                                <button type="submit" id="btnSubmit" disabled 
                                        class="bg-[#FFC232] hover:bg-[#FFD666] text-gray-900 px-4 sm:px-6 py-3 sm:py-4 rounded-xl sm:rounded-2xl font-black text-[9px] sm:text-[10px] uppercase tracking-[0.1em] sm:tracking-[0.2em] italic transition-all disabled:grayscale disabled:opacity-30 disabled:cursor-not-allowed group/btn2 active:scale-95 flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                                    <span class="hidden sm:inline">Simpan & Buat Invoice</span>
                                    <span class="sm:hidden">Simpan</span>
                                    <svg class="w-3 h-3 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                            
                            {{-- Mobile Ongkir Input (shown below action bar on mobile) --}}
                            <div class="sm:hidden mt-2 bg-gray-800/95 backdrop-blur-xl rounded-xl p-3 border border-white/10 flex items-center gap-3">
                                <label for="shipping_cost_mobile" class="text-[8px] font-black text-white/50 uppercase tracking-[0.15em] italic flex-shrink-0">Ongkir</label>
                                <input type="number" id="shipping_cost_mobile" 
                                       class="flex-1 bg-transparent border-b-2 border-white/10 focus:border-[#1B8A68] text-white font-black italic tracking-tighter text-base leading-none p-0 focus:ring-0 transition-colors appearance-none" 
                                       placeholder="Rp 0" min="0" step="1000">
                            </div>
                        </div>
                    </form>
                @else
                    <div class="mt-8 sm:mt-12 bg-white rounded-2xl sm:rounded-[2.5rem] p-10 sm:p-20 text-center border-4 border-dashed border-gray-100 group">
                        <div class="inline-flex items-center justify-center w-20 h-20 sm:w-32 sm:h-32 bg-gray-50 rounded-2xl sm:rounded-[2.5rem] mb-6 sm:mb-8 shadow-inner border border-gray-100 group-hover:scale-110 transition-transform duration-500">
                            <span class="text-4xl sm:text-6xl filter grayscale opacity-20">📋</span>
                        </div>
                        <h3 class="text-xl sm:text-3xl font-black text-gray-900 mb-2 italic tracking-tight uppercase">Data Tidak Ditemukan</h3>
                        <p class="text-gray-400 text-[9px] sm:text-[10px] font-black uppercase tracking-[0.2em] sm:tracking-[0.3em] italic mb-6 sm:mb-8">Tidak ada tagihan tertunda untuk pelanggan ini</p>
                        <div class="max-w-md mx-auto p-4 sm:p-6 bg-amber-50 rounded-xl sm:rounded-2xl border border-amber-100">
                            <p class="text-[10px] sm:text-[11px] text-amber-700 font-bold uppercase tracking-widest leading-relaxed">
                                Mungkin pelanggan tersebut belum memiliki tagihan baru, <br>
                                atau seluruh pesanannya sudah masuk ke dalam Invoice sebelumnya.
                            </p>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Group Checkbox
            const groupCheckboxes = document.querySelectorAll('.group-checkbox');
            groupCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const targetSelector = this.getAttribute('data-target');
                    const childCheckboxes = document.querySelectorAll(targetSelector);
                    childCheckboxes.forEach(child => {
                        child.checked = this.checked;
                    });
                    calculateTotals();
                });
            });

            // Handle Individual Checkbox
            const orderCheckboxes = document.querySelectorAll('.order-checkbox');
            orderCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    calculateTotals();
                });
            });

            // Handle Shipping Cost Input (desktop)
            const shippingCostInput = document.getElementById('shipping_cost');
            if (shippingCostInput) {
                shippingCostInput.addEventListener('input', function() {
                    // Sync to mobile input
                    const mobileInput = document.getElementById('shipping_cost_mobile');
                    if (mobileInput) mobileInput.value = this.value;
                    calculateTotals();
                });
            }

            // Handle Shipping Cost Input (mobile)
            const shippingCostMobile = document.getElementById('shipping_cost_mobile');
            if (shippingCostMobile) {
                shippingCostMobile.addEventListener('input', function() {
                    // Sync to desktop hidden input
                    if (shippingCostInput) shippingCostInput.value = this.value;
                    calculateTotals();
                });
            }

            function calculateTotals() {
                let count = 0;
                let total = 0;
                
                document.querySelectorAll('.order-checkbox:checked').forEach(cb => {
                    count++;
                    total += parseFloat(cb.getAttribute('data-price') || 0);
                });

                // Add shipping cost (from whichever input has a value)
                const shippingCost = parseFloat((shippingCostInput ? shippingCostInput.value : 0) || 0);
                total += shippingCost;

                document.getElementById('selectedCount').innerText = count;
                document.getElementById('totalSelectionPrice').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(total).replace('IDR', 'Rp');
                
                const btnSubmit = document.getElementById('btnSubmit');
                if (count > 0) {
                    btnSubmit.removeAttribute('disabled');
                } else {
                    btnSubmit.setAttribute('disabled', 'disabled');
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
