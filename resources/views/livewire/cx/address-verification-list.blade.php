<div class="min-h-screen bg-[#f8fafc]">
    {{-- Flatpickr Styles & Scripts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    {{-- High-End Header Section --}}
    <div class="bg-white border-b border-gray-200/60 pb-8 pt-6">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest border border-emerald-100 flex items-center gap-2">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            Address Verification Hub
                        </div>
                        <div class="px-3 py-1 rounded-full bg-teal-50 text-teal-600 text-[10px] font-black uppercase tracking-widest border border-teal-100">
                            CX Division
                        </div>
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                        Alamat Terverifikasi
                        <span class="text-gray-300 font-light text-2xl">/</span>
                        <span class="text-gray-400 font-medium text-lg">Verified Only</span>
                    </h1>
                    <p class="text-gray-500 font-medium">Daftar lengkap konsumen yang telah memverifikasi alamat pengiriman mereka secara online beserta status pengirimannya.</p>
                </div>

                {{-- Stats Floating Cards --}}
                <div class="flex gap-4">
                    <button wire:click="filterByPreset('today')" class="relative group outline-none">
                        <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                        <div class="relative bg-white px-6 py-4 rounded-2xl border {{ $date_start == date('Y-m-d') && $date_end == date('Y-m-d') ? 'border-emerald-500 ring-2 ring-emerald-50' : 'border-gray-100' }} flex items-center gap-4 min-w-[180px] transition-all">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">✓</div>
                            <div class="text-left">
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Hari Ini</div>
                                <div class="text-2xl font-black text-gray-900">{{ $stats['today'] }}</div>
                            </div>
                        </div>
                    </button>
                    <button wire:click="filterByPreset('week')" class="relative group outline-none">
                        <div class="absolute -inset-1 bg-gradient-to-r from-teal-500 to-blue-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                        <div class="relative bg-white px-6 py-4 rounded-2xl border {{ $date_start == \Carbon\Carbon::today()->startOfWeek()->toDateString() ? 'border-teal-500 ring-2 ring-teal-50' : 'border-gray-100' }} flex items-center gap-4 min-w-[180px] transition-all">
                            <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">📅</div>
                            <div class="text-left">
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Minggu Ini</div>
                                <div class="text-2xl font-black text-gray-900">{{ $stats['this_week'] }}</div>
                            </div>
                        </div>
                    </button>
                    <button wire:click="filterByPreset('all')" class="relative group outline-none">
                        <div class="absolute -inset-1 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                        <div class="relative bg-white px-6 py-4 rounded-2xl border {{ empty($date_start) && empty($date_end) ? 'border-purple-500 ring-2 ring-purple-50' : 'border-gray-100' }} flex items-center gap-4 min-w-[180px] transition-all">
                            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">👥</div>
                            <div class="text-left">
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Data</div>
                                <div class="text-2xl font-black text-gray-900">{{ $stats['total'] }}</div>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Advanced Filter Bar --}}
        <div class="bg-white/70 backdrop-blur-md border border-white/20 shadow-xl shadow-gray-200/40 rounded-[2.5rem] p-3 mb-10 sticky top-4 z-40">
            <div class="flex flex-col lg:flex-row items-center gap-3">
                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" 
                        class="w-full pl-12 pr-4 py-4 bg-gray-50/50 border-none rounded-[2rem] focus:ring-2 focus:ring-emerald-500 transition-all font-bold text-sm text-gray-700 placeholder-gray-400" 
                        placeholder="Cari Nama Pelanggan atau Nomor Telepon...">
                </div>

                <div class="flex items-center gap-3 w-full lg:w-auto">
                    <!-- Shipping Status Filter -->
                    <div class="relative">
                        <select wire:model.live="shipping_status" class="bg-gray-50/50 rounded-[2rem] px-5 py-3.5 text-xs font-black text-gray-700 border border-transparent focus:border-emerald-500 focus:ring-0 cursor-pointer shadow-none">
                            <option value="all">📦 Semua Status Pengiriman</option>
                            <option value="shipped">🟢 Sudah Dikirim (Verified/Resi)</option>
                            <option value="preparing">📦 Sedang Disiapkan (Gudang Shipping)</option>
                            <option value="workshop">🟡 Masih di Workshop</option>
                        </select>
                    </div>

                    <!-- Date Picker Range (Flatpickr) -->
                    <div wire:ignore class="relative" x-data="{
                        initFlatpickr() {
                            flatpickr($refs.datePicker, {
                                mode: 'range',
                                dateFormat: 'Y-m-d',
                                defaultDate: [@js($date_start), @js($date_end)],
                                locale: {
                                    rangeSeparator: ' s/d '
                                },
                                onChange: (selectedDates, dateStr, instance) => {
                                    if (selectedDates.length === 2) {
                                        let start = instance.formatDate(selectedDates[0], 'Y-m-d');
                                        let end = instance.formatDate(selectedDates[1], 'Y-m-d');
                                        $wire.set('date_start', start);
                                        $wire.set('date_end', end);
                                    } else if (selectedDates.length === 0) {
                                        $wire.set('date_start', '');
                                        $wire.set('date_end', '');
                                    }
                                }
                            });

                            $watch('$wire.date_start', (value) => {
                                if ($refs.datePicker._flatpickr) {
                                    if (!value) {
                                        $refs.datePicker._flatpickr.clear();
                                    } else {
                                        $refs.datePicker._flatpickr.setDate([value, $wire.date_end], false);
                                    }
                                }
                            });
                            $watch('$wire.date_end', (value) => {
                                if ($refs.datePicker._flatpickr && value) {
                                    $refs.datePicker._flatpickr.setDate([$wire.date_start, value], false);
                                }
                            });
                        }
                    }" x-init="initFlatpickr()">
                        <div class="flex items-center bg-gray-50/50 rounded-[2rem] px-5 py-3.5 gap-2 border border-transparent focus-within:border-emerald-500 transition-all">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mr-1">Filter Tanggal</span>
                            <input x-ref="datePicker" type="text" readonly 
                                class="bg-transparent border-none focus:ring-0 text-xs font-black text-gray-600 p-0 cursor-pointer w-44 text-center"
                                placeholder="Pilih Rentang Tanggal...">
                        </div>
                    </div>

                    <!-- Reset Filter Button -->
                    @if($search || $date_start || $date_end || $shipping_status !== 'all')
                        <button wire:click="resetFilters" 
                            class="px-5 py-3.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-extrabold text-xs uppercase tracking-widest rounded-[2rem] transition-all flex items-center gap-2 border border-rose-100/50 shadow-sm shrink-0">
                            🔄 Reset Filter
                        </button>
                    @endif

                    <div class="h-6 w-px bg-gray-200 mx-1 hidden lg:block"></div>

                    <!-- Print Buttons -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('cx.verified-addresses.print-report') }}?search={{ urlencode($search) }}&date_start={{ urlencode($date_start) }}&date_end={{ urlencode($date_end) }}" 
                            target="_blank"
                            class="px-5 py-3.5 bg-[#22B086] hover:bg-[#1fa17a] text-white font-extrabold text-[10px] uppercase tracking-widest rounded-[2rem] transition-all flex items-center gap-2 shadow-sm shrink-0">
                            🖨️ Cetak List
                        </a>
                        
                        <a href="{{ route('cx.verified-addresses.print-bulk-labels') }}?search={{ urlencode($search) }}&date_start={{ urlencode($date_start) }}&date_end={{ urlencode($date_end) }}" 
                            target="_blank"
                            class="px-5 py-3.5 bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-[10px] uppercase tracking-widest rounded-[2rem] transition-all flex items-center gap-2 shadow-sm shrink-0">
                            🏷️ Cetak Label Masal
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grouped Content Grid --}}
        <div class="space-y-12">
            @forelse($groupedCustomers as $date => $items)
                <div class="space-y-6">
                    {{-- Date Separator --}}
                    <div class="flex items-center gap-4">
                        <div class="px-6 py-2 bg-gray-900 text-white rounded-full text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-gray-200">
                            @if($date == date('Y-m-d'))
                                Hari Ini
                            @elseif($date == date('Y-m-d', strtotime('-1 day')))
                                Kemarin
                            @else
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                            @endif
                        </div>
                        <div class="h-0.5 flex-1 bg-gray-200/50 rounded-full"></div>
                    </div>

                    {{-- Customer Table --}}
                    <div class="bg-white border border-gray-100 rounded-[2rem] shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50/75 border-b border-gray-100 text-gray-400 text-[10px] font-black uppercase tracking-widest">
                                        <th class="py-4 px-6 text-center w-12">No</th>
                                        <th class="py-4 px-6 w-36">Waktu Verifikasi</th>
                                        <th class="py-4 px-6 w-44">Nama Pelanggan</th>
                                        <th class="py-4 px-6 w-36">No. Telepon</th>
                                        <th class="py-4 px-6">Alamat Pengiriman</th>
                                        <th class="py-4 px-6 w-32 text-center">SPK Aktif</th>
                                        <th class="py-4 px-6 w-52 text-center">Status Pengiriman</th>
                                        <th class="py-4 px-6 w-32 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($items as $index => $customer)
                                        @php
                                            $totalSpk = $customer->workOrders->count();
                                            
                                            // Shipped Orders (Verified or Has Resi or DIANTAR)
                                            $shippedOrders = $customer->workOrders->filter(function($wo) {
                                                $s = $wo->shipping;
                                                $statusVal = is_object($wo->status) ? $wo->status->value : $wo->status;
                                                return ($s && ((bool)$s->is_verified || !empty($s->resi_pengiriman))) || $statusVal === 'DIANTAR';
                                            });

                                            // Preparing Orders (In shippings table, but not verified yet)
                                            $preparingOrders = $customer->workOrders->filter(function($wo) use ($shippedOrders) {
                                                $s = $wo->shipping;
                                                return !$shippedOrders->contains('id', $wo->id) && !is_null($s);
                                            });

                                            $shippedCount = $shippedOrders->count();
                                            $preparingCount = $preparingOrders->count();
                                            
                                            $firstPrep = $preparingOrders->first()?->shipping;
                                            $firstShipped = $shippedOrders->first()?->shipping;
                                        @endphp
                                        <tr class="hover:bg-slate-50/30 transition-colors text-slate-700">
                                            {{-- No --}}
                                            <td class="py-4 px-6 text-center text-xs font-bold text-gray-400">
                                                {{ $loop->iteration }}
                                            </td>

                                            {{-- Waktu Verifikasi --}}
                                            <td class="py-4 px-6 text-xs font-bold font-mono text-gray-500 uppercase tracking-tight">
                                                {{ $customer->address_verified_at?->format('d M H:i') ?? '-' }}
                                            </td>

                                            {{-- Nama --}}
                                            <td class="py-4 px-6 text-sm font-black text-gray-900 tracking-tight">
                                                {{ $customer->name }}
                                            </td>

                                            {{-- Telepon --}}
                                            <td class="py-4 px-6 text-xs font-bold font-mono tracking-tight text-gray-600">
                                                {{ $customer->phone }}
                                            </td>

                                            {{-- Alamat --}}
                                            <td class="py-4 px-6 text-xs font-semibold leading-relaxed text-gray-600">
                                                <div class="font-bold text-gray-800 mb-0.5">{{ $customer->address }}</div>
                                                <div class="text-[10px] text-gray-400 uppercase tracking-wider">
                                                    Kel. {{ $customer->village ?? '-' }} | Kec. {{ $customer->district ?? '-' }} | {{ $customer->city ?? '-' }} | {{ $customer->province ?? '-' }} ({{ $customer->postal_code ?? '-' }})
                                                </div>
                                            </td>

                                            {{-- SPK Aktif --}}
                                            <td class="py-4 px-6 text-center text-xs">
                                                @if($totalSpk > 0)
                                                    <button type="button" wire:click="openSpkModal({{ $customer->id }})" 
                                                        class="underline decoration-dotted cursor-pointer text-emerald-600 hover:text-emerald-700 font-extrabold outline-none focus:outline-none transition-transform hover:scale-105">
                                                        {{ $totalSpk }} SPK
                                                    </button>
                                                @else
                                                    <span class="text-gray-400 font-bold uppercase text-[10px]">-</span>
                                                @endif
                                            </td>

                                            {{-- Status Pengiriman (3-Stage Marker with SPK Count & Shipping Date) --}}
                                            <td class="py-4 px-6 text-center text-xs">
                                                @if($totalSpk === 0)
                                                    <span class="text-[10px] text-gray-400 font-bold uppercase">Belum Ada SPK</span>
                                                @elseif($shippedCount === $totalSpk)
                                                    <div class="flex flex-col items-center gap-1">
                                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-wider border border-emerald-200 shadow-xs">
                                                            🟢 Sudah Dikirim ({{ $shippedCount }}/{{ $totalSpk }})
                                                        </span>
                                                        @if($firstShipped?->tanggal_pengiriman || $firstShipped?->resi_pengiriman)
                                                            <span class="text-[9.5px] font-mono text-gray-600 font-bold truncate max-w-[170px]" title="Tgl Pengiriman: {{ $firstShipped->tanggal_pengiriman ? $firstShipped->tanggal_pengiriman->format('d M Y') : '-' }}">
                                                                @if($firstShipped->tanggal_pengiriman)
                                                                    <span class="text-emerald-700 font-sans font-black">📅 {{ $firstShipped->tanggal_pengiriman->format('d M Y') }}</span>
                                                                @endif
                                                                @if($firstShipped->resi_pengiriman)
                                                                    <span class="text-gray-400">|</span> {{ $firstShipped->ekspedisi ? $firstShipped->ekspedisi . ': ' : '' }}{{ $firstShipped->resi_pengiriman }}
                                                                @endif
                                                            </span>
                                                        @endif
                                                    </div>
                                                @elseif($preparingCount > 0 && $shippedCount === 0)
                                                    <div class="flex flex-col items-center gap-1">
                                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-[10px] font-black uppercase tracking-wider border border-blue-200 shadow-xs">
                                                            📦 Sedang Disiapkan ({{ $preparingCount }}/{{ $totalSpk }})
                                                        </span>
                                                        <span class="text-[9px] text-blue-600 font-bold">
                                                            {{ $firstPrep?->ekspedisi ? $firstPrep->ekspedisi : 'Pengiriman Gudang' }}
                                                            @if($firstPrep?->tanggal_pengiriman)
                                                                | <span class="font-mono text-blue-800">📅 {{ $firstPrep->tanggal_pengiriman->format('d M Y') }}</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                @elseif($shippedCount > 0 || $preparingCount > 0)
                                                    <div class="flex flex-col items-center gap-1">
                                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-[10px] font-black uppercase tracking-wider border border-teal-200 shadow-xs">
                                                            🔵 Sebagian Dikirim ({{ $shippedCount + $preparingCount }}/{{ $totalSpk }})
                                                        </span>
                                                        <span class="text-[9px] text-teal-600 font-bold">
                                                            {{ $shippedCount }} Kirim | {{ $preparingCount }} Siap
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="flex flex-col items-center gap-1">
                                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-wider border border-amber-200 shadow-xs">
                                                            🟡 Masih di Workshop (0/{{ $totalSpk }})
                                                        </span>
                                                        <span class="text-[9px] text-amber-600 font-bold">Proses Pengerjaan</span>
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- Aksi (Cetak Label) --}}
                                            <td class="py-4 px-6 text-center text-xs">
                                                @if($customer->workOrders->isNotEmpty())
                                                    @php $latestOrder = $customer->workOrders->first(); @endphp
                                                    <a href="{{ route('admin.orders.shipping-label', $latestOrder->id) }}" target="_blank" 
                                                        class="inline-flex items-center gap-1 px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md shadow-teal-100">
                                                        🖨️ Cetak
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.customers.shipping-label', $customer->id) }}" target="_blank" 
                                                        class="inline-flex items-center gap-1 px-4 py-2 bg-slate-500 hover:bg-slate-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md shadow-slate-100">
                                                        🖨️ Cetak
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="text-center py-20 bg-white border border-gray-100 rounded-[2.5rem] shadow-sm max-w-md mx-auto px-6">
                    <div class="text-6xl mb-6">🔍</div>
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-2">Tidak Ada Data</h3>
                    <p class="text-gray-500 font-medium mb-6">Tidak ada alamat terverifikasi yang cocok dengan filter pencarian atau rentang tanggal Anda.</p>
                    <button wire:click="resetFilters" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs uppercase tracking-widest rounded-full shadow-lg shadow-emerald-100 transition-all">
                        Reset Filter
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $customers->links() }}
        </div>
    </div>

    {{-- Premium SPK Modal --}}
    @if($showSpkModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeSpkModal"></div>

            {{-- Modal Content --}}
            <div class="relative bg-white rounded-[2rem] shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-hidden flex flex-col border border-gray-100 animate-in fade-in zoom-in-95 duration-200 z-50">
                {{-- Header --}}
                <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest block mb-1">Customer Work Orders</span>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">SPK Pelanggan: {{ $selectedCustomerName }}</h3>
                    </div>
                    <button type="button" wire:click="closeSpkModal" class="p-2.5 rounded-xl bg-white border border-gray-200/60 hover:bg-gray-50 text-gray-400 hover:text-gray-600 transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Body (List of SPKs with Shipping Markers) --}}
                <div class="p-8 overflow-y-auto space-y-4 flex-1">
                    @forelse($selectedCustomerSpks as $order)
                        <div class="bg-slate-50 border border-slate-200/70 rounded-2xl p-5 hover:border-emerald-200 hover:bg-white transition-all space-y-3">
                            <div class="flex items-center justify-between gap-4">
                                <div class="space-y-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-base font-black text-gray-900 font-mono tracking-tight">{{ $order['spk_number'] }}</span>
                                        <span class="px-2.5 py-0.5 bg-slate-200 border border-slate-300 text-slate-700 text-[8px] font-black rounded uppercase tracking-wider">
                                            Status Workshop: {{ $order['status'] }}
                                        </span>
                                    </div>
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Sepatu: <span class="text-gray-800">{{ $order['shoe_brand'] }}</span> ({{ $order['shoe_color'] ?? '-' }})
                                    </p>
                                </div>

                                <a href="{{ route('admin.orders.shipping-label', $order['id']) }}" target="_blank" 
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md shadow-teal-100 shrink-0">
                                    🖨️ Cetak Label
                                </a>
                            </div>

                            {{-- 3-Stage Shipping Status Marker Box --}}
                            @if($order['is_shipped'])
                                <div class="bg-emerald-50/90 border border-emerald-200 rounded-xl p-3 text-xs text-emerald-900 font-semibold flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[9px] font-black uppercase tracking-wider">🟢 SUDAH DIKIRIM</span>
                                        @if($order['shipping_is_verified'])
                                            <span class="text-[10px] text-emerald-700 font-bold" title="Telah diverifikasi oleh bagian pengiriman">✅ Verified Shipping</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] font-mono font-bold text-emerald-800 flex items-center gap-2 flex-wrap">
                                        @if(!empty($order['tanggal_pengiriman']))
                                            <span class="bg-emerald-100/80 px-2.5 py-0.5 rounded-lg border border-emerald-300 text-emerald-900 font-sans font-black text-[10px] shadow-2xs">
                                                📅 {{ $order['tanggal_pengiriman'] }}
                                            </span>
                                        @endif
                                        <span>
                                            {{ $order['ekspedisi'] ? $order['ekspedisi'] . ' | ' : '' }}Resi: <span class="bg-white px-2 py-0.5 rounded border border-emerald-300 text-emerald-900 font-mono">{{ $order['resi_pengiriman'] ?? '-' }}</span>
                                        </span>
                                    </div>
                                </div>
                            @elseif($order['is_preparing_shipping'])
                                <div class="bg-blue-50/90 border border-blue-200 rounded-xl p-3 text-xs text-blue-900 font-semibold flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-lg bg-blue-600 text-white text-[9px] font-black uppercase tracking-wider">📦 SEDANG DISIAPKAN</span>
                                        <span class="text-[10px] text-blue-700 font-bold">Telah Masuk Pengiriman Gudang</span>
                                    </div>
                                    <div class="text-[11px] font-bold text-blue-800 flex items-center gap-2 flex-wrap">
                                        @if(!empty($order['tanggal_pengiriman']))
                                            <span class="bg-blue-100/80 px-2 py-0.5 rounded-lg border border-blue-300 text-blue-900 font-sans font-black text-[10px]">
                                                📅 {{ $order['tanggal_pengiriman'] }}
                                            </span>
                                        @endif
                                        <span>
                                            Ekspedisi: <span class="text-blue-900 font-black">{{ $order['ekspedisi'] ?? 'Anteraja' }}</span>
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="bg-amber-50/90 border border-amber-200 rounded-xl p-3 text-xs text-amber-900 font-semibold flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-lg bg-amber-500 text-white text-[9px] font-black uppercase tracking-wider">🟡 MASIH DI WORKSHOP</span>
                                        <span class="text-[10px] text-amber-700 font-bold">Sedang Dalam Pengerjaan Teknisi</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <p class="text-gray-400 font-bold uppercase tracking-wider text-xs">Tidak ada SPK untuk pelanggan ini.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Footer --}}
                <div class="px-8 py-5 border-t border-gray-100 flex justify-end bg-slate-50/50">
                    <button type="button" wire:click="closeSpkModal" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-black uppercase tracking-widest rounded-xl transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
