<x-app-layout>
    <div class="space-y-6" x-data="{
        activeTab: new URLSearchParams(window.location.search).get('tab') || 'table',
        showCreateModal: false,
        selectedOrder: null,
        searchQuery: '',
        searchResults: [],
        isSearching: false,
        hasAppointment: false,
        previewEvent: null,

        async searchSPK() {
            if (this.searchQuery.trim().length < 2) {
                this.searchResults = [];
                return;
            }
            this.isSearching = true;
            try {
                const res = await fetch(`{{ route('admin.complaints.search-orders') }}?q=${encodeURIComponent(this.searchQuery.trim())}`);
                this.searchResults = await res.json();
            } catch (err) {
                console.error(err);
                this.searchResults = [];
            } finally {
                this.isSearching = false;
            }
        },

        selectOrder(order) {
            this.selectedOrder = order;
            this.searchResults = [];
            this.searchQuery = '';
        },

        resetOrder() {
            this.selectedOrder = null;
            this.searchQuery = '';
            this.searchResults = [];
        },

        openCreateModal() {
            this.resetOrder();
            this.hasAppointment = false;
            this.showCreateModal = true;
        }
    }">
        <!-- ========================================== -->
        <!-- EXECUTIVE CLEAN HERO HEADER                -->
        <!-- ========================================== -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 lg:p-7 relative overflow-hidden transition-all">
            {{-- Subtle Background Brand Glow --}}
            <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-gradient-to-br from-[#22B086]/10 to-[#FFC232]/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-80 h-80 rounded-full bg-[#22B086]/5 blur-3xl pointer-events-none"></div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#22B086] to-[#1C8D6C] text-white flex items-center justify-center shadow-lg shadow-[#22B086]/20 border border-white/20 shrink-0">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-2xl lg:text-3xl font-black text-slate-900 tracking-tight">Keluhan &amp; Janji Temu</h1>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#22B086]/10 text-[#22B086] border border-[#22B086]/25">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#22B086] animate-pulse"></span>
                                Internal Modul
                            </span>
                        </div>
                        <p class="text-slate-500 text-xs lg:text-sm font-medium mt-1">
                            Pusat penanganan keluhan customer SPK internal, tindak lanjut workshop, dan manajemen jadwal temu.
                        </p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center flex-wrap gap-2.5 shrink-0">
                    <button type="button" @click="openCreateModal()" 
                        class="px-4 py-2.5 bg-gradient-to-r from-[#22B086] to-[#1C8D6C] hover:from-[#1C8D6C] hover:to-[#177257] text-white rounded-xl text-xs font-black transition-all flex items-center gap-2 shadow-md shadow-[#22B086]/25 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Catat Keluhan Baru</span>
                    </button>

                    <a href="{{ route('admin.complaints.trash') }}" 
                        class="px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-xs font-bold transition-all flex items-center gap-2 border border-slate-200 hover:border-slate-300 shadow-2xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Sampah</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- EXECUTIVE METRIC CARDS (SHOPIFY/LINEAR)   -->
        <!-- ========================================== -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 lg:gap-4">
            <!-- Pending Card -->
            <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-4 hover:border-amber-300 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 group-hover:text-amber-600 transition-colors">Pending</span>
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-black text-slate-900 group-hover:text-amber-600 transition-colors">{{ $statusCounts['PENDING'] }}</span>
                    <span class="text-[10px] text-slate-400 font-semibold">Menunggu</span>
                </div>
            </div>

            <!-- Process Card -->
            <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-4 hover:border-[#22B086]/50 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 group-hover:text-[#22B086] transition-colors">Diproses</span>
                    <span class="w-2 h-2 rounded-full bg-[#22B086]"></span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-black text-slate-900 group-hover:text-[#22B086] transition-colors">{{ $statusCounts['PROCESS'] }}</span>
                    <span class="text-[10px] text-slate-400 font-semibold">Workshop</span>
                </div>
            </div>

            <!-- Resolved Card -->
            <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-4 hover:border-emerald-300 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 group-hover:text-emerald-600 transition-colors">Selesai</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-black text-slate-900 group-hover:text-emerald-600 transition-colors">{{ $statusCounts['RESOLVED'] }}</span>
                    <span class="text-[10px] text-slate-400 font-semibold">Resolved</span>
                </div>
            </div>

            <!-- Rejected Card -->
            <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-4 hover:border-rose-300 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 group-hover:text-rose-600 transition-colors">Ditolak</span>
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-black text-slate-900 group-hover:text-rose-600 transition-colors">{{ $statusCounts['REJECTED'] }}</span>
                    <span class="text-[10px] text-slate-400 font-semibold">Invalid</span>
                </div>
            </div>

            <!-- Appointments Card -->
            <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-4 hover:border-indigo-300 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500 group-hover:text-indigo-600 transition-colors">Janji Temu</span>
                    @if($statusCounts['today_appointments'] > 0)
                        <span class="bg-rose-500 text-white text-[9px] font-black px-1.5 py-0.2 rounded-full animate-pulse">{{ $statusCounts['today_appointments'] }} Hari Ini</span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    @endif
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-black text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $statusCounts['with_appointments'] }}</span>
                    <span class="text-[10px] text-slate-400 font-semibold">Terjadwal</span>
                </div>
            </div>

            <!-- Total Card -->
            <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-4 hover:border-slate-300 hover:shadow-sm transition-all group">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Total Kasus</span>
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-black text-slate-900">{{ $statusCounts['total'] }}</span>
                    <span class="text-[10px] text-slate-400 font-semibold">Semua</span>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEGMENTED CONTROL TABS (APPLE / LINEAR)   -->
        <!-- ========================================== -->
        <div class="flex items-center justify-between bg-white px-5 py-3 rounded-2xl shadow-2xs border border-slate-200/80">
            <div class="inline-flex p-1 rounded-xl bg-slate-100 border border-slate-200/70 shadow-inner gap-1">
                <button type="button" @click="activeTab = 'table'"
                    :class="activeTab === 'table' ? 'bg-white text-slate-900 shadow-xs border border-slate-200/80 font-black' : 'text-slate-500 hover:text-slate-900 font-bold hover:bg-white/60'"
                    class="px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#22B086]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>Daftar Keluhan</span>
                    <span :class="activeTab === 'table' ? 'bg-[#22B086]/10 text-[#22B086]' : 'bg-slate-200 text-slate-600'" class="text-[10px] px-2 py-0.5 rounded-full font-black">
                        {{ $complaints->total() }}
                    </span>
                </button>

                <button type="button" @click="activeTab = 'calendar'"
                    :class="activeTab === 'calendar' ? 'bg-white text-slate-900 shadow-xs border border-slate-200/80 font-black' : 'text-slate-500 hover:text-slate-900 font-bold hover:bg-white/60'"
                    class="px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#FFC232]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Kalender Janji Temu</span>
                    <span :class="activeTab === 'calendar' ? 'bg-[#FFC232]/20 text-slate-800' : 'bg-slate-200 text-slate-600'" class="text-[10px] px-2 py-0.5 rounded-full font-black">
                        {{ count($calendarEvents) }}
                    </span>
                </button>
            </div>

            <div class="hidden sm:flex items-center gap-2 text-xs font-semibold text-slate-400">
                <span>Workshop CS Standard</span>
                <span>•</span>
                <span class="text-[#22B086] font-bold">Shoe Workshop</span>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: DAFTAR KELUHAN (TABLE VIEW)        -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'table'" x-cloak class="space-y-4">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
                <!-- Filters Header -->
                <div class="p-4 lg:p-5 bg-slate-50/60 border-b border-slate-200/80">
                    <form action="{{ route('admin.complaints.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                        <input type="hidden" name="tab" value="table">
                        
                        <!-- Search Input -->
                        <div class="relative lg:col-span-2">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Cari SPK, Nama, HP, atau Sepatu..." 
                                class="w-full text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] pl-9 pr-4 py-2.5 bg-white shadow-2xs font-medium placeholder:text-slate-400">
                            <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <!-- Category Filter -->
                        <select name="category" onchange="this.form.submit()" 
                            class="text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] bg-white shadow-2xs font-bold py-2.5 text-slate-700">
                            <option value="">📋 Semua Kategori</option>
                            <option value="QUALITY" {{ request('category') == 'QUALITY' ? 'selected' : '' }}>🔍 Kualitas Hasil</option>
                            <option value="LATE" {{ request('category') == 'LATE' ? 'selected' : '' }}>⏰ Keterlambatan</option>
                            <option value="SERVICE" {{ request('category') == 'SERVICE' ? 'selected' : '' }}>💬 Pelayanan CS</option>
                            <option value="DAMAGE" {{ request('category') == 'DAMAGE' ? 'selected' : '' }}>⚠️ Kerusakan</option>
                            <option value="OTHER" {{ request('category') == 'OTHER' ? 'selected' : '' }}>📌 Lainnya</option>
                        </select>

                        <!-- Status Filter -->
                        <select name="status" onchange="this.form.submit()" 
                            class="text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] bg-white shadow-2xs font-bold py-2.5 text-slate-700">
                            <option value="">🎯 Semua Status</option>
                            <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>🟠 Pending</option>
                            <option value="PROCESS" {{ request('status') == 'PROCESS' ? 'selected' : '' }}>🔵 Diproses</option>
                            <option value="RESOLVED" {{ request('status') == 'RESOLVED' ? 'selected' : '' }}>🟢 Selesai</option>
                            <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>🔴 Ditolak</option>
                        </select>

                        <!-- Appointment Filter & Reset -->
                        <div class="flex items-center gap-2">
                            <select name="appointment_filter" onchange="this.form.submit()" 
                                class="flex-1 text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] bg-white shadow-2xs font-bold py-2.5 text-slate-700">
                                <option value="">📅 Semua Janji</option>
                                <option value="has_appointment" {{ request('appointment_filter') == 'has_appointment' ? 'selected' : '' }}>Ada Janji</option>
                                <option value="today" {{ request('appointment_filter') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                                <option value="upcoming" {{ request('appointment_filter') == 'upcoming' ? 'selected' : '' }}>Mendatang</option>
                            </select>

                            @if(request()->anyFilled(['search', 'category', 'status', 'appointment_filter']))
                                <a href="{{ route('admin.complaints.index') }}" 
                                    class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors border border-slate-200" title="Reset Filter">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600">
                            <tr>
                                <th class="px-5 py-3.5 font-black uppercase tracking-wider text-[11px]">ID &amp; Waktu</th>
                                <th class="px-5 py-3.5 font-black uppercase tracking-wider text-[11px]">SPK &amp; Pelanggan</th>
                                <th class="px-5 py-3.5 font-black uppercase tracking-wider text-[11px]">Kategori &amp; Keluhan</th>
                                <th class="px-5 py-3.5 font-black uppercase tracking-wider text-[11px]">Janji Temu</th>
                                <th class="px-5 py-3.5 font-black uppercase tracking-wider text-[11px]">Status</th>
                                <th class="px-5 py-3.5 font-black uppercase tracking-wider text-[11px] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($complaints as $complaint)
                                <tr class="hover:bg-slate-50/70 transition-colors group">
                                    <!-- ID & Waktu -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <span class="bg-[#22B086] text-white font-black text-[11px] px-2.5 py-0.5 rounded-lg shadow-2xs">
                                                #{{ $complaint->id }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-slate-800 font-bold mt-1.5">{{ $complaint->created_at->format('d M Y') }}</div>
                                        <div class="text-[10px] text-slate-400 font-medium">{{ $complaint->created_at->format('H:i') }} WIB</div>
                                    </td>

                                    <!-- SPK & Pelanggan -->
                                    <td class="px-5 py-4">
                                        @if($complaint->workOrder)
                                            <a href="{{ route('admin.orders.show', $complaint->workOrder->id) }}" target="_blank" 
                                                class="font-black text-[#22B086] hover:text-[#1C8D6C] hover:underline flex items-center gap-1.5 text-xs">
                                                <svg class="w-3.5 h-3.5 text-[#FFC232] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span>{{ $complaint->workOrder->spk_number }}</span>
                                            </a>
                                            <div class="text-[11px] text-slate-500 mt-0.5 font-medium">
                                                {{ $complaint->workOrder->shoe_brand ?? '-' }} {{ $complaint->workOrder->shoe_type ? '• ' . $complaint->workOrder->shoe_type : '' }}
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 italic">SPK Tidak Terkait</span>
                                        @endif
                                        <div class="text-xs text-slate-900 font-bold mt-1.5">{{ $complaint->customer_name }}</div>
                                        <div class="text-[11px] text-slate-500 font-medium">
                                            📱 {{ $complaint->customer_phone }}
                                        </div>
                                    </td>

                                    <!-- Kategori & Keluhan -->
                                    <td class="px-5 py-4 max-w-xs">
                                        <div class="flex items-center gap-1.5 mb-1.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border bg-slate-100 text-slate-700 border-slate-200">
                                                {{ $complaint->category_label }}
                                            </span>
                                            @if($complaint->photos && count($complaint->photos) > 0)
                                                <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-1.5 py-0.5 rounded border border-slate-200 flex items-center gap-0.5" title="Ada {{ count($complaint->photos) }} foto bukti">
                                                    📷 {{ count($complaint->photos) }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-slate-600 leading-relaxed line-clamp-2" title="{{ $complaint->description }}">
                                            {{ $complaint->description }}
                                        </p>
                                    </td>

                                    <!-- Janji Temu -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($complaint->appointment_date)
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-1.5 font-bold text-slate-900">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $complaint->appointment_badge['dot'] }}"></span>
                                                    <span>{{ $complaint->appointment_date->format('d M Y') }}</span>
                                                    <span class="text-[#22B086] bg-[#22B086]/10 px-1.5 py-0.2 rounded text-[10px] font-black">{{ $complaint->appointment_date->format('H:i') }}</span>
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-medium">
                                                    {{ $complaint->location_label }}
                                                </div>
                                                <div>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $complaint->appointment_badge['bg'] }}">
                                                        {{ $complaint->appointment_badge['label'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">- Belum ada janji -</span>
                                        @endif
                                    </td>

                                    <!-- Status Keluhan -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @php
                                            $statStyles = [
                                                'PENDING'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'PROCESS'  => 'bg-[#22B086]/10 text-[#22B086] border-[#22B086]/25',
                                                'RESOLVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'REJECTED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            ];
                                            $badgeStyle = $statStyles[$complaint->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $badgeStyle }}">
                                            {{ $complaint->status }}
                                        </span>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.complaints.show', $complaint->id) }}" 
                                                class="px-2.5 py-1.5 bg-slate-50 hover:bg-[#22B086] text-slate-700 hover:text-white rounded-lg text-xs font-bold transition-all border border-slate-200 hover:border-[#22B086] flex items-center gap-1 shadow-2xs">
                                                <span>Detail</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                            <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST" onsubmit="return confirm('Pindahkan keluhan ini ke sampah?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus ke Sampah">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                            <div class="w-16 h-16 rounded-3xl bg-slate-50 text-[#22B086] border border-slate-200/80 flex items-center justify-center mb-3 shadow-inner">
                                                <svg class="h-8 w-8 text-[#22B086]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-base font-black text-slate-900">Tidak Ada Data Keluhan</h3>
                                            <p class="text-xs text-slate-500 mt-1">Belum ada keluhan internal atau data yang cocok dengan kriteria filter pencarian.</p>
                                            <button type="button" @click="openCreateModal()" class="mt-4 px-4 py-2 bg-gradient-to-r from-[#22B086] to-[#1C8D6C] text-white rounded-xl text-xs font-black shadow-md shadow-[#22B086]/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                                                Catat Keluhan Baru
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($complaints->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $complaints->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: KALENDER DENGAN SPLIT SIDEBAR       -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'calendar'" x-cloak class="space-y-6" x-data="appointmentCalendar(@js($calendarEvents))">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
                
                {{-- LEFT (SPAN 2): Interactive Calendar Grid --}}
                <div class="xl:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 space-y-5">
                    {{-- Calendar Header Controls --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-[#FFC232]/15 text-slate-900 border border-[#FFC232]/30 flex items-center justify-center shadow-xs shrink-0">
                                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-black text-slate-900 tracking-tight" x-text="monthNames[currentMonth] + ' ' + currentYear"></h2>
                                <p class="text-xs text-slate-500 font-medium">Navigasi bulan untuk melihat jadwal janji temu workshop</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 self-start sm:self-auto">
                            <button type="button" @click="prevMonth()" 
                                class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors" title="Bulan Sebelumnya">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button type="button" @click="goToToday()" 
                                class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors">
                                Bulan Ini
                            </button>
                            <button type="button" @click="nextMonth()" 
                                class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors" title="Bulan Berikutnya">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Calendar Grid Table --}}
                    <div class="border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
                        <!-- Weekdays Header -->
                        <div class="grid grid-cols-7 bg-slate-50 border-b border-slate-200 text-center">
                            <template x-for="(day, idx) in dayNames" :key="idx">
                                <div class="py-2.5 text-[11px] font-black text-slate-600 uppercase tracking-wider" x-text="day"></div>
                            </template>
                        </div>

                        <!-- Days Cells Grid -->
                        <div class="grid grid-cols-7 divide-x divide-y divide-slate-200 bg-white">
                            <template x-for="(cell, index) in calendarDays" :key="index">
                                <div class="min-h-[105px] p-2 transition-colors relative flex flex-col justify-between"
                                    :class="{
                                        'bg-slate-50/50 text-slate-400': !cell.isCurrentMonth,
                                        'bg-white hover:bg-slate-50/70': cell.isCurrentMonth,
                                        'bg-[#22B086]/5 ring-2 ring-[#22B086] ring-inset': cell.isToday
                                    }">
                                    <!-- Date Number Header -->
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-black rounded-full w-6 h-6 flex items-center justify-center"
                                            :class="{
                                                'bg-[#22B086] text-white shadow-2xs': cell.isToday,
                                                'text-slate-800': cell.isCurrentMonth && !cell.isToday,
                                                'text-slate-400': !cell.isCurrentMonth
                                            }"
                                            x-text="cell.date">
                                        </span>
                                        <template x-if="cell.events.length > 0">
                                            <span class="text-[9px] font-black text-indigo-700 bg-indigo-50 border border-indigo-100 px-1.5 py-0.2 rounded-full" x-text="cell.events.length + ' Janji'"></span>
                                        </template>
                                    </div>

                                    <!-- Events List inside Day Cell -->
                                    <div class="space-y-1 overflow-y-auto max-h-[80px] pr-0.5">
                                        <template x-for="event in cell.events" :key="event.id">
                                            <button type="button" @click="openEventPreview(event)"
                                                class="w-full text-left p-1 rounded-lg text-[10px] font-bold border transition-transform hover:scale-[1.02] active:scale-[0.98] block truncate shadow-2xs"
                                                :class="{
                                                    'bg-amber-50 text-amber-900 border-amber-200 hover:bg-amber-100': event.appointment_status === 'SCHEDULED',
                                                    'bg-emerald-50 text-emerald-900 border-emerald-200 hover:bg-emerald-100': event.appointment_status === 'COMPLETED',
                                                    'bg-rose-50 text-rose-900 border-rose-200 hover:bg-rose-100': event.appointment_status === 'CANCELLED'
                                                }">
                                                <div class="flex items-center gap-1 font-black">
                                                    <span x-text="event.time"></span>
                                                    <span class="truncate" x-text="event.spk_number"></span>
                                                </div>
                                                <div class="text-[9px] text-slate-600 font-medium truncate" x-text="event.customer_name"></div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="flex flex-wrap items-center gap-4 pt-2 text-xs font-semibold text-slate-500">
                        <span class="text-slate-700 font-black">Status:</span>
                        <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Dijadwalkan</span>
                        <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Selesai</span>
                        <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Dibatalkan</span>
                        <span class="text-slate-300">|</span>
                        <span>🏢 Workshop</span>
                        <span>📞 Telepon</span>
                        <span>🏠 Kunjungan</span>
                    </div>
                </div>

                {{-- RIGHT (SPAN 1): Split Sidebar (Today & Upcoming Agenda) --}}
                <div class="space-y-5">
                    
                    {{-- Card 1: Agenda Mendatang --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-5 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-[#22B086]/10 text-[#22B086] flex items-center justify-center font-black">
                                    📅
                                </div>
                                <h3 class="font-black text-slate-900 text-sm">Agenda Terdekat</h3>
                            </div>
                            <span class="text-[10px] font-black bg-[#22B086]/10 text-[#22B086] px-2 py-0.5 rounded-full">
                                {{ count($upcomingAppointments) }} Jadwal
                            </span>
                        </div>

                        <div class="space-y-3 max-h-[460px] overflow-y-auto pr-1">
                            @forelse($upcomingAppointments as $item)
                                <div class="p-3.5 rounded-2xl border border-slate-200/80 bg-slate-50/50 hover:bg-slate-50 hover:border-[#22B086]/40 transition-all shadow-2xs">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="bg-[#22B086] text-white text-[10px] font-black px-2 py-0.5 rounded-md shadow-2xs">
                                            {{ $item->workOrder ? $item->workOrder->spk_number : 'Keluhan #' . $item->id }}
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-500">
                                            {{ $item->appointment_date ? $item->appointment_date->format('d M, H:i') : '-' }} WIB
                                        </span>
                                    </div>
                                    <div class="font-bold text-xs text-slate-900">{{ $item->customer_name }}</div>
                                    <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                        <span>{{ $item->location_label }}</span>
                                        <span>•</span>
                                        <span>📱 {{ $item->customer_phone }}</span>
                                    </div>
                                    @if($item->appointment_notes)
                                        <p class="text-[11px] text-slate-600 bg-white p-2 rounded-xl border border-slate-100 mt-2 italic">
                                            "{{ $item->appointment_notes }}"
                                        </p>
                                    @endif
                                    <div class="mt-2.5 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                        <span class="font-semibold text-slate-400">{{ $item->category_label }}</span>
                                        <a href="{{ route('admin.complaints.show', $item->id) }}" class="font-bold text-[#22B086] hover:text-[#1C8D6C] flex items-center gap-0.5">
                                            <span>Detail Kasus</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-slate-400 italic text-xs">
                                    Tidak ada janji temu mendatang yang belum diselesaikan.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Card 2: Quick Tip Workshop --}}
                    <div class="bg-gradient-to-br from-[#22B086]/5 to-[#FFC232]/10 rounded-3xl p-5 border border-[#22B086]/20 text-xs text-slate-700 space-y-2">
                        <div class="flex items-center gap-2 font-black text-slate-900">
                            <span>💡</span>
                            <span>Standard Operasional Temu</span>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed font-medium">
                            Setiap janji temu otomatis dicatat di audit log SPK. Pastikan konfirmasi kedatangan customer H-1 melalui tombol WhatsApp di preview detail.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal Preview Event (Quick View) -->
            <div x-show="previewEvent !== null" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="previewEvent !== null" 
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                        @click="previewEvent = null"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="previewEvent !== null" 
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                        class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-100">
                        
                        <template x-if="previewEvent">
                            <div class="p-6 space-y-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="bg-[#22B086] text-white font-black text-xs px-2.5 py-0.5 rounded-lg" x-text="previewEvent.spk_number"></span>
                                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full border"
                                            :class="previewEvent.appointment_badge.bg" x-text="previewEvent.appointment_badge.label"></span>
                                    </div>
                                    <button type="button" @click="previewEvent = null" class="text-slate-400 hover:text-slate-600 p-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <div>
                                    <h4 class="text-lg font-black text-slate-900" x-text="previewEvent.customer_name"></h4>
                                    <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                        <span x-text="'📱 ' + previewEvent.customer_phone"></span>
                                        <span>•</span>
                                        <span x-text="'👟 ' + previewEvent.shoe_info"></span>
                                    </div>
                                </div>

                                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80 space-y-2 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500 font-semibold">Jadwal Temu:</span>
                                        <span class="font-black text-slate-900" x-text="previewEvent.datetime_formatted"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500 font-semibold">Lokasi:</span>
                                        <span class="font-bold text-slate-900" x-text="previewEvent.location_label"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500 font-semibold">Kategori:</span>
                                        <span class="font-bold text-[#22B086]" x-text="previewEvent.category"></span>
                                    </div>
                                </div>

                                <template x-if="previewEvent.appointment_notes && previewEvent.appointment_notes !== '-'">
                                    <div class="bg-amber-50/80 p-3 rounded-2xl border border-amber-200 text-xs">
                                        <span class="font-bold text-amber-900 block mb-0.5">Catatan Temu:</span>
                                        <p class="text-amber-800" x-text="previewEvent.appointment_notes"></p>
                                    </div>
                                </template>

                                <div>
                                    <span class="text-xs font-bold text-slate-700 block mb-1">Deskripsi Keluhan:</span>
                                    <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-2xl border border-slate-100" x-text="previewEvent.description"></p>
                                </div>

                                <div class="pt-2 flex items-center gap-2">
                                    <a :href="'https://wa.me/' + previewEvent.customer_phone.replace(/[^0-9]/g, '')" target="_blank"
                                        class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl text-center shadow-md transition-colors flex items-center justify-center gap-1.5">
                                        <span>💬 Chat WhatsApp</span>
                                    </a>
                                    <a :href="previewEvent.show_url" 
                                        class="flex-1 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl text-center shadow-md transition-colors">
                                        Buka Detail Kasus &rarr;
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL: CATAT KELUHAN BARU (INTERNAL FORM)  -->
        <!-- ========================================== -->
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCreateModal" 
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                    @click="showCreateModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showCreateModal" 
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-100">
                    
                    <form action="{{ route('admin.complaints.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-[#22B086] to-[#1C8D6C] p-6 text-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/20 p-2 backdrop-blur-sm flex items-center justify-center border border-white/20">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black tracking-tight">Catat Keluhan Pelanggan</h3>
                                    <p class="text-white/80 text-xs font-medium">Input manual keluhan internal &amp; agenda janji temu</p>
                                </div>
                            </div>
                            <button type="button" @click="showCreateModal = false" class="text-white/80 hover:text-white p-1 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                            <!-- STEP 1: PILIH SPK -->
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">
                                    1. Hubungkan ke SPK Order <span class="text-rose-500">*</span>
                                </label>

                                <!-- If Order Selected -->
                                <template x-if="selectedOrder">
                                    <div class="bg-[#22B086]/5 border-2 border-[#22B086]/40 rounded-2xl p-4 flex items-start justify-between gap-3">
                                        <input type="hidden" name="work_order_id" :value="selectedOrder.id">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="bg-[#22B086] text-white font-black text-xs px-2.5 py-0.5 rounded-lg" x-text="selectedOrder.spk_number"></span>
                                                <span class="text-xs font-bold text-slate-700" x-text="selectedOrder.status"></span>
                                            </div>
                                            <div class="text-sm font-black text-slate-900" x-text="selectedOrder.customer_name"></div>
                                            <div class="text-xs text-slate-600 flex items-center gap-2">
                                                <span x-text="'📱 ' + (selectedOrder.customer_phone || '-')"></span>
                                                <span>•</span>
                                                <span x-text="'👟 ' + (selectedOrder.shoe_brand || '') + ' ' + (selectedOrder.shoe_type || '')"></span>
                                            </div>
                                        </div>
                                        <button type="button" @click="resetOrder()" 
                                            class="text-xs font-bold text-rose-600 hover:text-rose-800 bg-white px-2.5 py-1.5 rounded-lg border border-rose-200 shadow-2xs transition-colors">
                                            Ganti SPK
                                        </button>
                                    </div>
                                </template>

                                <!-- If Order NOT Selected: Live Search Input -->
                                <template x-if="!selectedOrder">
                                    <div class="relative">
                                        <input type="text" x-model="searchQuery" @input.debounce.300ms="searchSPK()"
                                            placeholder="Ketik No SPK, Nama Customer, No HP, atau Sepatu..."
                                            class="w-full text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] pl-10 pr-4 py-2.5 bg-slate-50 focus:bg-white shadow-2xs font-medium">
                                        <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <div x-show="isSearching" class="absolute right-3 top-3 text-xs text-[#22B086] font-bold animate-pulse">
                                            Mencari...
                                        </div>

                                        <!-- Results Dropdown -->
                                        <div x-show="searchResults.length > 0" class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-2xl shadow-xl z-20 max-h-60 overflow-y-auto divide-y divide-slate-100">
                                            <template x-for="item in searchResults" :key="item.id">
                                                <div @click="selectOrder(item)" class="p-3 hover:bg-[#22B086]/5 cursor-pointer transition-colors flex items-center justify-between">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-black text-xs text-[#22B086]" x-text="item.spk_number"></span>
                                                            <span class="text-[10px] text-slate-400" x-text="'(' + item.status + ')'"></span>
                                                        </div>
                                                        <div class="text-xs font-bold text-slate-900 mt-0.5" x-text="item.customer_name"></div>
                                                        <div class="text-[11px] text-slate-500" x-text="(item.shoe_brand || '') + ' • ' + (item.customer_phone || '')"></div>
                                                    </div>
                                                    <span class="text-xs font-bold text-[#22B086]">Pilih &rarr;</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- STEP 2: DETAIL KELUHAN -->
                            <div class="space-y-4 pt-2 border-t border-slate-100">
                                <div>
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1">
                                        2. Kategori Keluhan <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="category" required class="w-full text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] bg-white font-bold py-2.5 text-slate-700">
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="QUALITY">🔍 Kualitas Hasil Reparasi</option>
                                        <option value="LATE">⏰ Keterlambatan Pengerjaan</option>
                                        <option value="SERVICE">💬 Pelayanan CS / Workshop</option>
                                        <option value="DAMAGE">⚠️ Kerusakan Produk / Sepatu</option>
                                        <option value="OTHER">📌 Lainnya</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1">
                                        Deskripsi Keluhan <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea name="description" rows="3" required
                                        placeholder="Ceritakan detail keluhan atau permasalahan yang disampaikan oleh customer..."
                                        class="w-full text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] p-3"></textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1">
                                        Foto Bukti / Pendukung (Opsional)
                                    </label>
                                    <input type="file" name="photos[]" multiple accept="image/*"
                                        class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#22B086]/10 file:text-[#22B086] hover:file:bg-[#22B086]/20 border border-slate-200 rounded-xl p-1 bg-white">
                                </div>
                            </div>

                            <!-- STEP 3: ATUR JANJI TEMU (TOGGLE) -->
                            <div class="pt-3 border-t border-slate-100 space-y-3">
                                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-200/80">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-[#FFC232] text-slate-900 flex items-center justify-center font-black text-xs">
                                            📅
                                        </div>
                                        <div>
                                            <label for="has_appointment_toggle" class="text-xs font-black text-slate-900 cursor-pointer">
                                                Jadwalkan Janji Temu?
                                            </label>
                                            <p class="text-[10px] text-slate-500">Agenda akan otomatis terpampang pada Tab Kalender</p>
                                        </div>
                                    </div>
                                    <input type="checkbox" id="has_appointment_toggle" x-model="hasAppointment"
                                        class="w-4 h-4 text-[#22B086] rounded focus:ring-[#22B086] cursor-pointer">
                                </div>

                                <!-- Collapsible Appointment Form -->
                                <div x-show="hasAppointment" x-transition class="space-y-3 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[11px] font-black text-slate-700 uppercase tracking-wider mb-1">
                                                Tanggal &amp; Jam Temu <span class="text-rose-500">*</span>
                                            </label>
                                            <input type="datetime-local" name="appointment_date" :required="hasAppointment"
                                                class="w-full text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] py-2 bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-black text-slate-700 uppercase tracking-wider mb-1">
                                                Lokasi Pertemuan <span class="text-rose-500">*</span>
                                            </label>
                                            <select name="appointment_location" :required="hasAppointment"
                                                class="w-full text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] py-2 bg-white font-medium">
                                                <option value="WORKSHOP">🏢 Di Workshop</option>
                                                <option value="CALL">📞 Telepon / Video Call</option>
                                                <option value="VISIT">🏠 Kunjungan Pelanggan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-black text-slate-700 uppercase tracking-wider mb-1">
                                            Catatan Janji Temu (Opsional)
                                        </label>
                                        <input type="text" name="appointment_notes" placeholder="Misal: Customer membawa sepatu langsung untuk cek lem ulang"
                                            class="w-full text-xs border-slate-200 rounded-xl focus:ring-2 focus:ring-[#22B086] focus:border-[#22B086] py-2 bg-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                            <button type="button" @click="showCreateModal = false"
                                class="px-4 py-2.5 bg-white text-slate-700 hover:bg-slate-100 rounded-xl text-xs font-bold border border-slate-200 transition-colors">
                                Batal
                            </button>
                            <button type="submit" :disabled="!selectedOrder"
                                :class="!selectedOrder ? 'opacity-50 cursor-not-allowed bg-slate-400' : 'bg-gradient-to-r from-[#22B086] to-[#1C8D6C] hover:from-[#1C8D6C] hover:to-[#177257] shadow-md shadow-[#22B086]/25'"
                                class="px-5 py-2.5 text-white rounded-xl text-xs font-black transition-all flex items-center gap-2">
                                <span>Simpan Keluhan</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Calendar Helper Script -->
    <script>
        function appointmentCalendar(eventsData) {
            return {
                events: eventsData || [],
                currentDate: new Date(),
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                monthNames: [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ],
                dayNames: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],

                init() {},

                prevMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                },

                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                },

                goToToday() {
                    const today = new Date();
                    this.currentMonth = today.getMonth();
                    this.currentYear = today.getFullYear();
                },

                get calendarDays() {
                    const year = this.currentYear;
                    const month = this.currentMonth;

                    const firstDayIndex = new Date(year, month, 1).getDay();
                    const adjustedFirstDay = (firstDayIndex === 0 ? 6 : firstDayIndex - 1);

                    const daysInCurrentMonth = new Date(year, month + 1, 0).getDate();
                    const daysInPrevMonth = new Date(year, month, 0).getDate();

                    const today = new Date();
                    const isTodayYearMonth = (today.getFullYear() === year && today.getMonth() === month);

                    const days = [];

                    // 1. Previous month trailing days
                    for (let i = adjustedFirstDay - 1; i >= 0; i--) {
                        const dateNum = daysInPrevMonth - i;
                        const prevMonthIndex = month === 0 ? 11 : month - 1;
                        const prevYear = month === 0 ? year - 1 : year;
                        const dateString = `${prevYear}-${String(prevMonthIndex + 1).padStart(2, '0')}-${String(dateNum).padStart(2, '0')}`;

                        days.push({
                            date: dateNum,
                            isCurrentMonth: false,
                            isToday: false,
                            dateString: dateString,
                            events: this.events.filter(e => e.date === dateString)
                        });
                    }

                    // 2. Current month days
                    for (let d = 1; d <= daysInCurrentMonth; d++) {
                        const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                        const isToday = isTodayYearMonth && (today.getDate() === d);

                        days.push({
                            date: d,
                            isCurrentMonth: true,
                            isToday: isToday,
                            dateString: dateString,
                            events: this.events.filter(e => e.date === dateString)
                        });
                    }

                    // 3. Next month leading days to complete the 35 or 42 grid cells
                    const remainingCells = 35 - days.length >= 0 ? 35 - days.length : 42 - days.length;
                    for (let nextDay = 1; nextDay <= remainingCells; nextDay++) {
                        const nextMonthIndex = month === 11 ? 0 : month + 1;
                        const nextYear = month === 11 ? year + 1 : year;
                        const dateString = `${nextYear}-${String(nextMonthIndex + 1).padStart(2, '0')}-${String(nextDay).padStart(2, '0')}`;

                        days.push({
                            date: nextDay,
                            isCurrentMonth: false,
                            isToday: false,
                            dateString: dateString,
                            events: this.events.filter(e => e.date === dateString)
                        });
                    }

                    return days;
                },

                openEventPreview(event) {
                    this.previewEvent = event;
                }
            };
        }
    </script>
</x-app-layout>
