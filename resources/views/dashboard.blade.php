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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-4">📍 Tracking Workshop</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                        @foreach($locations as $location => $orders)
                            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 rounded-lg p-4 border border-indigo-200 dark:border-gray-500 hover:shadow-lg transition-shadow cursor-pointer"
                                 x-data="{ open: false }"
                                 @click="open = !open">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-300">
                                        {{ $orders->count() }}
                                    </div>
                                    <div class="text-xs font-semibold text-gray-700 dark:text-gray-200 mt-1 line-clamp-2">
                                        {{ $location }}
                                    </div>
                                </div>
                                
                                @if($orders->count() > 0)
                                <div x-show="open" 
                                     x-transition
                                     x-cloak
                                     @click.stop
                                     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                                    <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-[80vh] overflow-auto">
                                        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b p-4 flex justify-between items-center">
                                            <h4 class="font-bold text-lg">{{ $location }} ({{ $orders->count() }} sepatu)</h4>
                                            <button @click="open = false" class="text-gray-500 hover:text-gray-700">✕</button>
                                        </div>
                                        <div class="p-4">
                                            <div class="space-y-2">
                                                @foreach($orders as $order)
                                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                                        <div class="flex-1">
                                                            <div>
                                                                <span class="font-mono text-sm font-bold">{{ $order->spk_number }}</span>
                                                                <span class="text-sm text-gray-600 dark:text-gray-300 ml-2">{{ $order->customer_name }}</span>
                                                            </div>
                                                            <div class="text-xs text-gray-500 mt-1 flex gap-3">
                                                                <span title="Tanggal Masuk">📥 {{ \Carbon\Carbon::parse($order->entry_date)->format('d M') }}</span>
                                                                <span title="Estimasi Selesai" class="{{ \Carbon\Carbon::parse($order->estimation_date)->isPast() && $order->status !== 'SELESAI' ? 'text-red-500 font-bold' : '' }}">
                                                                    ⏱️ {{ \Carbon\Carbon::parse($order->estimation_date)->format('d M') }}
                                                                </span>
                                                                @if($order->finished_date)
                                                                    <span title="Selesai" class="text-green-600 font-bold">✅ {{ \Carbon\Carbon::parse($order->finished_date)->format('d M') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <div class="text-xs text-gray-500 mb-1">{{ $order->shoe_brand }}</div>
                                                            <span class="text-xs px-2 py-1 rounded-full
                                                                @if($order->status === 'DITERIMA') bg-blue-100 text-blue-800
                                                                @elseif($order->status === 'ASSESSMENT') bg-yellow-100 text-yellow-800
                                                                @elseif($order->status === 'PREPARATION') bg-cyan-100 text-cyan-800
                                                                @elseif($order->status === 'SORTIR') bg-indigo-100 text-indigo-800
                                                                @elseif($order->status === 'PRODUCTION') bg-orange-100 text-orange-800
                                                                @elseif($order->status === 'QC') bg-teal-100 text-teal-800
                                                                @else bg-gray-100 text-gray-800
                                                                @endif">
                                                                {{ $order->status }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Charts Row 1 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Status Distribution --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">📊 Distribusi Status Order</h3>
                        <div style="height: 150px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Daily Trends --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">📈 Trend Order (7 Hari)</h3>
                        <div style="height: 150px;">
                            <canvas id="trendsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row 2 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Service Popularity --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">⭐ Popularitas Jasa</h3>
                        <div style="height: 150px;">
                            <canvas id="serviceChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Processing Time --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">⏱️ Waktu Proses (Rata-rata)</h3>
                        <div style="height: 150px;">
                            <canvas id="processingChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Technician Performance & Material Alerts --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Technician Performance --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">👥 Performa Teknisi</h3>
                        @if($technicianPerformance->count() > 0)
                            <div class="space-y-3">
                                @foreach($technicianPerformance as $index => $tech)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                                #{{ $index + 1 }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $tech['name'] }}</div>
                                                <div class="text-xs text-gray-500">{{ $tech['count'] }} order selesai</div>
                                            </div>
                                        </div>
                                        @if($index === 0)
                                            <span class="text-2xl">🏆</span>
                                        @elseif($index === 1)
                                            <span class="text-2xl">🥈</span>
                                        @elseif($index === 2)
                                            <span class="text-2xl">🥉</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic text-center py-8">Belum ada data performa</p>
                        @endif
                    </div>
                </div>

                {{-- Material Alerts --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">⚠️ Stok Material Menipis</h3>
                        @if($materialAlerts->count() > 0)
                            <div class="space-y-2">
                                @foreach($materialAlerts as $material)
                                    <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $material->name }}</div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                                Stock: {{ $material->stock }} {{ $material->unit }} (Min: {{ $material->min_stock }})
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-bold rounded">
                                            LOW
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-green-600 dark:text-green-400 text-center py-8">✓ Semua stok aman</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Revenue & Deadlines --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Revenue Chart --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">💰 Revenue (7 Hari)</h3>
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-4">
                            Rp {{ number_format($revenueData['total'], 0, ',', '.') }}
                        </div>
                        <div style="height: 120px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Upcoming Deadlines --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">📅 Deadline Mendatang</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border-l-4 border-red-500">
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-gray-100">Hari Ini</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Harus selesai hari ini</div>
                                </div>
                                <div class="text-3xl font-bold text-red-600 dark:text-red-400">
                                    {{ $upcomingDeadlines['today'] }}
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border-l-4 border-yellow-500">
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-gray-100">Besok</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Deadline besok</div>
                                </div>
                                <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                                    {{ $upcomingDeadlines['tomorrow'] }}
                                </div>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-l-4 border-blue-500">
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-gray-100">Minggu Ini</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">7 hari ke depan</div>
                                </div>
                                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
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
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">📦 Material Terlaris (7 Hari)</h3>
                        <div style="height: 150px;">
                            <canvas id="materialTrendsChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Service Trends --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">🔧 Trend Jasa (7 Hari)</h3>
                        <div style="height: 150px;">
                            <canvas id="serviceTrendsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- New Analytics Row 2 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Preparation Productivity --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">⚡ Produktivitas Preparation</h3>
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded">
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Orders</div>
                                <div class="text-2xl font-bold text-blue-600">{{ $preparationProductivity['totalOrders'] }}</div>
                            </div>
                            <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded">
                                <div class="text-sm text-gray-600 dark:text-gray-400">Avg Time</div>
                                <div class="text-2xl font-bold text-green-600">{{ $preparationProductivity['avgTime'] }}h</div>
                            </div>
                        </div>
                        @if($preparationProductivity['technicians']->count() > 0)
                            <div class="space-y-2">
                                @foreach($preparationProductivity['technicians'] as $tech)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                        <span class="text-sm font-medium">{{ $tech['name'] }}</span>
                                        <div class="text-xs text-gray-500">
                                            Sol: {{ $tech['sol'] }} | Upper: {{ $tech['upper'] }} | Total: <span class="font-bold">{{ $tech['total'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic text-center py-4">Belum ada data</p>
                        @endif
                    </div>
                </div>

                {{-- Inventory Value --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">💎 Nilai Inventori Material</h3>
                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-4">
                            Rp {{ number_format($inventoryValue['total'], 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-500 mb-4">Uang tertahan di material</div>
                        @if($inventoryValue['byMaterial']->count() > 0)
                            <div class="space-y-2">
                                @foreach($inventoryValue['byMaterial'] as $material)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded text-sm">
                                        <div class="flex-1">
                                            <div class="font-medium">{{ $material['name'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $material['stock'] }} × Rp {{ number_format($material['price'], 0, ',', '.') }}</div>
                                        </div>
                                        <div class="font-bold text-purple-600">
                                            Rp {{ number_format($material['value'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic text-center py-4">Belum ada material</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- New Analytics Row 3 (Purchase & Finance) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Pending POs --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">🛍️ Antrian Belanja</h3>
                        <div class="text-3xl font-bold text-orange-600 dark:text-orange-400 mb-1">
                            {{ $purchaseStats['pending_po'] }} PO
                        </div>
                        <div class="text-xs text-gray-500">Status Waiting / Pending</div>
                    </div>
                </div>

                {{-- Outstanding Debt --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">💳 Hutang Belanja (Unpaid)</h3>
                        <div class="text-3xl font-bold text-red-600 dark:text-red-400 mb-1">
                            Rp {{ number_format($purchaseStats['outstanding_debt'], 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-500">Tagihan belum dibayar</div>
                    </div>
                </div>

                {{-- Monthly Spend --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">📉 Total Belanja Bulan Ini</h3>
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                             Rp {{ number_format($purchaseStats['monthly_spend'], 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
                    label: 'Orders',
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
                    label: 'Revenue (Rp)',
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
    </script>
</x-app-layout>
