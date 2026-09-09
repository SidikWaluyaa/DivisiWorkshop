<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-teal-100">Order Detail:</span>
            <span class="font-mono bg-white/20 px-2.5 py-0.5 rounded-lg border border-white/30 text-white font-black text-sm tracking-wide">{{ $order->spk_number }}</span>
        </div>
    </x-slot>

    @php
        $isLunas = ($order->invoice && $order->invoice->status === 'Lunas') || (!$order->invoice && in_array($order->status_pembayaran, ['L', 'Lunas']));
        $shippingCost = (float)($order->invoice ? $order->invoice->shipping_cost : $order->shipping_cost);
        $totalServicesCost = (float)($order->workOrderServices->sum(fn($w) => (float)($w->cost ?: ($w->service->price ?? 0))));
        $totalBill = (float)($order->invoice ? $order->invoice->total_amount : ($order->total_amount ?: ($totalServicesCost + $shippingCost)));
        $remainingBill = (float)($order->invoice ? $order->invoice->remaining_amount : ($isLunas ? 0 : $totalBill));

        // Workshop Team Names
        $sortir = $order->picSortirSol->name ?? $order->picSortirUpper->name ?? '-';
        $prep = $order->prepWashingBy->name ?? $order->prepSolBy->name ?? $order->prepUpperBy->name ?? '-';
        if ($prep === 'Ai' || $prep === 'Ai QC') {
            $prep = 'Fikri';
        }
        $produksi = $order->prodSolBy->name ?? $order->prodUpperBy->name ?? $order->prodCleaningBy->name ?? $order->technicianProduction->name ?? '-';
        $qc = $order->qcFinalBy->name ?? $order->qcFinalPic->name ?? $order->qcCleanupBy->name ?? $order->qcJahitBy->name ?? '-';
    @endphp

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('finishShowApp', (initialServices) => ({
                openOtoModal: false,
                showRevisionModal: false,
                selectedOto: [],
                validDays: 3,
                validUntil: '',
                sendAutomation: '1',
                services: initialServices || [],
                searchOto: '',
                
                init() {
                    this.updateOtoDate();
                },
                
                toggleOto(id) {
                    const idx = this.selectedOto.findIndex(s => s.id === id);
                    if (idx > -1) {
                        this.selectedOto.splice(idx, 1);
                    } else {
                        const s = this.services.find(x => x.id === id);
                        if (s) {
                            const suggestedNormal = Math.ceil((s.price * 1.2) / 5000) * 5000;
                            this.selectedOto.push({ 
                                id: s.id, 
                                oto_price: s.price, 
                                normal_price: suggestedNormal,
                                hk_days: s.hk_days || 0,
                                name: s.name 
                            });
                        }
                    }
                },
                
                isSelectedOto(id) {
                    return this.selectedOto.some(s => s.id === id);
                },
                
                getSelectedOto(id) {
                    return this.selectedOto.find(s => s.id === id) || { oto_price: 0, normal_price: 0, hk_days: 0 };
                },
                
                setOtoDays(d) {
                    this.validDays = d;
                    this.updateOtoDate();
                },
                
                updateOtoDate() {
                    try {
                        const d = new Date();
                        const days = parseInt(this.validDays) || 0;
                        d.setDate(d.getDate() + days);
                        const m = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                        this.validUntil = d.getDate() + ' ' + m[d.getMonth()] + ' ' + d.getFullYear();
                    } catch (e) { 
                        this.validUntil = '-'; 
                    }
                },
                
                get totalOto() {
                    return this.selectedOto.reduce((a, b) => a + (Number(b.oto_price) || 0), 0);
                },
                
                get totalNormalOto() {
                    return this.selectedOto.reduce((a, b) => a + (Number(b.normal_price) || 0), 0);
                },

                get totalHkOto() {
                    return this.selectedOto.reduce((a, b) => a + (Number(b.hk_days) || 0), 0);
                },

                moneyFormat(val) {
                    return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
                },

                matchesSearchOto(name) {
                    if (!this.searchOto) return true;
                    return name.toLowerCase().includes(this.searchOto.toLowerCase());
                },

                get hasMatchesOto() {
                    if (!this.searchOto) return true;
                    return this.services.some(s => this.matchesSearchOto(s.name));
                }
            }));
        });

        window.confirmPickup = function(orderId, isBypass = false) {
            if (typeof Swal === 'undefined') {
                alert("Error: SweetAlert2 is not loaded. Please refresh the page.");
                return;
            }

            Swal.fire({
                title: isBypass ? 'Bypass Pengambilan (Belum Lunas)' : 'Konfirmasi Ambil Langsung',
                icon: isBypass ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isBypass ? '#f59e0b' : '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: isBypass ? 'Ya, Bypass & Ambil!' : 'Konfirmasi Ambil',
                cancelButtonText: 'Batal',
                html: `
                    <div class="text-left space-y-4 mt-2">
                        ${ isBypass ? '<div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 font-bold mb-2">⚠️ Peringatan: SPK ini belum lunas. Melanjutkan akan mem-bypass validasi kasir.</div>' : '' }
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase tracking-wider">Nama Pengambil / Keterangan <span class="text-red-500">*</span></label>
                            <input id="swal-pickup-method" type="text" value="Offline (Diambil Langsung)" placeholder="Nama pengambil / Customer sendiri..."
                                   class="swal2-input !w-full !m-0 !text-sm !rounded-xl">
                        </div>
                    </div>
                `,
                preConfirm: () => {
                    const method = document.getElementById('swal-pickup-method').value.trim();
                    if (!method) {
                        Swal.showValidationMessage('Nama pengambil / metode pengambilan harus diisi!');
                        return false;
                    }
                    return { pickup_method: method };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { pickup_method } = result.value;
                    const dynamicForm = document.createElement('form');
                    dynamicForm.method = 'POST';
                    dynamicForm.action = `/finish/${orderId}/pickup`;
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden'; csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    dynamicForm.appendChild(csrf);
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden'; methodInput.name = 'pickup_method';
                    methodInput.value = pickup_method;
                    dynamicForm.appendChild(methodInput);

                    if (isBypass) {
                        const notesInput = document.createElement('input');
                        notesInput.type = 'hidden'; notesInput.name = 'notes';
                        notesInput.value = 'BYPASS PEMBAYARAN: Diambil langsung oleh customer meskipun belum lunas.';
                        dynamicForm.appendChild(notesInput);
                    }
                    
                    document.body.appendChild(dynamicForm);
                    dynamicForm.submit();
                }
            });
        };
    </script>
    @endpush

    <div class="py-6" x-data="finishShowApp(@js($services))">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Top Navigation & Breadcrumbs Bar --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xs">
                <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('finish.index') }}" class="hover:text-teal-600 transition-colors">Workshop</a>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('finish.index') }}" class="hover:text-teal-600 transition-colors">Gudang & Finish</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 dark:text-white font-bold">{{ $order->spk_number }}</span>
                </nav>
                <div class="flex items-center gap-2">
                    <a href="{{ url('/track?spk=' . $order->spk_number) }}" target="_blank" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-slate-700 dark:text-gray-200 text-xs font-bold rounded-xl transition-all shadow-xs flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Lacak SPK</span>
                    </a>
                    <a href="{{ route('finish.index') }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-teal-600/20 flex items-center gap-1.5 active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali ke Gudang</span>
                    </a>
                </div>
            </div>

            {{-- MAIN 2-COLUMN GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                {{-- LEFT COLUMN: Order Info, Billing, Services & Actions (lg:col-span-7) --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- 1. Hero Customer & SPK Header Card --}}
                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-teal-700 via-teal-600 to-emerald-700 p-6 md:p-8 text-white shadow-xl shadow-teal-900/15 border border-teal-500/30">
                        <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
                        <div class="absolute -left-10 -top-10 w-48 h-48 rounded-full bg-emerald-400/20 blur-2xl pointer-events-none"></div>

                        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[11px] font-black uppercase tracking-wider text-teal-100 border border-white/20">
                                        <span>📍 {{ $order->current_location ?? 'Gudang Selesai' }}</span>
                                    </span>
                                    @if($order->has_active_oto)
                                        <span class="px-2.5 py-1 bg-amber-500 text-white font-black text-[10px] uppercase tracking-wider rounded-full shadow-xs animate-pulse">
                                            🔥 Ada OTO Aktif
                                        </span>
                                    @endif
                                </div>
                                <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white flex items-center gap-2">
                                    <span>{{ $order->customer_name ?? ($order->customer->name ?? 'Customer') }}</span>
                                </h1>
                                <div class="flex items-center gap-3 text-sm text-teal-100 font-medium">
                                    @if($order->customer_phone || ($order->customer->phone ?? false))
                                        @php
                                            $rawPhone = $order->customer_phone ?: ($order->customer->phone ?? '');
                                            $waPhone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $rawPhone));
                                        @endphp
                                        <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="inline-flex items-center gap-1 hover:text-white transition-colors bg-white/10 hover:bg-white/20 px-2.5 py-0.5 rounded-lg">
                                            <span>💬 {{ $rawPhone }}</span>
                                        </a>
                                    @endif
                                    @if($order->customer)
                                        <a href="{{ route('admin.customers.show', $order->customer->id) }}" class="inline-flex items-center gap-1 text-[11px] font-bold bg-white/20 hover:bg-white/30 px-2.5 py-1 rounded-lg text-white transition-all">
                                            <span>👤 Profil</span>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col items-start md:items-end gap-2 bg-black/20 backdrop-blur-md p-4 rounded-2xl border border-white/15">
                                <span class="text-[10px] uppercase font-bold text-teal-200 tracking-wider">Identitas Sepatu</span>
                                <div class="text-base font-black text-white flex items-center gap-1.5">
                                    <span>👟 {{ $order->shoe_brand ?? '-' }}</span>
                                </div>
                                <span class="text-xs text-teal-100 font-semibold">{{ $order->shoe_color ?? '-' }} @if($order->shoe_size) (Size {{ $order->shoe_size }}) @endif</span>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Financial & Warehouse Placement Cards --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        {{-- Card A: Status Pembayaran --}}
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Status Kasir / Billing</span>
                                    @if($isLunas)
                                        <span class="px-2.5 py-1 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-black text-[10px] rounded-lg border border-emerald-200 dark:border-emerald-800 uppercase">
                                            ✓ LUNAS
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 bg-rose-100 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 font-black text-[10px] rounded-lg border border-rose-200 dark:border-rose-800 uppercase animate-pulse">
                                            BELUM LUNAS
                                        </span>
                                    @endif
                                </div>
                                <div class="text-2xl font-black text-gray-900 dark:text-white">
                                    Rp {{ number_format($totalBill, 0, ',', '.') }}
                                </div>
                                @if(!$isLunas && $remainingBill > 0)
                                    <p class="text-xs font-bold text-rose-600 dark:text-rose-400 mt-0.5">
                                        Sisa: Rp {{ number_format($remainingBill, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>
                            @if($shippingCost > 0)
                                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-xs">
                                    <span class="text-gray-500 font-medium">📦 Ongkos Kirim:</span>
                                    <span class="font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Card B: Lokasi Rak Gudang --}}
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Rak Penyimpanan Gudang</span>
                                    @if($order->storage_rack_code)
                                        <span class="px-2 py-0.5 bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 font-black text-[10px] rounded-md border border-teal-200 dark:border-teal-800 uppercase">
                                            TERSIMPAN
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 font-black text-[10px] rounded-md border border-amber-200 dark:border-amber-800 uppercase">
                                            BELUM DI RAK
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xl font-black {{ $order->storage_rack_code ? 'text-teal-600 dark:text-teal-400 font-mono' : 'text-amber-600 dark:text-amber-400' }}">
                                    {{ $order->storage_rack_code ?: 'Menunggu Masuk Rak' }}
                                </div>
                                @if($order->storage_date)
                                    <p class="text-[10px] text-gray-400 mt-0.5 font-medium">
                                        Disimpan: {{ \Carbon\Carbon::parse($order->storage_date)->format('d M Y H:i') }}
                                    </p>
                                @endif
                            </div>
                            
                            {{-- Storage Button --}}
                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                <button type="button" 
                                        @click="$dispatch('storage-modal', { 
                                            workOrderId: {{ $order->id }},
                                            accessories: {
                                                tali: '{{ $order->accessories_tali }}',
                                                insole: '{{ $order->accessories_insole }}',
                                                box: '{{ $order->accessories_box }}',
                                                other: '{{ addslashes($order->accessories_other) }}'
                                            }
                                        })"
                                        class="w-full py-1.5 px-3 bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/30 dark:hover:bg-teal-900/50 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                    <span>📦</span>
                                    <span>{{ $order->storage_rack_code ? 'Ganti / Pindah Rak' : 'Simpan ke Gudang' }}</span>
                                </button>
                            </div>
                        </div>

                    </div>

                    {{-- 3. Rincian Layanan SPK Card --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="font-black text-xs uppercase tracking-wider text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                <span>🛠️</span> Rincian Paket & Layanan SPK
                            </h3>
                            <span class="text-xs font-bold text-gray-400">
                                {{ $order->workOrderServices->count() }} Layanan Terdaftar
                            </span>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($order->workOrderServices as $wos)
                                @php
                                    $itemCost = (float)($wos->cost ?: ($wos->service->price ?? 0));
                                @endphp
                                <div class="p-4 flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-gray-700 flex items-center justify-center text-xs font-black text-slate-600 dark:text-gray-300 shrink-0">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-xs text-gray-800 dark:text-gray-200 truncate">
                                                {{ $wos->custom_service_name ?: ($wos->service->name ?? 'Layanan Khusus') }}
                                            </p>
                                            <div class="flex items-center gap-2 text-[10px] text-gray-400 font-semibold">
                                                <span>Kategori: {{ $wos->category_name ?: ($wos->service->category ?? '-') }}</span>
                                                @if($wos->service && $wos->service->hk_days)
                                                    <span>• HK: {{ $wos->service->hk_days }} Hari</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="font-black text-xs text-gray-900 dark:text-white">
                                            Rp {{ number_format($itemCost, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-400 text-xs font-medium">
                                    Tidak ada data layanan spesifik pada SPK ini.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 4. Action Hub: Pengambilan & Penawaran OTO / Revisi --}}
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-200 dark:border-gray-700 shadow-md space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                            <h3 class="font-black text-xs uppercase tracking-wider text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                <span>🎯</span> Aksi Pengambilan & Pengelolaan SPK
                            </h3>
                            <span class="text-[10px] font-bold text-gray-400">Eksekusi Status</span>
                        </div>

                        @if(is_null($order->taken_date))
                            {{-- Action Group A: Opsi Pengambilan (Selaras dengan /finish) --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                
                                {{-- 1. Ambil Langsung (Offline) --}}
                                <div>
                                    <button type="button" 
                                            @if($isLunas) onclick="window.confirmPickup('{{ $order->id }}', false)" @else onclick="window.confirmPickup('{{ $order->id }}', true)" @endif
                                            class="w-full py-3.5 px-4 {{ $isLunas ? 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-emerald-500/20' : 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/20' }} text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                                        <span>🛍️</span>
                                        <span>{{ $isLunas ? 'Ambil Langsung' : 'Ambil Langsung (Bypass)' }}</span>
                                    </button>
                                </div>

                                {{-- 2. Ambil Pengiriman (Delivery) --}}
                                <div>
                                    <button type="button" 
                                            @click="$dispatch('shipping-modal', { workOrderId: {{ $order->id }}, isBypass: {{ $isLunas ? 'false' : 'true' }} })"
                                            class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-blue-500/20 transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                                        <span>🚚</span>
                                        <span>{{ $isLunas ? 'Ambil Pengiriman' : 'Ambil Pengiriman (Bypass)' }}</span>
                                    </button>
                                </div>

                            </div>

                            {{-- Action Group B: OTO & Revisi Teknik --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                
                                {{-- 3. Buat Penawaran OTO --}}
                                <button type="button" 
                                        @click="openOtoModal = true" 
                                        class="w-full py-3 px-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold text-xs rounded-xl shadow-md transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                                    <span>✨</span>
                                    <span>Buat Penawaran OTO</span>
                                </button>

                                {{-- 4. Ajukan Revisi Teknik --}}
                                <button type="button" 
                                        @click="showRevisionModal = true" 
                                        class="w-full py-3 px-4 bg-white dark:bg-gray-700 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 font-bold text-xs rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                                    <span>⚠️</span>
                                    <span>Ajukan Revisi Teknik</span>
                                </button>

                            </div>
                        @else
                            {{-- SUDAH DIAMBIL STATE --}}
                            <div class="p-5 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300 flex items-center justify-center text-2xl shrink-0">
                                        ✅
                                    </div>
                                    <div>
                                        <h4 class="font-black text-sm text-emerald-800 dark:text-emerald-300 uppercase tracking-wide">Barang Sudah Diambil Customer</h4>
                                        <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                            Waktu: <span class="font-bold">{{ $order->taken_date ? \Carbon\Carbon::parse($order->taken_date)->format('d M Y H:i') : '-' }}</span> 
                                            • Metode: <span class="font-bold">{{ $order->pickup_method ?? 'Offline' }}</span>
                                        </p>
                                    </div>
                                </div>
                                
                                @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                                    <form action="{{ route('finish.cancel-pickup', $order->id) }}" method="POST" onsubmit="return confirm('Batalkan status pengambilan untuk SPK ini?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition-all shadow-xs shrink-0 cursor-pointer">
                                            Batalkan Ambil
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif

                    </div>

                    {{-- 5. Dokumentasi Foto Selesai --}}
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="font-black text-xs uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                            <span>📸</span> Dokumentasi Foto Selesai (Finish)
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <x-photo-uploader :order="$order" step="FINISH_BEFORE" title="Foto Sebelum (Finish)" />
                            <x-photo-uploader :order="$order" step="FINISH_AFTER" title="Foto Sesudah (Finish)" />
                            <x-photo-uploader :order="$order" step="FINISH" title="Foto Hasil Akhir (Automation)" />
                        </div>
                    </div>

                    {{-- 6. Riwayat Revisi (Jika Pernah Ada Revisi) --}}
                    @if($order->revisions && $order->revisions->count() > 0)
                        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
                            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                <h3 class="text-xs font-black uppercase tracking-wider text-rose-600 flex items-center gap-2">
                                    <span>⚠️</span> Riwayat Revisi SPK
                                </h3>
                                <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-black rounded-md">
                                    {{ $order->revisions->count() }} Kejadian
                                </span>
                            </div>
                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($order->revisions()->orderBy('created_at', 'desc')->get() as $rev)
                                    <div class="p-5 space-y-3">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $rev->creator->name ?? 'System' }}</p>
                                                <p class="text-[10px] text-gray-400 font-semibold">{{ $rev->created_at->format('d M Y H:i') }}</p>
                                            </div>
                                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $rev->status === 'OPEN' ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                                                {{ $rev->status }}
                                            </span>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-3 border border-gray-100 dark:border-gray-700 text-xs text-gray-700 dark:text-gray-300 italic">
                                            "{{ $rev->description }}"
                                        </div>
                                        @if($rev->resolved_by)
                                            <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                                                <span>✓ Diselesaikan oleh {{ $rev->resolver->name ?? 'Teknisi' }} ({{ $rev->finished_at ? $rev->finished_at->format('d M Y H:i') : '-' }})</span>
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                {{-- RIGHT COLUMN: Workshop Team & Full Activity Timeline (lg:col-span-5) --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- 1. Summary Tim Workshop --}}
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-3xl p-6 border border-gray-200 dark:border-gray-700">
                        <h3 class="font-black text-gray-400 dark:text-gray-500 mb-6 uppercase text-[10px] tracking-[0.2em] flex items-center gap-2">
                            <span>👥</span> Petugas Stasiun Workshop
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            
                            {{-- Sortir --}}
                            <div class="p-3.5 bg-slate-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-400">Sortir</span>
                                </div>
                                <p class="text-xs font-black text-gray-800 dark:text-gray-200 truncate">{{ $sortir }}</p>
                            </div>

                            {{-- Preparation --}}
                            <div class="p-3.5 bg-slate-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-400">Preparation</span>
                                </div>
                                <p class="text-xs font-black text-gray-800 dark:text-gray-200 truncate">{{ $prep }}</p>
                            </div>

                            {{-- Produksi --}}
                            <div class="p-3.5 bg-slate-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-400">Produksi</span>
                                </div>
                                <p class="text-xs font-black text-gray-800 dark:text-gray-200 truncate">{{ $produksi }}</p>
                            </div>

                            {{-- QC --}}
                            <div class="p-3.5 bg-slate-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-400">Quality Control</span>
                                </div>
                                <p class="text-xs font-black text-teal-600 dark:text-teal-400 truncate">{{ $qc }}</p>
                            </div>

                        </div>
                    </div>

                    {{-- 2. Workshop Activity Timeline (Chronological: Awal / Pending ➔ Selesai) --}}
                    <div class="bg-white dark:bg-gray-800 shadow-md rounded-3xl p-6 border border-gray-200 dark:border-gray-700 space-y-5">
                        
                        <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-3">
                            <h3 class="font-black text-xs uppercase tracking-wider text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                <span>⏱️</span> Workshop Activity Timeline
                            </h3>
                            <span class="px-2.5 py-0.5 bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 text-[10px] font-black rounded-full border border-teal-200 dark:border-teal-800">
                                {{ $order->logs->count() }} Aktivitas (Kronologis)
                            </span>
                        </div>

                        {{-- Real-time Audit Logs Feed Ordered from Oldest to Newest (SPK_PENDING / Dibuat ➔ Selesai) --}}
                        <div class="space-y-4 max-h-[600px] overflow-y-auto pr-1">
                            @php
                                $orderedLogs = $order->logs()->with('user')->orderBy('created_at', 'asc')->orderBy('id', 'asc')->get();
                            @endphp
                            @forelse($orderedLogs as $log)
                                @php
                                    $stepUpper = strtoupper($log->step ?? '');
                                    $dotBg = match(true) {
                                        str_contains($stepUpper, 'PENDING') || str_contains($stepUpper, 'CS') => 'bg-purple-500 ring-purple-100 dark:ring-purple-950',
                                        str_contains($stepUpper, 'SORTIR') => 'bg-sky-500 ring-sky-100 dark:ring-sky-950',
                                        str_contains($stepUpper, 'PREP') => 'bg-amber-500 ring-amber-100 dark:ring-amber-950',
                                        str_contains($stepUpper, 'PROD') => 'bg-blue-500 ring-blue-100 dark:ring-blue-950',
                                        str_contains($stepUpper, 'HANDOVER') || str_contains($stepUpper, 'SURAT') => 'bg-slate-500 ring-slate-100 dark:ring-slate-900',
                                        str_contains($stepUpper, 'QC') => 'bg-teal-500 ring-teal-100 dark:ring-teal-950',
                                        str_contains($stepUpper, 'OUTBOUND') => 'bg-cyan-500 ring-cyan-100 dark:ring-cyan-950',
                                        str_contains($stepUpper, 'OTO') => 'bg-orange-500 ring-orange-100 dark:ring-orange-950',
                                        str_contains($stepUpper, 'REVIS') => 'bg-rose-500 ring-rose-100 dark:ring-rose-950',
                                        str_contains($stepUpper, 'FINISH') || str_contains($stepUpper, 'STORAGE') || str_contains($stepUpper, 'GUDANG') || str_contains($stepUpper, 'SELESAI') => 'bg-emerald-500 ring-emerald-100 dark:ring-emerald-950',
                                        default => 'bg-slate-400 ring-slate-100 dark:ring-slate-800',
                                    };

                                    $badgeClass = match(true) {
                                        str_contains($stepUpper, 'PENDING') || str_contains($stepUpper, 'CS') => 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                        str_contains($stepUpper, 'SORTIR') => 'bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-800',
                                        str_contains($stepUpper, 'PREP') => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                        str_contains($stepUpper, 'PROD') => 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                                        str_contains($stepUpper, 'HANDOVER') || str_contains($stepUpper, 'SURAT') => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                        str_contains($stepUpper, 'QC') => 'bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300 border-teal-200 dark:border-teal-800',
                                        str_contains($stepUpper, 'OUTBOUND') => 'bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800',
                                        str_contains($stepUpper, 'OTO') => 'bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 border-orange-200 dark:border-orange-800',
                                        str_contains($stepUpper, 'REVIS') => 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                        str_contains($stepUpper, 'FINISH') || str_contains($stepUpper, 'STORAGE') || str_contains($stepUpper, 'GUDANG') || str_contains($stepUpper, 'SELESAI') => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                        default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600',
                                    };

                                    $displayStep = match(strtoupper($log->step ?? '')) {
                                        'SPK_PENDING' => 'SPK DIBUAT (PENDING)',
                                        'CS_INTAKE' => 'CS INTAKE',
                                        'HANDOVER' => 'SERAH TERIMA (SJ)',
                                        'STAGING_OUTBOUND' => 'STAGING OUTBOUND',
                                        'OUTBOUND_QC' => 'MANIFEST OUTBOUND',
                                        default => ($log->step ?: 'SYSTEM')
                                    };
                                @endphp
                                <div class="relative pl-6 pb-4 border-l-2 border-gray-150 dark:border-gray-700 last:pb-0">
                                    <span class="absolute -left-[6px] top-1.5 w-3 h-3 rounded-full {{ $dotBg }} ring-4"></span>
                                    
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md border {{ $badgeClass }}">
                                            {{ $loop->iteration }}. {{ $displayStep }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-semibold" title="{{ $log->created_at->format('d M Y H:i:s') }}">
                                            {{ $log->created_at->format('d M H:i') }}
                                        </span>
                                    </div>

                                    <p class="text-xs font-bold text-gray-800 dark:text-gray-200 leading-relaxed">
                                        {{ $log->description }}
                                    </p>

                                    <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-400">
                                        <span>👤 {{ $log->user->name ?? 'Sistem Workshop' }}</span>
                                        @if($log->action)
                                            <span>• <span class="font-mono text-[9px] bg-slate-100 dark:bg-gray-800 px-1 py-0.5 rounded text-gray-500 dark:text-gray-400">{{ $log->action }}</span></span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-400 text-xs font-medium">
                                    Belum ada catatan aktivitas workshop pada SPK ini.
                                </div>
                            @endforelse
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- ========================================================================= --}}
        {{-- MODALS SECTION --}}
        {{-- ========================================================================= --}}

        {{-- 1. STORAGE MODAL (Include partial) --}}
        @include('storage.partials.assign-modal')

        {{-- 2. SHIPPING PICKUP MODAL --}}
        <div x-data="{ 
                showShippingModal: false, 
                targetOrderId: null,
                isShippingBypass: false,
                closeShipping() { this.showShippingModal = false; this.targetOrderId = null; this.isShippingBypass = false; }
             }" 
             @shipping-modal.window="showShippingModal = true; targetOrderId = $event.detail.workOrderId; isShippingBypass = $event.detail.isBypass || false"
             x-show="showShippingModal" 
             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
             style="display: none;"
             x-cloak>
            
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-6 md:p-8 w-full max-w-md text-left transform transition-all border border-gray-100 dark:border-gray-700"
                 @click.away="closeShipping()">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-black text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <span class="p-2 bg-blue-100 text-blue-600 rounded-xl" :class="isShippingBypass ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600'">🚚</span>
                        <span x-text="isShippingBypass ? 'Bypass Pengiriman' : 'Proses Pengiriman'"></span>
                    </h3>
                    <button @click="closeShipping()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div x-show="isShippingBypass" class="mb-4 p-3.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-800 dark:text-amber-300 text-xs font-bold flex items-start gap-2">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <p>⚠️ PERINGATAN: SPK ini BELUM LUNAS. Melanjutkan akan mem-bypass validasi pembayaran kasir.</p>
                </div>

                <form :action="`/finish/${targetOrderId}/pickup-delivery`" method="POST">
                    @csrf
                    <template x-if="isShippingBypass">
                        <input type="hidden" name="notes" value="BYPASS PEMBAYARAN: Masuk antrean pengiriman ekspedisi meskipun belum lunas.">
                    </template>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1 uppercase tracking-wider">Ekspedisi / Kurir <span class="text-red-500">*</span></label>
                            <input type="text" name="pickup_method" required placeholder="Contoh: PCP Express, JNE, Grab, Paxel..."
                                   class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl text-xs font-bold p-3 shadow-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1 uppercase tracking-wider">Ongkir Real Workshop <span class="text-[10px] text-gray-400 font-normal italic">(Biaya asli ke kurir)</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">Rp</span>
                                <input type="number" name="actual_shipping_cost" placeholder="0"
                                       class="w-full pl-9 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl text-xs font-bold p-3 shadow-xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1 uppercase tracking-wider">Tanggal Masuk Pengiriman <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_masuk" required value="{{ date('Y-m-d') }}"
                                   class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl text-xs font-bold p-3 shadow-xs">
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="closeShipping()" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 rounded-xl text-xs font-bold transition-all">Batal</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-1.5 active:scale-95 text-white"
                                :class="isShippingBypass ? 'bg-amber-500 hover:bg-amber-600 shadow-amber-500/20' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/20'">
                            <span x-text="isShippingBypass ? 'Konfirmasi Bypass' : 'Konfirmasi Pengiriman'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 3. OTO MODAL --}}
        <div x-show="openOtoModal" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" 
             style="display: none;" 
             x-cloak>
            
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col border border-gray-100 dark:border-gray-700" @click.away="openOtoModal = false">
                <div class="p-6 pb-3 text-center border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-amber-500 to-orange-500 text-white">
                    <h3 class="text-2xl font-black">Penawaran One Time Offer (OTO)</h3>
                    <p class="text-amber-100 text-xs mt-0.5">Penawaran paket layanan spesial untuk customer ✨</p>
                </div>

                {{-- OTO Search Bar --}}
                <div class="px-6 pt-4 pb-2">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" 
                               x-model="searchOto" 
                               class="w-full bg-gray-50 dark:bg-gray-700/50 border-gray-200 dark:border-gray-600 focus:border-amber-500 focus:ring-amber-500 rounded-xl pl-9 pr-9 py-2.5 text-xs text-gray-800 dark:text-gray-100 font-bold transition-all" 
                               placeholder="Cari layanan OTO... (contoh: Repaint, Sol, Deep Clean)">
                        <button type="button" 
                                x-show="searchOto.length > 0" 
                                @click="searchOto = ''" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-3">
                    <form id="otoForm" action="{{ route('finish.create-oto', $order->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($services as $s)
                            <div @click="toggleOto({{ $s['id'] }})" 
                                 x-show="matchesSearchOto('{{ addslashes($s['name']) }}')"
                                 class="border-2 rounded-2xl p-3.5 cursor-pointer transition-all"
                                 :class="isSelectedOto({{ $s['id'] }}) ? 'border-amber-500 bg-amber-50 dark:bg-amber-950/20 shadow-xs' : 'border-gray-100 dark:border-gray-700 hover:border-gray-200'">
                                <div class="flex justify-between items-center font-bold text-xs text-gray-800 dark:text-gray-100">
                                    <span>{{ $s['name'] }}</span>
                                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center" :class="isSelectedOto({{ $s['id'] }}) ? 'bg-amber-500 border-amber-500' : 'border-gray-300'">
                                        <span x-show="isSelectedOto({{ $s['id'] }})" class="text-[9px] text-white">✓</span>
                                    </div>
                                </div>
                                <div class="mt-2 flex items-center justify-between">
                                    <div class="text-sm font-black text-amber-600 dark:text-amber-400">
                                        Rp {{ number_format($s['price'], 0, ',', '.') }}
                                        <span class="text-[9px] font-bold text-gray-400 uppercase">(Harga Promo)</span>
                                    </div>
                                </div>
                                 
                                <div x-show="isSelectedOto({{ $s['id'] }})" @click.stop class="mt-3 space-y-2 pt-2 border-t border-amber-200/50 dark:border-amber-800/50">
                                    <div>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase mb-0.5">Harga Normal (Sebelum Diskon)</p>
                                        <input type="number" 
                                               name="services[{{ $s['id'] }}][normal_price]" 
                                               x-model.number="getSelectedOto({{ $s['id'] }}).normal_price"
                                               :disabled="!isSelectedOto({{ $s['id'] }})"
                                               class="w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-lg text-xs font-bold text-gray-500 focus:ring-amber-500 focus:border-amber-500 py-1 px-2">
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase mb-0.5">Tambahan Hari Kerja (HK)</p>
                                        <input type="number" 
                                               name="services[{{ $s['id'] }}][hk_days]" 
                                               x-model.number="getSelectedOto({{ $s['id'] }}).hk_days"
                                               :disabled="!isSelectedOto({{ $s['id'] }})"
                                               class="w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600 rounded-lg text-xs font-bold text-gray-700 dark:text-gray-200 focus:ring-amber-500 focus:border-amber-500 py-1 px-2">
                                    </div>
                                </div>
                                 
                                <input type="hidden" name="services[{{ $s['id'] }}][id]" value="{{ $s['id'] }}" :disabled="!isSelectedOto({{ $s['id'] }})">
                                <input type="hidden" name="services[{{ $s['id'] }}][oto_price]" value="{{ $s['price'] }}" :disabled="!isSelectedOto({{ $s['id'] }})">
                                <input type="hidden" name="services[{{ $s['id'] }}][discount]" :value="getSelectedOto({{ $s['id'] }}).normal_price - {{ $s['price'] }}" :disabled="!isSelectedOto({{ $s['id'] }})">
                            </div>
                            @endforeach
                        </div>

                        {{-- Empty Search --}}
                        <div x-show="!hasMatchesOto" 
                             style="display: none;" 
                             x-cloak
                             class="py-8 text-center text-gray-400 text-xs font-bold">
                            Tidak ada layanan OTO yang cocok dengan kata kunci.
                        </div>

                        {{-- Reason Description --}}
                        <div class="mt-4">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-wider mb-1">Alasan Penawaran OTO</label>
                            <textarea name="description" rows="2" required
                                      class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-3 text-xs focus:ring-amber-500 focus:border-amber-500 font-medium"
                                      placeholder="Contoh: Sol sudah tipis dan mulai lepas, disarankan jahit sol agar awet..."></textarea>
                        </div>

                        {{-- Masa Berlaku --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-center">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Masa Berlaku Penawaran</p>
                            <div class="flex justify-center gap-3">
                                @foreach([3, 7, 14] as $d)
                                <label class="cursor-pointer">
                                    <input type="radio" name="valid_days" value="{{ $d }}" 
                                           @click="setOtoDays({{ $d }})"
                                           :checked="validDays == {{ $d }}"
                                           class="sr-only">
                                    <div class="w-14 h-12 rounded-xl border-2 flex flex-col items-center justify-center transition-all"
                                         :class="validDays == {{ $d }} ? 'border-amber-500 bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 font-bold' : 'border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-400'">
                                        <span class="text-sm font-black">{{ $d }}</span>
                                        <span class="text-[8px] uppercase">Hari</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-2 font-bold">Berlaku sampai: <span x-text="validUntil"></span></p>
                        </div>
                    </form>
                </div>

                <div class="p-6 pt-3 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700">
                    <div x-show="selectedOto.length > 0" class="flex justify-between items-end mb-4">
                        <div>
                            <p class="text-[9px] uppercase font-black text-gray-400">Total Normal</p>
                            <p class="text-sm font-bold text-gray-400 line-through" x-text="moneyFormat(totalNormalOto)"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] uppercase font-black text-amber-500">Total Promo OTO ✨</p>
                            <p class="text-2xl font-black text-amber-600 dark:text-amber-400" x-text="moneyFormat(totalOto)"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="openOtoModal = false" class="py-3 font-bold text-xs text-gray-500 uppercase rounded-xl hover:bg-gray-200 dark:hover:bg-gray-800 transition-all">Batal</button>
                        <button type="submit" form="otoForm" :disabled="selectedOto.length === 0" class="bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl py-3 font-black uppercase text-xs shadow-lg disabled:opacity-50 transition-all active:scale-95">Kirim OTO</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. REVISION MODAL --}}
        <div x-show="showRevisionModal" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" 
             style="display: none;"
             x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-100 dark:border-gray-700" @click.away="showRevisionModal = false">
                <div class="p-6 text-center bg-rose-600 text-white">
                    <h3 class="text-xl font-black uppercase tracking-wider">Ajukan Revisi Teknik</h3>
                    <p class="text-rose-100 text-xs mt-1">Kembalikan unit ke workshop untuk perbaikan stasiun</p>
                </div>
                
                <form action="{{ route('revision.request', $order->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Deskripsi Masalah / Alasan Revisi <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="4" required
                                  class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl p-3.5 text-xs focus:ring-rose-500 focus:border-rose-500 font-medium"
                                  placeholder="Jelaskan detail masalah teknis yang perlu diperbaiki ulang oleh teknisi..."></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Foto Masalah (Bisa lebih dari 1)</label>
                        <input type="file" name="photos[]" accept="image/*" multiple
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                        <p class="text-[9px] text-gray-400 mt-1 italic">Format: JPG, PNG, WEBP. Maks 5MB/foto.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" @click="showRevisionModal = false" class="py-3 font-black text-xs text-gray-400 uppercase tracking-wider hover:bg-gray-100 rounded-xl transition-all">Batal</button>
                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white rounded-xl py-3 font-black uppercase text-xs shadow-md shadow-rose-200 dark:shadow-none transition-all active:scale-95">Kirim ke Revisi</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-app-layout>
