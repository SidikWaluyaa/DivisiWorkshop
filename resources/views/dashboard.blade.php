<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Analytics') }}
        </h2>
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            
            {{-- Location Overview --}}
            <div class="dashboard-card" x-data="{ activeLocation: null }">
                <div class="dashboard-card-header">
                    <h3 class="dashboard-card-title">📍 Pelacakan Workshop</h3>
                </div>
                <div class="dashboard-card-body">
                    {{-- Location Summary Cards --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                        @foreach($locations as $location => $orders)
                            <div class="location-card cursor-pointer" 
                                 @click="activeLocation = activeLocation === '{{ $location }}' ? null : '{{ $location }}'">
                                <div class="text-center">
                                    <div class="location-count">{{ $orders->count() }}</div>
                                    <div class="location-name line-clamp-2">{{ $location }}</div>
                                </div>
                                @if($orders->count() > 0)
                                    <div class="text-center mt-2">
                                        <svg class="w-5 h-5 mx-auto transition-transform duration-300" 
                                             :class="{ 'rotate-180': activeLocation === '{{ $location }}' }"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Expanded Tables (Full Width) --}}
                    @foreach($locations as $location => $orders)
                        @if($orders->count() > 0)
                        <div x-show="activeLocation === '{{ $location }}'" 
                             x-collapse
                             class="mb-4 bg-white rounded-lg border-2 border-teal-200 shadow-lg overflow-hidden">
                            {{-- Table Header --}}
                            <div class="bg-gradient-to-r from-teal-600 to-orange-500 px-4 py-3">
                                <h4 class="font-bold text-white">📍 {{ $location }} - {{ $orders->count() }} Sepatu</h4>
                            </div>
                            
                            {{-- Table Content --}}
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gradient-to-r from-teal-50 to-orange-50 border-b-2 border-teal-200 sticky top-0">
                                        <tr>
                                            <th class="text-left py-3 px-4 font-bold text-teal-700">No SPK</th>
                                            <th class="text-left py-3 px-4 font-bold text-teal-700">Pelanggan</th>
                                            <th class="text-left py-3 px-4 font-bold text-teal-700">Merek</th>
                                            <th class="text-center py-3 px-4 font-bold text-teal-700">Tanggal Masuk</th>
                                            <th class="text-center py-3 px-4 font-bold text-teal-700">Estimasi</th>
                                            <th class="text-center py-3 px-4 font-bold text-teal-700">Selesai</th>
                                            <th class="text-center py-3 px-4 font-bold text-teal-700">Status</th>
                                        </tr>
                                    </thead>
                                </table>
                                
                                {{-- Scrollable tbody wrapper --}}
                                <div class="{{ $orders->count() > 3 ? 'max-h-[240px] overflow-y-auto dashboard-scroll' : '' }}">
                                    <table class="w-full text-sm">
                                        <tbody>
                                            @foreach($orders as $order)
                                            <tr class="border-b border-gray-100 hover:bg-teal-50 transition-colors">
                                                <td class="py-3 px-4">
                                                    <span class="font-mono text-xs font-bold text-teal-700">{{ $order->spk_number }}</span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    <span class="font-medium text-gray-900">{{ $order->customer_name }}</span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    <span class="text-gray-700 text-xs">{{ $order->shoe_brand ?? '-' }}</span>
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <div class="flex items-center justify-center gap-1 text-xs text-blue-600">
                                                        <span>📥</span>
                                                        <span>{{ \Carbon\Carbon::parse($order->entry_date)->format('d/m/Y') }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <div class="flex items-center justify-center gap-1 text-xs {{ \Carbon\Carbon::parse($order->estimation_date)->isPast() && $order->status !== 'SELESAI' ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                                        <span>⏱️</span>
                                                        <span>{{ \Carbon\Carbon::parse($order->estimation_date)->format('d/m/Y') }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    @if($order->finished_date)
                                                        <div class="flex items-center justify-center gap-1 text-xs text-green-600 font-bold">
                                                            <span>✅</span>
                                                            <span>{{ \Carbon\Carbon::parse($order->finished_date)->format('d/m/Y') }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400 text-xs">-</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="status-badge {{ in_array($order->status, ['PRODUCTION', 'ASSESSMENT']) ? 'orange' : 'teal' }}">
                                                        {{ $order->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                {{-- Show scroll info if data > 3 --}}
                                @if($orders->count() > 3)
                                    <div class="bg-gradient-to-r from-teal-50 to-orange-50 px-4 py-2 text-center border-t-2 border-teal-200">
                                        <p class="text-xs text-teal-700 font-semibold">
                                            📋 Menampilkan {{ $orders->count() }} sepatu - Scroll untuk melihat semua
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>


            {{-- Revenue Widget with Period Filters --}}
            <div class="grid grid-cols-1 gap-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">💰 Analisis Pendapatan</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div x-data="{ activePeriod: 'month' }">
                            {{-- Period Filter Tabs --}}
                            <div class="flex flex-wrap gap-2 mb-6">
                                <button 
                                    @click="activePeriod = 'today'"
                                    :class="activePeriod === 'today' ? 'bg-teal-600 text-white shadow-md' : 'bg-white text-teal-600 border-2 border-teal-200 hover:border-teal-400'"
                                    class="px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200">
                                    Hari Ini
                                </button>
                                <button 
                                    @click="activePeriod = 'week'"
                                    :class="activePeriod === 'week' ? 'bg-teal-600 text-white shadow-md' : 'bg-white text-teal-600 border-2 border-teal-200 hover:border-teal-400'"
                                    class="px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200">
                                    Minggu Ini
                                </button>
                                <button 
                                    @click="activePeriod = 'month'"
                                    :class="activePeriod === 'month' ? 'bg-teal-600 text-white shadow-md' : 'bg-white text-teal-600 border-2 border-teal-200 hover:border-teal-400'"
                                    class="px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200">
                                    Bulan Ini
                                </button>
                                <button 
                                    @click="activePeriod = 'year'"
                                    :class="activePeriod === 'year' ? 'bg-teal-600 text-white shadow-md' : 'bg-white text-teal-600 border-2 border-teal-200 hover:border-teal-400'"
                                    class="px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200">
                                    Tahun Ini
                                </button>
                            </div>

                            {{-- Revenue Display for Today --}}
                            <div x-show="activePeriod === 'today'" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100">
                                <div class="stat-card orange mb-4">
                                    <div class="stat-icon">�</div>
                                    <div class="stat-value">Rp {{ number_format($revenueData['periods']['today']['total'], 0, ',', '.') }}</div>
                                    <div class="stat-label">Pendapatan Hari Ini</div>
                                    <div class="text-sm opacity-80 mt-2">{{ $revenueData['periods']['today']['count'] }} Order Selesai</div>
                                </div>
                            </div>

                            {{-- Revenue Display for This Week --}}
                            <div x-show="activePeriod === 'week'" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100">
                                <div class="stat-card" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
                                    <div class="stat-icon">💵</div>
                                    <div class="stat-value">Rp {{ number_format($revenueData['periods']['week']['total'], 0, ',', '.') }}</div>
                                    <div class="stat-label">Pendapatan Minggu Ini</div>
                                    <div class="text-sm opacity-80 mt-2">{{ $revenueData['periods']['week']['count'] }} Order Selesai</div>
                                </div>
                            </div>

                            {{-- Revenue Display for This Month --}}
                            <div x-show="activePeriod === 'month'" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100">
                                <div class="stat-card" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                    <div class="stat-icon">💵</div>
                                    <div class="stat-value">Rp {{ number_format($revenueData['periods']['month']['total'], 0, ',', '.') }}</div>
                                    <div class="stat-label">Pendapatan Bulan Ini</div>
                                    <div class="text-sm opacity-80 mt-2">{{ $revenueData['periods']['month']['count'] }} Order Selesai</div>
                                </div>
                                {{-- Chart for Monthly View --}}
                                <div class="mt-4">
                                    <div class="chart-container" style="height: 120px;">
                                        <canvas id="revenueChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            {{-- Revenue Display for This Year --}}
                            <div x-show="activePeriod === 'year'" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100">
                                <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                                    <div class="stat-icon">💵</div>
                                    <div class="stat-value">Rp {{ number_format($revenueData['periods']['year']['total'], 0, ',', '.') }}</div>
                                    <div class="stat-label">Pendapatan Tahun Ini</div>
                                    <div class="text-sm opacity-80 mt-2">{{ $revenueData['periods']['year']['count'] }} Order Selesai</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row 1 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Status Distribution --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">📊 Distribusi Status Order</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="chart-container" style="height: 150px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Daily Trends --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">📈 Trend Order (7 Hari)</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="chart-container" style="height: 150px;">
                            <canvas id="trendsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row 2 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Service Popularity --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">⭐ Popularitas Jasa</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="chart-container" style="height: 150px;">
                            <canvas id="serviceChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Processing Time --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">⏱️ Waktu Proses (Rata-rata)</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="chart-container" style="height: 150px;">
                            <canvas id="processingChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Technician Performance & Material Alerts --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">👥 Performa Teknisi</h3>
                    </div>
                    <div class="dashboard-card-body">
                        
                        @if($technicianPerformance->count() > 0)
                            <div x-data="{ activeTab: '{{ $technicianPerformance->keys()->first() }}' }">
                                {{-- Tabs Navigation --}}
                                <div class="flex space-x-2 overflow-x-auto pb-2 mb-4 dashboard-scroll">
                                    @foreach($technicianPerformance as $spec => $techs)
                                        <button 
                                            @click="activeTab = '{{ $spec }}'"
                                            :class="{ 'active': activeTab === '{{ $spec }}' }"
                                            class="tab-button">
                                            {{ $spec }}
                                        </button>
                                    @endforeach
                                </div>

                                {{-- Tabs Content --}}
                                @foreach($technicianPerformance as $spec => $techs)
                                    <div x-show="activeTab === '{{ $spec }}'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                                        
                                        <div class="bg-gradient-to-br from-teal-50 to-orange-50 rounded-lg p-4">
                                            <h4 class="font-bold text-teal-700 mb-3 uppercase text-xs tracking-wider border-b border-teal-200 pb-2">
                                                🏆 Papan Peringkat: {{ $spec }}
                                            </h4>
                                            <div class="space-y-2 max-h-60 overflow-y-auto dashboard-scroll pr-1">
                                                @foreach($techs as $index => $tech)
                                                    <div class="leaderboard-item flex items-center justify-between">
                                                        <div class="flex items-center gap-3">
                                                            <div class="rank-badge {{ $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : 'default')) }}">
                                                                {{ $index + 1 }}
                                                            </div>
                                                            <div>
                                                                <div class="font-semibold text-gray-900 text-sm">{{ $tech['name'] }}</div>
                                                                <div class="text-xs text-gray-500">{{ $tech['count'] }} pekerjaan</div>
                                                            </div>
                                                        </div>
                                                        @if($index === 0)
                                                            <span class="text-lg" title="Top Performer">👑</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic text-center py-8">Belum ada data performa</p>
                        @endif
                    </div>
                </div>

                {{-- Material Alerts --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">⚠️ Stok Material Menipis</h3>
                    </div>
                    <div class="dashboard-card-body">
                        @if($materialAlerts->count() > 0)
                            <div class="space-y-3">
                                @foreach($materialAlerts as $material)
                                    <div class="alert-card danger flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900">{{ $material->name }}</div>
                                            <div class="text-xs text-gray-600 mt-1">
                                                Stock: {{ $material->stock }} {{ $material->unit }} (Min: {{ $material->min_stock }})
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                                            RENDAH
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert-card info text-center py-8">
                                <span class="text-green-600 font-semibold">✓ Semua stok aman</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Technician Specialization & Deadlines --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Technician Specialization Distribution --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">� Distribusi Spesialisasi Teknisi</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="chart-container" style="height: 150px;">
                            <canvas id="technicianSpecChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Upcoming Deadlines --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">📅 Deadline Mendatang</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="space-y-3">
                            <div class="alert-card danger flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-gray-900">🔥 Hari Ini</div>
                                    <div class="text-xs text-gray-600">Harus selesai hari ini</div>
                                </div>
                                <div class="text-3xl font-bold text-red-600">
                                    {{ $upcomingDeadlines['today'] }}
                                </div>
                            </div>
                            <div class="alert-card warning flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-gray-900">⚡ Besok</div>
                                    <div class="text-xs text-gray-600">Deadline besok</div>
                                </div>
                                <div class="text-3xl font-bold text-orange-600">
                                    {{ $upcomingDeadlines['tomorrow'] }}
                                </div>
                            </div>
                            <div class="alert-card info flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-gray-900">📆 Minggu Ini</div>
                                    <div class="text-xs text-gray-600">7 hari ke depan</div>
                                </div>
                                <div class="text-3xl font-bold text-blue-600">
                                    {{ $upcomingDeadlines['thisWeek'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- New Analytics Row 1 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Material Trends --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">📦 Material Terlaris (7 Hari)</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="chart-container" style="height: 150px;">
                            <canvas id="materialTrendsChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Service Trends --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">🔧 Trend Jasa (7 Hari)</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="chart-container" style="height: 150px;">
                            <canvas id="serviceTrendsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- New Analytics Row 2 - Top Suppliers --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Top Suppliers by Spend --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">🏆 Top Supplier (Pembelian Terbanyak)</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="chart-container" style="height: 150px;">
                            <canvas id="supplierSpendChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Best Suppliers by Rating --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">⭐ Top Supplier (Kualitas Terbaik)</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="chart-container" style="height: 150px;">
                            <canvas id="supplierRatingChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inventory & Material Category --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Inventory Value --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">💎 Nilai Inventori Material</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="stat-card teal-light mb-4">
                            <div class="stat-icon">💰</div>
                            <div class="stat-value">Rp {{ number_format($inventoryValue['total'], 0, ',', '.') }}</div>
                            <div class="stat-label">Uang Tertahan</div>
                        </div>
                        @if($inventoryValue['byMaterial']->count() > 0)
                            <div class="space-y-2 max-h-48 overflow-y-auto dashboard-scroll">
                                @foreach($inventoryValue['byMaterial'] as $material)
                                    <div class="leaderboard-item flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">{{ $material['name'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $material['stock'] }} × Rp {{ number_format($material['price'], 0, ',', '.') }}</div>
                                        </div>
                                        <div class="font-bold text-teal-600">
                                            Rp {{ number_format($material['value'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert-card info text-center py-4">
                                <p class="text-gray-500 italic">Belum ada material</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Material Category Distribution --}}
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h3 class="dashboard-card-title">📦 Distribusi Kategori Material</h3>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="materialCategoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>


            {{-- New Analytics Row 3 (Purchase & Finance) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Pending POs --}}
                <div class="bg-white rounded-2xl p-6 shadow-md border-l-4 border-orange-500 hover:shadow-xl transition-all duration-300 group relative overflow-hidden">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Antrian Belanja</span>
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-orange-100 text-orange-600">MENUNGGU</span>
                            </div>
                            <div class="text-3xl font-black text-gray-800 flex items-baseline gap-1">
                                {{ $purchaseStats['pending_po'] }}
                                <span class="text-sm font-medium text-gray-400">PO</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-2 font-medium">Menunggu persetujuan</div>
                        </div>
                        <div class="p-3 bg-orange-50 text-orange-500 rounded-xl group-hover:bg-orange-500 group-hover:text-white transition-all duration-300 shadow-sm">
                            <span class="text-2xl">🛍️</span>
                        </div>
                    </div>
                </div>

                {{-- Outstanding Debt --}}
                <div class="bg-white rounded-2xl p-6 shadow-md border-l-4 border-red-500 hover:shadow-xl transition-all duration-300 group relative overflow-hidden">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Hutang Belanja</span>
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-600">BELUM LUNAS</span>
                            </div>
                            <div class="text-3xl font-black text-gray-800">
                                <span class="text-lg text-gray-500 font-bold">Rp</span> {{ number_format($purchaseStats['outstanding_debt'], 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-gray-500 mt-2 font-medium">Tagihan belum dibayar</div>
                        </div>
                        <div class="p-3 bg-red-50 text-red-500 rounded-xl group-hover:bg-red-500 group-hover:text-white transition-all duration-300 shadow-sm">
                            <span class="text-2xl">💳</span>
                        </div>
                    </div>
                </div>

                {{-- Monthly Spend --}}
                <div class="bg-white rounded-2xl p-6 shadow-md border-l-4 border-yellow-500 hover:shadow-xl transition-all duration-300 group relative overflow-hidden">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Belanja Bulan Ini</span>
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-700 uppercase">{{ \Carbon\Carbon::now()->format('M Y') }}</span>
                            </div>
                            <div class="text-3xl font-black text-gray-800">
                                <span class="text-lg text-gray-500 font-bold">Rp</span> {{ number_format($purchaseStats['monthly_spend'], 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-gray-500 mt-2 font-medium">Total pengeluaran</div>
                        </div>
                        <div class="p-3 bg-yellow-50 text-yellow-600 rounded-xl group-hover:bg-yellow-500 group-hover:text-white transition-all duration-300 shadow-sm">
                            <span class="text-2xl">📉</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js Local --}}
    <script src="{{ asset('js/vendor/chart.min.js') }}"></script>
    
    <script>
        // Chart colors
        const colors = {
            primary: '#6366f1',
            success: '#10b981',
            warning: '#f59e0b',
            danger: '#ef4444',
            info: '#3b82f6',
            purple: '#8b5cf6',
            pink: '#ec4899',
            teal: '#14b8a6',
        };

        const statusColors = {
            'DITERIMA': '#3b82f6',
            'ASSESSMENT': '#eab308',
            'PREPARATION': '#06b6d4',
            'SORTIR': '#6366f1',
            'PRODUCTION': '#f97316',
            'QC': '#14b8a6',
            'SELESAI': '#10b981',
        };

        // Status Distribution Donut Chart
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: @json($statusDistribution['labels']),
                datasets: [{
                    data: @json($statusDistribution['data']),
                    backgroundColor: @json($statusDistribution['labels']).map(status => statusColors[status] || colors.primary),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Daily Trends Line Chart
        new Chart(document.getElementById('trendsChart'), {
            type: 'line',
            data: {
                labels: @json($dailyTrends['labels']),
                datasets: [{
                    label: 'Pesanan',
                    data: @json($dailyTrends['data']),
                    borderColor: colors.primary,
                    backgroundColor: colors.primary + '20',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Service Popularity Bar Chart
        new Chart(document.getElementById('serviceChart'), {
            type: 'bar',
            data: {
                labels: @json($servicePopularity['labels']),
                datasets: [{
                    label: 'Jumlah',
                    data: @json($servicePopularity['data']),
                    backgroundColor: colors.purple,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Processing Time Bar Chart
        new Chart(document.getElementById('processingChart'), {
            type: 'bar',
            data: {
                labels: @json($processingTimes['labels']),
                datasets: [{
                    label: 'Jam',
                    data: @json($processingTimes['data']),
                    backgroundColor: colors.info,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Revenue Line Chart
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: @json($revenueData['daily']['labels']),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: @json($revenueData['daily']['data']),
                    borderColor: colors.success,
                    backgroundColor: colors.success + '20',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Material Trends Bar Chart
        new Chart(document.getElementById('materialTrendsChart'), {
            type: 'bar',
            data: {
                labels: @json($materialTrends['labels']),
                datasets: [{
                    label: 'Penggunaan',
                    data: @json($materialTrends['data']),
                    backgroundColor: colors.purple,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Service Trends Line Chart (Multi-line)
        const serviceTrendsData = @json($serviceTrends);
        const serviceColors = [colors.primary, colors.success, colors.warning];
        
        new Chart(document.getElementById('serviceTrendsChart'), {
            type: 'line',
            data: {
                labels: serviceTrendsData.labels,
                datasets: serviceTrendsData.datasets.map((dataset, index) => ({
                    label: dataset.label,
                    data: dataset.data,
                    borderColor: serviceColors[index] || colors.primary,
                    backgroundColor: (serviceColors[index] || colors.primary) + '20',
                    tension: 0.4,
                    fill: false
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 10
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        // Supplier Spend Bar Chart
        new Chart(document.getElementById('supplierSpendChart'), {
            type: 'bar',
            data: {
                labels: @json($supplierAnalytics['bySpend']['labels']),
                datasets: [{
                    label: 'Total Belanja (Rp)',
                    data: @json($supplierAnalytics['bySpend']['data']),
                    backgroundColor: colors.pink,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Supplier Rating Bar Chart
        new Chart(document.getElementById('supplierRatingChart'), {
            type: 'bar',
            data: {
                labels: @json($supplierAnalytics['byRating']['labels']),
                datasets: [{
                    label: 'Rating (1-5)',
                    data: @json($supplierAnalytics['byRating']['data']),
                    backgroundColor: colors.warning,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Material Category Pie Chart
        new Chart(document.getElementById('materialCategoryChart'), {
            type: 'pie',
            data: {
                labels: @json($materialCategoryStats['labels']),
                datasets: [{
                    data: @json($materialCategoryStats['data']),
                    backgroundColor: [colors.purple, colors.pink, colors.info, colors.warning],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });

        // Technician Specialization Bar Chart
        new Chart(document.getElementById('technicianSpecChart'), {
            type: 'bar',
            data: {
                labels: @json($technicianSpecializationStats['labels']),
                datasets: [{
                    label: 'Jumlah Teknisi',
                    data: @json($technicianSpecializationStats['data']),
                    backgroundColor: colors.teal,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
