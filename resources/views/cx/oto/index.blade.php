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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @forelse($otos as $oto)
            @php 
                $isUrgent = Carbon\Carbon::parse($oto->valid_until)->diffInDays(now()) < 3;
                $rawStatus = is_string($oto->status) ? $oto->status : ($oto->status->value ?? '');
                $normalizedStatus = strtoupper($oto->getRawOriginal('status') ?: $rawStatus);
            @endphp
            <div class="group relative bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 hover:border-orange-200 transition-all duration-500 overflow-hidden flex flex-col h-full">
                <div class="p-8 flex-1">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-4">
                                <span class="px-3 py-1 rounded-xl bg-slate-900 text-white text-[10px] font-black tracking-tighter shadow-lg">
                                    {{ $oto->workOrder->spk_number }}
                                </span>
                                @if($normalizedStatus === 'PENDING_CX')
                                    <span class="px-3 py-1 rounded-xl bg-orange-100 text-orange-600 text-[9px] font-black uppercase tracking-widest border border-orange-200">
                                        New Lead
                                    </span>
                                @elseif($normalizedStatus === 'PENDING_CUSTOMER')
                                    <span class="px-3 py-1 rounded-xl bg-purple-100 text-purple-600 text-[9px] font-black uppercase tracking-widest border border-purple-200">
                                        Awaiting Response
                                    </span>
                                @elseif($normalizedStatus === 'CONTACTED')
                                    <span class="px-3 py-1 rounded-xl bg-blue-100 text-blue-600 text-[9px] font-black uppercase tracking-widest border border-blue-200">
                                        Follow Up
                                    </span>
                                @endif
                                
                                @if($isUrgent && in_array($normalizedStatus, ['PENDING_CX', 'CONTACTED', 'PENDING_CUSTOMER']))
                                    <span class="px-3 py-1 rounded-xl bg-red-500 text-white text-[9px] font-black uppercase tracking-widest animate-pulse shadow-md shadow-red-100">
                                        Priority
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 leading-none mb-2">{{ $oto->workOrder->customer_name }}</h3>
                            <div class="flex items-center gap-2 text-slate-400 font-bold text-[11px] uppercase tracking-widest">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $oto->workOrder->customer_phone }}
                            </div>
                        </div>

                        <div class="text-right">
                            @if(in_array($normalizedStatus, ['PENDING_CX', 'CONTACTED']))
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl px-4 py-2 shadow-sm">
                                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Expires In</span>
                                    <span class="text-xs font-black {{ $isUrgent ? 'text-red-500 animate-pulse' : 'text-slate-800' }}">
                                        {{ Carbon\Carbon::parse($oto->valid_until)->diffForHumans(null, true) }}
                                    </span>
                                </div>
                            @else
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl px-4 py-2 shadow-sm">
                                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Last Activity</span>
                                    <span class="text-xs font-black text-slate-800">
                                        {{ ($oto->customer_responded_at ?: $oto->updated_at)->format('d M') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Offer Box -->
                    <div class="bg-slate-50 rounded-[2rem] p-6 mb-8 border border-slate-100 relative group/offer">
                        <div class="absolute -top-3 left-6 px-4 py-1 bg-white border border-slate-100 rounded-full shadow-sm">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                Exclusive Offer
                            </span>
                        </div>
                        <div class="pt-2">
                            <p class="text-sm font-bold text-slate-700 leading-relaxed mb-6">{{ $oto->proposed_services }}</p>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-200/60">
                                <div>
                                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Total OTO Value</span>
                                    <span class="text-2xl font-black text-slate-900 tracking-tighter">{{ $oto->total_oto_price }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-emerald-500 text-white text-[10px] font-black shadow-lg shadow-emerald-100">
                                        SAVE {{ $oto->total_discount }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        {{-- Hubungi Customer --}}
                        <div class="flex-[3]" x-data="{ openContact: false }">
                            <button @click="openContact = true" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-black transition-all shadow-xl shadow-slate-200 flex justify-center items-center gap-3 active:scale-95">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                Hubungi Customer
                            </button>

                            {{-- Contact Modal --}}
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
                                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">OTO CRM Pipeline</p>
                                                    </div>
                                                </div>
                                                <div class="bg-slate-50 p-5 rounded-2xl mb-8 border border-slate-100 italic text-[11px] font-bold text-slate-600 leading-relaxed">
                                                    "Halo Kak {{ $oto->workOrder->customer_name }}, sepatu {{ $oto->workOrder->shoe_brand }} Anda hampir selesai! Ada promo OTO {{ $oto->proposed_services }} cuma {{ $oto->total_oto_price }}. Minat?"
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
                                                    <textarea name="notes" rows="3" class="w-full bg-slate-50 border-0 rounded-2xl py-4 px-5 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-orange-500/10 transition-all" placeholder="Enter details..."></textarea>
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
                        </div>

                        {{-- Action Group (Accept/Reject) --}}
                        <div class="flex-1 flex gap-2">
                            @if(!in_array($normalizedStatus, ['ACCEPTED', 'CANCELLED', 'REJECTED', 'EXPIRED']))
                                <div class="flex-1" x-data="{ openAccept: false }">
                                    <button @click="openAccept = true" class="w-full h-full bg-emerald-500 text-white py-4 rounded-2xl hover:bg-emerald-600 transition-all shadow-xl shadow-emerald-100 flex justify-center items-center active:scale-95" title="Accept">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                    <div x-show="openAccept" class="fixed inset-0 z-[100] overflow-y-auto" style="display:none;" x-cloak>
                                        <div class="flex items-center justify-center min-h-screen px-4">
                                            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="openAccept = false"></div>
                                            <div class="relative bg-white rounded-[2.5rem] overflow-hidden shadow-2xl w-full max-w-lg border-4 border-emerald-50 z-10 p-10 text-center">
                                                <form action="{{ route('cx.oto.accept', $oto->id) }}" method="POST">
                                                    @csrf
                                                    <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                    <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-4">Confirm Acceptance</h3>
                                                    <p class="text-sm text-slate-500 font-bold leading-relaxed mb-8">Set order to <span class="text-emerald-600 uppercase">Express Priority</span>?</p>
                                                    <div class="flex gap-4">
                                                        <button type="button" @click="openAccept = false" class="flex-1 px-6 py-4 bg-slate-50 text-slate-400 rounded-2xl text-[10px] font-black uppercase tracking-widest">Back</button>
                                                        <button type="submit" class="flex-1 px-6 py-4 bg-emerald-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 shadow-lg shadow-emerald-100">Confirm</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1" x-data="{ openReject: false }">
                                    <button @click="openReject = true" class="w-full h-full bg-rose-50 text-rose-500 py-4 rounded-2xl border-2 border-rose-100 hover:bg-rose-100 transition-all flex justify-center items-center active:scale-95" title="Reject">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                    <div x-show="openReject" class="fixed inset-0 z-[100] overflow-y-auto" style="display:none;" x-cloak>
                                        <div class="flex items-center justify-center min-h-screen px-4">
                                            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="openReject = false"></div>
                                            <div class="relative bg-white rounded-[2.5rem] overflow-hidden shadow-2xl w-full max-w-lg border-4 border-rose-50 z-10 p-10">
                                                <form action="{{ route('cx.oto.cancel', $oto->id) }}" method="POST">
                                                    @csrf
                                                    <h3 class="text-2xl font-black text-slate-900 uppercase mb-6">Reject Offer</h3>
                                                    <div class="space-y-6 mb-8 text-left">
                                                        <div>
                                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Reason</label>
                                                            <select name="rejection_reason" required class="w-full bg-slate-50 border-0 rounded-2xl py-4 px-5 text-sm font-black text-slate-700">
                                                                <option value="">Select Reason...</option>
                                                                <option value="MAHAL">Expensive</option>
                                                                <option value="TIDAK_BUTUH">Not Needed</option>
                                                                <option value="LAINNYA">Other</option>
                                                            </select>
                                                        </div>
                                                        <textarea name="rejection_notes" rows="3" class="w-full bg-slate-50 border-0 rounded-2xl py-4 px-5 text-sm font-bold text-slate-700" placeholder="Details..."></textarea>
                                                    </div>
                                                    <div class="flex gap-4">
                                                        <button type="button" @click="openReject = false" class="flex-1 px-6 py-4 bg-slate-50 text-slate-400 rounded-2xl text-[10px] font-black uppercase tracking-widest">Back</button>
                                                        <button type="submit" class="flex-1 px-6 py-4 bg-rose-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-rose-100">Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($normalizedStatus === 'ACCEPTED')
                                <div class="flex-1 bg-emerald-500 text-white rounded-2xl flex items-center justify-center">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            @else
                                <div class="flex-1 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Status Banner --}}
                @if($normalizedStatus === 'ACCEPTED')
                    <div class="bg-emerald-600 py-3 px-8 flex items-center justify-between text-white text-[10px] font-black uppercase tracking-widest">
                        <span>Offer Converted Successfully</span>
                        <span class="opacity-50">PRODUCTION SYNCED</span>
                    </div>
                @elseif(in_array($normalizedStatus, ['CANCELLED', 'REJECTED', 'EXPIRED']))
                    <div class="bg-rose-600 py-3 px-8 flex items-center justify-between text-white text-[10px] font-black uppercase tracking-widest">
                        <span>Offer Closed: {{ $normalizedStatus }}</span>
                        <span class="opacity-50">{{ $oto->rejection_reason ?: 'N/A' }}</span>
                    </div>
                @endif
                {{-- Interactions History Feed --}}
                @if($oto->contactLogs->count() > 0)
                <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between group-hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex -space-x-2">
                            @foreach($oto->contactLogs->take(3) as $log)
                                <div class="w-7 h-7 rounded-full bg-white border-2 border-slate-100 flex items-center justify-center text-[9px] font-black text-slate-500 uppercase shadow-sm" title="{{ $log->contactedBy->name }}">
                                    {{ substr($log->contactedBy->name, 0, 1) }}
                                </div>
                            @endforeach
                        </div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                            {{ $oto->contactLogs->count() }} Interactions
                        </span>
                    </div>
                    <div class="text-[9px] font-black text-slate-300 uppercase">
                        {{ $oto->contactLogs->first()->created_at->diffForHumans() }}
                    </div>
                </div>
                @endif
            </div>

            @empty
            <div class="col-span-2 text-center py-20 bg-white rounded-[2.5rem] shadow-xl border border-dashed border-slate-200">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Pool OTO Kosong</h3>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-2">Tidak ada penawaran aktif saat ini.</p>
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
