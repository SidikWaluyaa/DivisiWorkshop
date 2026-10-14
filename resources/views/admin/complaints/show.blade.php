<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- ========================================== -->
        <!-- EXECUTIVE CLEAN HERO HEADER                -->
        <!-- ========================================== -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-7 relative overflow-hidden transition-all">
            {{-- Background Subtle Glow --}}
            <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-gradient-to-br from-[#22B086]/10 to-[#FFC232]/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-80 h-80 rounded-full bg-[#22B086]/5 blur-3xl pointer-events-none"></div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 relative z-10">
                <div class="flex items-start sm:items-center gap-4">
                    <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-br from-[#22B086] to-[#1C8D6C] text-white flex items-center justify-center shadow-lg shadow-[#22B086]/20 border border-white/20 shrink-0">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-mono font-black bg-slate-900 text-white shadow-2xs">
                                TIKET #{{ $complaint->id }}
                            </span>
                            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Detail Keluhan Pelanggan</h1>
                            @php
                                $statusStyles = [
                                    'PENDING'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'PROCESS'  => 'bg-teal-50 text-[#22B086] border-teal-200',
                                    'RESOLVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                ];
                                $curStatusStyle = $statusStyles[$complaint->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-black border {{ $curStatusStyle }}">
                                <span class="w-2 h-2 rounded-full {{ $complaint->status === 'RESOLVED' ? 'bg-emerald-500' : ($complaint->status === 'PROCESS' ? 'bg-[#22B086]' : ($complaint->status === 'REJECTED' ? 'bg-rose-500' : 'bg-amber-500 animate-pulse')) }}"></span>
                                {{ $complaint->status }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1 flex items-center gap-2 flex-wrap">
                            <span>Pelanggan: <strong class="text-slate-800">{{ $complaint->customer_name }}</strong> ({{ $complaint->customer_phone }})</span>
                            @if($complaint->workOrder)
                                <span class="text-slate-300">•</span>
                                <span>No. SPK: <strong class="text-[#22B086] font-mono">{{ $complaint->workOrder->spk_number }}</strong></span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Header Actions --}}
                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('admin.complaints.index') }}" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 hover:border-slate-300 shadow-2xs transition-all active:scale-95">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Daftar Keluhan</span>
                    </a>
                    @if($complaint->work_order_id)
                        <a href="{{ route('admin.orders.show', $complaint->work_order_id) }}" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-sm transition-all active:scale-95">
                            <svg class="w-4 h-4 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Buka SPK</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MAIN CONTENT (2 COLUMNS LAYOUT)            -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- LEFT COLUMN: Complaint Info, Appointment, & SPK -->
            <div class="lg:col-span-2 space-y-6">

                <!-- 1. KELUHAN DETAILS CARD -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#22B086]"></span>
                            Informasi Keluhan Pelanggan
                        </h3>
                        <span class="text-xs text-slate-400 font-medium">ID #{{ $complaint->id }}</span>
                    </div>

                    <div class="p-6 space-y-5">
                        {{-- 2 Meta Cards (Category & Date) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @php
                                $categoryBadgeConfig = [
                                    'QUALITY' => ['dot' => 'bg-purple-500', 'text' => 'text-purple-700', 'bg' => 'bg-purple-50/60', 'border' => 'border-purple-200', 'icon' => '🔍', 'label' => 'Kualitas Hasil Reparasi'],
                                    'DAMAGE'  => ['dot' => 'bg-rose-500', 'text' => 'text-rose-700', 'bg' => 'bg-rose-50/60', 'border' => 'border-rose-200', 'icon' => '⚠️', 'label' => 'Kerusakan Produk / Sepatu'],
                                    'LATE'    => ['dot' => 'bg-amber-500', 'text' => 'text-amber-700', 'bg' => 'bg-amber-50/60', 'border' => 'border-amber-200', 'icon' => '⏰', 'label' => 'Keterlambatan Pengerjaan'],
                                    'SERVICE' => ['dot' => 'bg-sky-500', 'text' => 'text-sky-700', 'bg' => 'bg-sky-50/60', 'border' => 'border-sky-200', 'icon' => '💬', 'label' => 'Pelayanan CS / Workshop'],
                                    'OTHER'   => ['dot' => 'bg-slate-500', 'text' => 'text-slate-700', 'bg' => 'bg-slate-50/60', 'border' => 'border-slate-200', 'icon' => '📌', 'label' => 'Lainnya'],
                                ];
                                $catItem = $categoryBadgeConfig[$complaint->category] ?? $categoryBadgeConfig['OTHER'];
                            @endphp
                            
                            {{-- Category Card --}}
                            <div class="p-4 rounded-2xl border {{ $catItem['border'] }} {{ $catItem['bg'] }} flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center text-xl shadow-2xs border {{ $catItem['border'] }} shrink-0">
                                    {{ $catItem['icon'] }}
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Kategori Keluhan</p>
                                    <p class="font-bold text-sm text-slate-900 mt-0.5">{{ $catItem['label'] }}</p>
                                </div>
                            </div>

                            {{-- Created Date Card --}}
                            <div class="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/60 flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center text-[#22B086] shadow-2xs border border-slate-200 shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Waktu Dilaporkan</p>
                                    <p class="font-bold text-sm text-slate-900 mt-0.5">{{ $complaint->created_at->format('d M Y') }}</p>
                                    <p class="text-xs text-slate-500 font-medium">{{ $complaint->created_at->format('H:i:s') }} WIB</p>
                                </div>
                            </div>
                        </div>

                        {{-- Modern Customer Voice Quote Bubble --}}
                        <div class="rounded-2xl bg-gradient-to-r from-amber-50/70 via-slate-50/50 to-white border-l-4 border-amber-400 border-y border-r border-slate-200/80 p-5 shadow-2xs">
                            <div class="flex items-center justify-between gap-2 mb-2.5 pb-2.5 border-b border-amber-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-xs font-bold shrink-0">
                                        💬
                                    </div>
                                    <span class="text-xs font-black uppercase tracking-wider text-slate-800">
                                        Deskripsi Keluhan Customer:
                                    </span>
                                </div>
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-100/80 px-2.5 py-0.5 rounded-md">
                                    Pernyataan Pelanggan
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-slate-800 leading-relaxed italic pl-1 whitespace-pre-wrap">
                                “{{ $complaint->description }}”
                            </p>
                        </div>

                        {{-- Admin Notes / Tanggapan Workshop if any --}}
                        @if($complaint->admin_notes)
                            <div class="rounded-2xl bg-teal-50/40 border-l-4 border-[#22B086] border-y border-r border-teal-100 p-5 shadow-2xs">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-sm">🛠️</span>
                                    <span class="text-xs font-black uppercase tracking-wider text-teal-900">
                                        Tanggapan / Catatan Penanganan Workshop:
                                    </span>
                                </div>
                                <p class="text-xs font-medium text-slate-700 leading-relaxed pl-1 whitespace-pre-wrap">
                                    {{ $complaint->admin_notes }}
                                </p>
                            </div>
                        @endif

                        {{-- Bukti Foto Galeri --}}
                        @if($complaint->photos && count($complaint->photos) > 0)
                            <div class="pt-2">
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="text-xs font-black uppercase tracking-wider text-slate-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#22B086]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Lampiran Bukti Foto ({{ count($complaint->photos) }} Berkas)
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-medium">Klik foto untuk membuka ukuran penuh</span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3.5">
                                    @foreach($complaint->photo_urls as $idx => $photoUrl)
                                        <a href="{{ $photoUrl }}" target="_blank" class="group relative block rounded-2xl overflow-hidden border-2 border-slate-200 aspect-square hover:border-[#22B086] transition-all shadow-xs hover:shadow-lg">
                                            <img src="{{ $photoUrl }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" alt="Bukti Foto {{ $idx + 1 }}">
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-2.5">
                                                <span class="text-white text-[11px] font-bold flex items-center gap-1 drop-shadow">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                                    Perbesar
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. AGENDA JANJI TEMU CARD (Executive Light) -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50/80 via-white to-indigo-50/40 border-b border-indigo-100/80 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-xs">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-sm font-black text-indigo-950 uppercase tracking-wider">
                                Agenda Janji Temu Pelanggan
                            </h3>
                        </div>
                        @if($complaint->appointment_date)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $complaint->appointment_badge['bg'] }}">
                                <span class="w-2 h-2 rounded-full {{ $complaint->appointment_badge['dot'] }}"></span>
                                {{ $complaint->appointment_badge['label'] }}
                            </span>
                        @endif
                    </div>

                    <div class="p-6">
                        @if($complaint->appointment_date)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="bg-indigo-50/70 p-4 rounded-2xl border border-indigo-100">
                                    <p class="text-indigo-700 text-[10px] font-black uppercase mb-1">Tanggal &amp; Waktu</p>
                                    <p class="font-black text-slate-900 text-sm">{{ $complaint->appointment_date->format('d M Y') }}</p>
                                    <p class="text-xs text-indigo-600 font-bold mt-0.5">{{ $complaint->appointment_date->format('H:i') }} WIB</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                                    <p class="text-slate-500 text-[10px] font-black uppercase mb-1">Lokasi Pertemuan</p>
                                    <p class="font-black text-slate-900 text-sm">{{ $complaint->location_label }}</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                                    <p class="text-slate-500 text-[10px] font-black uppercase mb-1">Status Janji</p>
                                    <p class="font-black text-slate-900 text-sm">{{ $complaint->appointment_status }}</p>
                                </div>
                            </div>

                            @if($complaint->appointment_notes)
                                <div class="bg-amber-50/70 p-4 rounded-2xl border border-amber-200 text-xs text-amber-900">
                                    <span class="font-black block uppercase tracking-wider mb-1 text-amber-800">Catatan Janji Temu:</span>
                                    <p class="leading-relaxed">{{ $complaint->appointment_notes }}</p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8 text-slate-400">
                                <div class="w-12 h-12 mx-auto rounded-2xl bg-indigo-50 text-indigo-400 flex items-center justify-center mb-2.5">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-slate-700">Belum ada agenda janji temu</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Jadwalkan tanggal pertemuan atau sesi konsultasi melalui form di sebelah kanan.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 3. INFORMASI PESANAN SPK CARD -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-slate-900 text-[#FFC232] flex items-center justify-center shadow-xs">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">
                                Info Pesanan: {{ $complaint->workOrder ? $complaint->workOrder->spk_number : 'No SPK' }}
                            </h3>
                        </div>
                        @if($complaint->work_order_id)
                            <a href="{{ route('admin.orders.show', $complaint->work_order_id) }}" target="_blank"
                                class="inline-flex items-center gap-1 text-xs font-bold text-[#22B086] hover:text-[#1C8D6C]">
                                <span>Lihat Detail SPK</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-5">
                            <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200/80">
                                <p class="text-slate-400 text-[10px] font-black uppercase mb-1">Pelanggan</p>
                                <p class="font-bold text-slate-900 text-sm">{{ $complaint->workOrder ? $complaint->workOrder->customer_name : $complaint->customer_name }}</p>
                                <p class="text-xs text-slate-500 font-medium mt-1">📱 {{ $complaint->customer_phone }}</p>
                            </div>
                            <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200/80">
                                <p class="text-slate-400 text-[10px] font-black uppercase mb-1">Sepatu</p>
                                <p class="font-bold text-slate-900 text-sm">{{ $complaint->workOrder ? $complaint->workOrder->shoe_brand : '-' }}</p>
                                <p class="text-xs text-slate-500 font-medium mt-1">{{ $complaint->workOrder ? ($complaint->workOrder->shoe_type . ' • ' . $complaint->workOrder->shoe_color) : '-' }}</p>
                            </div>
                        </div>

                        {{-- Services --}}
                        @if($complaint->workOrder && $complaint->workOrder->services)
                        <div class="pt-4 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2.5">Layanan yang Dikerjakan</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($complaint->workOrder->services as $service)
                                    <span class="inline-flex items-center px-3 py-1 bg-[#22B086]/10 border border-[#22B086]/25 rounded-xl text-xs font-bold text-[#166534]">
                                        <svg class="h-3.5 w-3.5 text-[#22B086] mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ $service->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        {{-- Technicians --}}
                        @if($complaint->workOrder)
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2.5">Riwayat Teknisi Pengerjaan</p>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                {{-- Main Technician --}}
                                @if($complaint->workOrder->technicianProduction)
                                    <div class="flex items-center bg-amber-50/60 p-3 rounded-2xl border border-amber-200/80">
                                        <div class="h-8 w-8 rounded-xl bg-[#FFC232] flex items-center justify-center text-slate-900 font-black text-xs mr-3 shadow-2xs shrink-0">
                                            {{ substr($complaint->workOrder->technicianProduction->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-amber-800 font-black uppercase">Teknisi Utama</p>
                                            <p class="text-xs font-bold text-slate-900">{{ $complaint->workOrder->technicianProduction->name }}</p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Sol Technician --}}
                                @if($complaint->workOrder->prodSolBy)
                                    <div class="flex items-center bg-slate-50 p-3 rounded-2xl border border-slate-200">
                                        <div class="h-8 w-8 rounded-xl bg-slate-300 flex items-center justify-center text-slate-700 font-bold text-xs mr-3 shrink-0">
                                            {{ substr($complaint->workOrder->prodSolBy->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-slate-400 font-black uppercase">Sol</p>
                                            <p class="text-xs font-bold text-slate-800">{{ $complaint->workOrder->prodSolBy->name }}</p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Upper Technician --}}
                                @if($complaint->workOrder->prodUpperBy)
                                    <div class="flex items-center bg-slate-50 p-3 rounded-2xl border border-slate-200">
                                        <div class="h-8 w-8 rounded-xl bg-slate-300 flex items-center justify-center text-slate-700 font-bold text-xs mr-3 shrink-0">
                                            {{ substr($complaint->workOrder->prodUpperBy->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-slate-400 font-black uppercase">Upper</p>
                                            <p class="text-xs font-bold text-slate-800">{{ $complaint->workOrder->prodUpperBy->name }}</p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Cleaning Technician --}}
                                @if($complaint->workOrder->prodCleaningBy)
                                    <div class="flex items-center bg-slate-50 p-3 rounded-2xl border border-slate-200">
                                        <div class="h-8 w-8 rounded-xl bg-slate-300 flex items-center justify-center text-slate-700 font-bold text-xs mr-3 shrink-0">
                                            {{ substr($complaint->workOrder->prodCleaningBy->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-slate-400 font-black uppercase">Cleaning / Treatment</p>
                                            <p class="text-xs font-bold text-slate-800">{{ $complaint->workOrder->prodCleaningBy->name }}</p>
                                        </div>
                                    </div>
                                @endif
                                
                                @if(!$complaint->workOrder->technicianProduction && !$complaint->workOrder->prodSolBy && !$complaint->workOrder->prodUpperBy && !$complaint->workOrder->prodCleaningBy)
                                    <div class="col-span-2 text-xs text-slate-400 italic py-2">Belum ada riwayat teknisi yang tercatat.</div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Actions & Updates (Sidebar) -->
            <div class="space-y-6">

                <!-- QUICK ACTIONS CARD -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-5 space-y-3">
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#22B086]"></span>
                        Pintasan Cepat
                    </h4>

                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $complaint->customer_phone);
                        if (str_starts_with($cleanPhone, '0')) {
                            $cleanPhone = '62' . substr($cleanPhone, 1);
                        }
                        $spkNum = $complaint->workOrder ? $complaint->workOrder->spk_number : 'SPK';
                        $waText = "Halo Kak {$complaint->customer_name}, kami dari Shoe Workshop mengenai keluhan pada SPK #{$spkNum}";
                        if ($complaint->appointment_date) {
                            $waText .= " dan jadwal pertemuan pada " . $complaint->appointment_date->format('d/m/Y H:i') . " WIB.";
                        }
                    @endphp

                    @if($cleanPhone && strlen($cleanPhone) >= 9)
                        <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode($waText) }}" target="_blank"
                            class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold shadow-md shadow-emerald-600/20 hover:shadow-lg transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.071.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/></svg>
                            <span>Hubungi Customer via WhatsApp</span>
                        </a>
                    @endif

                    @if($complaint->work_order_id)
                        <a href="{{ route('admin.orders.show', $complaint->work_order_id) }}" target="_blank"
                            class="w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-2xl text-xs font-bold border border-slate-200 transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Buka Halaman Detail SPK</span>
                        </a>
                    @endif
                </div>

                <!-- UPDATE STATUS & APPOINTMENT FORM -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <div class="bg-[#22B086] text-white p-1.5 rounded-lg shadow-2xs">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                        Update Status &amp; Janji Temu
                    </h3>
                    
                    <form action="{{ route('admin.complaints.update', $complaint) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5 block">Status Penanganan</label>
                            <div class="grid grid-cols-2 gap-2">
                                @php
                                    $statusOptionStyles = [
                                        'PENDING'  => ['bg' => 'bg-amber-50', 'text' => 'text-amber-800', 'border' => 'border-amber-300', 'ring' => 'ring-amber-500', 'icon' => '🟠'],
                                        'PROCESS'  => ['bg' => 'bg-teal-50', 'text' => 'text-teal-800', 'border' => 'border-teal-300', 'ring' => 'ring-teal-500', 'icon' => '🔵'],
                                        'RESOLVED' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-800', 'border' => 'border-emerald-300', 'ring' => 'ring-emerald-500', 'icon' => '🟢'],
                                        'REJECTED' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-800', 'border' => 'border-rose-300', 'ring' => 'ring-rose-500', 'icon' => '🔴'],
                                    ];
                                @endphp
                                @foreach($statusOptionStyles as $statusKey => $stStyle)
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="status" value="{{ $statusKey }}" class="peer sr-only" {{ $complaint->status == $statusKey ? 'checked' : '' }}>
                                        <div class="px-3 py-2.5 rounded-xl border-2 {{ $stStyle['bg'] }} {{ $stStyle['text'] }} {{ $stStyle['border'] }} text-xs font-black text-center peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:{{ $stStyle['ring'] }} transition-all opacity-60 peer-checked:opacity-100 hover:opacity-90 peer-checked:shadow-sm flex items-center justify-center gap-1.5">
                                            <span>{{ $stStyle['icon'] }}</span>
                                            {{ $statusKey }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Appointment Fields -->
                        <div class="pt-3 border-t border-slate-100 space-y-3">
                            <label class="text-xs font-black text-indigo-900 uppercase tracking-wider block flex items-center gap-1.5">
                                <span>📅</span> Jadwal Janji Temu
                            </label>
                            
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 block mb-1">Tanggal &amp; Waktu</label>
                                <input type="datetime-local" name="appointment_date" 
                                    value="{{ $complaint->appointment_date ? $complaint->appointment_date->format('Y-m-d\TH:i') : '' }}"
                                    class="w-full text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] bg-slate-50 py-2">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[11px] font-bold text-slate-600 block mb-1">Lokasi</label>
                                    <select name="appointment_location" class="w-full text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] bg-slate-50 py-2">
                                        <option value="WORKSHOP" {{ $complaint->appointment_location === 'WORKSHOP' ? 'selected' : '' }}>🏢 Workshop</option>
                                        <option value="CALL" {{ $complaint->appointment_location === 'CALL' ? 'selected' : '' }}>📞 Call / WA</option>
                                        <option value="VISIT" {{ $complaint->appointment_location === 'VISIT' ? 'selected' : '' }}>🏠 Kunjungan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[11px] font-bold text-slate-600 block mb-1">Status Janji</label>
                                    <select name="appointment_status" class="w-full text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] bg-slate-50 py-2">
                                        <option value="SCHEDULED" {{ $complaint->appointment_status === 'SCHEDULED' ? 'selected' : '' }}>⏰ Terjadwal</option>
                                        <option value="COMPLETED" {{ $complaint->appointment_status === 'COMPLETED' ? 'selected' : '' }}>✅ Selesai</option>
                                        <option value="CANCELLED" {{ $complaint->appointment_status === 'CANCELLED' ? 'selected' : '' }}>❌ Dibatalkan</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="text-[11px] font-bold text-slate-600 block mb-1">Catatan Temu</label>
                                <input type="text" name="appointment_notes" value="{{ $complaint->appointment_notes }}" placeholder="Misal: Customer membawa sepatu ke workshop"
                                    class="w-full text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] bg-slate-50 py-2">
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5 block">Catatan Admin Workshop</label>
                            <textarea name="admin_notes" rows="3" class="w-full text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] bg-slate-50 p-2.5" placeholder="Tambahkan catatan tindak lanjut internal...">{{ $complaint->admin_notes }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 bg-[#22B086] hover:bg-[#1C8D6C] text-white rounded-xl text-xs font-black shadow-md shadow-[#22B086]/25 hover:shadow-lg transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </form>

                    <div class="mt-5 pt-5 border-t border-slate-100">
                        <form action="{{ route('admin.complaints.destroy', $complaint) }}" method="POST" onsubmit="return confirmDelete(event)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2.5 px-4 bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 rounded-xl text-xs font-bold border border-rose-200 hover:border-rose-300 transition-all flex items-center justify-center gap-2 group">
                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                <span>Pindahkan ke Sampah</span>
                            </button>
                        </form>
                    </div>
                </div>

                <script>
                    function confirmDelete(e) {
                        e.preventDefault();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Hapus Keluhan?',
                                text: "Data akan dipindahkan ke Sampah. Anda masih bisa memulihkannya nanti.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Ya, Hapus!',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    e.target.submit();
                                }
                            });
                        } else {
                            if (confirm('Yakin ingin memindahkan keluhan ini ke Sampah?')) {
                                e.target.submit();
                            }
                        }
                    }
                </script>
            </div>
        </div>

    </div>
</x-app-layout>
