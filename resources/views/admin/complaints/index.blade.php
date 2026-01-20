<x-app-layout>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-teal-500 via-teal-600 to-orange-500 rounded-2xl shadow-xl p-8 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black">Keluhan Pelanggan</h1>
                        <p class="text-white/80 text-sm font-medium mt-1">Kelola dan tanggapi keluhan customer</p>
                    </div>
                </div>
                <a href="{{ route('admin.complaints.trash') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl text-sm font-bold transition-all flex items-center gap-2 border border-white/20 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Sampah / Deleted</span>
                </a>
            </div>
        </div>

        <!-- Status Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Pending Card -->
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl shadow-md border-2 border-orange-200 p-5 hover:shadow-lg transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-gradient-to-br from-orange-400 to-orange-500 p-2.5 rounded-lg shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-3xl font-black text-orange-600 group-hover:scale-110 transition-transform">{{ $statusCounts['PENDING'] }}</span>
                </div>
                <p class="text-xs font-bold text-orange-700 uppercase tracking-wider">Pending</p>
                <p class="text-[10px] text-orange-500 mt-0.5">Menunggu tindakan</p>
            </div>

            <!-- Process Card -->
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl shadow-md border-2 border-teal-200 p-5 hover:shadow-lg transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-gradient-to-br from-teal-400 to-teal-500 p-2.5 rounded-lg shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <span class="text-3xl font-black text-teal-600 group-hover:scale-110 transition-transform">{{ $statusCounts['PROCESS'] }}</span>
                </div>
                <p class="text-xs font-bold text-teal-700 uppercase tracking-wider">Diproses</p>
                <p class="text-[10px] text-teal-500 mt-0.5">Sedang ditangani</p>
            </div>

            <!-- Resolved Card -->
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl shadow-md border-2 border-emerald-200 p-5 hover:shadow-lg transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-gradient-to-br from-emerald-400 to-emerald-500 p-2.5 rounded-lg shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-3xl font-black text-emerald-600 group-hover:scale-110 transition-transform">{{ $statusCounts['RESOLVED'] }}</span>
                </div>
                <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Selesai</p>
                <p class="text-[10px] text-emerald-500 mt-0.5">Telah diselesaikan</p>
            </div>

            <!-- Rejected Card -->
            <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-xl shadow-md border-2 border-red-200 p-5 hover:shadow-lg transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-gradient-to-br from-red-400 to-red-500 p-2.5 rounded-lg shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <span class="text-3xl font-black text-red-600 group-hover:scale-110 transition-transform">{{ $statusCounts['REJECTED'] }}</span>
                </div>
                <p class="text-xs font-bold text-red-700 uppercase tracking-wider">Ditolak</p>
                <p class="text-[10px] text-red-500 mt-0.5">Tidak valid</p>
            </div>

            <!-- Total Card -->
            <div class="bg-gradient-to-br from-slate-50 to-gray-50 rounded-xl shadow-md border-2 border-slate-200 p-5 hover:shadow-lg transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-gradient-to-br from-slate-400 to-slate-500 p-2.5 rounded-lg shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="text-3xl font-black text-slate-600 group-hover:scale-110 transition-transform">{{ $statusCounts['total'] }}</span>
                </div>
                <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Total</p>
                <p class="text-[10px] text-slate-500 mt-0.5">Semua keluhan</p>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Filters Header -->
            <div class="p-6 bg-gradient-to-r from-slate-50 to-gray-50 border-b border-gray-200">
                <form action="{{ route('admin.complaints.index') }}" method="GET" class="flex flex-col lg:flex-row gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="Cari SPK, Nama Customer, atau Nomor HP..." 
                            class="w-full text-sm border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 pl-10 pr-4 py-2.5 bg-white shadow-sm">
                        <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- Category Filter -->
                    <select name="category" onchange="this.form.submit()" 
                        class="text-sm border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white shadow-sm font-medium">
                        <option value="">📋 Semua Kategori</option>
                        <option value="QUALITY" {{ request('category') == 'QUALITY' ? 'selected' : '' }}>🔍 Kualitas</option>
                        <option value="LATE" {{ request('category') == 'LATE' ? 'selected' : '' }}>⏰ Terlambat</option>
                        <option value="SERVICE" {{ request('category') == 'SERVICE' ? 'selected' : '' }}>💬 Layanan</option>
                        <option value="DAMAGE" {{ request('category') == 'DAMAGE' ? 'selected' : '' }}>⚠️ Kerusakan</option>
                        <option value="OTHER" {{ request('category') == 'OTHER' ? 'selected' : '' }}>📌 Lainnya</option>
                    </select>

                    <!-- Status Filter -->
                    <select name="status" onchange="this.form.submit()" 
                        class="text-sm border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white shadow-sm font-medium">
                        <option value="">🎯 Semua Status</option>
                        <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>🟠 Pending</option>
                        <option value="PROCESS" {{ request('status') == 'PROCESS' ? 'selected' : '' }}>🔵 Diproses</option>
                        <option value="RESOLVED" {{ request('status') == 'RESOLVED' ? 'selected' : '' }}>🟢 Selesai</option>
                        <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>🔴 Ditolak</option>
                    </select>

                    @if(request()->anyFilled(['search', 'category', 'status']))
                        <a href="{{ route('admin.complaints.index') }}" 
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-700 text-sm font-bold flex items-center justify-center transition-colors border border-slate-200 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-teal-50 to-orange-50 border-b-2 border-teal-200">
                        <tr>
                            <th class="px-6 py-4 text-left">
                                <span class="text-xs font-black text-teal-700 uppercase tracking-wider">ID & Waktu</span>
                            </th>
                            <th class="px-6 py-4 text-left">
                                <span class="text-xs font-black text-teal-700 uppercase tracking-wider">Info Pesanan</span>
                            </th>
                            <th class="px-6 py-4 text-left">
                                <span class="text-xs font-black text-teal-700 uppercase tracking-wider">Kategori & Keluhan</span>
                            </th>
                            <th class="px-6 py-4 text-left">
                                <span class="text-xs font-black text-teal-700 uppercase tracking-wider">Status</span>
                            </th>
                            <th class="px-6 py-4 text-right">
                                <span class="text-xs font-black text-teal-700 uppercase tracking-wider">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($complaints as $complaint)
                            <tr class="hover:bg-gradient-to-r hover:from-teal-50/30 hover:to-orange-50/30 transition-all group">
                                <!-- ID & Time -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="bg-gradient-to-br from-teal-500 to-teal-600 text-white font-black text-xs px-2.5 py-1 rounded-lg shadow-sm">
                                            #{{ $complaint->id }}
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1 block">{{ $complaint->created_at->format('d M Y') }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $complaint->created_at->format('H:i') }}</span>
                                </td>

                                <!-- Order Info -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        {{ optional($complaint->workOrder)->spk_number ?? 'No SPK' }}
                                    </div>
                                    <div class="text-xs text-gray-600 mt-1 font-medium">{{ $complaint->customer_name }}</div>
                                    <div class="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                        </svg>
                                        {{ $complaint->customer_phone }}
                                    </div>
                                </td>

                                <!-- Category & Description -->
                                <td class="px-6 py-4">
                                    @php
                                        $categoryConfig = [
                                            'QUALITY' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'border' => 'border-purple-300', 'icon' => '🔍', 'label' => 'Kualitas'],
                                            'DAMAGE' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'border' => 'border-red-300', 'icon' => '⚠️', 'label' => 'Kerusakan'],
                                            'LATE' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-300', 'icon' => '⏰', 'label' => 'Terlambat'],
                                            'SERVICE' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-300', 'icon' => '💬', 'label' => 'Layanan'],
                                            'OTHER' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-300', 'icon' => '📌', 'label' => 'Lainnya'],
                                        ];
                                        $config = $categoryConfig[$complaint->category] ?? $categoryConfig['OTHER'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold border-2 {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }} mb-2">
                                        <span>{{ $config['icon'] }}</span>
                                        {{ $config['label'] }}
                                    </span>
                                    <p class="text-gray-600 text-xs leading-relaxed max-w-xs" title="{{ $complaint->description }}">
                                        {{ Str::limit($complaint->description, 80) }}
                                    </p>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4">
                                    @php
                                        $statusConfig = [
                                            'PENDING' => ['bg' => 'from-orange-400 to-orange-500', 'text' => 'text-white', 'icon' => '🟠'],
                                            'PROCESS' => ['bg' => 'from-teal-400 to-teal-500', 'text' => 'text-white', 'icon' => '🔵'],
                                            'RESOLVED' => ['bg' => 'from-emerald-400 to-emerald-500', 'text' => 'text-white', 'icon' => '🟢'],
                                            'REJECTED' => ['bg' => 'from-red-400 to-red-500', 'text' => 'text-white', 'icon' => '🔴'],
                                        ];
                                        $statusStyle = $statusConfig[$complaint->status] ?? $statusConfig['PENDING'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black bg-gradient-to-r {{ $statusStyle['bg'] }} {{ $statusStyle['text'] }} shadow-md">
                                        <span>{{ $statusStyle['icon'] }}</span>
                                        {{ $complaint->status }}
                                    </span>
                                </td>

                                <!-- Action -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.complaints.show', $complaint->id) }}" 
                                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white text-xs font-bold rounded-lg shadow-md hover:shadow-lg transition-all">
                                            Detail
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                        
                                        <form action="{{ route('admin.complaints.destroy', $complaint->id) }}" method="POST" onsubmit="return confirm('Pindahkan ke Sampah?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition-colors border border-red-200" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gradient-to-br from-teal-100 to-orange-100 p-6 rounded-full mb-4">
                                            <svg class="h-16 w-16 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-lg font-bold text-gray-700">Belum Ada Keluhan</p>
                                        <p class="text-sm text-gray-500 mt-1">Keluhan pelanggan akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($complaints->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gradient-to-r from-slate-50 to-gray-50">
                    {{ $complaints->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
