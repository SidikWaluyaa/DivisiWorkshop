<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC] pb-32">
        {{-- Elite Sticky Header --}}
        <div class="bg-white/80 shadow-2xl border-b border-gray-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-6 py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <a href="{{ route('finance.invoices.index') }}" class="group flex items-center justify-center w-12 h-12 bg-white rounded-2xl shadow-lg border border-gray-100 hover:border-[#1B8A68]/30 hover:shadow-emerald-100 transition-all active:scale-95">
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-[#1B8A68] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="bg-[#1B8A68] text-white text-[10px] font-black px-2 py-0.5 rounded-md uppercase tracking-widest shadow-lg shadow-emerald-100 italic">TRANSAKSI</span>
                                <h1 class="text-2xl font-black text-gray-900 tracking-tight italic">Buat Invoice Gabungan</h1>
                            </div>
                            <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                Inventarisasi Tagihan Belum Terbit
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 mt-10">
            {{-- PENCARIAN PELANGGAN --}}
            <div class="bg-white rounded-[2.5rem] shadow-2xl p-10 border border-gray-100 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#1B8A68]/5 rounded-bl-[5rem] -mr-8 -mt-8 transition-transform group-hover:scale-110 duration-700"></div>
                
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] mb-8 flex items-center gap-3 italic">
                    <div class="w-8 h-8 bg-emerald-50 rounded-xl flex items-center justify-center border border-emerald-100 shadow-inner">
                        🔍
                    </div>
                    Cari Data Pelanggan & Cek Tagihan
                </h3>
                
                <form action="{{ route('finance.invoices.create') }}" method="GET" class="relative z-10">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex-1 relative group/input">
                            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within/input:text-[#1B8A68] transition-colors text-xl">👤</span>
                            <input type="text" name="search" id="search" value="{{ $search ?? '' }}" 
                                   class="w-full pl-16 pr-6 py-6 bg-[#F8FAFC] border-2 border-transparent rounded-[2rem] focus:bg-white focus:border-[#1B8A68]/20 focus:ring-4 focus:ring-[#1B8A68]/5 text-lg font-black italic tracking-tight placeholder-gray-300 transition-all shadow-inner" 
                                   placeholder="Masukkan Nama atau Nomor HP Pelanggan..." required>
                        </div>
                        <button type="submit" class="bg-[#FFC232] hover:bg-[#FFD666] text-gray-900 px-10 py-6 rounded-[2rem] font-black text-xs uppercase tracking-[0.2em] transition-all shadow-xl shadow-amber-100 hover:shadow-amber-200 hover:-translate-y-1 active:scale-95 italic flex items-center justify-center gap-3">
                            Cari Sekarang
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- HASIL PENCARIAN --}}
            @if($search)
                @if(!empty($groupedOrders) && count($groupedOrders) > 0)
                    <div class="mt-12 mb-8 flex items-end justify-between">
                        <div>
                            <h2 class="text-4xl font-black text-gray-900 tracking-tighter italic leading-none mb-3">
                                Rincian <span class="text-[#1B8A68]">Tagihan</span> Ditemukan
                            </h2>
                            <div class="flex items-center gap-4">
                                <div class="px-4 py-1.5 bg-emerald-50 border border-emerald-100 rounded-full">
                                    <span class="text-[11px] font-black text-[#1B8A68] uppercase tracking-widest italic tracking-tight">{{ $customer->customer_name }}</span>
                                </div>
                                <span class="text-gray-400 text-[11px] font-black uppercase tracking-widest opacity-60 italic">{{ $customer->customer_phone }}</span>
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

                        <div class="space-y-8">
                            @foreach($groupedOrders as $groupKey => $orders)
                                @php
                                    $keyParts = explode('|', $groupKey);
                                    $spkNumber = $keyParts[0];
                                    $dropOffDate = \Carbon\Carbon::parse($keyParts[1])->translatedFormat('d F Y, H:i');
                                @endphp

                                <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden group/card transition-all duration-500 hover:shadow-[#1B8A68]/5">
                                    {{-- Group Header --}}
                                    <div class="bg-gray-50/50 px-10 py-6 border-b border-gray-100 flex justify-between items-center relative overflow-hidden">
                                        <div class="absolute inset-y-0 left-0 w-1.5 bg-[#1B8A68]"></div>
                                        <div class="flex items-center gap-6">
                                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-lg border border-gray-100 text-xl">📦</div>
                                            <div>
                                                <h3 class="font-black text-gray-900 text-lg italic tracking-tight uppercase leading-none mb-1.5">Kedatangan: {{ $spkNumber }}</h3>
                                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic">Tanggal Masuk: {{ $dropOffDate }}</p>
                                            </div>
                                        </div>
                                        <label class="flex items-center gap-3 cursor-pointer group/toggle">
                                            <input type="checkbox" class="form-checkbox h-6 w-6 text-[#1B8A68] rounded-lg border-2 border-gray-200 focus:ring-[#1B8A68]/20 group-checkbox transition-all" data-target=".group-{{ Str::slug($spkNumber) }}">
                                            <span class="text-[11px] font-black text-[#1B8A68] uppercase tracking-widest italic group-hover/toggle:underline transition-all">Pilih Semua di Sesi Ini</span>
                                        </label>
                                    </div>

                                    {{-- Group Body --}}
                                    <div class="divide-y divide-gray-50">
                                        @foreach($orders as $order)
                                            <div class="group/item relative transition-all duration-300 hover:bg-[#F8FAFC]">
                                                <label class="flex items-center p-10 cursor-pointer w-full">
                                                    <div class="flex-shrink-0 mr-8 relative">
                                                        <input type="checkbox" name="work_order_ids[]" value="{{ $order->id }}" 
                                                               class="form-checkbox h-8 w-8 text-[#1B8A68] rounded-xl border-2 border-gray-200 focus:ring-[#1B8A68]/20 order-checkbox group-{{ Str::slug($spkNumber) }} transition-all"
                                                               data-price="{{ $order->total_transaksi }}">
                                                    </div>
                                                    
                                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-8 items-center">
                                                        <div class="flex items-center gap-6">
                                                            <div class="flex flex-col">
                                                                <div class="flex items-center gap-3 mb-1">
                                                                    <span class="px-2 py-0.5 bg-emerald-50 text-[#1B8A68] text-[9px] font-black rounded-lg border border-emerald-100 uppercase tracking-widest italic">ID SPK</span>
                                                                    <p class="font-black text-gray-900 italic tracking-tight truncate">{{ $order->spk_number }}</p>
                                                                </div>
                                                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic leading-none opacity-60">{{ $order->category }}</p>
                                                            </div>
                                                            <!-- Label Button -->
                                                            <div class="flex flex-col items-center">
                                                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] italic mb-1">LABEL</span>
                                                                <a href="{{ route('reception.print-tag', $order->id) }}" target="_blank" class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-[#1B8A68] shadow-lg border border-emerald-100 hover:border-[#1B8A68]/30 hover:bg-emerald-50 transition-all active:scale-95 group/btn">
                                                                    <svg class="w-4 h-4 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                                </a>
                                                            </div>
                                                        </div>

                                                        <div class="md:col-span-2">
                                                            <div class="flex items-center gap-3 mb-1.5">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-[#1B8A68]"></span>
                                                                <p class="font-black text-gray-900 italic tracking-tight text-lg leading-none uppercase">{{ $order->shoe_brand }} {{ $order->shoe_type }}</p>
                                                            </div>
                                                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic opacity-80 pl-4">
                                                                @foreach($order->workOrderServices as $svc)
                                                                    {{ $svc->custom_service_name ?? ($svc->service ? $svc->service->name : 'Layanan') }}
                                                                    @if(!$loop->last) <span class="mx-1.5">•</span> @endif
                                                                @endforeach
                                                            </p>
                                                        </div>

                                                        <div class="text-right">
                                                            <p class="text-2xl font-black text-gray-900 tracking-tighter italic tabular-nums leading-none mb-2">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</p>
                                                            @if($order->status === \App\Enums\WorkOrderStatus::SELESAI)
                                                                <span class="inline-flex items-center px-3 py-1 bg-emerald-50 text-[#1B8A68] text-[9px] font-black rounded-xl border border-emerald-100 uppercase tracking-[0.2em] italic shadow-sm">Siap Tagih</span>
                                                            @else
                                                                <span class="inline-flex items-center px-3 py-1 bg-amber-50 text-[#FFC232] text-[9px] font-black rounded-xl border border-amber-100 uppercase tracking-[0.2em] italic shadow-sm">{{ $order->status }}</span>
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

                        {{-- ELITE ACTION BAR (STIKY) - COMPACT VERSION --}}
                        <div class="fixed bottom-6 left-1/2 -translate-x-1/2 w-full max-w-3xl px-6 z-50">
                            <div class="bg-gray-900 rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.5)] p-1 border border-white/10 backdrop-blur-3xl overflow-hidden flex items-center justify-between group/action transition-all hover:scale-[1.01]">
                                <div class="absolute inset-y-0 left-0 w-1.5 bg-[#1B8A68]"></div>
                                
                                <div class="px-6 py-3 flex items-center gap-6">
                                    <div class="flex flex-col">
                                        <span class="text-[8px] font-black text-white/40 uppercase tracking-[0.2em] mb-0.5 italic">Item</span>
                                        <div class="flex items-center gap-1.5">
                                            <span id="selectedCount" class="text-xl font-black text-white italic tracking-tighter leading-none">0</span>
                                            <span class="text-[9px] text-white/30 font-black italic">Terpilih</span>
                                        </div>
                                    </div>
                                    <div class="h-6 w-px bg-white/10"></div>
                                    <div class="flex flex-col">
                                        <span class="text-[8px] font-black text-white/40 uppercase tracking-[0.2em] mb-0.5 italic">Total Tagihan</span>
                                        <span id="totalSelectionPrice" class="text-xl font-black text-[#1B8A68] italic tracking-tighter tabular-nums leading-none">Rp 0</span>
                                    </div>
                                </div>

                                <button type="submit" id="btnSubmit" disabled 
                                        class="bg-[#FFC232] hover:bg-[#FFD666] text-gray-900 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] italic transition-all disabled:grayscale disabled:opacity-30 disabled:cursor-not-allowed group/btn2 active:scale-95 flex items-center gap-2">
                                    <span>Simpan & Buat Invoice</span>
                                    <svg class="w-3 h-3 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="mt-12 bg-white rounded-[2.5rem] p-20 text-center border-4 border-dashed border-gray-100 group">
                        <div class="inline-flex items-center justify-center w-32 h-32 bg-gray-50 rounded-[2.5rem] mb-8 shadow-inner border border-gray-100 group-hover:scale-110 transition-transform duration-500">
                            <span class="text-6xl filter grayscale opacity-20">📋</span>
                        </div>
                        <h3 class="text-3xl font-black text-gray-900 mb-2 italic tracking-tight uppercase">Data Tidak Ditemukan</h3>
                        <p class="text-gray-400 text-[10px] font-black uppercase tracking-[0.3em] italic mb-8">Tidak ada tagihan tertunda untuk pelanggan ini</p>
                        <div class="max-w-md mx-auto p-6 bg-amber-50 rounded-2xl border border-amber-100">
                            <p class="text-[11px] text-amber-700 font-bold uppercase tracking-widest leading-relaxed">
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

            function calculateTotals() {
                let count = 0;
                let total = 0;
                
                document.querySelectorAll('.order-checkbox:checked').forEach(cb => {
                    count++;
                    total += parseFloat(cb.getAttribute('data-price') || 0);
                });

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
