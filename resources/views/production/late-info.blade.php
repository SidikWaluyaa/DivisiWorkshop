<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="p-2 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg shadow-lg border border-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                <div class="flex flex-col">
                    <h2 class="font-bold text-xl leading-tight tracking-wide text-white">
                        {{ __('Informasi Keterlambatan') }}
                    </h2>
                    <div class="text-xs font-medium text-white/80">
                       Monitoring Deadline Produksi (JSON Sync Active)
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20 text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                    <span class="text-sm font-bold uppercase tracking-wider">Live Monitoring</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Alert Info for Spreadsheet --}}
            <div class="mb-8 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-xl shadow-sm flex items-start gap-4">
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-blue-800 text-sm">Integrasi Spreadsheet Aktif</h4>
                    <p class="text-blue-700 text-xs mt-1">
                        Gunakan URL berikut di Google Sheets <code>ImportJSON</code>: <br>
                        <span class="font-mono bg-blue-100 px-2 py-1 rounded mt-1 inline-block border border-blue-200">
                            {{ url('/api/sync_late_production.php?token=' . (config('app.sync_token') ?? 'SECRET_TOKEN_12345')) }}
                        </span>
                    </p>
                </div>
            </div>

            {{-- Status Filters & Search --}}
            <div class="mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    @php
                        $currentStatus = request('status');
                    @endphp
                    <a href="{{ route('production.late-info') }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ !$currentStatus ? 'bg-gray-900 text-white shadow-lg' : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-200' }}">
                        SEMUA
                    </a>
                    <a href="{{ route('production.late-info', ['status' => 'LATE', 'search' => request('search')]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $currentStatus == 'LATE' ? 'bg-red-600 text-white shadow-lg shadow-red-200' : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-200' }}">
                        TERLAMBAT
                    </a>
                    <a href="{{ route('production.late-info', ['status' => 'WARNING', 'search' => request('search')]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $currentStatus == 'WARNING' ? 'bg-orange-500 text-white shadow-lg shadow-orange-200' : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-200' }}">
                        MENDEKATI (<= 5 HARI)
                    </a>
                    <a href="{{ route('production.late-info', ['status' => 'ON TRACK', 'search' => request('search')]) }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $currentStatus == 'ON TRACK' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-200' : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-200' }}">
                        ON TRACK
                    </a>
                </div>

                <div class="w-full lg:w-72">
                    <form action="{{ route('production.late-info') }}" method="GET" class="relative group">
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400 group-focus-within:text-gray-900 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               class="block w-full pl-10 pr-10 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all shadow-sm" 
                               placeholder="Cari SPK / Nama Pelanggan...">
                        
                        @if(request('search'))
                            <a href="{{ route('production.late-info', ['status' => request('status')]) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Inventory Table --}}
            <div class="bg-white overflow-hidden shadow-2xl rounded-3xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">No</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">SPK / Pelanggan</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">Deadline</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">Sisa Hari</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-widest">Prioritas</th>
                                <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-widest">Deskripsi / Alasan</th>
                                <th class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-widest">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-400">
                                        {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-gray-900 font-mono">{{ $order->spk_number }}</span>
                                            <span class="text-xs text-gray-500 font-medium">{{ $order->customer_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path>
                                            </svg>
                                            <span class="text-sm font-bold text-gray-700">
                                                {{ $order->estimation_date ? $order->estimation_date->format('d M Y') : '-' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $days = (int) $order->calendar_days_remaining;
                                            $colorClass = $days < 0 ? 'text-red-600 bg-red-50' : ($days <= 5 ? 'text-orange-600 bg-orange-50' : 'text-green-600 bg-green-50');
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-sm font-black {{ $colorClass }} border border-current border-opacity-20 shadow-sm">
                                            {{ $days }} Hari
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusLabel = $order->warning_status ?? 'ON TRACK';
                                            $badgeClass = match($statusLabel) {
                                                'LATE' => 'bg-red-500 text-white shadow-red-200',
                                                'WARNING' => 'bg-orange-400 text-white shadow-orange-200',
                                                default => 'bg-emerald-400 text-white shadow-emerald-200',
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-black tracking-tighter uppercase shadow-lg {{ $badgeClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $scale = $order->priority_scale ?? 3;
                                            $scaleColor = match((int)$scale) {
                                                1 => 'bg-red-600',
                                                2 => 'bg-orange-500',
                                                default => 'bg-emerald-500',
                                            };
                                        @endphp
                                        <div class="flex justify-center items-center gap-1">
                                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-white font-black text-sm shadow-md {{ $scaleColor }}">
                                                {{ $scale }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap min-w-[200px]">
                                        <div class="relative group" x-data="{ saving: false }">
                                            <input type="text" 
                                                   value="{{ $order->late_description }}"
                                                   @blur="
                                                        if($el.value != '{{ $order->late_description }}') {
                                                            saving = true;
                                                            fetch('{{ route('production.late-info.update-description') }}', {
                                                                method: 'POST',
                                                                headers: {
                                                                    'Content-Type': 'application/json',
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                },
                                                                body: JSON.stringify({ id: {{ $order->id }}, description: $el.value })
                                                            })
                                                            .then(response => response.json())
                                                            .then(data => {
                                                                saving = false;
                                                                if(data.status === 'success') {
                                                                    // Optional: Feedback visual
                                                                }
                                                            })
                                                            .catch(err => {
                                                                saving = false;
                                                                alert('Gagal menyimpan deskripsi');
                                                            });
                                                        }
                                                   "
                                                   class="w-full bg-gray-50 border border-gray-200 rounded-lg text-xs font-semibold px-3 py-1.5 focus:bg-white focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all"
                                                   placeholder="Alasan / Catatan...">
                                            <div x-show="saving" class="absolute right-2 top-1/2 -translate-y-1/2">
                                                <svg class="animate-spin h-3 w-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route('production.index', ['search' => $order->spk_number]) }}" 
                                           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-xl text-xs font-bold hover:bg-black hover:shadow-xl transition-all active:scale-95 shadow-lg">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Proses @ Stasiun
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-4 text-gray-400">
                                            <svg class="w-16 h-16 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <h5 class="font-black text-gray-600">Alhamdulillah!</h5>
                                                <p class="text-sm">Semua pengerjaan produksi tepat waktu.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>

            {{-- Footer Info --}}
            <div class="mt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Skala 1: Terlambat
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-orange-500"></span> Skala 2: Warning (<= 5 Hari)
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Skala 3: On Track
                    </div>
                </div>
                <div>
                    Terakhir diperbarui: {{ now()->format('d M Y H:i:s') }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
