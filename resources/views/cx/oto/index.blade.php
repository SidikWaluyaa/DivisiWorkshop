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

        <!-- OTO List -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($otos as $oto)
            @php 
                $isUrgent = Carbon\Carbon::parse($oto->valid_until)->diffInDays(now()) < 3;
                $rawStatus = is_string($oto->status) ? $oto->status : ($oto->status->value ?? '');
                $normalizedStatus = strtoupper($oto->getRawOriginal('status') ?: $rawStatus);
            @endphp
            <div class="group relative bg-white rounded-3xl shadow-xl border-2 {{ $isUrgent ? 'border-red-100 animate-pulse-subtle' : 'border-gray-50' }} hover:border-orange-200 transition-all duration-300 overflow-hidden">
                <div class="p-8">
                    <!-- Header -->
                    <div class="flex flex-col lg:flex-row justify-between items-start gap-4 mb-6">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <span class="font-mono text-[11px] font-black px-3 py-1 rounded-lg bg-gray-900 text-white shadow-lg">
                                    {{ $oto->workOrder->spk_number }}
                                </span>
                                @if($normalizedStatus === 'PENDING_CX')
                                    <span class="px-3 py-1 rounded-lg bg-yellow-400 text-gray-900 text-[10px] font-black uppercase tracking-widest shadow-sm">
                                        New Lead
                                    </span>
                                @elseif($normalizedStatus === 'PENDING_CUSTOMER')
                                    <span class="px-3 py-1 rounded-lg bg-purple-500 text-white text-[10px] font-black uppercase tracking-widest shadow-sm">
                                        Menunggu Customer
                                    </span>
                                @elseif($normalizedStatus === 'CONTACTED')
                                    <span class="px-3 py-1 rounded-lg bg-blue-500 text-white text-[10px] font-black uppercase tracking-widest shadow-sm">
                                        Follow Up
                                    </span>
                                @endif
                                
                                @if($isUrgent && in_array($normalizedStatus, ['PENDING_CX', 'CONTACTED', 'PENDING_CUSTOMER']))
                                    <span class="px-3 py-1 rounded-lg bg-red-600 text-white text-[10px] font-black uppercase tracking-widest animate-pulse">
                                        Hot Lead
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-xl font-black text-gray-900 mb-1 group-hover:text-orange-600 transition-colors">{{ $oto->workOrder->customer_name }}</h3>
                            <div class="flex items-center gap-2 text-xs text-gray-500 font-bold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $oto->workOrder->customer_phone }}
                            </div>
                        </div>
                        
                        @if(in_array($normalizedStatus, ['PENDING_CX', 'CONTACTED']))
                        <div class="text-right bg-gray-50 p-3 rounded-2xl border border-gray-100 min-w-[120px]">
                            <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Expires In</div>
                            <div class="text-sm font-black {{ $isUrgent ? 'text-red-600 animate-pulse' : 'text-gray-800' }}">
                                {{ Carbon\Carbon::parse($oto->valid_until)->diffForHumans() }}
                            </div>
                            <div class="text-[10px] text-gray-400 font-bold">{{ Carbon\Carbon::parse($oto->valid_until)->format('d M Y') }}</div>
                        </div>
                        @else
                        <div class="text-right bg-gray-50 p-3 rounded-2xl border border-gray-100 min-w-[120px]">
                            <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Status Date</div>
                            <div class="text-sm font-black text-gray-800">
                                {{ $oto->customer_responded_at ? $oto->customer_responded_at->format('d M Y') : $oto->updated_at->format('d M Y') }}
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Offer Details -->
                    <div class="bg-gradient-to-br from-orange-50 to-pink-50 rounded-3xl p-6 mb-6 border border-orange-100 shadow-inner">
                        <div class="text-[10px] font-black text-orange-800 mb-3 uppercase tracking-[0.2em] flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-orange-500 animate-ping"></span>
                            PROPOSED OFFER
                        </div>
                        <div class="space-y-4">
                            <div class="text-sm font-black text-gray-800 leading-relaxed">
                                {{ $oto->proposed_services }}
                            </div>
                            <div class="pt-4 border-t border-orange-200/50 flex justify-between items-end">
                                <div>
                                    <div class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-1">Price After Discount</div>
                                    <div class="font-black text-orange-700 text-2xl tracking-tighter">{{ $oto->total_oto_price }}</div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-black bg-green-500 text-white px-3 py-1 rounded-full shadow-lg">
                                        SAVE {{ $oto->total_discount }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-3" x-data="{ openContact: false }">
                        <button @click="openContact = true" 
                            class="flex-[2] bg-[#0f172a] text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all shadow-xl hover:shadow-gray-200 flex justify-center items-center gap-3 active:scale-95">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            Hubungi Customer
                        </button>

                        <!-- Refined Enterprise Contact Modal -->
                        <div x-show="openContact" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openContact = false">
                                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                                </div>
                                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                    <form action="{{ route('cx.oto.contact', $oto->id) }}" method="POST">
                                        @csrf
                                        <div class="bg-white p-6">
                                            <div class="flex items-center gap-3 mb-6">
                                                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600 shadow-sm">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Log Kontak Customer</h3>
                                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">OTO Follow-up CRM</p>
                                                </div>
                                            </div>
                                            
                                            <!-- Compact Script Box -->
                                            <div class="bg-slate-50 p-4 rounded-xl mb-6 border border-slate-100 relative group">
                                                <button type="button" class="absolute top-3 right-3 text-slate-300 hover:text-orange-500" onclick="navigator.clipboard.writeText(this.parentElement.querySelector('p').innerText)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                                </button>
                                                <p class="text-[11px] text-slate-600 font-bold italic leading-relaxed">"Halo Kak {{ $oto->workOrder->customer_name }}, sepatu {{ $oto->workOrder->shoe_brand }} Anda hampir selesai! Ada promo OTO {{ $oto->proposed_services }} cuma {{ $oto->total_oto_price }}. Minat?"</p>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4 mb-6">
                                                <div class="col-span-1">
                                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.1em] mb-2">Metode</label>
                                                    <select name="contact_method" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500/20">
                                                        <option value="WHATSAPP">WhatsApp</option>
                                                        <option value="PHONE">Phone</option>
                                                    </select>
                                                </div>
                                                <div class="col-span-1">
                                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.1em] mb-2">Respon</label>
                                                    <select name="customer_response" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500/20">
                                                        <option value="INTERESTED">Tertarik</option>
                                                        <option value="NEED_TIME">Mikir dulu</option>
                                                        <option value="NOT_INTERESTED">Tolak</option>
                                                        <option value="NO_ANSWER">DNR</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.1em] mb-2">Catatan</label>
                                                <textarea name="notes" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-orange-500/20" placeholder="Hasil pembicaraan..."></textarea>
                                            </div>
                                        </div>
                                        <div class="bg-slate-50 p-6 flex gap-3">
                                            <button type="button" @click="openContact = false" class="flex-1 px-4 py-3 bg-white text-slate-400 border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition-all">Batal</button>
                                            <button type="submit" class="flex-1 px-4 py-3 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg">Simpan Log</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-1 gap-2">
                            <!-- Direct Actions -->
                            <div class="flex-1" x-data="{ openAccept: false }">
                                <button @click="openAccept = true" class="w-full h-full bg-green-500 text-white py-4 rounded-2xl hover:bg-green-600 transition-all shadow-lg flex justify-center items-center active:scale-95" title="Customer Accept">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                                
                                 <!-- Accept Modal -->
                                 <div x-show="openAccept" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openAccept = false">
                                            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm opacity-75"></div>
                                        </div>
                                        <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-4 border-green-50">
                                            <form action="{{ route('cx.oto.accept', $oto->id) }}" method="POST">
                                                @csrf
                                                <div class="bg-white p-8">
                                                    <div class="sm:flex sm:items-start">
                                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-2xl bg-green-100 sm:mx-0">
                                                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        </div>
                                                        <div class="mt-4 text-center sm:mt-0 sm:ml-6 sm:text-left">
                                                            <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Konfirmasi Terima OTO</h3>
                                                            <div class="mt-3">
                                                                <p class="text-sm text-gray-500 font-medium leading-relaxed">
                                                                    Apakah Anda yakin customer menyetujui penawaran ini? Order akan otomatis ditambahkan layanan dan masuk ke antrian <strong class="text-green-600">PRIORITAS (Express)</strong>.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-gray-50 p-6 sm:px-8 flex flex-col sm:flex-row-reverse gap-3">

                        <!-- Action Buttons / Result State -->
                        @if($normalizedStatus === 'ACCEPTED')
                            <div class="bg-green-600 border border-green-500 rounded-2xl p-5 flex items-center justify-between flex-1 shadow-lg shadow-green-100">
                                <div>
                                    <span class="text-[10px] font-black text-white/80 uppercase tracking-widest block mb-1">Conversion Success</span>
                                    <p class="text-sm font-black text-white">Jasa Telah Masuk Produksi</p>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                        @elseif(in_array($normalizedStatus, ['CANCELLED', 'REJECTED', 'EXPIRED']))
                            <div class="bg-red-500/10 border border-red-500/20 rounded-2xl p-4 flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-black text-red-600 uppercase tracking-widest">Offer Terminated</span>
                                    <span class="text-[9px] font-black bg-red-100 text-red-600 px-2 py-0.5 rounded-lg uppercase">{{ $oto->rejection_reason ?: $normalizedStatus }}</span>
                                </div>
                                <p class="text-xs font-bold text-red-700 italic">"{{ $oto->rejection_notes ?: 'Tidak ada catatan tambahan' }}"</p>
                            </div>
                        @else
                            <div class="flex flex-1 gap-3">
                                <button @click="openContact = true" class="flex-[3] bg-[#0f172a] text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-black transition-all flex items-center justify-center gap-3 active:scale-95">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    Hubungi Customer
                                </button>

                                <form action="{{ route('cx.oto.accept', $oto->id) }}" method="POST" onsubmit="return confirm('Konfirmasi: Customer SETUJU dengan OTO ini?')" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full h-full bg-green-500 text-white py-4 rounded-2xl hover:bg-green-600 transition-all shadow-lg shadow-green-100 flex justify-center items-center active:scale-95" title="Accept OTO">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </form>

                                <div class="flex-1" x-data="{ openReject: false }">
                                    <button @click="openReject = true" class="w-full h-full bg-red-50 text-red-500 py-4 rounded-2xl hover:bg-red-100 transition-all border-2 border-red-100 flex justify-center items-center active:scale-95" title="Cancel OTO">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>

                                    <!-- Rejection Reason Modal -->
                                    <div x-show="openReject" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
                                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openReject = false">
                                                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm opacity-75"></div>
                                            </div>
                                            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                <form action="{{ route('cx.oto.cancel', $oto->id) }}" method="POST">
                                                    @csrf
                                                    <div class="bg-white p-8">
                                                        <div class="flex items-center gap-4 mb-6">
                                                            <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center text-red-600">
                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            </div>
                                                            <div>
                                                                <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Alasan Penolakan</h3>
                                                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">OTO Rejection Tracking</p>
                                                            </div>
                                                        </div>

                                                        <div class="space-y-6">
                                                            <div>
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Kenapa Customer Menolak?</label>
                                                                <select name="rejection_reason" required class="w-full bg-gray-50 border-0 rounded-2xl py-4 px-5 text-sm font-black text-gray-700 focus:ring-4 focus:ring-red-500/10 transition-all">
                                                                    <option value="">Pilih Alasan Utama...</option>
                                                                    <option value="MAHAL">Terlalu Mahal (Harga Tidak Cocok)</option>
                                                                    <option value="TIDAK_BUTUH">Tidak Merasa Butuh (Hanya Ingin Jasa Awal)</option>
                                                                    <option value="PIKIR_PIKIR">Butuh Waktu / Masih Pikir-pikir</option>
                                                                    <option value="KUALITAS">Ragu dengan Kualitas/Garansi</option>
                                                                    <option value="LAINNYA">Alasan Lainnya</option>
                                                                </select>
                                                            </div>

                                                            <div>
                                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Catatan Tambahan (Opsional)</label>
                                                                <textarea name="rejection_notes" rows="3" class="w-full bg-gray-50 border-0 rounded-2xl py-4 px-5 text-sm font-bold text-gray-700 focus:ring-4 focus:ring-red-500/10 transition-all" placeholder="Tulis detail alasan jika ada..."></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="bg-gray-50 p-6 sm:px-8 flex flex-col sm:flex-row-reverse gap-3">
                                                        <button type="submit" class="flex-1 px-6 py-4 bg-red-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-red-100 hover:bg-red-700 transition-all active:scale-95">
                                                            Konfirmasi Pembatalan
                                                        </button>
                                                        <button type="button" @click="openReject = false" class="flex-1 px-6 py-4 bg-white text-gray-500 border-2 border-gray-100 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gray-50 transition-all">
                                                            Kembali
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- History -->
                @if($oto->contactLogs->count() > 0)
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                    <div class="text-xs font-bold text-gray-500 mb-2 uppercase">Riwayat Kontak</div>
                    <div class="space-y-3">
                        @foreach($oto->contactLogs->take(2) as $log)
                        <div class="flex text-xs">
                            <div class="w-20 text-gray-400">{{ $log->created_at->format('d/m H:i') }}</div>
                            <div class="flex-1">
                                <span class="font-medium text-gray-700">{{ $log->contactedBy->name }}</span>
                                <span class="text-gray-500">: {{ Str::limit($log->notes, 40) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div class="col-span-2 text-center py-20 bg-white rounded-xl shadow-sm border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Kolam OTO Kosong</h3>
                <p class="mt-1 text-sm text-gray-500">Tidak ada penawaran OTO yang perlu ditangani saat ini.</p>
            </div>
            @endforelse
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
