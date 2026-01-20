<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Analytics') }}
        </h2>
        
        <!-- ApexCharts CDN -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
        
        <style>
            [x-cloak] { display: none !important; }
            
            /* Premium Dashboard Styles */
            .section-gradient {
                background: linear-gradient(135deg, rgba(20, 184, 166, 0.03) 0%, rgba(249, 115, 22, 0.03) 100%);
            }
            
            .chart-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .chart-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            }
            
            .section-divider {
                height: 2px;
                background: linear-gradient(90deg, transparent, rgba(20, 184, 166, 0.3), rgba(249, 115, 22, 0.3), transparent);
            }
            
            .metric-card {
                transition: all 0.3s ease;
            }
            
            .metric-card:hover {
                transform: translateY(-2px) scale(1.02);
            }
            
            /* ApexCharts Custom Styling */
            .apexcharts-tooltip {
                background: rgba(255, 255, 255, 0.95) !important;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(20, 184, 166, 0.2) !important;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
            }
            
            .apexcharts-tooltip-title {
                background: linear-gradient(135deg, #14b8a6, #f97316) !important;
                color: white !important;
                font-weight: 700 !important;
            }
            
            /* Smooth Animations */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .animate-fade-in-up {
                animation: fadeInUp 0.6s ease-out;
            }
            
            /* Section Header Glow */
            .section-icon-glow {
                box-shadow: 0 0 20px rgba(20, 184, 166, 0.3);
            }
        </style>
    </x-slot>

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Premium Header --}}
            <div class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-3xl p-8 shadow-2xl overflow-hidden border border-gray-700/50">
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10 mix-blend-soft-light"></div>
                <!-- Background Decor -->
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-teal-500 rounded-full blur-3xl opacity-20 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-orange-500 rounded-full blur-3xl opacity-20 animate-pulse" style="animation-delay: 1s;"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-3 py-1 bg-white/10 text-white rounded-full text-xs font-bold uppercase tracking-wider backdrop-blur-md border border-white/10">
                                {{ Auth::user()->role === 'owner' ? 'Owner Dashboard' : 'Workshop Admin' }}
                            </span>
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-xs text-green-400 font-bold uppercase">System Online</span>
                        </div>
                        <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight mb-2 drop-shadow-lg">
                            Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋
                        </h1>
                        <p class="text-gray-400 text-lg font-medium max-w-xl">
                            Selamat datang kembali di pusat kontrol operasional workshop Anda.
                        </p>
                    </div>
                    <div class="text-right bg-white/5 p-4 rounded-2xl backdrop-blur-md border border-white/10">
                        <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-400 font-mono tracking-tighter">
                            {{ \Carbon\Carbon::now()->format('H:i') }}
                        </div>
                        <div class="text-gray-400 font-bold uppercase tracking-widest text-xs mt-1">
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Key Metrics Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Revenue Card -->
                <div class="group bg-white rounded-3xl p-6 shadow-xl shadow-gray-200/50 border border-gray-100 hover:border-teal-200 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg class="w-24 h-24 text-teal-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05 1.18 1.91 2.53 1.91 1.33 0 2.26-.87 2.26-2.02 0-1.13-.95-1.58-2.82-2.03-2.03-.49-3.21-1.35-3.21-3.08 0-1.63 1.25-2.88 3.12-3.17V4h2.67v1.92c1.4.3 2.75 1.24 3.01 3.14h-1.92c-.22-1.28-1.28-1.75-2.22-1.75-1.29 0-2.12.87-2.12 1.84 0 1.04 1.12 1.48 2.66 1.84 2.22.53 3.37 1.5 3.37 3.23 0 1.77-1.39 2.94-3.32 3.11z"/></svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Pendapatan (Bulan Ini)</p>
                        <h3 class="text-3xl font-black text-gray-800 tracking-tight group-hover:text-teal-600 transition-colors">
                            Rp {{ number_format($revenueData['periods']['month']['total'] / 1000, 0, ',', '.') }}<span class="text-lg text-gray-400 font-bold">rb</span>
                        </h3>
                        <div class="mt-4 flex items-center gap-2">
                            <span class="px-2 py-1 bg-teal-50 text-teal-700 text-xs font-bold rounded-lg group-hover:bg-teal-100 transition-colors">
                                +{{ $revenueData['periods']['month']['count'] }} Order
                            </span>
                            <span class="text-xs text-gray-400 font-medium">terselesaikan</span>
                        </div>
                    </div>
                </div>

                <!-- Active Orders Card -->
                <div class="group bg-white rounded-3xl p-6 shadow-xl shadow-gray-200/50 border border-gray-100 hover:border-orange-200 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg class="w-24 h-24 text-orange-600" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v2c0 1.1.9 2 2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1.9-2-2-2V4c0-1.1-.9-2-2-2zm-8 18H6V8h6v12zm8 0h-6V8h6v12zM8 4h8v2H8V4z"/></svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Order Aktif</p>
                        <h3 class="text-3xl font-black text-gray-800 tracking-tight group-hover:text-orange-600 transition-colors">
                            {{ $activeOrdersCount }}
                        </h3>
                        <div class="mt-4 flex items-center gap-2">
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-orange-500 h-1.5 rounded-full" style="width: 70%"></div>
                            </div>
                            <span class="text-xs text-orange-600 font-bold">Running</span>
                        </div>
                    </div>
                </div>

                <!-- Complaints Card -->
                <div class="group bg-white rounded-3xl p-6 shadow-xl shadow-gray-200/50 border border-gray-100 hover:border-rose-200 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg class="w-24 h-24 text-rose-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Komplain Pending</p>
                        <h3 class="text-3xl font-black text-gray-800 tracking-tight group-hover:text-rose-600 transition-colors">
                            {{ $complaintAnalytics['status_counts']['PENDING'] ?? 0 }}
                        </h3>
                        <div class="mt-4 flex items-center gap-2">
                             @if(($complaintAnalytics['overdue_count'] ?? 0) > 0)
                                <span class="px-2 py-1 bg-rose-50 text-rose-600 text-xs font-bold rounded-lg animate-pulse border border-rose-100">
                                    {{ $complaintAnalytics['overdue_count'] }} Overdue!
                                </span>
                             @else
                                <span class="px-2 py-1 bg-green-50 text-green-600 text-xs font-bold rounded-lg border border-green-100">
                                    Semua Aman
                                </span>
                             @endif
                        </div>
                    </div>
                </div>

                <!-- Staff Card -->
                <div class="group bg-white rounded-3xl p-6 shadow-xl shadow-gray-200/50 border border-gray-100 hover:border-blue-200 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg class="w-24 h-24 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Total Staff</p>
                        <h3 class="text-3xl font-black text-gray-800 tracking-tight group-hover:text-blue-600 transition-colors">
                            {{ $activeStaffCount }}
                        </h3>
                        <div class="mt-4 flex items-center gap-2">
                             <span class="px-2 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-lg border border-blue-100">
                                Aktif Bekerja
                            </span>
                        </div>
                    </div>
                </div>
            </div>


            {{-- SECTION: Live Operations --}}
            <section class="section-gradient rounded-3xl p-8 animate-fade-in-up">
                <!-- Section Header -->
                <div class="flex items-center gap-4 mb-8">
                    <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg section-icon-glow">
                        <span class="text-2xl">📍</span>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Live Operations</h2>
                        <p class="text-sm text-gray-500 font-medium">Pantau posisi setiap sepatu di lantai produksi secara real-time</p>
                    </div>
                    <div class="hidden md:block flex-grow h-px section-divider"></div>
                </div>

                {{-- Live Workshop Flow --}}
                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-gray-200/50 border border-gray-100 chart-card" x-data="{ activeLocation: null }">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                        <div>
                        <h3 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-3">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-orange-100 text-orange-600">📍</span>
                            Live Workshop Flow
                        </h3>
                        <p class="text-gray-500 text-sm font-medium mt-1 ml-14">Pantau posisi setiap sepatu di lantai produksi secara real-time.</p>
                    </div>
                </div>

                {{-- Location Triggers (Horizontal Scroll / Grid) --}}
                <div class="flex flex-wrap gap-3 mb-8">
                    @foreach($locations as $location => $orders)
                        @php
                            $count = $orders->count();
                            // Determine color based on location/status keywords
                            $colorClass = 'gray'; // default
                            $icon = '📍';

                            if (str_contains($location, 'Penerimaan')) { $colorClass = 'blue'; $icon = '📥'; }
                            elseif (str_contains($location, 'Preparation')) { $colorClass = 'cyan'; $icon = '🧼'; }
                            elseif (str_contains($location, 'Sortir')) { $colorClass = 'indigo'; $icon = '📋'; }
                            elseif (str_contains($location, 'Jahit')) { $colorClass = 'orange'; $icon = '🧵'; }
                            elseif (str_contains($location, 'Clean Up')) { $colorClass = 'teal'; $icon = '✨'; }
                            elseif (str_contains($location, 'QC Akhir')) { $colorClass = 'green'; $icon = '✅'; }
                            elseif (str_contains($location, 'Selesai')) { $colorClass = 'emerald'; $icon = '🛍️'; }
                        @endphp
                        
                        <button 
                            @click="activeLocation = activeLocation === '{{ $location }}' ? null : '{{ $location }}'"
                            :class="activeLocation === '{{ $location }}' 
                                ? 'bg-{{ $colorClass }}-600 text-white shadow-lg shadow-{{ $colorClass }}-500/30 ring-2 ring-{{ $colorClass }}-400 ring-offset-2' 
                                : 'bg-white text-gray-600 border border-gray-200 hover:border-{{ $colorClass }}-400 hover:bg-{{ $colorClass }}-50'"
                            class="group relative flex items-center gap-3 px-5 py-3 rounded-2xl transition-all duration-200 ease-out">
                            
                            <span class="text-xl">{{ $icon }}</span>
                            <div class="text-left">
                                <div class="text-[10px] uppercase font-bold tracking-wider opacity-70 leading-none mb-1 group-hover:text-{{ $colorClass }}-600"
                                     :class="activeLocation === '{{ $location }}' ? 'text-{{ $colorClass }}-100' : ''">
                                    Lokasi
                                </div>
                                <div class="font-bold text-sm leading-none">{{ $location }}</div>
                            </div>
                            <span class="ml-2 flex items-center justify-center w-6 h-6 rounded-full text-xs font-black transition-colors"
                                  :class="activeLocation === '{{ $location }}' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600 group-hover:bg-{{ $colorClass }}-100 group-hover:text-{{ $colorClass }}-700'">
                                {{ $count }}
                            </span>
                            
                            {{-- Active Indicator --}}
                             <div x-show="activeLocation === '{{ $location }}'" 
                                  class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-4 h-4 bg-{{ $colorClass }}-600 rotate-45 border-r border-b border-{{ $colorClass }}-400"></div>
                        </button>
                    @endforeach
                </div>
                
                {{-- Expanded Tables --}}
                @foreach($locations as $location => $orders)
                    @if($orders->count() > 0)
                        <div x-show="activeLocation === '{{ $location }}'" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-collapse
                             class="mb-6 bg-white rounded-2xl border border-gray-100 shadow-xl overflow-hidden relative z-10">
                            
                            {{-- Table Header --}}
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                                <h4 class="font-black text-gray-800 flex items-center gap-2">
                                    <span>📂</span> Detail: <span class="text-teal-600">{{ $location }}</span>
                                </h4>
                                <span class="text-xs font-bold text-gray-400 uppercase">{{ $orders->count() }} Sepatu dalam antrian</span>
                            </div>
                            
                            {{-- Table Content --}}
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-white text-gray-500 border-b border-gray-100">
                                        <tr>
                                            <th class="text-left py-4 px-6 font-bold uppercase text-xs tracking-wider">No SPK</th>
                                            <th class="text-left py-4 px-6 font-bold uppercase text-xs tracking-wider">Pelanggan</th>
                                            <th class="text-left py-4 px-6 font-bold uppercase text-xs tracking-wider">Merek</th>
                                            <th class="text-center py-4 px-6 font-bold uppercase text-xs tracking-wider">Tanggal Masuk</th>
                                            <th class="text-center py-4 px-6 font-bold uppercase text-xs tracking-wider">Estimasi</th>
                                            <th class="text-center py-4 px-6 font-bold uppercase text-xs tracking-wider">Status System</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($orders as $order)
                                        <tr class="hover:bg-teal-50/50 transition-colors group">
                                            <td class="py-4 px-6">
                                                <a href="{{ route('reception.show', $order->id) }}" class="font-mono text-sm font-bold text-teal-600 hover:text-teal-800 hover:underline">
                                                    {{ $order->spk_number }}
                                                </a>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="font-bold text-gray-900">{{ $order->customer_name }}</div>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $order->shoe_brand ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                <span class="text-xs text-gray-500 font-medium font-mono">
                                                    {{ \Carbon\Carbon::parse($order->entry_date)->format('d/m/Y') }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                @php
                                                    $estDates = \Carbon\Carbon::parse($order->estimation_date);
                                                    $isOverdue = $estDates->isPast() && $order->status !== 'SELESAI';
                                                    $isToday = $estDates->isToday();
                                                @endphp
                                                <div class="flex items-center justify-center gap-1">
                                                    @if($isOverdue)
                                                        <span class="px-2 py-1 bg-red-50 text-red-600 rounded-md text-xs font-bold ring-1 ring-red-200">
                                                            {{ $estDates->format('d/m') }}!
                                                        </span>
                                                    @elseif($isToday)
                                                         <span class="px-2 py-1 bg-orange-50 text-orange-600 rounded-md text-xs font-bold ring-1 ring-orange-200">
                                                            Hari Ini
                                                        </span>
                                                    @else
                                                        <span class="text-gray-500 text-xs font-mono">{{ $estDates->format('d/m') }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                <span class="status-badge {{ in_array($order->status->value, ['PRODUCTION', 'ASSESSMENT', 'PREPARATION', 'SORTIR', 'QC']) ? 'orange' : 'teal' }} text-[10px]">
                                                    {{ $order->status->label() }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            </section>
            {{-- END SECTION: Live Operations --}}


            {{-- SECTION: Business Intelligence --}}
            <section class="section-gradient rounded-3xl p-8 animate-fade-in-up">
                <!-- Section Header -->
                <div class="flex items-center gap-4 mb-8">
                    <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-lg section-icon-glow">
                        <span class="text-2xl">📊</span>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Business Intelligence</h2>
                        <p class="text-sm text-gray-500 font-medium">Analisis pendapatan dan monitoring keluhan pelanggan</p>
                    </div>
                    <div class="hidden md:block flex-grow h-px section-divider"></div>
                </div>

            <div class="mt-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-3">
                        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 text-blue-600">📊</span>
                        Business Intelligence
                    </h3>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Left Column: Financials (2/3) --}}
                    <div class="lg:col-span-2 space-y-8">
                        {{-- Revenue Widget --}}
                        <div class="dashboard-card" x-data="{ activePeriod: 'month' }">
                            <div class="dashboard-card-header flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                <h3 class="dashboard-card-title">💰 Analisis Pendapatan</h3>
                                
                                {{-- Period Filter Tabs --}}
                                <div class="flex bg-gray-100 p-1 rounded-xl">
                                    <button @click="activePeriod = 'today'" :class="activePeriod === 'today' ? 'bg-white text-teal-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all">Hari Ini</button>
                                    <button @click="activePeriod = 'week'" :class="activePeriod === 'week' ? 'bg-white text-teal-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all">Minggu</button>
                                    <button @click="activePeriod = 'month'" :class="activePeriod === 'month' ? 'bg-white text-teal-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all">Bulan</button>
                                    <button @click="activePeriod = 'year'" :class="activePeriod === 'year' ? 'bg-white text-teal-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all">Tahun</button>
                                </div>
                            </div>
                            <div class="dashboard-card-body">
                                {{-- Dynamic Content based on activePeriod --}}
                                <template x-if="activePeriod === 'today'">
                                    <div class="flex items-center justify-between p-4 bg-orange-50 rounded-xl border border-orange-100">
                                        <div>
                                            <div class="text-sm text-gray-500 font-bold uppercase">Pendapatan Hari Ini</div>
                                            <div class="text-2xl font-black text-orange-600">Rp {{ number_format($revenueData['periods']['today']['total'], 0, ',', '.') }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-3xl font-bold text-orange-200">{{ $revenueData['periods']['today']['count'] }}</div>
                                            <div class="text-xs text-orange-400 font-bold uppercase">Order</div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="activePeriod === 'week'">
                                    <div class="flex items-center justify-between p-4 bg-teal-50 rounded-xl border border-teal-100">
                                        <div>
                                            <div class="text-sm text-gray-500 font-bold uppercase">Pendapatan Minggu Ini</div>
                                            <div class="text-2xl font-black text-teal-600">Rp {{ number_format($revenueData['periods']['week']['total'], 0, ',', '.') }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-3xl font-bold text-teal-200">{{ $revenueData['periods']['week']['count'] }}</div>
                                            <div class="text-xs text-teal-400 font-bold uppercase">Order</div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="activePeriod === 'month'">
                                    <div>
                                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-100 mb-6">
                                            <div>
                                                <div class="text-sm text-gray-500 font-bold uppercase">Pendapatan Bulan Ini</div>
                                                <div class="text-2xl font-black text-blue-600">Rp {{ number_format($revenueData['periods']['month']['total'], 0, ',', '.') }}</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-3xl font-bold text-blue-200">{{ $revenueData['periods']['month']['count'] }}</div>
                                                <div class="text-xs text-blue-400 font-bold uppercase">Order</div>
                                            </div>
                                        </div>
                                        <div class="chart-container" style="height: 200px;">
                                            <canvas id="revenueChart"></canvas>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="activePeriod === 'year'">
                                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-xl border border-purple-100">
                                        <div>
                                            <div class="text-sm text-gray-500 font-bold uppercase">Pendapatan Tahun Ini</div>
                                            <div class="text-2xl font-black text-purple-600">Rp {{ number_format($revenueData['periods']['year']['total'], 0, ',', '.') }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-3xl font-bold text-purple-200">{{ $revenueData['periods']['year']['count'] }}</div>
                                            <div class="text-xs text-purple-400 font-bold uppercase">Order</div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Complaints (1/3) --}}
                    <div class="space-y-6">
                        <div class="dashboard-card border-l-4 border-rose-500">
                            <div class="dashboard-card-header flex justify-between items-center">
                                <h3 class="dashboard-card-title text-rose-700">🚨 Keluhan</h3>
                                @if($complaintAnalytics['overdue_count'] > 0)
                                    <span class="px-2 py-0.5 bg-red-600 text-white rounded text-[10px] font-black animate-pulse">
                                        {{ $complaintAnalytics['overdue_count'] }} OVERDUE
                                    </span>
                                @endif
                            </div>
                            <div class="dashboard-card-body">
                                <div class="chart-container mb-4" style="height: 120px;">
                                    <canvas id="complaintCategoryChart"></canvas>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('admin.complaints.index', ['status' => 'PENDING']) }}" class="flex flex-col items-center p-2 bg-rose-50 rounded-lg hover:bg-rose-100 transition-colors">
                                        <span class="text-xl font-black text-rose-600">{{ $complaintAnalytics['status_counts']['PENDING'] }}</span>
                                        <span class="text-[10px] uppercase font-bold text-rose-400">Pending</span>
                                    </a>
                                    <a href="{{ route('admin.complaints.index', ['status' => 'PROCESS']) }}" class="flex flex-col items-center p-2 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                                        <span class="text-xl font-black text-orange-600">{{ $complaintAnalytics['status_counts']['PROCESS'] }}</span>
                                        <span class="text-[10px] uppercase font-bold text-orange-400">Proses</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Recent Complaints List --}}
                        <div class="bg-white rounded-3xl p-6 shadow-lg shadow-gray-200/50 border border-gray-100">
                             <div class="flex justify-between items-center mb-4">
                                <h4 class="font-bold text-gray-800 text-sm">Terbaru</h4>
                                <a href="{{ route('admin.complaints.index') }}" class="text-xs text-teal-600 font-bold hover:underline">Lihat Semua</a>
                            </div>
                            <div class="space-y-3">
                                @forelse($complaintAnalytics['recent']->take(3) as $complaint)
                                    <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="block p-3 rounded-xl bg-gray-50 hover:bg-orange-50 transition-colors group border border-gray-100">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="text-xs font-black text-gray-700 group-hover:text-orange-600">{{ $complaint->workOrder->spk_number ?? '-' }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $complaint->created_at->diffForHumans(null, true) }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 line-clamp-1 mb-1">{{ $complaint->description }}</p>
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-white text-gray-500 border border-gray-200">
                                            {{ $complaint->category }}
                                        </span>
                                    </a>
                                @empty
                                    <div class="text-center py-4 text-gray-400 text-xs italic">Tidak ada keluhan</div>
                                @endforelse
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
            </div>
            </section>
            {{-- END SECTION: Business Intelligence --}}

            {{-- SECTION: Operational Performance --}}
            <section class="section-gradient rounded-3xl p-8 animate-fade-in-up">
                <!-- Section Header -->
                <div class="flex items-center gap-4 mb-8">
                    <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg section-icon-glow">
                        <span class="text-2xl">🏭</span>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Operational Performance</h2>
                        <p class="text-sm text-gray-500 font-medium">Performa teknisi, waktu proses, dan deadline mendatang</p>
                    </div>
                    <div class="hidden md:block flex-grow h-px section-divider"></div>
                </div>

            <div class="mt-12">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-3">
                        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-teal-100 text-teal-600">🏭</span>
                        Operational Performance
                    </h3>
                </div>

                {{-- Row 1: Status & Trends --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    {{-- Status Distribution --}}
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3 class="dashboard-card-title">📊 Distribusi Status</h3>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="chart-container" style="height: 250px;">
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
                            <div class="chart-container" style="height: 250px;">
                                <canvas id="trendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Row 2: Technician Performance (Featured) --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 mb-8 text-white shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-teal-500 rounded-full mix-blend-overlay filter blur-3xl opacity-20 -mr-16 -mt-16"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold flex items-center gap-2">
                                <span>🏆</span> Leaderboard Teknisi
                            </h3>
                           
                        </div>

                         @if($technicianPerformance->count() > 0)
                            <div x-data="{ activeTab: '{{ $technicianPerformance->keys()->first() }}' }">
                                {{-- Tabs --}}
                                <div class="flex space-x-1 mb-6 bg-white/10 p-1 rounded-xl inline-flex backdrop-blur-md">
                                    @foreach($technicianPerformance as $spec => $techs)
                                        <button 
                                            @click="activeTab = '{{ $spec }}'"
                                            :class="{ 'bg-teal-500 text-white shadow-lg': activeTab === '{{ $spec }}', 'text-gray-300 hover:bg-white/5': activeTab !== '{{ $spec }}' }"
                                            class="px-4 py-2 rounded-lg text-xs font-bold transition-all duration-200 uppercase tracking-wider">
                                            {{ $spec }}
                                        </button>
                                    @endforeach
                                </div>

                                {{-- Leaderboard Grid --}}
                                @foreach($technicianPerformance as $spec => $techs)
                                    <div x-show="activeTab === '{{ $spec }}'" 
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 translate-y-4"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        
                                        @foreach($techs->take(3) as $index => $tech)
                                            <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center gap-4 relative overflow-hidden group hover:bg-white/10 transition-colors">
                                                <div class="text-4xl font-black opacity-20 absolute right-2 bottom-0 group-hover:scale-110 transition-transform">#{{ $index + 1 }}</div>
                                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold
                                                    {{ $index === 0 ? 'bg-yellow-400 text-yellow-900 shadow-[0_0_15px_rgba(250,204,21,0.5)]' : 
                                                      ($index === 1 ? 'bg-gray-300 text-gray-800' : 
                                                      ($index === 2 ? 'bg-orange-400 text-orange-900' : 'bg-gray-700 text-gray-400')) }}">
                                                    {{ substr($tech['name'], 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-lg">{{ $tech['name'] }}</div>
                                                    <div class="text-teal-300 font-mono text-sm">{{ $tech['count'] }} Order Selesai</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-400">Belum ada data performa teknisi.</div>
                        @endif
                    </div>
                </div>

                {{-- Row 3: Processing Time & Deadlines --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                     {{-- Processing Time --}}
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3 class="dashboard-card-title">⏱️ Rata-rata Waktu Proses</h3>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="chart-container" style="height: 200px;">
                                <canvas id="processingChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Upcoming Deadlines --}}
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3 class="dashboard-card-title">📅 Deadline Mendatang</h3>
                        </div>
                        <div class="dashboard-card-body space-y-4">
                            <div class="flex items-center justify-between p-4 bg-red-50 rounded-xl border border-red-100">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">🔥</span>
                                    <div>
                                        <div class="font-black text-gray-800 uppercase text-xs tracking-wider">Hari Ini</div>
                                        <div class="text-xs text-red-600 font-bold">Harus Selesai!</div>
                                    </div>
                                </div>
                                <div class="text-3xl font-black text-red-600">{{ $upcomingDeadlines['today'] }}</div>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-orange-50 rounded-xl border border-orange-100">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">⚡</span>
                                    <div>
                                        <div class="font-black text-gray-800 uppercase text-xs tracking-wider">Besok</div>
                                        <div class="text-xs text-orange-600 font-bold">Segera</div>
                                    </div>
                                </div>
                                <div class="text-3xl font-black text-orange-600">{{ $upcomingDeadlines['tomorrow'] }}</div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-100">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl">📅</span>
                                    <div>
                                        <div class="font-black text-gray-800 uppercase text-xs tracking-wider">Minggu Ini</div>
                                        <div class="text-xs text-blue-600 font-bold">7 Hari Kedepan</div>
                                    </div>
                                </div>
                                <div class="text-3xl font-black text-blue-600">{{ $upcomingDeadlines['thisWeek'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </section>
            {{-- END SECTION: Operational Performance --}}

            {{-- SECTION: Supply Chain & Inventory --}}
            <section class="section-gradient rounded-3xl p-8 animate-fade-in-up">
                <!-- Section Header -->
                <div class="flex items-center gap-4 mb-8">
                    <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center shadow-lg section-icon-glow">
                        <span class="text-2xl">📦</span>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Supply Chain & Inventory</h2>
                        <p class="text-sm text-gray-500 font-medium">Monitoring inventori, material, dan supplier analytics</p>
                    </div>
                    <div class="hidden md:block flex-grow h-px section-divider"></div>
                </div>

            <div class="mt-12 mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-3">
                        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-orange-100 text-orange-600">📦</span>
                        Supply Chain & Inventory
                    </h3>
                </div>

                {{-- Key Metrics Pills --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                     {{-- Inventory Value --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-2xl">💎</div>
                        <div>
                            <div class="text-xs text-gray-500 font-bold uppercase tracking-wider">Nilai Inventori</div>
                            <div class="text-xl font-black text-gray-800">Rp {{ number_format($inventoryValue['total'] / 1000000, 1, ',', '.') }}jt</div>
                        </div>
                    </div>

                    {{-- Pending POs --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-2xl">📝</div>
                        <div>
                            <div class="text-xs text-gray-500 font-bold uppercase tracking-wider">Pending PO</div>
                            <div class="text-xl font-black text-gray-800">{{ $purchaseStats['pending_po'] }} Order</div>
                        </div>
                    </div>

                     {{-- Monthly Spend --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-2xl">💸</div>
                        <div>
                            <div class="text-xs text-gray-500 font-bold uppercase tracking-wider">Belanja Bulanan</div>
                            <div class="text-xl font-black text-gray-800">Rp {{ number_format($purchaseStats['monthly_spend'] / 1000000, 1, ',', '.') }}jt</div>
                        </div>
                    </div>

                    {{-- Material Alerts --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 {{ $materialAlerts->count() > 0 ? 'ring-2 ring-red-100' : '' }}">
                        <div class="w-12 h-12 rounded-xl {{ $materialAlerts->count() > 0 ? 'bg-red-50 text-red-500 animate-pulse' : 'bg-green-50 text-green-500' }} flex items-center justify-center text-2xl">⚠️</div>
                        <div>
                            <div class="text-xs text-gray-500 font-bold uppercase tracking-wider">Stok Alert</div>
                            <div class="text-xl font-black {{ $materialAlerts->count() > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $materialAlerts->count() }} Item</div>
                        </div>
                    </div>
                </div>

                {{-- Inventory Details & Charts --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Material Trends --}}
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3 class="dashboard-card-title">📉 Material Popular (7 Hari)</h3>
                        </div>
                        <div class="dashboard-card-body">
                             <div class="chart-container" style="height: 200px;">
                                <canvas id="materialTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Supplier Analytics --}}
                    <div class="dashboard-card">
                         <div x-data="{ supplierTab: 'spend' }">
                            <div class="dashboard-card-header flex justify-between items-center">
                                <h3 class="dashboard-card-title">🤝 Supplier Analytics</h3>
                                <div class="flex bg-gray-100 p-0.5 rounded-lg">
                                    <button @click="supplierTab = 'spend'" :class="supplierTab === 'spend' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-400'" class="px-3 py-1 text-xs font-bold rounded-md transition-all">Spend</button>
                                    <button @click="supplierTab = 'rating'" :class="supplierTab === 'rating' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-400'" class="px-3 py-1 text-xs font-bold rounded-md transition-all">Rating</button>
                                </div>
                            </div>
                            <div class="dashboard-card-body">
                                <div x-show="supplierTab === 'spend'" class="chart-container" style="height: 200px;">
                                    <canvas id="supplierSpendChart"></canvas>
                                </div>
                                <div x-show="supplierTab === 'rating'" class="chart-container" style="height: 200px;">
                                    <canvas id="supplierRatingChart"></canvas>
                                </div>
                            </div>
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

        // Status Distribution Donut Chart (ApexCharts) - Premium Interactive
        const statusOptions = {
            chart: {
                type: 'donut',
                height: 250,
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: @json($statusDistribution['labels']).map(status => statusColors[status] || colors.primary),
            series: @json($statusDistribution['data']),
            labels: @json($statusDistribution['labels']),
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Orders',
                                fontSize: '14px',
                                fontWeight: 700,
                                color: '#374151',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            },
                            value: {
                                fontSize: '24px',
                                fontWeight: 900,
                                color: '#14b8a6'
                            }
                        }
                    }
                }
            },
            legend: {
                position: 'bottom',
                fontSize: '12px',
                fontWeight: 600,
                markers: {
                    width: 10,
                    height: 10,
                    radius: 2
                }
            },
            dataLabels: {
                enabled: false
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function(value) {
                        return value + ' orders'
                    }
                }
            }
        };
        
        const statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
        statusChart.render();

        // Daily Trends Area Chart (ApexCharts) - Orange Gradient
        const trendsOptions = {
            chart: {
                type: 'area',
                height: 250,
                toolbar: { show: false },
                zoom: { enabled: false },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#f97316'], // Orange
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.6,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            markers: {
                size: 4,
                colors: ['#f97316'],
                strokeWidth: 2,
                strokeColors: '#fff',
                hover: {
                    size: 6
                }
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: 'Orders',
                data: @json($dailyTrends['data'])
            }],
            xaxis: {
                categories: @json($dailyTrends['labels']),
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                },
                min: 0
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
                xaxis: {
                    lines: { show: false }
                }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function(value) {
                        return value + ' orders'
                    }
                }
            }
        };
        
        const trendsChart = new ApexCharts(document.querySelector("#trendsChart"), trendsOptions);
        trendsChart.render();



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


        // Revenue Area Chart (ApexCharts) - Premium Gradient
        const revenueOptions = {
            chart: {
                type: 'area',
                height: 200,
                toolbar: { show: false },
                zoom: { enabled: false },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#14b8a6'], // Teal
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                name: 'Pendapatan',
                data: @json($revenueData['daily']['data'])
            }],
            xaxis: {
                categories: @json($revenueData['daily']['labels']),
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '11px',
                        fontWeight: 600
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6b7280',
                        fontSize: '11px',
                        fontWeight: 600
                    },
                    formatter: function(value) {
                        return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                    }
                }
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
                xaxis: {
                    lines: { show: false }
                }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        };
        
        const revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
        revenueChart.render();


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



        // Complaint Category Chart
        new Chart(document.getElementById('complaintCategoryChart'), {
            type: 'doughnut',
            data: {
                labels: @json($complaintAnalytics['category_counts']['labels']),
                datasets: [{
                    data: @json($complaintAnalytics['category_counts']['data']),
                    backgroundColor: [colors.primary, colors.danger, colors.warning, colors.success, colors.info],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</x-app-layout>
