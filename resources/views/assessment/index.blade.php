<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm shadow-sm border border-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            
            <div class="flex flex-col">
                <h2 class="font-bold text-xl leading-tight tracking-wide">
                    {{ __('Assessment Station') }}
                </h2>
                <div class="text-xs font-medium opacity-90">
                    {{ \Carbon\Carbon::now()->format('l, d F Y') }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            <div class="dashboard-card overflow-hidden">
                <div class="dashboard-card-header flex flex-col md:flex-row justify-between md:items-center gap-3">
                    <h3 class="dashboard-card-title">
                        📋 Antrian Assessment (Menunggu Pengecekan)
                    </h3>
                     <div class="flex flex-wrap items-center gap-2">
                          {{-- Search Form --}}
                          <form method="GET" action="{{ route('assessment.index') }}" class="relative">
                             @if(request('invoice_status'))
                                 @foreach((array)request('invoice_status') as $status)
                                     <input type="hidden" name="invoice_status[]" value="{{ $status }}">
                                 @endforeach
                             @endif
                             <input type="text" 
                                    name="search" 
                                    value="{{ request('search') }}"
                                    placeholder="Cari SPK / Customer..." 
                                    class="pl-9 pr-4 py-1.5 text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 shadow-sm w-48 transition-all focus:w-64">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                 <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                 </svg>
                             </div>
                         </form>

                         <span class="px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-xs font-bold shadow-sm">
                             Total: {{ $queue->total() }}
                         </span>
                     </div>
                 </div>

                 <div class="dashboard-card-body p-0">
                     <!-- SUB-HEADER FILTER TABS -->
                     <div class="border-b border-gray-100 bg-white px-6 py-4">
                         <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                             <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 scrollbar-none w-full">
                                 @php
                                     $activeStatuses = (array) request('invoice_status', []);
                                     $activeStatuses = array_filter($activeStatuses); // bersihkan nilai kosong
                                     $isAllActive = empty($activeStatuses);
                                 @endphp
                                 
                                 <!-- TAB SEMUA -->
                                 <button type="button" onclick="toggleStatusFilter('all')" 
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 {{ $isAllActive ? 'bg-teal-50 text-teal-700 shadow-sm ring-1 ring-teal-100' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}">
                                     <span>Semua</span>
                                     <span class="px-2 py-0.5 rounded-full text-xs {{ $isAllActive ? 'bg-teal-200 text-teal-800' : 'bg-gray-100 text-gray-600' }}">{{ $counts['all'] }}</span>
                                 </button>
                                 
                                 <!-- TAB LUNAS -->
                                 @php $isLunasActive = in_array('Lunas', $activeStatuses); @endphp
                                 <button type="button" onclick="toggleStatusFilter('Lunas')" 
                                    class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 {{ $isLunasActive ? 'bg-emerald-50 text-emerald-700 shadow-sm ring-1 ring-emerald-100' : 'text-gray-400 hover:text-emerald-700 hover:bg-emerald-50/50' }}">
                                     <div class="flex items-center justify-center w-4 h-4 rounded border {{ $isLunasActive ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-gray-300 bg-white' }} transition-colors duration-150">
                                         @if($isLunasActive)
                                             <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                         @endif
                                     </div>
                                     <span>Lunas</span>
                                     <span class="px-2 py-0.5 rounded-full text-xs {{ $isLunasActive ? 'bg-emerald-200/80 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">{{ $counts['lunas'] }}</span>
                                 </button>
                                 
                                 <!-- TAB DP/CICIL -->
                                 @php $isDpActive = in_array('DP/Cicil', $activeStatuses); @endphp
                                 <button type="button" onclick="toggleStatusFilter('DP/Cicil')" 
                                    class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 {{ $isDpActive ? 'bg-indigo-50 text-indigo-700 shadow-sm ring-1 ring-indigo-100' : 'text-gray-400 hover:text-indigo-700 hover:bg-indigo-50/50' }}">
                                     <div class="flex items-center justify-center w-4 h-4 rounded border {{ $isDpActive ? 'border-indigo-500 bg-indigo-500 text-white' : 'border-gray-300 bg-white' }} transition-colors duration-150">
                                         @if($isDpActive)
                                             <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                         @endif
                                     </div>
                                     <span>DP / Cicil</span>
                                     <span class="px-2 py-0.5 rounded-full text-xs {{ $isDpActive ? 'bg-indigo-200/80 text-indigo-800' : 'bg-gray-100 text-gray-600' }}">{{ $counts['dp'] }}</span>
                                 </button>
                                 
                                 <!-- TAB BELUM BAYAR -->
                                 @php $isBbActive = in_array('Belum Bayar', $activeStatuses); @endphp
                                 <button type="button" onclick="toggleStatusFilter('Belum Bayar')" 
                                    class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 {{ $isBbActive ? 'bg-rose-50 text-rose-700 shadow-sm ring-1 ring-rose-100' : 'text-gray-400 hover:text-rose-700 hover:bg-rose-50/50' }}">
                                     <div class="flex items-center justify-center w-4 h-4 rounded border {{ $isBbActive ? 'border-rose-500 bg-rose-500 text-white' : 'border-gray-300 bg-white' }} transition-colors duration-150">
                                         @if($isBbActive)
                                             <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                         @endif
                                     </div>
                                     <span>Belum Bayar</span>
                                     <span class="px-2 py-0.5 rounded-full text-xs {{ $isBbActive ? 'bg-rose-200/80 text-rose-800' : 'bg-gray-100 text-gray-600' }}">{{ $counts['belum_bayar'] }}</span>
                                 </button>
                                 
                                 <!-- TAB BELUM ADA INVOICE -->
                                 @php $isNoneActive = in_array('none', $activeStatuses); @endphp
                                 <button type="button" onclick="toggleStatusFilter('none')" 
                                    class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl text-sm font-bold transition-all duration-200 {{ $isNoneActive ? 'bg-slate-100 text-slate-800 shadow-sm ring-1 ring-slate-200' : 'text-gray-400 hover:text-slate-700 hover:bg-slate-50' }}">
                                     <div class="flex items-center justify-center w-4 h-4 rounded border {{ $isNoneActive ? 'border-slate-500 bg-slate-500 text-white' : 'border-gray-300 bg-white' }} transition-colors duration-150">
                                         @if($isNoneActive)
                                             <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                         @endif
                                     </div>
                                     <span>Belum Ada Invoice</span>
                                     <span class="px-2 py-0.5 rounded-full text-xs {{ $isNoneActive ? 'bg-slate-200 text-slate-800' : 'bg-gray-100 text-gray-600' }}">{{ $counts['none'] }}</span>
                                 </button>
                             </div>
                         </div>
                     </div>

                    <div class="overflow-x-auto lg:overflow-x-hidden -mx-4 sm:mx-0">
                        <table class="min-w-full w-full text-sm text-left text-gray-500 table-auto">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200 whitespace-nowrap">
                                <tr>
                                    <th scope="col" class="px-2 py-2.5 text-center w-8">
                                        <input type="checkbox" id="select-all-spk" class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500 cursor-pointer transition-colors duration-150">
                                    </th>
                                    <th scope="col" class="px-2 py-2.5 font-bold text-teal-800 text-center">Prioritas</th>
                                    <th scope="col" class="px-2 py-2.5 font-bold text-teal-800">SPK</th>
                                    <th scope="col" class="px-2 py-2.5 font-bold text-teal-800 text-center">Status Invoice</th>
                                    <th scope="col" class="px-2 py-2.5 font-bold text-teal-800">Pelanggan</th>
                                    <th scope="col" class="px-2 py-2.5 font-bold text-teal-800">Brand / Info</th>
                                    <th scope="col" class="px-2 py-2.5 font-bold text-teal-800">Masuk Sejak</th>
                                    <th scope="col" class="px-2 py-2.5 font-bold text-teal-800 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($queue as $order)
                                <tr class="transition-all duration-200 {{ $order->fast_track_status === 'yes' ? 'bg-orange-50/40 border-l-4 border-orange-500 hover:bg-orange-50/60' : 'bg-white hover:bg-slate-50/80 hover:scale-[1.002] hover:shadow-xs' }}">
                                    <td class="px-2 py-2.5 text-center whitespace-nowrap w-8">
                                        <input type="checkbox" name="spk_ids[]" value="{{ $order->id }}" class="spk-checkbox w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500 cursor-pointer transition-colors duration-150">
                                    </td>
                                    <td class="px-2 py-2.5 text-center whitespace-nowrap">
                                        @if($order->fast_track_status === 'yes')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-100 text-orange-850 border border-orange-200 shadow-xs ring-4 ring-orange-50/50 animate-pulse">
                                                🚀 FAST TRACK
                                            </span>
                                        @elseif(in_array($order->priority, ['Prioritas', 'Urgent', 'Express']))
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200 shadow-xs ring-4 ring-rose-50/50">
                                                <svg class="w-3 h-3 mr-1 text-rose-500 animate-bounce" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.45-.412-1.725a1 1 0 00-1.426-.692l-.08.03c-.233.09-.38.31-.486.602-.15.412-.21 1.056.037 1.814.242.74.721 1.63 1.542 2.37.77.695 1.785 1.123 2.81 1.123 2.112 0 3.966-1.523 4.454-3.55.337-1.4.156-2.825-.36-4.013a7.618 7.618 0 00-1.332-1.897zM7.222 16.712a1 1 0 01-.176 1.397L6 19l2.768.923a1 1 0 01.633 1.265l-.3 1.002 2.924-.73-1.03-3.606-2.551-2.55a1 1 0 01-.844.757l-1.378.65z" clip-rule="evenodd" /></svg>
                                                PRIORITAS
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-gray-50 text-gray-500 border border-gray-200">
                                                REGULER
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2.5 whitespace-nowrap">
                                        @if($order->fast_track_status === 'yes')
                                            <span class="font-mono font-black text-orange-700 bg-orange-50 px-1.5 py-0.5 rounded-lg border border-orange-200 shadow-xs hover:bg-orange-100 transition-colors duration-150 cursor-pointer inline-block" title="Salin No SPK" onclick="navigator.clipboard.writeText('{{ $order->spk_number }}'); Swal.fire({icon: 'success', title: 'SPK Berhasil Disalin!', showConfirmButton: false, timer: 1200, customClass: {popup: 'rounded-2xl'}})">
                                                {{ $order->spk_number }}
                                            </span>
                                        @else
                                            <span class="font-mono font-black text-teal-700 bg-teal-50 px-1.5 py-0.5 rounded-lg border border-teal-200/50 shadow-xs hover:bg-teal-100 transition-colors duration-150 cursor-pointer inline-block" title="Salin No SPK" onclick="navigator.clipboard.writeText('{{ $order->spk_number }}'); Swal.fire({icon: 'success', title: 'SPK Berhasil Disalin!', showConfirmButton: false, timer: 1200, customClass: {popup: 'rounded-2xl'}})">
                                                {{ $order->spk_number }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2.5 text-center whitespace-nowrap">
                                        @if($order->invoice)
                                            @php
                                                $status = $order->invoice->status;
                                                $badgeClass = match($status) {
                                                    'Lunas' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60 ring-4 ring-emerald-50/50',
                                                    'DP/Cicil' => 'bg-indigo-50 text-indigo-700 border-indigo-200/60 ring-4 ring-indigo-50/50',
                                                    default => 'bg-rose-50 text-rose-700 border-rose-200/60 ring-4 ring-rose-50/50',
                                                };
                                                $pulseColor = match($status) {
                                                    'Lunas' => 'bg-emerald-500',
                                                    'DP/Cicil' => 'bg-indigo-500',
                                                    default => 'bg-rose-500',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-black border shadow-xs transition-all duration-200 hover:brightness-95 {{ $badgeClass }}">
                                                <span class="relative flex h-1.5 w-1.5 mr-1.5">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $pulseColor }}"></span>
                                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 {{ $pulseColor }}"></span>
                                                </span>
                                                {{ $status }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-black bg-slate-50 text-slate-600 border border-slate-200 shadow-xs">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-slate-400"></span>
                                                Belum Invoice
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2.5">
                                        @php
                                            $name = $order->customer_name ?? 'Guest';
                                            $words = explode(' ', preg_replace('/\s+/', ' ', trim($name)));
                                            $initials = '';
                                            if (count($words) >= 2) {
                                                $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                            } else {
                                                $initials = strtoupper(substr($name, 0, min(2, strlen($name))));
                                            }
                                            
                                            $colors = [
                                                ['bg-emerald-100 text-emerald-800', 'bg-emerald-500'],
                                                ['bg-indigo-100 text-indigo-800', 'bg-indigo-500'],
                                                ['bg-amber-100 text-amber-800', 'bg-amber-500'],
                                                ['bg-rose-100 text-rose-800', 'bg-rose-500'],
                                                ['bg-sky-100 text-sky-800', 'bg-sky-500'],
                                                ['bg-violet-100 text-violet-800', 'bg-violet-500'],
                                                ['bg-teal-100 text-teal-800', 'bg-teal-500'],
                                            ];
                                            $colorIndex = (crc32($name) % count($colors));
                                            $selectedColor = $colors[$colorIndex];
                                        @endphp
                                        <div class="flex items-center gap-1.5">
                                            <div class="flex items-center justify-center w-7 h-7 rounded-xl font-black text-[10px] tracking-wider shadow-inner {{ $selectedColor[0] }} shrink-0">
                                                {{ $initials }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-gray-900 leading-tight truncate max-w-[110px]" title="{{ $order->customer_name }}">{{ $order->customer_name }}</div>
                                                <div class="text-[10px] text-gray-500 mt-0.5 flex items-center gap-0.5">
                                                    <svg class="w-2.5 h-2.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                    </svg>
                                                    <span class="truncate max-w-[95px]">{{ $order->customer_phone ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2.5">
                                        <div class="flex items-center gap-1.5">
                                            <div class="p-1 bg-gradient-to-br from-amber-50 to-orange-100 text-amber-600 rounded-lg border border-amber-200/50 shadow-xs shrink-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-gray-800 tracking-wide text-xs truncate max-w-[100px]" title="{{ $order->shoe_brand }}">{{ $order->shoe_brand }}</div>
                                                <div class="flex flex-wrap gap-1 mt-0.5">
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[9px] font-extrabold uppercase bg-slate-100 text-slate-700 border border-slate-200/50">
                                                        🎨 {{ $order->shoe_color }}
                                                    </span>
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[9px] font-extrabold uppercase bg-amber-50 text-amber-800 border border-amber-200/40">
                                                        📏 S-{{ $order->shoe_size }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2.5 whitespace-nowrap">
                                        @php
                                            $daysInQueue = $order->updated_at->diffInDays(\Carbon\Carbon::now());
                                            $slaClass = 'text-gray-600 bg-gray-50 border-gray-200/40';
                                            $slaIconClass = 'text-gray-400';
                                            $pulseDots = '';
                                            
                                            if ($daysInQueue >= 3) {
                                                $slaClass = 'text-rose-700 bg-rose-50 border-rose-200/40 font-extrabold ring-4 ring-rose-50 animate-pulse';
                                                $slaIconClass = 'text-rose-500';
                                                $pulseDots = 'bg-rose-500';
                                            } elseif ($daysInQueue >= 2) {
                                                $slaClass = 'text-amber-700 bg-amber-50 border-amber-200/40 font-bold';
                                                $slaIconClass = 'text-amber-500';
                                                $pulseDots = 'bg-amber-500';
                                            }
                                        @endphp
                                        <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-lg border text-[11px] font-semibold {{ $slaClass }}">
                                            @if($pulseDots)
                                                <span class="relative flex h-1.5 w-1.5">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $pulseDots }}"></span>
                                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 {{ $pulseDots }}"></span>
                                                </span>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 {{ $slaIconClass }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                            <span>{{ $order->updated_at->diffForHumans(null, true) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('assessment.create', $order->id) }}" class="inline-flex items-center justify-center px-2.5 py-1 bg-teal-600 hover:bg-teal-700 text-white text-[10px] font-black uppercase tracking-wider rounded-lg shadow-sm hover:shadow transition-all duration-150 group">
                                                <span>Mulai Cek</span>
                                                <svg class="w-3 h-3 ml-0.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('assessment.gallery-spk', $order->id) }}" class="p-1 text-slate-400 hover:text-teal-600 hover:bg-teal-50 border border-gray-200 hover:border-teal-200 rounded-lg transition-all duration-150" title="Kelola Foto SPK">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </a>
                                            
                                            <form action="{{ route('assessment.skip-dispatch', $order->id) }}" method="POST" class="confirm-skip-form inline-block">
                                                @csrf
                                                <button type="submit" class="p-1 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-200 rounded-lg transition-all duration-150" title="Selesaikan & Lanjut Flow SPK">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                    </svg>
                                                </button>
                                            </form>
 
                                            <form action="{{ route('assessment.destroy', $order->id) }}" method="POST" class="confirm-delete-form inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600 hover:bg-red-50 border border-gray-200 hover:border-red-200 rounded-lg transition-all duration-150" title="Hapus">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-2.132-1.859L4.764 7M16 17v-4m-4 4v-4m-4 4v-4m-6-6h14m2 0a2 2 0 002-2V7a2 2 0 00-2 2H3a2 2 0 00-2 2v.17c0 1.1.9 2 2 2h1M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center text-gray-500 bg-gray-50/30">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="p-4 bg-gray-100 rounded-full mb-3">
                                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <p class="font-medium text-gray-900">Tidak ada antrian</p>
                                            <p class="text-sm">Belum ada sepatu untuk di-assessment dengan filter terpilih.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if($queue->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $queue->links() }}
                </div>
                @else
                <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 flex justify-between items-center">
                    <span>* Segera proses sepatu yang baru masuk untuk menjaga SLA.</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- FLOATING BULK ACTIONS BAR (STANDAR BIG 4 TECH) -->
    <div id="bulk-actions-bar" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 bg-slate-900/90 text-white px-6 py-4 rounded-2xl shadow-2xl border border-slate-800 flex items-center gap-6 translate-y-20 opacity-0 pointer-events-none transition-all duration-300 backdrop-blur-xl">
        <div class="flex items-center gap-2.5">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-teal-500"></span>
            </span>
            <span id="selected-count" class="font-bold text-sm text-gray-100 whitespace-nowrap">0 SPK Terpilih</span>
        </div>
        <div class="h-6 w-px bg-slate-700"></div>
        <div class="flex items-center gap-3">
            <!-- Action 1: Print Bulk -->
            <button id="btn-print-bulk" class="inline-flex items-center justify-center px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 hover:border-slate-600 text-xs font-black uppercase tracking-wider rounded-lg shadow-sm transition-all transform hover:-translate-y-0.5 whitespace-nowrap">
                🖨️ Cetak Massal
            </button>
            
            <!-- Action 2: Process Bulk (Selesaikan & Lanjut Flow) -->
            <form id="form-bulk-process" action="{{ route('assessment.skip-dispatch-bulk') }}" method="POST" class="inline-block m-0">
                @csrf
                <input type="hidden" name="ids" id="bulk-ids-input" value="">
                <button type="submit" id="btn-process-bulk" class="inline-flex items-center justify-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-black uppercase tracking-wider rounded-lg shadow-md hover:shadow-xl transition-all transform hover:-translate-y-0.5 whitespace-nowrap">
                    ⚡ Selesaikan & Lanjut Flow
                </button>
            </form>
        </div>
    </div>

</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('print_spk_final_id'))
        Swal.fire({
            title: 'Assessment Selesai!',
            text: "SPK Final siap dicetak. Pastikan printer siap.",
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#1B8A68', // Signature emerald
            cancelButtonColor: '#6b7280',
            confirmButtonText: '🖨️ Cetak SPK Final',
            cancelButtonText: 'Tutup',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl font-bold px-4 py-2',
                cancelButton: 'rounded-xl font-bold px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Open Print Page in New Tab
                const url = "{{ route('assessment.print-spk', session('print_spk_final_id')) }}";
                window.open(url, '_blank');
            }
        });
    @endif
</script>

<script>
function toggleStatusFilter(status) {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    
    // Ambil status yang sedang aktif dari parameter array 'invoice_status[]'
    let activeStatuses = params.getAll('invoice_status[]');
    
    // Hapus paginasi page jika ada perubahan filter untuk menghindari offset error
    params.delete('page');

    if (status === 'all') {
        params.delete('invoice_status[]');
    } else {
        if (activeStatuses.includes(status)) {
            // Hapus status jika sudah terpilih (toggle off)
            activeStatuses = activeStatuses.filter(s => s !== status);
        } else {
            // Tambahkan status jika belum terpilih (toggle on)
            activeStatuses.push(status);
        }
        
        // Bersihkan dan bangun kembali parameter invoice_status[] di URL
        params.delete('invoice_status[]');
        activeStatuses.forEach(s => {
            params.append('invoice_status[]', s);
        });
    }
    
    // Arahkan ke URL yang telah dimutasi
    window.location.search = params.toString();
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all-spk');
    const spkCheckboxes = document.querySelectorAll('.spk-checkbox');
    const bulkActionsBar = document.getElementById('bulk-actions-bar');
    const selectedCountSpan = document.getElementById('selected-count');
    const btnPrintBulk = document.getElementById('btn-print-bulk');

    // Single Skip Confirmations via SweetAlert2
    document.querySelectorAll('.confirm-skip-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Selesaikan & Lanjut Flow?',
                text: "SPK ini akan dipindahkan statusnya berdasarkan status pembayaran invoice.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1B8A68', // Emerald signature
                cancelButtonColor: '#6b7280', // Slate-500
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl font-bold px-4 py-2',
                    cancelButton: 'rounded-xl font-bold px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Single Delete Confirmations via SweetAlert2
    document.querySelectorAll('.confirm-delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Antrian?',
                text: "Tindakan ini akan menghapus SPK dari antrian assessment!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Red-500
                cancelButtonColor: '#6b7280', // Slate-500
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl font-bold px-4 py-2',
                    cancelButton: 'rounded-xl font-bold px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    function updateSelectionState() {
        const checkedBoxes = document.querySelectorAll('.spk-checkbox:checked');
        const checkedCount = checkedBoxes.length;

        // Update count text
        selectedCountSpan.textContent = `${checkedCount} SPK Terpilih`;

        // Update Floating Actions Bar visibility
        if (checkedCount > 0) {
            bulkActionsBar.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
            bulkActionsBar.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
        } else {
            bulkActionsBar.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
            bulkActionsBar.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
        }

        // Update Master Checkbox state (including indeterminate)
        if (checkedCount === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCount === spkCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Individual checkbox listener
    spkCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectionState);
    });

    // Master checkbox listener
    selectAllCheckbox.addEventListener('change', function() {
        spkCheckboxes.forEach(cb => {
            cb.checked = selectAllCheckbox.checked;
        });
        updateSelectionState();
    });

    // Print Bulk action click
    btnPrintBulk.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.spk-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        const ids = Array.from(checkedBoxes).map(cb => cb.value).join(',');
        
        // Open consolidated print page in new tab
        const printUrl = `{{ route('assessment.print-bulk') }}?ids=${ids}`;
        window.open(printUrl, '_blank');

        // Uncheck all and reset UI state after initiating print
        selectAllCheckbox.checked = false;
        spkCheckboxes.forEach(cb => cb.checked = false);
        updateSelectionState();
    });

    // Bulk Process action submit
    const formBulkProcess = document.getElementById('form-bulk-process');
    const bulkIdsInput = document.getElementById('bulk-ids-input');

    formBulkProcess.addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.spk-checkbox:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            return;
        }

        e.preventDefault();
        
        Swal.fire({
            title: 'Proses Massal?',
            text: `Selesaikan assessment & lanjut alur kerja untuk ${checkedBoxes.length} SPK terpilih?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1B8A68', // Signature emerald
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Proses!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl font-bold px-4 py-2',
                cancelButton: 'rounded-xl font-bold px-4 py-2'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const ids = Array.from(checkedBoxes).map(cb => cb.value).join(',');
                bulkIdsInput.value = ids;
                formBulkProcess.submit();
            }
        });
    });
});
</script>
