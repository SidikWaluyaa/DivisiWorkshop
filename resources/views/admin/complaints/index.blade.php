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
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-teal-600 via-teal-700 to-orange-500 rounded-2xl shadow-xl p-6 lg:p-8 text-white relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
            </div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 backdrop-blur-md p-3.5 rounded-2xl shadow-inner border border-white/20">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl lg:text-3xl font-black tracking-tight">Keluhan & Janji Temu</h1>
                            <span class="bg-white/20 text-white text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full border border-white/20">Internal Modul</span>
                        </div>
                        <p class="text-white/90 text-sm font-medium mt-1">Kelola keluhan pelanggan SPK internal, tindak lanjut, dan agenda temu customer.</p>
                    </div>
                </div>
                <div class="flex items-center flex-wrap gap-2.5">
                    <button type="button" @click="openCreateModal()" 
                        class="px-4 py-2.5 bg-white text-teal-800 hover:bg-teal-50 rounded-xl text-sm font-black transition-all flex items-center gap-2 shadow-lg shadow-black/10 hover:shadow-xl hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Catat Keluhan Baru</span>
                    </button>
                    <a href="{{ route('admin.complaints.trash') }}" 
                        class="px-4 py-2.5 bg-white/15 hover:bg-white/25 text-white rounded-xl text-sm font-bold transition-all flex items-center gap-2 border border-white/20 shadow-sm backdrop-blur-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Sampah</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Status Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 lg:gap-4">
            <!-- Pending Card -->
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl shadow-sm border border-orange-200/80 p-4 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between mb-2">
                    <div class="bg-orange-500 text-white p-2 rounded-xl shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-black text-orange-600 group-hover:scale-110 transition-transform">{{ $statusCounts['PENDING'] }}</span>
                </div>
                <p class="text-[11px] font-bold text-orange-700 uppercase tracking-wider">Pending</p>
                <p class="text-[10px] text-orange-500">Menunggu respons</p>
            </div>

            <!-- Process Card -->
            <div class="bg-gradient-to-br from-blue-50 to-teal-50 rounded-2xl shadow-sm border border-teal-200/80 p-4 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between mb-2">
                    <div class="bg-teal-500 text-white p-2 rounded-xl shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-black text-teal-600 group-hover:scale-110 transition-transform">{{ $statusCounts['PROCESS'] }}</span>
                </div>
                <p class="text-[11px] font-bold text-teal-700 uppercase tracking-wider">Diproses</p>
                <p class="text-[10px] text-teal-500">Sedang ditangani</p>
            </div>

            <!-- Resolved Card -->
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl shadow-sm border border-emerald-200/80 p-4 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between mb-2">
                    <div class="bg-emerald-500 text-white p-2 rounded-xl shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-black text-emerald-600 group-hover:scale-110 transition-transform">{{ $statusCounts['RESOLVED'] }}</span>
                </div>
                <p class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Selesai</p>
                <p class="text-[10px] text-emerald-500">Terselesaikan</p>
            </div>

            <!-- Rejected Card -->
            <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-2xl shadow-sm border border-red-200/80 p-4 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between mb-2">
                    <div class="bg-red-500 text-white p-2 rounded-xl shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <span class="text-2xl font-black text-red-600 group-hover:scale-110 transition-transform">{{ $statusCounts['REJECTED'] }}</span>
                </div>
                <p class="text-[11px] font-bold text-red-700 uppercase tracking-wider">Ditolak</p>
                <p class="text-[10px] text-red-500">Tidak valid</p>
            </div>

            <!-- Appointments Card -->
            <div class="bg-gradient-to-br from-indigo-50 to-violet-50 rounded-2xl shadow-sm border border-indigo-200/80 p-4 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between mb-2">
                    <div class="bg-indigo-600 text-white p-2 rounded-xl shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-indigo-700 group-hover:scale-110 transition-transform block">{{ $statusCounts['with_appointments'] }}</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-[11px] font-bold text-indigo-800 uppercase tracking-wider">Janji Temu</p>
                    @if($statusCounts['today_appointments'] > 0)
                        <span class="bg-rose-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full animate-pulse">{{ $statusCounts['today_appointments'] }} Hari Ini</span>
                    @endif
                </div>
                <p class="text-[10px] text-indigo-500">Total terjadwal</p>
            </div>

            <!-- Total Card -->
            <div class="bg-gradient-to-br from-slate-50 to-gray-50 rounded-2xl shadow-sm border border-slate-200 p-4 hover:shadow-md transition-all group">
                <div class="flex items-center justify-between mb-2">
                    <div class="bg-slate-700 text-white p-2 rounded-xl shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-black text-slate-700 group-hover:scale-110 transition-transform">{{ $statusCounts['total'] }}</span>
                </div>
                <p class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Total Kasus</p>
                <p class="text-[10px] text-slate-500">Semua keluhan</p>
            </div>
        </div>

        <!-- Navigation Tabs: Table vs Calendar -->
        <div class="flex items-center gap-2 border-b border-gray-200 bg-white px-4 py-2.5 rounded-2xl shadow-sm">
            <button type="button" @click="activeTab = 'table'"
                :class="activeTab === 'table' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
                class="px-4 py-2 rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <span>Daftar Keluhan</span>
                <span :class="activeTab === 'table' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-700'" class="text-xs px-2 py-0.5 rounded-full font-black">
                    {{ $complaints->total() }}
                </span>
            </button>

            <button type="button" @click="activeTab = 'calendar'"
                :class="activeTab === 'calendar' ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
                class="px-4 py-2 rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Kalender Janji Temu</span>
                <span :class="activeTab === 'calendar' ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-700'" class="text-xs px-2 py-0.5 rounded-full font-black">
                    {{ count($calendarEvents) }}
                </span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: DAFTAR KELUHAN (TABLE VIEW)        -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'table'" x-cloak class="space-y-4">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <!-- Filters Header -->
                <div class="p-5 bg-gradient-to-r from-slate-50 to-gray-50 border-b border-gray-200">
                    <form action="{{ route('admin.complaints.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                        <input type="hidden" name="tab" value="table">
                        
                        <!-- Search Input -->
                        <div class="relative lg:col-span-2">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Cari SPK, Nama, HP, atau Sepatu..." 
                                class="w-full text-sm border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 pl-10 pr-4 py-2 bg-white shadow-sm">
                            <svg class="w-4 h-4 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <!-- Category Filter -->
                        <select name="category" onchange="this.form.submit()" 
                            class="text-sm border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white shadow-sm font-medium py-2">
                            <option value="">📋 Semua Kategori</option>
                            <option value="QUALITY" {{ request('category') == 'QUALITY' ? 'selected' : '' }}>🔍 Kualitas Hasil</option>
                            <option value="LATE" {{ request('category') == 'LATE' ? 'selected' : '' }}>⏰ Keterlambatan</option>
                            <option value="SERVICE" {{ request('category') == 'SERVICE' ? 'selected' : '' }}>💬 Pelayanan</option>
                            <option value="DAMAGE" {{ request('category') == 'DAMAGE' ? 'selected' : '' }}>⚠️ Kerusakan</option>
                            <option value="OTHER" {{ request('category') == 'OTHER' ? 'selected' : '' }}>📌 Lainnya</option>
                        </select>

                        <!-- Status Filter -->
                        <select name="status" onchange="this.form.submit()" 
                            class="text-sm border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white shadow-sm font-medium py-2">
                            <option value="">🎯 Semua Status</option>
                            <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>🟠 Pending</option>
                            <option value="PROCESS" {{ request('status') == 'PROCESS' ? 'selected' : '' }}>🔵 Diproses</option>
                            <option value="RESOLVED" {{ request('status') == 'RESOLVED' ? 'selected' : '' }}>🟢 Selesai</option>
                            <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>🔴 Ditolak</option>
                        </select>

                        <!-- Appointment Filter & Reset -->
                        <div class="flex items-center gap-2">
                            <select name="appointment_filter" onchange="this.form.submit()" 
                                class="flex-1 text-sm border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white shadow-sm font-medium py-2">
                                <option value="">📅 Semua Janji Temu</option>
                                <option value="has_appointment" {{ request('appointment_filter') == 'has_appointment' ? 'selected' : '' }}>Ada Janji</option>
                                <option value="today" {{ request('appointment_filter') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                                <option value="upcoming" {{ request('appointment_filter') == 'upcoming' ? 'selected' : '' }}>Mendatang</option>
                            </select>

                            @if(request()->anyFilled(['search', 'category', 'status', 'appointment_filter']))
                                <a href="{{ route('admin.complaints.index') }}" 
                                    class="p-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-700 text-sm font-bold flex items-center justify-center transition-colors border border-slate-200" title="Reset Filter">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gradient-to-r from-teal-50/80 to-orange-50/50 border-b-2 border-teal-200/80">
                            <tr>
                                <th class="px-5 py-3.5 text-xs font-black text-teal-800 uppercase tracking-wider">ID & Waktu</th>
                                <th class="px-5 py-3.5 text-xs font-black text-teal-800 uppercase tracking-wider">SPK & Pelanggan</th>
                                <th class="px-5 py-3.5 text-xs font-black text-teal-800 uppercase tracking-wider">Kategori & Keluhan</th>
                                <th class="px-5 py-3.5 text-xs font-black text-teal-800 uppercase tracking-wider">Janji Temu</th>
                                <th class="px-5 py-3.5 text-xs font-black text-teal-800 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-xs font-black text-teal-800 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($complaints as $complaint)
                                <tr class="hover:bg-teal-50/20 transition-colors group">
                                    <!-- ID & Waktu -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="bg-gradient-to-br from-teal-600 to-teal-700 text-white font-black text-xs px-2.5 py-1 rounded-lg shadow-sm">
                                                #{{ $complaint->id }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-600 font-semibold mt-1.5">{{ $complaint->created_at->format('d M Y') }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium">{{ $complaint->created_at->format('H:i') }} WIB</div>
                                    </td>

                                    <!-- SPK & Pelanggan -->
                                    <td class="px-5 py-4">
                                        @if($complaint->workOrder)
                                            <a href="{{ route('admin.orders.show', $complaint->workOrder->id) }}" target="_blank" 
                                                class="font-black text-teal-700 hover:text-teal-900 hover:underline flex items-center gap-1.5 text-sm">
                                                <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span>{{ $complaint->workOrder->spk_number }}</span>
                                            </a>
                                            <div class="text-[11px] text-gray-500 mt-0.5 font-medium">
                                                {{ $complaint->workOrder->shoe_brand ?? '-' }} {{ $complaint->workOrder->shoe_type ? '• ' . $complaint->workOrder->shoe_type : '' }}
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">SPK Tidak Terkait</span>
                                        @endif
                                        <div class="text-xs text-gray-900 font-bold mt-1.5">{{ $complaint->customer_name }}</div>
                                        <div class="text-xs text-gray-500 font-medium flex items-center gap-1">
                                            <span>📱 {{ $complaint->customer_phone }}</span>
                                        </div>
                                    </td>

                                    <!-- Kategori & Keluhan -->
                                    <td class="px-5 py-4 max-w-xs">
                                        @php
                                            $catConfigs = [
                                                'QUALITY' => ['bg' => 'bg-purple-100 text-purple-800 border-purple-200', 'icon' => '🔍'],
                                                'DAMAGE'  => ['bg' => 'bg-rose-100 text-rose-800 border-rose-200', 'icon' => '⚠️'],
                                                'LATE'    => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'icon' => '⏰'],
                                                'SERVICE' => ['bg' => 'bg-sky-100 text-sky-800 border-sky-200', 'icon' => '💬'],
                                                'OTHER'   => ['bg' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => '📌'],
                                            ];
                                            $cConf = $catConfigs[$complaint->category] ?? $catConfigs['OTHER'];
                                        @endphp
                                        <div class="flex items-center gap-1.5 mb-1.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold border {{ $cConf['bg'] }}">
                                                <span>{{ $cConf['icon'] }}</span>
                                                <span>{{ $complaint->category_label }}</span>
                                            </span>
                                            @if($complaint->photos && count($complaint->photos) > 0)
                                                <span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-1.5 py-0.5 rounded border border-gray-200 flex items-center gap-0.5" title="Ada {{ count($complaint->photos) }} foto bukti">
                                                    📷 {{ count($complaint->photos) }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-700 leading-relaxed line-clamp-2" title="{{ $complaint->description }}">
                                            {{ $complaint->description }}
                                        </p>
                                    </td>

                                    <!-- Janji Temu -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($complaint->appointment_date)
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-1.5 font-bold text-xs text-gray-900">
                                                    <svg class="w-3.5 h-3.5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    <span>{{ $complaint->appointment_date->format('d M Y') }}</span>
                                                    <span class="text-teal-700 bg-teal-50 px-1.5 py-0.2 rounded text-[10px] font-black">{{ $complaint->appointment_date->format('H:i') }}</span>
                                                </div>
                                                <div class="text-[11px] text-gray-600 font-medium">
                                                    {{ $complaint->location_label }}
                                                </div>
                                                <div>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $complaint->appointment_badge['bg'] }}">
                                                        <span class="w-1.5 h-1.5 rounded-full {{ $complaint->appointment_badge['dot'] }}"></span>
                                                        {{ $complaint->appointment_badge['label'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">- Belum ada janji -</span>
                                        @endif
                                    </td>

                                    <!-- Status Keluhan -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @php
                                            $statStyles = [
                                                'PENDING'  => 'bg-amber-100 text-amber-800 border-amber-300',
                                                'PROCESS'  => 'bg-teal-100 text-teal-800 border-teal-300',
                                                'RESOLVED' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                                'REJECTED' => 'bg-rose-100 text-rose-800 border-rose-300',
                                            ];
                                            $badgeStyle = $statStyles[$complaint->status] ?? 'bg-gray-100 text-gray-800 border-gray-300';
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black border {{ $badgeStyle }} shadow-sm">
                                            {{ $complaint->status }}
                                        </span>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.complaints.show', $complaint->id) }}" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-teal-50 text-teal-700 hover:bg-teal-100 rounded-lg text-xs font-bold transition-colors border border-teal-200">
                                                Detail
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                            <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST" onsubmit="return confirm('Pindahkan keluhan ini ke sampah?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus ke Sampah">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-teal-50 p-4 rounded-2xl mb-3 text-teal-600">
                                                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <p class="text-base font-bold text-gray-800">Tidak Ada Data Keluhan</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Belum ada keluhan yang sesuai dengan filter pencarian.</p>
                                            <button type="button" @click="openCreateModal()" class="mt-4 px-4 py-2 bg-teal-600 text-white rounded-xl text-xs font-bold hover:bg-teal-700 transition-colors shadow-sm">
                                                + Catat Keluhan Baru
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
                    <div class="px-6 py-4 border-t border-gray-100 bg-slate-50/50">
                        {{ $complaints->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: KALENDER JANJI TEMU                -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'calendar'" x-cloak class="space-y-6" x-data="appointmentCalendar(@js($calendarEvents))">
            <!-- Calendar Container -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <!-- Calendar Controls Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white p-3 rounded-2xl shadow-md">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-900" x-text="monthNames[currentMonth] + ' ' + currentYear"></h2>
                            <p class="text-xs text-gray-500 font-medium">Klik pada tanggal atau badge untuk melihat detail janji temu customer.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="prevMonth()" 
                            class="p-2.5 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors" title="Bulan Sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" @click="goToToday()" 
                            class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-100 transition-colors">
                            Hari Ini
                        </button>
                        <button type="button" @click="nextMonth()" 
                            class="p-2.5 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors" title="Bulan Berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex flex-wrap items-center gap-3 pt-4 pb-2 text-xs font-medium text-gray-600">
                    <span class="font-bold text-gray-800">Keterangan:</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Dijadwalkan</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Selesai</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Dibatalkan</span>
                    <span class="text-gray-300">|</span>
                    <span>🏢 Workshop</span>
                    <span>📞 Telepon</span>
                    <span>🏠 Kunjungan</span>
                </div>

                <!-- Calendar Grid Table -->
                <div class="mt-4 border border-gray-200 rounded-2xl overflow-hidden shadow-inner">
                    <!-- Weekdays Header -->
                    <div class="grid grid-cols-7 bg-slate-50 border-b border-gray-200 text-center">
                        <template x-for="(day, idx) in dayNames" :key="idx">
                            <div class="py-2.5 text-xs font-black text-slate-700 uppercase tracking-wider" x-text="day"></div>
                        </template>
                    </div>

                    <!-- Days Cells Grid -->
                    <div class="grid grid-cols-7 divide-x divide-y divide-gray-200 bg-white">
                        <template x-for="(cell, index) in calendarDays" :key="index">
                            <div class="min-h-[110px] p-2 transition-colors relative flex flex-col justify-between"
                                :class="{
                                    'bg-slate-50/60 text-gray-400': !cell.isCurrentMonth,
                                    'bg-white hover:bg-slate-50/80': cell.isCurrentMonth,
                                    'bg-teal-50/40 ring-2 ring-teal-500 ring-inset': cell.isToday
                                }">
                                <!-- Date Number Header -->
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-black rounded-full w-6 h-6 flex items-center justify-center"
                                        :class="{
                                            'bg-teal-600 text-white shadow-sm': cell.isToday,
                                            'text-gray-800': cell.isCurrentMonth && !cell.isToday,
                                            'text-gray-400': !cell.isCurrentMonth
                                        }"
                                        x-text="cell.date">
                                    </span>
                                    <template x-if="cell.events.length > 0">
                                        <span class="text-[10px] font-black text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded-full" x-text="cell.events.length + ' Janji'"></span>
                                    </template>
                                </div>

                                <!-- Events List inside Day -->
                                <div class="space-y-1 overflow-y-auto max-h-[85px] pr-0.5">
                                    <template x-for="event in cell.events" :key="event.id">
                                        <button type="button" @click="openEventPreview(event)"
                                            class="w-full text-left p-1 rounded-lg text-[10px] font-bold border transition-transform hover:scale-[1.02] active:scale-[0.98] block truncate"
                                            :class="{
                                                'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100': event.appointment_status === 'SCHEDULED',
                                                'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100': event.appointment_status === 'COMPLETED',
                                                'bg-rose-50 text-rose-800 border-rose-200 hover:bg-rose-100': event.appointment_status === 'CANCELLED'
                                            }">
                                            <div class="flex items-center gap-1 font-black">
                                                <span x-text="event.time"></span>
                                                <span class="truncate" x-text="event.spk_number"></span>
                                            </div>
                                            <div class="text-[9px] text-gray-600 font-medium truncate" x-text="event.customer_name"></div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Upcoming Agenda List (Quick Glance) -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="bg-teal-500 text-white p-2 rounded-xl">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900">Agenda Mendatang</h3>
                            <p class="text-xs text-gray-500">Janji temu yang akan datang dalam waktu dekat</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold bg-teal-50 text-teal-700 px-3 py-1 rounded-full border border-teal-200">
                        {{ count($upcomingAppointments) }} Agenda
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($upcomingAppointments as $item)
                        <div class="bg-gradient-to-br from-slate-50 to-gray-50 p-4 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-teal-600 text-white text-[10px] font-black px-2 py-0.5 rounded">
                                    {{ $item->workOrder ? $item->workOrder->spk_number : 'Keluhan #' . $item->id }}
                                </span>
                                <span class="text-[11px] font-bold text-gray-600">
                                    {{ $item->appointment_date ? $item->appointment_date->format('d M Y, H:i') : '-' }} WIB
                                </span>
                            </div>
                            <div class="font-bold text-sm text-gray-900">{{ $item->customer_name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                <span>{{ $item->location_label }}</span>
                                <span>•</span>
                                <span>📱 {{ $item->customer_phone }}</span>
                            </div>
                            @if($item->appointment_notes)
                                <p class="text-xs text-gray-600 bg-white p-2 rounded-lg border border-gray-100 mt-2 italic">
                                    "{{ $item->appointment_notes }}"
                                </p>
                            @endif
                            <div class="mt-3 pt-2 border-t border-gray-200 flex items-center justify-between">
                                <span class="text-[10px] font-bold text-gray-500">{{ $item->category_label }}</span>
                                <a href="{{ route('admin.complaints.show', $item->id) }}" class="text-xs font-bold text-teal-600 hover:text-teal-800 flex items-center gap-1">
                                    Detail & Tindak Lanjut &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-gray-400 italic text-sm">
                            Tidak ada janji temu mendatang yang belum diselesaikan.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Modal Preview Event (Quick View) -->
            <div x-show="previewEvent !== null" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="previewEvent !== null" 
                        x-transition:enter="ease-out duration-300" 
                        x-transition:enter-start="opacity-0" 
                        x-transition:enter-end="opacity-100" 
                        x-transition:leave="ease-in duration-200" 
                        x-transition:leave-start="opacity-100" 
                        x-transition:leave-end="opacity-0" 
                        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                        @click="previewEvent = null"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="previewEvent !== null" 
                        x-transition:enter="ease-out duration-300" 
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                        x-transition:leave="ease-in duration-200" 
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                        class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                        
                        <template x-if="previewEvent">
                            <div class="p-6 space-y-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="bg-teal-600 text-white font-black text-xs px-2.5 py-1 rounded-lg" x-text="previewEvent.spk_number"></span>
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full border"
                                            :class="previewEvent.appointment_badge.bg" x-text="previewEvent.appointment_badge.label"></span>
                                    </div>
                                    <button type="button" @click="previewEvent = null" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <div>
                                    <h4 class="text-xl font-black text-gray-900" x-text="previewEvent.customer_name"></h4>
                                    <div class="text-xs text-gray-500 flex items-center gap-2 mt-1">
                                        <span x-text="'📱 ' + previewEvent.customer_phone"></span>
                                        <span>•</span>
                                        <span x-text="'👟 ' + previewEvent.shoe_info"></span>
                                    </div>
                                </div>

                                <div class="bg-slate-50 p-3.5 rounded-xl border border-gray-200 space-y-2 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-500 font-semibold">Jadwal Temu:</span>
                                        <span class="font-black text-gray-900" x-text="previewEvent.datetime_formatted"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-500 font-semibold">Lokasi Pertemuan:</span>
                                        <span class="font-bold text-gray-900" x-text="previewEvent.location_label"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-500 font-semibold">Kategori Keluhan:</span>
                                        <span class="font-bold text-teal-700" x-text="previewEvent.category"></span>
                                    </div>
                                </div>

                                <template x-if="previewEvent.appointment_notes && previewEvent.appointment_notes !== '-'">
                                    <div class="bg-amber-50/70 p-3 rounded-xl border border-amber-200 text-xs">
                                        <span class="font-bold text-amber-900 block mb-0.5">Catatan Janji Temu:</span>
                                        <p class="text-amber-800" x-text="previewEvent.appointment_notes"></p>
                                    </div>
                                </template>

                                <div>
                                    <span class="text-xs font-bold text-gray-700 block mb-1">Ringkasan Keluhan:</span>
                                    <p class="text-xs text-gray-600 bg-gray-50 p-3 rounded-xl border border-gray-100" x-text="previewEvent.description"></p>
                                </div>

                                <div class="pt-2 flex items-center gap-2">
                                    <a :href="'https://wa.me/' + previewEvent.customer_phone.replace(/[^0-9]/g, '')" target="_blank"
                                        class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl text-center shadow-md transition-colors flex items-center justify-center gap-1.5">
                                        <span>💬 Chat WhatsApp</span>
                                    </a>
                                    <a :href="previewEvent.show_url" 
                                        class="flex-1 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl text-center shadow-md transition-colors">
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
                    x-transition:enter="ease-out duration-300" 
                    x-transition:enter-start="opacity-0" 
                    x-transition:enter-end="opacity-100" 
                    x-transition:leave="ease-in duration-200" 
                    x-transition:leave-start="opacity-100" 
                    x-transition:leave-end="opacity-0" 
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                    @click="showCreateModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showCreateModal" 
                    x-transition:enter="ease-out duration-300" 
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave="ease-in duration-200" 
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
                    
                    <form action="{{ route('admin.complaints.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-teal-600 to-orange-500 p-6 text-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="bg-white/20 p-2.5 rounded-xl backdrop-blur-sm">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black">Catat Keluhan Pelanggan</h3>
                                    <p class="text-white/80 text-xs font-medium">Input manual keluhan internal & agenda janji temu</p>
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
                                <label class="block text-xs font-black text-gray-700 uppercase tracking-wider">
                                    1. Hubungkan ke SPK Order <span class="text-rose-500">*</span>
                                </label>

                                <!-- If Order Selected -->
                                <template x-if="selectedOrder">
                                    <div class="bg-teal-50 border-2 border-teal-500/40 rounded-2xl p-4 flex items-start justify-between gap-3">
                                        <input type="hidden" name="work_order_id" :value="selectedOrder.id">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="bg-teal-600 text-white font-black text-xs px-2.5 py-0.5 rounded-lg" x-text="selectedOrder.spk_number"></span>
                                                <span class="text-xs font-bold text-teal-800" x-text="selectedOrder.status"></span>
                                            </div>
                                            <div class="text-sm font-black text-gray-900" x-text="selectedOrder.customer_name"></div>
                                            <div class="text-xs text-gray-600 flex items-center gap-2">
                                                <span x-text="'📱 ' + (selectedOrder.customer_phone || '-')"></span>
                                                <span>•</span>
                                                <span x-text="'👟 ' + (selectedOrder.shoe_brand || '') + ' ' + (selectedOrder.shoe_type || '')"></span>
                                            </div>
                                        </div>
                                        <button type="button" @click="resetOrder()" 
                                            class="text-xs font-bold text-rose-600 hover:text-rose-800 bg-white px-2.5 py-1.5 rounded-lg border border-rose-200 shadow-sm transition-colors">
                                            Ganti SPK
                                        </button>
                                    </div>
                                </template>

                                <!-- If Order NOT Selected: Live Search Input -->
                                <template x-if="!selectedOrder">
                                    <div class="relative">
                                        <input type="text" x-model="searchQuery" @input.debounce.300ms="searchSPK()"
                                            placeholder="Ketik No SPK, Nama Customer, No HP, atau Sepatu..."
                                            class="w-full text-sm border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 pl-10 pr-4 py-2.5 bg-gray-50 focus:bg-white shadow-sm font-medium">
                                        <svg class="w-5 h-5 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <div x-show="isSearching" class="absolute right-3 top-3 text-xs text-teal-600 font-bold animate-pulse">
                                            Mencari...
                                        </div>

                                        <!-- Results Dropdown -->
                                        <div x-show="searchResults.length > 0" class="absolute left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl z-20 max-h-60 overflow-y-auto divide-y divide-gray-100">
                                            <template x-for="item in searchResults" :key="item.id">
                                                <div @click="selectOrder(item)" class="p-3 hover:bg-teal-50/50 cursor-pointer transition-colors flex items-center justify-between">
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-black text-xs text-teal-700" x-text="item.spk_number"></span>
                                                            <span class="text-[10px] text-gray-500" x-text="'(' + item.status + ')'"></span>
                                                        </div>
                                                        <div class="text-xs font-bold text-gray-900 mt-0.5" x-text="item.customer_name"></div>
                                                        <div class="text-[11px] text-gray-500" x-text="(item.shoe_brand || '') + ' • ' + (item.customer_phone || '')"></div>
                                                    </div>
                                                    <span class="text-xs font-bold text-teal-600">Pilih &rarr;</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- STEP 2: DETAIL KELUHAN -->
                            <div class="space-y-4 pt-2 border-t border-gray-100">
                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">
                                        2. Kategori Keluhan <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="category" required class="w-full text-sm border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white font-medium py-2.5">
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="QUALITY">🔍 Kualitas Hasil Reparasi</option>
                                        <option value="LATE">⏰ Keterlambatan Pengerjaan</option>
                                        <option value="SERVICE">💬 Pelayanan CS / Workshop</option>
                                        <option value="DAMAGE">⚠️ Kerusakan Produk / Sepatu</option>
                                        <option value="OTHER">📌 Lainnya</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">
                                        Deskripsi Keluhan <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea name="description" rows="3" required
                                        placeholder="Ceritakan detail keluhan atau permasalahan yang disampaikan oleh customer..."
                                        class="w-full text-sm border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 p-3"></textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-1">
                                        Foto Bukti / Pendukung (Opsional, max 5 file)
                                    </label>
                                    <input type="file" name="photos[]" multiple accept="image/*"
                                        class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 border border-gray-200 rounded-xl p-1 bg-white">
                                </div>
                            </div>

                            <!-- STEP 3: ATUR JANJI TEMU (TOGGLE) -->
                            <div class="pt-3 border-t border-gray-100 space-y-3">
                                <div class="flex items-center justify-between p-3 bg-indigo-50/60 rounded-xl border border-indigo-100">
                                    <div class="flex items-center gap-2.5">
                                        <div class="bg-indigo-600 text-white p-1.5 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label for="has_appointment_toggle" class="text-xs font-black text-indigo-900 cursor-pointer">
                                                Buat Jadwal Janji Temu?
                                            </label>
                                            <p class="text-[10px] text-indigo-700">Jika diisi, agenda akan otomatis muncul di Tab Kalender</p>
                                        </div>
                                    </div>
                                    <input type="checkbox" id="has_appointment_toggle" x-model="hasAppointment"
                                        class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500 cursor-pointer">
                                </div>

                                <!-- Collapsible Appointment Form -->
                                <div x-show="hasAppointment" x-transition class="space-y-3 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[11px] font-black text-gray-700 uppercase tracking-wider mb-1">
                                                Tanggal & Jam Temu <span class="text-rose-500">*</span>
                                            </label>
                                            <input type="datetime-local" name="appointment_date" :required="hasAppointment"
                                                class="w-full text-xs border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 py-2 bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-black text-gray-700 uppercase tracking-wider mb-1">
                                                Lokasi Pertemuan <span class="text-rose-500">*</span>
                                            </label>
                                            <select name="appointment_location" :required="hasAppointment"
                                                class="w-full text-xs border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 py-2 bg-white font-medium">
                                                <option value="WORKSHOP">🏢 Di Workshop</option>
                                                <option value="CALL">📞 Telepon / Video Call</option>
                                                <option value="VISIT">🏠 Kunjungan Pelanggan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-black text-gray-700 uppercase tracking-wider mb-1">
                                            Catatan Janji Temu (Opsional)
                                        </label>
                                        <input type="text" name="appointment_notes" placeholder="Misal: Customer membawa sepatu langsung untuk cek lem ulang"
                                            class="w-full text-xs border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 py-2 bg-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2.5">
                            <button type="button" @click="showCreateModal = false"
                                class="px-4 py-2.5 bg-white text-gray-700 hover:bg-gray-100 rounded-xl text-xs font-bold border border-gray-200 transition-colors">
                                Batal
                            </button>
                            <button type="submit" :disabled="!selectedOrder"
                                :class="!selectedOrder ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-600/20'"
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

                init() {
                    // Initialize
                },

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

                    const firstDayIndex = new Date(year, month, 1).getDay(); // 0 is Sun, 1 is Mon...
                    // Adjust to start on Monday (0: Mon, ..., 6: Sun)
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
