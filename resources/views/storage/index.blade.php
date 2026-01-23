<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-teal-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            
            {{-- Premium Header --}}
            <section class="relative overflow-hidden bg-gradient-to-br from-teal-600 via-teal-700 to-orange-600 rounded-3xl shadow-2xl">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative px-8 py-10">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div>
                            <h1 class="text-4xl lg:text-5xl font-black text-white tracking-tight">
                                Gudang Finish
                            </h1>
                            <p class="text-gray-100 text-lg font-medium mt-2">
                                Storage Management & Tracking
                            </p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('storage.racks.index') }}" class="px-6 py-2 bg-white/20 backdrop-blur-sm border-2 border-white/20 text-white rounded-lg font-bold hover:bg-white/30 transition-colors flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                                Kelola Rak
                            </a>

                            {{-- Search Bar --}}
                            <form action="{{ route('storage.index') }}" method="GET" class="flex gap-2">
                                <input type="text" name="search" value="{{ $search ?? '' }}" 
                                       placeholder="Cari SPK / Customer / Rak..." 
                                       class="px-4 py-2 rounded-lg border-2 border-white/20 bg-white/10 backdrop-blur-sm text-white placeholder-white/60 focus:outline-none focus:border-white/40">
                                <button type="submit" class="px-6 py-2 bg-white text-teal-600 rounded-lg font-bold hover:bg-gray-100 transition-colors">
                                    Search
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            {{-- KPI Cards --}}
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-kpi-card 
                    title="Total Tersimpan" 
                    :value="$stats['total_stored']" 
                    icon="📦"
                    color="teal"
                />
                <x-kpi-card 
                    title="Sudah Diambil" 
                    :value="$stats['total_retrieved']" 
                    icon="✅"
                    color="green"
                />
                <x-kpi-card 
                    title="Overdue (>7 hari)" 
                    :value="$stats['overdue_count']" 
                    icon="⚠️"
                    color="red"
                />
                <x-kpi-card 
                    title="Rata-rata Penyimpanan" 
                    :value="number_format($stats['avg_storage_days'], 1) . ' hari'" 
                    icon="📊"
                    color="gray"
                />
            </section>

            {{-- Rack Utilization --}}
            <section class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-black text-gray-800">Kapasitas Rak</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                        <div class="text-center">
                            <div class="text-3xl font-black text-teal-600">{{ $rackUtilization['total_racks'] }}</div>
                            <div class="text-sm text-gray-600">Total Rak</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-black text-orange-600">{{ $rackUtilization['total_used'] }}</div>
                            <div class="text-sm text-gray-600">Terpakai</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-black text-green-600">{{ $rackUtilization['total_available'] }}</div>
                            <div class="text-sm text-gray-600">Tersedia</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-black text-red-600">{{ $rackUtilization['full_racks'] }}</div>
                            <div class="text-sm text-gray-600">Penuh</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-black text-gray-600">{{ number_format($rackUtilization['utilization_percentage'], 1) }}%</div>
                            <div class="text-sm text-gray-600">Utilitas</div>
                        </div>
                    </div>

                    {{-- Rack Grid Visualization --}}
                    <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                        @foreach($racks as $rack)
                            @php
                                $utilization = $rack->getUtilizationPercentage();
                                $color = $utilization >= 100 ? 'bg-red-500' : ($utilization >= 75 ? 'bg-yellow-500' : ($utilization >= 50 ? 'bg-orange-500' : 'bg-green-500'));
                            @endphp
                            <div class="relative group">
                                <div class="aspect-square {{ $color }} rounded-lg flex items-center justify-center text-white font-bold text-xs hover:scale-110 transition-transform cursor-pointer">
                                    {{ $rack->rack_code }}
                                </div>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-10">
                                    {{ $rack->current_count }}/{{ $rack->capacity }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Stored Items Table --}}
            <section class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-teal-50 px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-black text-gray-800">Sepatu di Gudang</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">SPK</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Rak</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Stored</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($storedItems as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">{{ $item->workOrder->spk_number }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $item->workOrder->customer->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->workOrder->customer->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-sm font-bold bg-teal-100 text-teal-700">
                                            {{ $item->rack_code }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $item->stored_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php $days = $item->getStorageDurationDays(); @endphp
                                        <span class="px-2 py-1 rounded text-xs font-bold {{ $days > 7 ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $days }} hari
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('storage.label', $item->id) }}" target="_blank" class="text-teal-600 hover:text-teal-900 bg-teal-50 hover:bg-teal-100 p-1.5 rounded-md transition-colors" title="Print Label">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                            <form action="{{ route('storage.retrieve', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Ambil sepatu dari gudang?')" class="text-orange-600 hover:text-orange-900 bg-orange-50 hover:bg-orange-100 p-1.5 rounded-md transition-colors" title="Retrieve (Ambil)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                                                </button>
                                            </form>
                                            <form action="{{ route('storage.unassign', $item->work_order_id) }}" method="POST" class="inline" onsubmit="return confirm('Lepas tag rak? Item akan kembali ke status Menunggu Disimpan.');">
                                                @csrf
                                                <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors" title="Lepas Tag">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        Tidak ada sepatu di gudang
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(method_exists($storedItems, 'links'))
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $storedItems->links() }}
                    </div>
                @endif
            </section>

        </div>
    </div>
</x-app-layout>
