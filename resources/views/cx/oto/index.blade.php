<x-app-layout>
    <!-- Content -->
    <div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-[#0f172a] pb-32 pt-12 border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 rounded-full bg-orange-500/10 text-orange-500 text-[10px] font-black uppercase tracking-widest border border-orange-500/20">
                            Revenue Hub
                        </span>
                    </div>
                    <h1 class="text-4xl font-black text-white tracking-tight italic uppercase">
                        OTO <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-pink-500">Command Center</span>
                    </h1>
                    <p class="mt-2 text-gray-400 text-sm font-medium">Optimalkan conversion rate dan tingkatkan revenue melalui penawaran strategis.</p>
                </div>
                
                <!-- Modern Stats Dashboard -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 w-full md:w-auto">
                    <div class="bg-white/[0.03] backdrop-blur-xl rounded-2xl p-4 border border-white/10 shadow-2xl">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-blue-500/20 rounded-lg text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Active Leads</div>
                        </div>
                        <div class="text-2xl font-black text-white">{{ $stats['active_total'] }} <span class="text-[10px] font-medium text-gray-500 italic">ORDERS</span></div>
                    </div>
                    
                    <div class="bg-white/[0.03] backdrop-blur-xl rounded-2xl p-4 border border-white/10 shadow-2xl">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-green-500/20 rounded-lg text-green-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Closing Rate</div>
                        </div>
                        <div class="text-2xl font-black text-green-400">{{ $stats['closing_rate'] }}<span class="text-sm font-black">%</span></div>
                    </div>

                    <div class="bg-white/[0.03] backdrop-blur-xl rounded-2xl p-4 border border-white/10 shadow-2xl">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-orange-500/20 rounded-lg text-orange-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                            <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Potential</div>
                        </div>
                        <div class="text-lg font-black text-white">Rp {{ number_format($stats['total_potential'] / 1000, 0) }}k</div>
                    </div>

                    <div class="bg-gradient-to-br from-green-600 to-emerald-700 rounded-2xl p-4 border border-white/20 shadow-2xl">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-white/20 rounded-lg text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="text-[9px] font-black text-white/70 uppercase tracking-widest">Revenue Achieved</div>
                        </div>
                        <div class="text-lg font-black text-white">Rp {{ number_format($stats['total_achieved'] / 1000, 0) }}k</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16">
        <!-- Navigation Tabs -->
        <div class="flex flex-wrap items-center gap-3 mb-10">
            <a href="{{ route('cx.oto.index', ['filter' => 'all']) }}" 
               class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all {{ $filter === 'all' ? 'bg-[#0f172a] text-white shadow-xl shadow-gray-300' : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-100' }}">
                Active Leads
            </a>
            <a href="{{ route('cx.oto.index', ['filter' => 'pending']) }}" 
               class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all {{ $filter === 'pending' ? 'bg-[#0f172a] text-white shadow-xl shadow-gray-300' : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-100' }}">
                New Leads
            </a>
            <a href="{{ route('cx.oto.index', ['filter' => 'contacted']) }}" 
               class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all {{ $filter === 'contacted' ? 'bg-[#0f172a] text-white shadow-xl shadow-gray-300' : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-100' }}">
                Follow Up
            </a>
            <a href="{{ route('cx.oto.index', ['filter' => 'accepted']) }}" 
               class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all {{ $filter === 'accepted' ? 'bg-green-600 text-white shadow-xl shadow-green-100' : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-100' }}">
                Success
            </a>
            <a href="{{ route('cx.oto.index', ['filter' => 'cancelled']) }}" 
               class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all {{ $filter === 'cancelled' ? 'bg-red-600 text-white shadow-xl shadow-red-100' : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-100' }}">
                Rejected
            </a>
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-lg p-4 mb-6 flex items-center justify-between">
            <div class="flex space-x-2">
                <a href="{{ route('cx.oto.index', ['filter' => 'all']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ $filter === 'all' ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:bg-gray-100' }}">
                   Semua
                </a>
                <a href="{{ route('cx.oto.index', ['filter' => 'urgent']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ $filter === 'urgent' ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:bg-gray-100' }}">
                   🔥 Urgent (< 3 hari)
                </a>
                <a href="{{ route('cx.oto.index', ['filter' => 'my']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ $filter === 'my' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">
                   👋 My OTO
                </a>
            </div>
            
            <form action="{{ route('cx.oto.index') }}" method="GET" class="relative">
                <input type="text" name="search" placeholder="Cari SPK / Customer..." 
                       class="pl-10 pr-4 py-2 border rounded-lg focus:ring-orange-500 focus:border-orange-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" class="feather feather-search" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
            </form>
        </div>

        <!-- OTO Collapsible Master-Detail Table -->
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200/70 overflow-hidden" x-data="{ expandedId: null }">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="py-4 px-4 w-12 text-center"></th>
                            <th class="py-4 px-5">SPK & Urgensi</th>
                            <th class="py-4 px-5">Pelanggan & Sepatu</th>
                            <th class="py-4 px-5">Layanan OTO</th>
                            <th class="py-4 px-5">Penawaran Harga</th>
                            <th class="py-4 px-4 text-center">Otomatisasi</th>
                            <th class="py-4 px-5 text-right">Detail & Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($otos as $oto)
                        @php 
                            $isUrgent = Carbon\Carbon::parse($oto->valid_until)->diffInDays(now()) < 3;
                            $rawStatus = is_string($oto->status) ? $oto->status : ($oto->status->value ?? '');
                            $normalizedStatus = strtoupper($oto->getRawOriginal('status') ?: $rawStatus);

                            $wo = $oto->workOrder;
                            $custName = $oto->customer_name ?: ($wo->customer_name ?? 'Pelanggan');
                            $shoeBrand = $wo->shoe_brand ?? '-';
                            $shoeColor = $wo->shoe_color ?? '';
                            $shoeSize = $wo->shoe_size ?? '';
                            $woStatus = $wo ? ($wo->status->value ?? $wo->status) : '';

                            // Clean phone number for WhatsApp
                            $rawPhone = $oto->customer_phone ?: ($wo->customer_phone ?? '');
                            $cleanPhone = preg_replace('/[^0-9]/', '', (string)$rawPhone);
                            if (str_starts_with($cleanPhone, '0')) {
                                $cleanPhone = '62' . substr($cleanPhone, 1);
                            }

                            // Friendly & High-Converting WhatsApp Message
                            $shoeText = $shoeBrand . ($shoeColor ? " ({$shoeColor})" : "");
                            $servicesText = $oto->proposed_services;
                            $normalPrice = $oto->total_normal_price;
                            $otoPrice = $oto->total_oto_price;
                            $discount = $oto->total_discount;
                            $validDate = Carbon\Carbon::parse($oto->valid_until)->translatedFormat('d M Y');
                            $trackUrl = url('/track?spk=' . ($oto->spk_number ?: ($wo->spk_number ?? '')));

                            $waMessage = "Halo Kak {$custName}, salam hangat dari Tim Workshop kami! 🙏\n\n";
                            $waMessage .= "Saat ini sepatu Anda *{$shoeText}* dengan No. SPK *#{$oto->spk_number}* sedang dalam pengerjaan.\n\n";
                            $waMessage .= "Teknisi ahli kami merekomendasikan penambahan layanan:\n";
                            $waMessage .= "✨ *{$servicesText}*\n";
                            if (!empty($oto->description)) {
                                $waMessage .= "📝 _Catatan Teknisi:_ \"{$oto->description}\"\n";
                            }
                            $waMessage .= "\nSpesial untuk Kakak, kami berikan penawaran eksklusif OTO:\n";
                            if (!empty($normalPrice)) {
                                $waMessage .= "💰 Harga Normal: ~{$normalPrice}~\n";
                            }
                            $waMessage .= "🔥 *Harga OTO Spesial: {$otoPrice}*";
                            if (!empty($discount)) {
                                $waMessage .= " (Hemat {$discount}!)\n";
                            } else {
                                $waMessage .= "\n";
                            }
                            $waMessage .= "⏳ Penawaran berlaku s.d: *{$validDate}*\n\n";
                            $waMessage .= "Kakak bisa cek foto & progres pengerjaan terkini di link berikut:\n";
                            $waMessage .= "👉 {$trackUrl}\n\n";
                            $waMessage .= "Apakah Kakak berminat mengambil promo OTO ini agar sepatu kembali optimal? Terima kasih banyak Kak! 😊";

                            $waUrl = "https://wa.me/{$cleanPhone}?text=" . rawurlencode($waMessage);
                        @endphp

                        {{-- Summary Row --}}
                        <tr class="transition-colors group"
                            :class="expandedId === {{ $oto->id }} ? 'bg-orange-50/40 border-l-4 border-l-orange-500 shadow-xs' : 'hover:bg-slate-50/80 cursor-pointer'">
                            
                            {{-- Col 1: Chevron Toggle --}}
                            <td class="py-4 px-4 text-center cursor-pointer select-none" @click="expandedId = (expandedId === {{ $oto->id }} ? null : {{ $oto->id }})">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center transition-all"
                                     :class="expandedId === {{ $oto->id }} ? 'bg-orange-500 text-white shadow-md shadow-orange-500/30 rotate-180' : 'bg-slate-100 text-slate-400 group-hover:bg-orange-100 group-hover:text-orange-600'">
                                    <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </td>

                            {{-- Col 2: SPK & Urgensi --}}
                            <td class="py-4 px-5 cursor-pointer" @click="expandedId = (expandedId === {{ $oto->id }} ? null : {{ $oto->id }})">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-900 text-white font-mono text-[10px] font-black tracking-wider shadow-xs">
                                        {{ $oto->spk_number }}
                                    </span>
                                    @if($oto->status === 'PENDING_CX')
                                        <span class="px-2 py-0.5 rounded-md bg-orange-100 text-orange-700 text-[9px] font-black uppercase tracking-wider">
                                            NEW LEAD
                                        </span>
                                    @elseif($oto->status === 'CONTACTED')
                                        <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 text-[9px] font-black uppercase tracking-wider">
                                            CONTACTED
                                        </span>
                                    @elseif($oto->status === 'ACCEPTED')
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-[9px] font-black uppercase tracking-wider">
                                            ACCEPTED
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[9px] font-black uppercase tracking-wider">
                                            {{ $oto->status }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    @php 
                                        $validUntil = Carbon\Carbon::parse($oto->valid_until);
                                        $isPast = $validUntil->isPast();
                                        $diffDays = (int) now()->diffInDays($validUntil, false);
                                    @endphp
                                    @if($isPast)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black text-rose-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> EXPIRED
                                        </span>
                                    @elseif($diffDays <= 3)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                                            🔥 Sisa {{ $diffDays }} hari
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-400">
                                            ⏳ Sisa {{ $diffDays }} hari
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Col 3: Pelanggan & Sepatu --}}
                            <td class="py-4 px-5 cursor-pointer" @click="expandedId = (expandedId === {{ $oto->id }} ? null : {{ $oto->id }})">
                                <div class="font-black text-slate-900 leading-snug group-hover:text-orange-600 transition-colors">
                                    {{ $custName }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mb-1 flex items-center gap-1">
                                    <span>📞</span> {{ $cleanPhone ?: '-' }}
                                </div>
                                <div class="flex flex-wrap items-center gap-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[9px] font-bold">
                                        👟 {{ $shoeBrand }}
                                    </span>
                                    @if(!empty($shoeColor))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-50 text-slate-500 text-[9px] font-medium border border-slate-200/60">
                                            🎨 {{ $shoeColor }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Col 4: Layanan OTO --}}
                            <td class="py-4 px-5 cursor-pointer" @click="expandedId = (expandedId === {{ $oto->id }} ? null : {{ $oto->id }})">
                                <div class="font-black text-slate-800 text-xs mb-1">
                                    {{ $oto->proposed_services }}
                                </div>
                                @if(!empty($oto->estimated_days))
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 text-[9px] font-bold border border-amber-200/60">
                                        ⏱️ +{{ $oto->estimated_days }} HK
                                    </span>
                                @endif
                            </td>

                            {{-- Col 5: Penawaran Harga --}}
                            <td class="py-4 px-5 cursor-pointer" @click="expandedId = (expandedId === {{ $oto->id }} ? null : {{ $oto->id }})">
                                @if(!empty($oto->total_normal_price))
                                    <div class="text-[10px] text-slate-400 font-bold line-through">
                                        {{ $oto->total_normal_price }}
                                    </div>
                                @endif
                                <div class="text-sm font-black text-slate-900 font-poppins">
                                    {{ $oto->total_oto_price }}
                                </div>
                                @if(!empty($oto->total_discount))
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[9px] font-black">
                                        ✨ Hemat {{ $oto->total_discount }}
                                    </span>
                                @endif
                            </td>

                            {{-- Col 6: Otomatisasi (CRM Sync) --}}
                            <td class="py-4 px-4 text-center cursor-pointer" @click="expandedId = (expandedId === {{ $oto->id }} ? null : {{ $oto->id }})">
                                @if($oto->send_automation)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[9px] font-black uppercase tracking-wider border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        TRUE
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-[9px] font-black uppercase tracking-wider border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        FALSE
                                    </span>
                                @endif
                            </td>

                            {{-- Col 7: Action Toggle Button --}}
                            <td class="py-4 px-5 text-right">
                                <button type="button" 
                                        @click.stop="expandedId = (expandedId === {{ $oto->id }} ? null : {{ $oto->id }})"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all cursor-pointer shadow-xs active:scale-95"
                                        :class="expandedId === {{ $oto->id }} ? 'bg-orange-500 text-white shadow-orange-500/20' : 'bg-slate-100 hover:bg-orange-50 text-slate-700 hover:text-orange-600 border border-slate-200/60'">
                                    <span x-text="expandedId === {{ $oto->id }} ? 'Tutup' : 'Detail & Aksi'"></span>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="expandedId === {{ $oto->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        {{-- Collapsible Drawer Row --}}
                        <tr x-show="expandedId === {{ $oto->id }}" 
                            x-cloak 
                            class="bg-gradient-to-b from-orange-50/30 via-slate-50/50 to-white border-b-2 border-orange-200/80 shadow-inner">
                            <td colspan="7" class="p-6 sm:p-8" x-data="{ openContact: false, openAccept: false, openReject: false }">
                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                                    
                                    {{-- Panel Kiri: Identitas Sepatu & Info Pengusul (Col 4) --}}
                                    <div class="lg:col-span-4 flex flex-col gap-4">
                                        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-xs">
                                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                                <span>👟</span> Detail Sepatu & Pengerjaan SPK
                                            </h4>
                                            <div class="space-y-2.5">
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-slate-500 font-medium">Merk Sepatu:</span>
                                                    <span class="font-black text-slate-900">{{ $shoeBrand }}</span>
                                                </div>
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-slate-500 font-medium">Warna:</span>
                                                    <span class="font-black text-slate-900">{{ $shoeColor ?: '-' }}</span>
                                                </div>
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-slate-500 font-medium">Ukuran:</span>
                                                    <span class="font-black text-slate-900">{{ $shoeSize ?: '-' }}</span>
                                                </div>
                                                <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-100">
                                                    <span class="text-slate-500 font-medium">Status Pengerjaan:</span>
                                                    <span class="px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-black uppercase tracking-wider border border-blue-200/60">
                                                        {{ $woStatus ?: 'PRODUKSI' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Info Pengusul Teknisi --}}
                                        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-xs">
                                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                                <span>👤</span> Rekomendasi Diajukan Oleh
                                            </h4>
                                            <div class="text-xs font-black text-slate-800">
                                                {{ $oto->creator->name ?? 'Teknisi Workshop' }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-bold mt-0.5">
                                                📅 {{ $oto->created_at ? $oto->created_at->translatedFormat('d M Y H:i') : '-' }}
                                            </div>
                                            @if(!empty($oto->estimated_days))
                                                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                                                    <span class="text-slate-500 font-medium">Tambahan Waktu:</span>
                                                    <span class="font-black text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/60">+{{ $oto->estimated_days }} Hari Kerja</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Panel Tengah: Catatan Teknisi & Riwayat CRM (Col 4) --}}
                                    <div class="lg:col-span-4 flex flex-col gap-4">
                                        {{-- Kotak Catatan / Alasan Teknisi --}}
                                        <div class="bg-amber-50/60 border border-amber-200/80 p-5 rounded-2xl">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="w-6 h-6 rounded-lg bg-amber-500 text-white flex items-center justify-center text-xs font-black">📝</span>
                                                <span class="text-[10px] font-black text-amber-800 uppercase tracking-widest">Catatan & Temuan Teknisi</span>
                                            </div>
                                            <p class="text-xs text-amber-950 font-bold leading-relaxed italic bg-white/70 p-3.5 rounded-xl border border-amber-200/50">
                                                "{{ !empty($oto->description) ? $oto->description : 'Tidak ada catatan spesifik dari teknisi.' }}"
                                            </p>
                                        </div>

                                        {{-- Riwayat Interaksi CX --}}
                                        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-xs flex-1">
                                            <div class="flex items-center justify-between mb-3">
                                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                                                    <span>💬</span> Riwayat Kontak Pelanggan
                                                </h4>
                                                <span class="text-[9px] font-black text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">
                                                    {{ $oto->contactLogs->count() }} Kali
                                                </span>
                                            </div>
                                            @if($oto->contactLogs->count() > 0)
                                                <div class="space-y-2 max-h-44 overflow-y-auto pr-1">
                                                    @foreach($oto->contactLogs->take(4) as $log)
                                                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                                            <div class="flex items-center justify-between mb-1">
                                                                <span class="font-black text-slate-800 text-[10px]">{{ $log->contactedBy->name ?? 'CS' }}</span>
                                                                <span class="text-[9px] text-slate-400">{{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</span>
                                                            </div>
                                                            <div class="flex items-center gap-1.5 mb-1">
                                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase {{ $log->contact_method === 'WHATSAPP' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                                                    {{ $log->contact_method }}
                                                                </span>
                                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-slate-200 text-slate-700">
                                                                    {{ $log->customer_response }}
                                                                </span>
                                                            </div>
                                                            @if(!empty($log->notes))
                                                                <p class="text-[11px] text-slate-600 font-medium italic">"{{ $log->notes }}"</p>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-6 text-slate-400">
                                                    <span class="text-2xl block mb-1">📭</span>
                                                    <p class="text-[11px] font-bold">Belum ada catatan interaksi.</p>
                                                    <p class="text-[9px] text-slate-400">Gunakan tombol Log Interaksi setelah menghubungi pelanggan.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Panel Kanan: Rincian Finansial & Control Action Hub (Col 4) --}}
                                    <div class="lg:col-span-4 flex flex-col justify-between gap-4">
                                        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-xs">
                                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                                <span>💰</span> Komparasi Biaya & Uang Muka
                                            </h4>
                                            <div class="space-y-2 mb-3">
                                                @if(!empty($oto->total_normal_price))
                                                    <div class="flex items-center justify-between text-xs">
                                                        <span class="text-slate-400 font-medium">Harga Normal:</span>
                                                        <span class="font-bold text-slate-400 line-through">{{ $oto->total_normal_price }}</span>
                                                    </div>
                                                @endif
                                                @if(!empty($oto->total_discount))
                                                    <div class="flex items-center justify-between text-xs">
                                                        <span class="text-emerald-600 font-bold">Potongan Diskon:</span>
                                                        <span class="font-black text-emerald-600">-{{ $oto->total_discount }} ({{ number_format($oto->discount_percent, 0) }}%)</span>
                                                    </div>
                                                @endif
                                                <div class="flex items-center justify-between text-sm pt-2 border-t border-slate-100">
                                                    <span class="text-slate-800 font-black">Harga Spesial OTO:</span>
                                                    <span class="text-base font-black text-slate-900 font-poppins">{{ $oto->total_oto_price }}</span>
                                                </div>
                                            </div>

                                            @if($oto->dp_required)
                                                <div class="p-2.5 rounded-xl {{ $oto->dp_paid ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }} text-xs font-bold flex items-center justify-between">
                                                    <span>Status DP: {{ $oto->dp_required }}</span>
                                                    <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded {{ $oto->dp_paid ? 'bg-emerald-200 text-emerald-900' : 'bg-amber-200 text-amber-900' }}">
                                                        {{ $oto->dp_paid ? 'LUNAS' : 'BELUM DIBAYAR' }}
                                                    </span>
                                                </div>
                                            @endif

                                            {{-- Toggle Otomatisasi --}}
                                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                                <div>
                                                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest">Otomatisasi CRM</span>
                                                    <span class="text-[10px] font-bold {{ $oto->send_automation ? 'text-emerald-600' : 'text-rose-600' }}">
                                                        {{ $oto->send_automation ? '● Aktif (Siap Kirim)' : '○ Non-Aktif' }}
                                                    </span>
                                                </div>
                                                <form action="{{ route('cx.oto.toggle-automation', $oto->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all">
                                                        Ubah
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- Full Action Suite Hub --}}
                                        <div class="flex flex-col gap-2">
                                            {{-- Chat WhatsApp Button --}}
                                            <a href="{{ $waUrl }}" target="_blank" 
                                               class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 px-4 rounded-2xl font-black text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/20 active:scale-95 transition-all text-center cursor-pointer">
                                                <svg class="w-4 h-4 fill-white shrink-0" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824z"/></svg>
                                                <span>Chat WhatsApp Langsung</span>
                                            </a>

                                            {{-- Log Interaksi Button --}}
                                            <button type="button" @click="openContact = true" class="w-full bg-slate-900 hover:bg-black text-white py-3 px-4 rounded-2xl font-black text-xs uppercase tracking-wider transition-all shadow-md shadow-slate-900/10 flex justify-center items-center gap-2 active:scale-95 cursor-pointer">
                                                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                <span>Log Interaksi Customer</span>
                                            </button>

                                            {{-- Decision Buttons: Accept & Reject --}}
                                            @if(!in_array($normalizedStatus, ['ACCEPTED', 'CANCELLED', 'REJECTED', 'EXPIRED']))
                                                <div class="grid grid-cols-2 gap-2 mt-1">
                                                    <button type="button" @click="openAccept = true" class="py-2.5 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-black text-[11px] uppercase tracking-wider flex items-center justify-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                        <span>Terima OTO</span>
                                                    </button>
                                                    <button type="button" @click="openReject = true" class="py-2.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-black text-[11px] uppercase tracking-wider flex items-center justify-center gap-1.5 transition-all active:scale-95 cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        <span>Tolak OTO</span>
                                                    </button>
                                                </div>
                                            @elseif($normalizedStatus === 'ACCEPTED')
                                                <div class="w-full py-2.5 px-4 rounded-xl bg-emerald-100 text-emerald-800 font-black text-xs uppercase tracking-wider text-center">
                                                    ✓ Penawaran Diterima Pelanggan
                                                </div>
                                            @else
                                                <div class="w-full py-2.5 px-4 rounded-xl bg-slate-100 text-slate-500 font-black text-xs uppercase tracking-wider text-center">
                                                    ✕ Penawaran Ditutup ({{ $normalizedStatus }})
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Modals Inside Container with x-teleport --}}
                                {{-- 1. Contact Modal --}}
                                <template x-teleport="body">
                                    <div x-show="openContact" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
                                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                            <div class="fixed inset-0 transition-opacity" @click="openContact = false">
                                                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                                            </div>
                                            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-10">
                                                <form action="{{ route('cx.oto.contact', $oto->id) }}" method="POST">
                                                    @csrf
                                                    <div class="p-8">
                                                        <div class="flex items-center gap-4 mb-8">
                                                            <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-500">
                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                            </div>
                                                            <div>
                                                                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Log Interaction</h3>
                                                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">SPK #{{ $oto->spk_number }} · {{ $custName }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="bg-slate-50 p-5 rounded-2xl mb-8 border border-slate-100 italic text-[11px] font-bold text-slate-600 leading-relaxed">
                                                            "Halo Kak {{ $custName }}, sepatu {{ $shoeText }} Anda sedang kami kerjakan! Ada penawaran khusus OTO {{ $oto->proposed_services }} promo hanya {{ $oto->total_oto_price }}. Berminat?"
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-4 mb-8">
                                                            <div>
                                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Method</label>
                                                                <select name="contact_method" class="w-full bg-slate-50 border-0 rounded-2xl py-4 px-5 text-xs font-black text-slate-700 focus:ring-4 focus:ring-orange-500/10 transition-all">
                                                                    <option value="WHATSAPP">WhatsApp</option>
                                                                    <option value="PHONE">Phone</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Response</label>
                                                                <select name="customer_response" class="w-full bg-slate-50 border-0 rounded-2xl py-4 px-5 text-xs font-black text-slate-700 focus:ring-4 focus:ring-orange-500/10 transition-all">
                                                                    <option value="INTERESTED">Interested</option>
                                                                    <option value="NEED_TIME">Thinking</option>
                                                                    <option value="NOT_INTERESTED">Declined</option>
                                                                    <option value="NO_ANSWER">No Answer</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Internal Notes</label>
                                                            <textarea name="notes" rows="3" class="w-full bg-slate-50 border-0 rounded-2xl py-4 px-5 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-orange-500/10 transition-all" placeholder="Catatan hasil komunikasi dengan customer..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="bg-slate-50 p-6 flex gap-4">
                                                        <button type="button" @click="openContact = false" class="flex-1 px-6 py-4 bg-white text-slate-400 border border-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-100 transition-all">Cancel</button>
                                                        <button type="submit" class="flex-1 px-6 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg">Save Log</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- 2. Accept Modal --}}
                                <template x-teleport="body">
                                    <div x-show="openAccept" class="fixed inset-0 z-[100] overflow-y-auto" style="display:none;" x-cloak>
                                        <div class="flex items-center justify-center min-h-screen px-4">
                                            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="openAccept = false"></div>
                                            <div class="relative bg-white rounded-[2.5rem] overflow-hidden shadow-2xl w-full max-w-lg border-4 border-emerald-50 z-10 p-10 text-center">
                                                <form action="{{ route('cx.oto.accept', $oto->id) }}" method="POST">
                                                    @csrf
                                                    <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                    <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-4">Konfirmasi Terima OTO</h3>
                                                    <p class="text-sm text-slate-500 font-bold leading-relaxed mb-8">Pelanggan setuju mengambil penawaran OTO untuk SPK #{{ $oto->spk_number }}? Status pesanan akan disesuaikan ke pengerjaan OTO workshop.</p>
                                                    <div class="flex gap-4">
                                                        <button type="button" @click="openAccept = false" class="flex-1 px-6 py-4 bg-slate-50 text-slate-400 rounded-2xl text-[10px] font-black uppercase tracking-widest">Kembali</button>
                                                        <button type="submit" class="flex-1 px-6 py-4 bg-emerald-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 shadow-lg shadow-emerald-100">Setuju &amp; Proses</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- 3. Reject Modal --}}
                                <template x-teleport="body">
                                    <div x-show="openReject" class="fixed inset-0 z-[100] overflow-y-auto" style="display:none;" x-cloak>
                                        <div class="flex items-center justify-center min-h-screen px-4">
                                            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="openReject = false"></div>
                                            <div class="relative bg-white rounded-[2.5rem] overflow-hidden shadow-2xl w-full max-w-lg border-4 border-rose-50 z-10 p-10">
                                                <form action="{{ route('cx.oto.reject', $oto->id) }}" method="POST">
                                                    @csrf
                                                    <h3 class="text-2xl font-black text-slate-900 uppercase mb-6">Tolak Penawaran OTO</h3>
                                                    <div class="space-y-6 mb-8 text-left">
                                                        <div>
                                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Alasan Penolakan</label>
                                                            <select name="rejection_reason" required class="w-full bg-slate-50 border-0 rounded-2xl py-4 px-5 text-sm font-black text-slate-700">
                                                                <option value="">Pilih Alasan...</option>
                                                                <option value="MAHAL">Kemahalan / Budget Kurang</option>
                                                                <option value="TIDAK_BUTUH">Pelanggan Merasa Tidak Perlu</option>
                                                                <option value="BURU_BURU">Tidak Ingin Tambah Waktu Pengerjaan</option>
                                                                <option value="LAINNYA">Alasan Lainnya</option>
                                                            </select>
                                                        </div>
                                                        <textarea name="rejection_notes" rows="3" class="w-full bg-slate-50 border-0 rounded-2xl py-4 px-5 text-sm font-bold text-slate-700" placeholder="Detail keterangan penolakan customer..."></textarea>
                                                    </div>
                                                    <div class="flex gap-4">
                                                        <button type="button" @click="openReject = false" class="flex-1 px-6 py-4 bg-slate-50 text-slate-400 rounded-2xl text-[10px] font-black uppercase tracking-widest">Batal</button>
                                                        <button type="submit" class="flex-1 px-6 py-4 bg-rose-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-rose-100">Simpan Penolakan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-20 bg-white">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Pool OTO Kosong</h3>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-2">Tidak ada penawaran aktif saat ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $otos->links() }}
        </div>
    </div>

    <!-- Styles for OTO Command Center -->
    <style>
        @keyframes pulse-subtle {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.01); border-color: rgba(239, 68, 68, 0.2); }
        }
        .animate-pulse-subtle {
            animation: pulse-subtle 3s infinite ease-in-out;
        }
        [x-cloak] { display: none !important; }
    </style>
</div>
</x-app-layout>
