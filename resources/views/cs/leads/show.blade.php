<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xs font-black text-white/70 uppercase tracking-[0.3em]">Lead Profile Detail</h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Premium Header Relocation --}}
            <div class="mb-8">
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100 overflow-hidden relative group transition-all duration-500 hover:shadow-gray-300/50">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[#22AF85]/5 to-transparent rounded-full -mr-20 -mt-20 blur-3xl opacity-50 transition-opacity group-hover:opacity-100"></div>
                    
                    <div class="p-8 md:p-10 relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                        <div class="flex items-center gap-8">
                            {{-- Avatar / Icon --}}
                            <div class="relative flex-shrink-0">
                                <div class="absolute -inset-1 bg-gradient-to-r from-[#22AF85] to-[#FFC232] rounded-3xl blur opacity-25 group-hover:opacity-60 transition duration-1000 group-hover:duration-200"></div>
                                <div class="relative w-20 h-20 bg-white rounded-3xl flex items-center justify-center text-[#22AF85] shadow-xl border border-gray-100 overflow-hidden transform transition-all duration-500 group-hover:scale-105 group-hover:rotate-3">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </div>
                            </div>

                            {{-- Lead Info --}}
                            <div class="space-y-3">
                                <div class="flex items-center gap-4 flex-wrap">
                                    <h2 class="font-black text-4xl md:text-5xl text-gray-900 leading-tight tracking-tighter uppercase mb-0">
                                        {{ $lead->customer_name ?? 'Guest' }}
                                    </h2>
                                    <div class="px-4 py-1.5 bg-gray-50 rounded-xl border-2 border-gray-100 text-[11px] font-black text-gray-400 tracking-widest uppercase shadow-sm">
                                        ID: #{{ str_pad($lead->id, 5, '0', STR_PAD_LEFT) }}
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4 flex-wrap">
                                    {{-- Status Badge --}}
                                    <div class="relative group/badge">
                                        <div class="absolute -inset-0.5 bg-[#22AF85] rounded-xl blur opacity-20 group-hover/badge:opacity-40 transition"></div>
                                        <span class="relative px-6 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest bg-[#22AF85] text-white flex items-center gap-2 shadow-lg shadow-green-100 transition-all">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                            {{ $lead->status }}
                                        </span>
                                    </div>

                                    {{-- Priority Badge --}}
                                    <div class="relative group/badge">
                                        <div class="absolute -inset-0.5 bg-[#FFC232] rounded-xl blur opacity-20 group-hover/badge:opacity-40 transition"></div>
                                        <span class="relative px-6 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest border-2 border-[#FFC232]/30 text-gray-900 bg-[#FFC232]/10 flex items-center gap-2 transition-all group-hover/badge:bg-[#FFC232]/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#FFC232]"></span>
                                            {{ $lead->priority }}
                                        </span>
                                    </div>

                                    <div class="hidden md:block w-1.5 h-1.5 rounded-full bg-gray-200"></div>
                                    
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        Inscribed: {{ $lead->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Area --}}
                        <div class="flex items-center gap-4 w-full md:w-auto mt-4 md:mt-0 pt-6 md:pt-0 border-t md:border-0 border-gray-100">
                            <a href="{{ route('cs.dashboard') }}" 
                               class="flex-1 md:flex-initial group relative flex items-center justify-center gap-3 px-10 py-4 bg-gray-50 hover:bg-white text-gray-900 border-2 border-gray-100 hover:border-[#22AF85]/20 rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-sm hover:shadow-2xl hover:shadow-[#22AF85]/10 hover:-translate-y-1 overflow-hidden">
                                <svg class="w-5 h-5 text-[#22AF85] group-hover:-translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                                <span>Back to Base</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Left Column: Customer Info & Actions --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- Customer Information --}}
                    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100">
                        <div class="p-8 pb-4 flex justify-between items-center bg-gray-50/50">
                            <div>
                                <h3 class="font-black text-gray-900 uppercase tracking-tighter text-2xl">Profil Customer</h3>
                                <div class="w-16 h-2 bg-[#22AF85] rounded-full mt-2.5"></div>
                            </div>
                            <button onclick="openEditModal()" class="bg-white hover:bg-[#22AF85] hover:text-white text-[#22AF85] border-2 border-[#22AF85]/10 p-3 rounded-2xl transition shadow-sm group" title="Edit Profil">
                                <svg class="w-6 h-6 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                        </div>
                        <div class="p-8 space-y-5">
                            <div class="group">
                                <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Nama Lengkap</label>
                                <p class="text-gray-900 font-bold text-lg leading-tight">{{ $lead->customer_name ?? '-' }}</p>
                            </div>
                            <div class="group">
                                <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Kontak Telepon</label>
                                <div class="flex items-center gap-3">
                                    <p class="text-gray-900 font-bold text-lg leading-tight">{{ $lead->customer_phone }}</p>
                                    <a href="{{ $lead->wa_greeting_link }}" target="_blank" class="text-[#22AF85] hover:bg-[#22AF85] hover:text-white font-black text-[10px] uppercase tracking-widest flex items-center gap-2 border-2 border-[#22AF85]/20 bg-[#22AF85]/5 px-4 py-1.5 rounded-xl transition">
                                        WhatsApp
                                    </a>
                                </div>
                            </div>
                            <div class="group">
                                <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Email</label>
                                <p class="text-gray-900 font-semibold">{{ $lead->customer_email ?? '-' }}</p>
                            </div>
                            <div class="group">
                                <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Alamat Pengiriman</label>
                                <p class="text-gray-900 font-semibold leading-relaxed">{{ $lead->customer_address ?? '-' }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="group">
                                    <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Kota</label>
                                    <p class="text-gray-900 font-bold">{{ $lead->customer_city ?? '-' }}</p>
                                </div>
                                <div class="group">
                                    <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Provinsi</label>
                                    <p class="text-gray-900 font-bold">{{ $lead->customer_province ?? '-' }}</p>
                                </div>
                            </div>
                            <hr class="border-gray-100 mt-6 mb-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="group">
                                    <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Sumber Lead</label>
                                    <p class="text-gray-900 font-bold">📱 {{ $lead->source }}</p>
                                </div>
                                <div class="group">
                                    <label class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1 block">Tipe Channel</label>
                                    <span class="inline-block px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $lead->channel === 'ONLINE' ? 'bg-[#22AF85]/10 text-[#22AF85]' : 'bg-[#FFC232]/10 text-[#FFC232]' }}">
                                        {{ $lead->channel }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100">
                        <div class="p-8 pb-4 bg-gray-50/50">
                            <h3 class="font-black text-gray-900 uppercase tracking-tighter text-2xl">Quick Actions</h3>
                            <div class="w-16 h-2 bg-[#FFC232] rounded-full mt-2.5"></div>
                        </div>
                        <div class="p-8 space-y-4">
                            @if($lead->status === 'GREETING')
                                <button onclick="moveToKonsultasi()" class="w-full bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 py-4 rounded-[1.25rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-yellow-100 transition transform hover:-translate-y-1">
                                    → Pindah ke Konsultasi
                                </button>
                            @endif

                            @if($lead->status === 'KONSULTASI')
                                <button onclick="openQuotationModal()" class="w-full bg-[#22AF85] hover:bg-[#22AF85]/90 text-white py-4 rounded-[1.25rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-green-100 transition transform hover:-translate-y-1">
                                    ➕ Buat Quotation
                                </button>
                                @if($lead->canMoveToClosing())
                                    <button onclick="moveToClosing()" class="w-full bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 py-4 rounded-[1.25rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-yellow-100 transition transform hover:-translate-y-1">
                                        → Pindah ke Closing
                                    </button>
                                @endif
                            @endif

                            @if($lead->status === 'CLOSING')
                                @if(!$lead->spk)
                                    <button onclick="openSpkModal()" class="w-full bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 py-4 rounded-[1.25rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-yellow-100 transition transform hover:-translate-y-1">
                                        📄 Generate SPK
                                    </button>
                                @elseif($lead->spk->canBeHandedToWorkshop())
                                    <button onclick="openHandoverModal()" class="w-full bg-[#22AF85] hover:bg-[#22AF85]/90 text-white py-4 rounded-[1.25rem] font-black text-xs uppercase tracking-widest shadow-lg shadow-green-100 transition transform hover:-translate-y-1">
                                        ✅ Serahkan ke Workshop
                                    </button>
                                @endif
                            @endif

                            <div class="grid grid-cols-2 gap-3">
                                <button onclick="openActivityModal()" class="bg-gray-50 border-2 border-gray-100 hover:bg-[#22AF85]/5 hover:border-[#22AF85]/20 text-gray-900 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition">
                                    📝 Log Aktivitas
                                </button>

                                <button onclick="openFollowUpModal()" class="bg-gray-50 border-2 border-gray-100 hover:bg-[#FFC232]/5 hover:border-[#FFC232]/20 text-gray-900 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition">
                                    ⏰ Set Follow Up
                                </button>
                            </div>

                            <button onclick="markLost()" class="w-full border-2 border-red-50 text-red-400 hover:bg-red-50 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition">
                                ❌ Mark as LOST
                            </button>
                        </div>
                    </div>

                </div>

                {{-- Right Column: Timeline & Details --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Quotations Section --}}
                    @if(in_array($lead->status, ['KONSULTASI', 'CLOSING', 'CONVERTED']))
                        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100">
                            <div class="p-8 pb-4 flex justify-between items-center bg-gray-50/50">
                                <div>
                                    <h3 class="font-black text-gray-900 uppercase tracking-tighter text-2xl">Quotations History</h3>
                                    <div class="w-16 h-2 bg-[#FFC232] rounded-full mt-2.5"></div>
                                </div>
                            </div>
                            <div class="p-8">
                                @forelse($lead->quotations as $quotation)
                                    <div class="border-2 rounded-[2rem] p-6 mb-6 {{ $quotation->status === 'ACCEPTED' ? 'border-[#22AF85]/20 bg-[#22AF85]/5' : 'border-gray-50 bg-white shadow-sm' }}">
                                        <div class="flex justify-between items-start mb-6">
                                            <div>
                                                <h4 class="font-black text-gray-900 text-lg uppercase tracking-widest">{{ $quotation->quotation_number }}</h4>
                                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-1">Versi {{ $quotation->version }} • {{ $quotation->created_at->format('d M Y') }}</p>
                                            </div>
                                            <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest leading-none {{ $quotation->status === 'ACCEPTED' ? 'bg-[#22AF85] text-white' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $quotation->status }}
                                            </span>
                                        </div>
                                        
                                        {{-- Items --}}
                                        <div class="bg-white/50 border border-gray-100/50 rounded-2xl p-4 mb-4">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#FFC232]"></span>
                                                Data Barang ({{ count($quotation->quotationItems ?? []) }} unit)
                                            </p>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                @foreach($quotation->quotationItems as $item)
                                                    <div class="bg-white rounded-2xl p-4 border border-gray-50 shadow-sm hover:shadow-md transition group">
                                                        <div class="flex items-start gap-3">
                                                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-xl group-hover:bg-[#22AF85]/10 transition duration-300">
                                                                {{ $item->category_icon ?? '📦' }}
                                                            </div>
                                                            <div class="flex-1">
                                                                <div class="flex justify-between">
                                                                    <p class="font-black text-xs text-gray-900 uppercase tracking-tight">{{ $item->label }}</p>
                                                                    <button data-item="{{ json_encode($item) }}" onclick="openEditItemModal({{ $item->id }}, this)" class="text-gray-300 hover:text-[#22AF85] transition" title="Edit Item">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                                    </button>
                                                                </div>
                                                                <div class="mt-2 space-y-1">
                                                                    <p class="text-[10px] font-bold text-gray-500"><span class="text-gray-400">ID:</span> #{{ $item->item_number }}</p>
                                                                    @if($item->shoe_color)
                                                                        <p class="text-[10px] font-bold text-gray-500"><span class="text-gray-400">Color:</span> {{ $item->shoe_color }}</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="mt-6 flex flex-wrap gap-3">
                                            @if($quotation->status === 'ACCEPTED')
                                                <button onclick="rejectQuotation({{ $quotation->id }})" class="bg-white hover:bg-red-50 text-red-500 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition border-2 border-red-50">
                                                    ❌ Batalkan
                                                </button>
                                            @endif

                                            <a href="{{ route('cs.quotations.export-pdf', $quotation->id) }}" target="_blank" class="bg-[#22AF85] hover:bg-[#22AF85]/90 text-white px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition shadow-lg shadow-green-100 flex items-center gap-2">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                Download PDF
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-12 text-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-dashed border-gray-100 text-gray-300">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        </div>
                                        <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Belum ada quotation</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    {{-- SPK Section --}}
                    @if($lead->spk)
                        <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
                            <div class="p-6 pb-0">
                                <h3 class="font-black text-gray-900 uppercase tracking-tighter text-xl">Service Production Key (SPK)</h3>
                                <div class="w-12 h-1.5 bg-[#22AF85] rounded-full mt-2"></div>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="text-xs text-gray-500 font-semibold">SPK Number</label>
                                        <p class="text-lg font-bold text-gray-900">{{ $lead->spk->spk_number }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 font-semibold">Status</label>
                                        <p><span class="px-2 py-1 rounded text-xs font-semibold {{ $lead->spk->status_badge_class }}">{{ $lead->spk->label }}</span></p>
                                    </div>
                                </div>

                                <div class="bg-gray-50 rounded p-3 mb-3">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-gray-600">Total Price:</span>
                                        <span class="font-bold">Rp {{ number_format($lead->spk->total_price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between mb-2">
                                        <span class="text-gray-600">DP Amount:</span>
                                        <span class="font-semibold text-yellow-600">Rp {{ number_format($lead->spk->dp_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Remaining:</span>
                                        <span class="font-semibold text-red-600">Rp {{ number_format($lead->spk->remaining_payment, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="text-xs text-gray-500 font-semibold">DP Status</label>
                                    <p><span class="px-2 py-1 rounded text-xs font-semibold {{ $lead->spk->dp_status_badge_class }}">{{ $lead->spk->dp_status }}</span></p>
                                </div>
                                @if($lead->spk->status === 'WAITING_DP')
                                    <button onclick="openDpModal()" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg font-semibold mb-2">
                                        💰 Konfirmasi DP Dibayar
                                    </button>
                                @elseif($lead->spk->status === 'WAITING_VERIFICATION')
                                    <div class="w-full bg-yellow-100 text-yellow-700 py-2 px-3 rounded-lg font-bold text-center mb-2 border-2 border-yellow-200">
                                        ⏳ Menunggu Verifikasi Finance
                                    </div>
                                @elseif($lead->spk->status === 'HANDED_TO_WORKSHOP')
                                    <div class="w-full bg-green-100 text-green-700 py-2 px-3 rounded-lg font-bold text-center mb-2 border-2 border-green-200">
                                        ✅ Sudah Diserahkan ke Workshop
                                    </div>
                                @elseif($lead->spk->canBeHandedToWorkshop())
                                    <button onclick="openHandoverModal()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold mb-2 flex items-center justify-center gap-2">
                                        🚚 Serahkan ke Workshop
                                    </button>
                                @endif

                                {{-- PDF Download --}}
                                <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                                    <a href="{{ route('cs.spk.export-pdf', $lead->spk->id) }}" target="_blank" class="flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Download PDF SPK
                                    </a>
                                    <a href="{{ route('cs.spk.shipping-label', $lead->spk->id) }}" target="_blank" class="flex items-center justify-center gap-2 bg-[#f0fdf4] hover:bg-[#22AF85]/10 text-[#22AF85] py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition border-2 border-[#22AF85]/10 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Resi Pengiriman
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Activity Timeline --}}
                    <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
                        <div class="p-6 pb-0">
                            <h3 class="font-black text-gray-900 uppercase tracking-tighter text-xl">Customer Journey</h3>
                            <div class="w-12 h-1.5 bg-gray-200 rounded-full mt-2"></div>
                        </div>
                        <div class="p-4 max-h-96 overflow-y-auto">
                            @forelse($lead->activities as $activity)
                                <div class="flex gap-3 mb-4 pb-4 border-b border-gray-200 last:border-0">
                                    <div class="text-2xl">{{ $activity->type_icon }}</div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="font-semibold text-gray-900">{{ $activity->user->name ?? 'System' }}</span>
                                            <span class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700">{!! $activity->formatted_content !!}</p>
                                        @if($activity->channel)
                                            <span class="text-xs text-gray-500">via {{ $activity->channel }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">Belum ada aktivitas</p>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- Modal: Edit Profil (Governed Edit) --}}
    {{-- Modal: Edit Profil (Governed Edit) --}}
    <div id="editModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
        <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white mb-20 overflow-hidden">
            {{-- Modal Header --}}
            <div class="bg-gray-50/80 px-10 py-8 flex justify-between items-center border-b border-gray-100">
                <div>
                    <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Profil Customer</h3>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="w-12 h-1.5 bg-[#22AF85] rounded-full"></div>
                        <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Governed Revision System</p>
                    </div>
                </div>
                <button onclick="closeEditModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 hover:border-gray-900 transition-all duration-300 group">
                    <svg class="w-6 h-6 group-rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-10">
                @if(in_array($lead->status, ['CONVERTED', 'LOST']))
                    <div class="mb-10 p-6 bg-red-50 border-2 border-red-100 rounded-[2rem] flex items-start gap-5">
                <div class="w-12 h-12 flex-shrink-0 bg-red-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-red-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                        <div>
                            <p class="text-red-900 font-black text-sm uppercase tracking-widest mb-1.5 text-red-500">Data Terkunci (Locked)</p>
                            <p class="text-gray-600 text-xs font-bold leading-relaxed">
                                Lead ini sudah berada di tahap <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-lg">{{ $lead->status }}</span>. Log audit akan mencatat setiap perubahan secara mendalam.
                            </p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('cs.leads.update', $lead->id) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Nama Lengkap *</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', $lead->customer_name) }}" required 
                                       class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">No. WhatsApp *</label>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone', $lead->customer_phone) }}" required 
                                       class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Email</label>
                                <input type="email" name="customer_email" value="{{ old('customer_email', $lead->customer_email) }}" 
                                       class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Sumber Lead *</label>
                                <select name="source" required class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                                    <option value="WhatsApp" {{ $lead->source == 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                                    <option value="Instagram" {{ $lead->source == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                                    <option value="Website" {{ $lead->source == 'Website' ? 'selected' : '' }}>Website</option>
                                    <option value="Referral" {{ $lead->source == 'Referral' ? 'selected' : '' }}>Referral</option>
                                    <option value="Walk-in" {{ $lead->source == 'Walk-in' ? 'selected' : '' }}>Walk-in</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Prioritas *</label>
                                <select name="priority" required class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#FFC232] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                                    <option value="HOT" {{ $lead->priority == 'HOT' ? 'selected' : '' }}>🔥 HOT</option>
                                    <option value="WARM" {{ $lead->priority == 'WARM' ? 'selected' : '' }}>☀️ WARM</option>
                                    <option value="COLD" {{ $lead->priority == 'COLD' ? 'selected' : '' }}>❄️ COLD</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Alamat Lengkap</label>
                            <textarea name="customer_address" rows="1" class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">{{ old('customer_address', $lead->customer_address) }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Kota</label>
                                <input type="text" name="customer_city" value="{{ old('customer_city', $lead->customer_city) }}" 
                                       class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Provinsi</label>
                                <input type="text" name="customer_province" value="{{ old('customer_province', $lead->customer_province) }}" 
                                       class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-gray-100">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Alasan Revisi / Perubahan *</label>
                        <textarea name="revision_reason" rows="3" {{ in_array($lead->status, ['CONVERTED', 'LOST']) ? 'required' : '' }}
                                  class="w-full px-6 py-4 rounded-3xl border-2 border-gray-100 focus:border-red-400 focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-red-50/10 placeholder-red-200"
                                  placeholder="Jelaskan alasan perubahan data untuk audit trail..."></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" onclick="closeEditModal()" class="flex-1 px-8 py-5 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-xs rounded-3xl transition-all duration-300">Tutup</button>
                        <button type="submit" class="flex-[2] px-8 py-5 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-widest text-xs rounded-3xl shadow-xl shadow-yellow-100 transition-all duration-300 transform hover:-translate-y-1">Simpan Revisi Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Modal: Log Activity --}}
    <div id="activityModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
        <div class="relative top-20 mx-auto p-0 border-0 w-full max-w-xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white mb-20 overflow-hidden">
            {{-- Modal Header --}}
            <div class="bg-gray-50/80 px-10 py-8 flex justify-between items-center border-b border-gray-100">
                <div>
                    <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Log Aktivitas</h3>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="w-12 h-1.5 bg-[#22AF85] rounded-full"></div>
                        <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Lead Engagement Tracking</p>
                    </div>
                </div>
                <button onclick="closeActivityModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 hover:border-gray-900 transition-all duration-300 group">
                    <svg class="w-6 h-6 group-rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-10">
                <form action="{{ route('cs.activities.store', $lead->id) }}" method="POST" class="space-y-8">
                    @csrf
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Tipe Interaksi *</label>
                            <select name="type" required class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                                <option value="CHAT">💬 Chat</option>
                                <option value="CALL">📞 Telepon</option>
                                <option value="EMAIL">📧 Email</option>
                                <option value="MEETING">🤝 Meeting</option>
                                <option value="NOTE">📝 Catatan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Channel / Media</label>
                            <input type="text" name="channel" placeholder="WhatsApp, IG, dll"
                                   class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Isi Komunikasi *</label>
                        <textarea name="content" required rows="5" 
                                  class="w-full px-6 py-4 rounded-3xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30 placeholder-gray-300"
                                  placeholder="Detail percakapan atau perkembangan terbaru..."></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" onclick="closeActivityModal()" class="flex-1 px-8 py-5 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-xs rounded-3xl transition-all duration-300">Batal</button>
                        <button type="submit" class="flex-[2] px-8 py-5 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-widest text-xs rounded-3xl shadow-xl shadow-yellow-100 transition-all duration-300 transform hover:-translate-y-1">Simpan Aktivitas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Create Quotation (Multi-Item Data Barang) --}}
    <div id="quotationModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
        <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-4xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white mb-20 overflow-hidden" 
             x-data="quotationManager()">
            {{-- Modal Header --}}
            <div class="bg-gray-50/80 px-10 py-8 flex justify-between items-center border-b border-gray-100">
                <div>
                    <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Draft Quotation</h3>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="w-12 h-1.5 bg-[#22AF85] rounded-full"></div>
                        <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Multi-Item Intake System</p>
                    </div>
                </div>
                <button onclick="closeQuotationModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 hover:border-gray-900 transition-all duration-300 group">
                    <svg class="w-6 h-6 group-rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-10">
                <form action="{{ route('cs.quotations.store', $lead->id) }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                    @csrf
                    <div class="space-y-6">
                        <div class="p-6 bg-[#22AF85]/5 border-2 border-[#22AF85]/10 rounded-[2rem] flex items-center gap-5">
                            <div class="w-12 h-12 bg-[#22AF85] rounded-2xl flex items-center justify-center text-white shadow-lg shadow-green-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            </div>
                            <p class="text-gray-600 text-xs font-bold leading-relaxed">
                                <span class="text-[#22AF85] font-black uppercase tracking-widest block mb-1">Input Data Barang</span>
                                Anda bisa menambahkan beberapa barang sekaligus. Layanan akan dipilih saat Generate SPK.
                            </p>
                        </div>

                        {{-- Items Container --}}
                        <div class="space-y-8 mt-10">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="bg-gray-50/50 rounded-[2.5rem] p-8 border-2 border-gray-100 relative group transition-all duration-300 hover:border-[#22AF85]/20 hover:bg-white hover:shadow-2xl hover:shadow-gray-200/50">
                                    {{-- Item Header --}}
                                    <div class="flex justify-between items-center mb-8 pb-6 border-b border-gray-100">
                                        <div class="flex items-center gap-4">
                                            <span class="w-10 h-10 rounded-xl bg-[#22AF85] text-white flex items-center justify-center font-black text-sm shadow-lg shadow-green-100" x-text="index + 1"></span>
                                            <h4 class="text-xl font-black text-gray-900 uppercase tracking-tight">Data Item</h4>
                                        </div>
                                        <button type="button" @click="removeItem(index)" 
                                                x-show="items.length > 1"
                                                class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all">
                                            <span>Hapus Item</span>
                                        </button>
                                    </div>

                                    {{-- Item Data Form --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        {{-- Category --}}
                                        <div class="space-y-3">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kategori Barang *</label>
                                            <div class="flex gap-3">
                                                <select x-model="item.categoryOpt" @change="item.category = item.categoryOpt === 'Lainnya' ? '' : item.categoryOpt" 
                                                        class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm">
                                                    <option value="">Pilih...</option>
                                                    <option value="Sepatu">Sepatu</option>
                                                    <option value="Tas">Tas</option>
                                                    <option value="Dompet">Dompet</option>
                                                    <option value="Topi">Topi</option>
                                                    <option value="Lainnya">Lainnya...</option>
                                                </select>
                                                <input type="text" :name="'items[' + index + '][category]'" x-model="item.category" required
                                                       placeholder="Ketik manual..." 
                                                       class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm transition-all"
                                                       :class="item.categoryOpt !== 'Lainnya' ? 'bg-gray-100 border-gray-100 cursor-not-allowed opacity-50' : ''"
                                                       :readonly="item.categoryOpt !== 'Lainnya'">
                                            </div>
                                        </div>

                                        {{-- Type --}}
                                        <div class="space-y-3">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Jenis / Model</label>
                                            <div class="flex gap-3">
                                                <select x-model="item.typeOpt" @change="item.shoe_type = item.typeOpt === 'Lainnya' ? '' : item.typeOpt"
                                                        class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm">
                                                    <option value="">Pilih...</option>
                                                    <option value="Casual">Casual</option>
                                                    <option value="Sneakers">Sneakers</option>
                                                    <option value="Outdoor">Outdoor</option>
                                                    <option value="Sport">Sport</option>
                                                    <option value="Lainnya">Lainnya...</option>
                                                </select>
                                                <input type="text" :name="'items[' + index + '][shoe_type]'" x-model="item.shoe_type"
                                                       placeholder="Ketik manual..." 
                                                       class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm transition-all"
                                                       :class="item.typeOpt !== 'Lainnya' ? 'bg-gray-100 border-gray-100 cursor-not-allowed opacity-50' : ''"
                                                       :readonly="item.typeOpt !== 'Lainnya'">
                                            </div>
                                        </div>

                                        {{-- Brand --}}
                                        <div class="space-y-3">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Brand</label>
                                            <div class="flex gap-3">
                                                <select x-model="item.brandOpt" @change="item.shoe_brand = item.brandOpt === 'Lainnya' ? '' : item.brandOpt"
                                                        class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm">
                                                    <option value="">Pilih...</option>
                                                    <option value="Nike">Nike</option>
                                                    <option value="Adidas">Adidas</option>
                                                    <option value="Puma">Puma</option>
                                                    <option value="New Balance">New Balance</option>
                                                    <option value="Lainnya">Lainnya...</option>
                                                </select>
                                                <input type="text" :name="'items[' + index + '][shoe_brand]'" x-model="item.shoe_brand"
                                                       placeholder="Ketik manual..." 
                                                       class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm transition-all"
                                                       :class="item.brandOpt !== 'Lainnya' ? 'bg-gray-100 border-gray-100 cursor-not-allowed opacity-50' : ''"
                                                       :readonly="item.brandOpt !== 'Lainnya'">
                                            </div>
                                        </div>

                                        {{-- Size --}}
                                        <div class="space-y-3">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Ukuran</label>
                                            <div class="flex gap-3">
                                                <select x-model="item.sizeOpt" @change="item.shoe_size = item.sizeOpt === 'Lainnya' ? '' : item.sizeOpt"
                                                        class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm">
                                                    <option value="">Pilih...</option>
                                                    <option value="40">40</option>
                                                    <option value="41">41</option>
                                                    <option value="42">42</option>
                                                    <option value="43">43</option>
                                                    <option value="Lainnya">Lainnya...</option>
                                                </select>
                                                <input type="text" :name="'items[' + index + '][shoe_size]'" x-model="item.shoe_size"
                                                       placeholder="Ketik manual..." 
                                                       class="w-1/2 px-5 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm transition-all"
                                                       :class="item.sizeOpt !== 'Lainnya' ? 'bg-gray-100 border-gray-100 cursor-not-allowed opacity-50' : ''"
                                                       :readonly="item.sizeOpt !== 'Lainnya'">
                                            </div>
                                        </div>

                                        {{-- Color & Notes --}}
                                        <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
                                            <div class="space-y-3">
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Warna</label>
                                                <input type="text" :name="'items[' + index + '][shoe_color]'" x-model="item.shoe_color"
                                                       placeholder="Contoh: Merah, Hitam Putih..." 
                                                       class="w-full px-6 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm transition-all">
                                            </div>
                                            <div class="space-y-3">
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kondisi / Kerusakan</label>
                                                <textarea :name="'items[' + index + '][condition_notes]'" x-model="item.condition_notes" rows="1"
                                                          placeholder="Contoh: Kotor, sol lepas, pudar..." 
                                                          class="w-full px-6 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white shadow-sm transition-all"></textarea>
                                            </div>
                                        </div>

                                        {{-- Item Notes (Keterangan Besar SPK) --}}
                                        <div class="col-span-1 md:col-span-2 mt-4">
                                            <label class="block text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                Instruksi Khusus Produksi (Workshop)
                                            </label>
                                            <textarea :name="'items[' + index + '][item_notes]'" x-model="item.item_notes" rows="3"
                                                      placeholder="Catatan teknis pengerjaan untuk tim workshop..." 
                                                      class="w-full px-8 py-6 rounded-3xl border-2 border-[#22AF85]/10 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-[#22AF85]/5 placeholder-[#22AF85]/30 transition-all"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Add Item Button --}}
                        <button type="button" @click="addItem()" 
                                class="w-full py-8 border-4 border-dashed border-gray-100 rounded-[2.5rem] text-gray-400 hover:border-[#22AF85]/30 hover:text-[#22AF85] hover:bg-[#22AF85]/5 transition-all duration-300 group">
                            <div class="flex flex-col items-center gap-2">
                                <span class="text-3xl group-scale-110 transition-transform">➕</span>
                                <span class="text-xs font-black uppercase tracking-[0.2em]">Tambah Item Lainnya</span>
                            </div>
                        </button>

                        {{-- Notes --}}
                        <div class="pt-10 border-t border-gray-100">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Catatan Umum Quotation</label>
                            <textarea name="notes" rows="3" 
                                      class="w-full px-8 py-6 rounded-[2rem] border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-gray-50/30 transition-all" 
                                      placeholder="Tambahkan catatan syarat & ketentuan atau info umum..."></textarea>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-10">
                        <button type="button" onclick="closeQuotationModal()" class="flex-1 px-8 py-6 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-xs rounded-3xl transition-all duration-300">Batal</button>
                        <button type="submit" class="flex-[2] px-8 py-6 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-widest text-xs rounded-3xl shadow-xl shadow-yellow-100 transition-all duration-300 transform hover:-translate-y-1">Simpan & Terbitkan Quotation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function quotationManager() {
            return {
                items: [{
                    categoryOpt: '',
                    category: '',
                    typeOpt: '',
                    shoe_type: '',
                    brandOpt: '',
                    shoe_brand: '',
                    sizeOpt: '',
                    shoe_size: '',
                    shoe_color: '',
                    condition_notes: '',
                    item_notes: ''
                }],
                addItem() {
                    this.items.push({
                        categoryOpt: '',
                        category: '',
                        typeOpt: '',
                        shoe_type: '',
                        brandOpt: '',
                        shoe_brand: '',
                        sizeOpt: '',
                        shoe_size: '',
                        shoe_color: '',
                        condition_notes: '',
                        item_notes: ''
                    });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                }
            }
        }
    </script>

    {{-- Modal: Generate SPK --}}
    <div id="spkModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
        <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-4xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white mb-20 overflow-hidden">
            {{-- Modal Header --}}
            <div class="bg-gray-50/80 px-10 py-8 flex justify-between items-center border-b border-gray-100">
                <div>
                    <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Generate SPK</h3>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="w-12 h-1.5 bg-[#22AF85] rounded-full"></div>
                        <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Production Order Command</p>
                    </div>
                </div>
                <button onclick="closeSpkModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 hover:border-gray-900 transition-all duration-300 group">
                    <svg class="w-6 h-6 group-rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-10">
                <form action="{{ route('cs.spk.generate', $lead->id) }}" method="POST" class="space-y-10">
                    @csrf
                    {{-- SPK Preview Section --}}
                    <div class="p-8 bg-gray-900 rounded-[2rem] shadow-2xl relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-[#22AF85]/10 rounded-full blur-3xl -mr-32 -mt-32 transition-all group-hover:bg-[#22AF85]/20"></div>
                        
                        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                            <div class="flex-1 text-center md:text-left">
                                <label class="block text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em] mb-4">Preview Draft Nomor SPK</label>
                                <div id="spkPreview" class="text-4xl md:text-5xl font-mono font-black text-white tracking-tighter truncate selection:bg-[#22AF85]">
                                    F-{{ date('ym-d') }}-XXXX-{{ strtoupper($lead->cs->cs_code ?? '??') }}
                                </div>
                                <input type="hidden" name="spk_number" id="finalSpkNumber">
                                <div class="flex items-center gap-4 mt-6">
                                    <span class="px-3 py-1 bg-[#22AF85] text-white rounded-lg text-[9px] font-black uppercase tracking-widest">Auto Generated</span>
                                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">[KODE]-[YYMM]-[DD]-[SEQ]-[CS]</p>
                                </div>
                            </div>
                            
                            <div class="w-full md:w-auto grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2">Metode Kirim</label>
                                    <select name="delivery_type" id="deliveryTypeSelect" required onchange="updateSpkPreview()"
                                            class="w-full px-5 py-3 rounded-xl border-2 border-gray-800 bg-gray-800 text-white text-xs font-bold focus:border-[#22AF85] focus:ring-0 transition-all">
                                        <option value="Offline" data-code="F">Offline (F)</option>
                                        <option value="Online" data-code="N">Online (N)</option>
                                        <option value="Pickup" data-code="P">Pickup (P)</option>
                                        <option value="Ojol" data-code="O">Ojol (O)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2">Kode CS</label>
                                    <input type="text" name="manual_cs_code" id="manualCsInput" required maxlength="5" value="{{ $lead->cs->cs_code ?? '' }}" 
                                           oninput="updateSpkPreview()"
                                           class="w-full px-5 py-3 rounded-xl border-2 border-gray-800 bg-gray-800 text-white text-xs font-bold focus:border-[#22AF85] focus:ring-0 transition-all uppercase">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        {{-- Logistics & Priority --}}
                        <div class="space-y-8">
                            <div>
                                <h4 class="text-xs font-black text-gray-900 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">01</span>
                                    Logistics & Scheduling
                                </h4>
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Prioritas *</label>
                                        <select name="priority" required class="w-full px-6 py-4 rounded-2xl border-2 border-gray-50 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-gray-50/30">
                                            <option value="Reguler">Reguler</option>
                                            <option value="Prioritas">Prioritas</option>
                                            <option value="Urgent">Urgent</option>
                                            <option value="Express">Express</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Expected Delivery</label>
                                        <input type="date" name="expected_delivery_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                               class="w-full px-6 py-4 rounded-2xl border-2 border-gray-50 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-gray-50/30">
                                    </div>
                                </div>
                            </div>

                            {{-- Special Instructions --}}
                            <div>
                                <label class="block text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    Instruksi Khusus (Optional)
                                </label>
                                <textarea name="special_instructions" rows="4" 
                                          class="w-full px-8 py-6 rounded-3xl border-2 border-[#22AF85]/10 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-[#22AF85]/5 placeholder-[#22AF85]/30 transition-all"
                                          placeholder="Catatan pengerjaan atau request khusus customer...">{{ $lead->getAcceptedQuotation()->notes ?? '' }}</textarea>
                            </div>
                        </div>

                        {{-- Customer Data Verification --}}
                        <div>
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">02</span>
                                Data Shipment Verification
                            </h4>
                            <div class="p-8 bg-gray-50/50 rounded-[2.5rem] border-2 border-gray-100 space-y-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nama Penerima</label>
                                        <input type="text" name="customer_name" value="{{ $lead->customer_name }}" required
                                               class="w-full px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-black text-gray-900 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Telepon</label>
                                        <input type="text" name="customer_phone" value="{{ $lead->customer_phone }}" required
                                               class="w-full px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-black text-gray-900 bg-white shadow-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Alamat Lengkap</label>
                                    <textarea name="customer_address" rows="2" required
                                              class="w-full px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm">{{ $lead->customer_address }}</textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Kota</label>
                                        <input type="text" name="customer_city" value="{{ $lead->customer_city }}" required
                                               class="w-full px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Provinsi</label>
                                        <input type="text" name="customer_province" value="{{ $lead->customer_province }}" required
                                               class="w-full px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Multi-Item Service Mapping --}}
                    @php
                        $acceptedQuotation = $lead->getAcceptedQuotation();
                    @endphp
                    @if($acceptedQuotation && count($acceptedQuotation->quotationItems ?? []) > 0)
                        <div class="pt-10 border-t border-gray-100">
                            <h4 class="text-xs font-black text-gray-900 uppercase tracking-[0.2em] mb-8 flex items-center justify-between">
                                <span class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-[#22AF85] flex items-center justify-center text-white shadow-lg shadow-green-100 text-[10px]">03</span>
                                    Service Configuration per Item
                                </span>
                                <span class="px-4 py-1.5 bg-[#22AF85]/10 text-[#22AF85] rounded-full text-[9px] font-black uppercase tracking-widest border border-[#22AF85]/20">
                                    Active Quotation: #{{ $acceptedQuotation->quotation_number }}
                                </span>
                            </h4>

                            <div class="space-y-6">
                                @foreach($acceptedQuotation->quotationItems as $quotationItem)
                                    <div class="bg-gray-50/50 rounded-[2.5rem] p-8 border-2 border-gray-100 transition-all duration-500 hover:border-[#22AF85]/20 hover:bg-white hover:shadow-2xl hover:shadow-gray-200/50 group">
                                        <div class="flex flex-col md:flex-row gap-8">
                                            {{-- Item Brief --}}
                                            <div class="md:w-1/3 space-y-4">
                                                <div class="flex items-start gap-4">
                                                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-3xl shadow-sm border border-gray-100 group-hover:scale-110 transition-transform">
                                                        {{ $quotationItem->category_icon }}
                                                    </div>
                                                    <div>
                                                        <h5 class="text-lg font-black text-gray-900 leading-tight">Item #{{ $quotationItem->item_number }}</h5>
                                                        <p class="text-xs text-[#22AF85] font-black uppercase tracking-widest mt-1">{{ $quotationItem->category }}</p>
                                                    </div>
                                                </div>
                                                <div class="p-5 bg-white rounded-2xl border border-gray-100 space-y-2 shadow-sm">
                                                    <div class="flex justify-between text-[10px] font-bold">
                                                        <span class="text-gray-400 uppercase tracking-widest">Brand</span>
                                                        <span class="text-gray-900 font-black">{{ $quotationItem->shoe_brand ?: '-' }}</span>
                                                    </div>
                                                    <div class="flex justify-between text-[10px] font-bold">
                                                        <span class="text-gray-400 uppercase tracking-widest">Color</span>
                                                        <span class="text-gray-900 font-black">{{ $quotationItem->shoe_color ?: '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Service Selection --}}
                                            <div class="flex-1 space-y-6">
                                                <div class="relative">
                                                    <input type="text" id="search-services-{{ $quotationItem->id }}" onkeyup="filterServices({{ $quotationItem->id }})"
                                                           placeholder="Cari layanan workshop..."
                                                           class="w-full px-12 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 bg-white placeholder-gray-300 transition-all shadow-sm">
                                                    <svg class="w-5 h-5 absolute left-5 top-1/2 -translate-y-1/2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                                </div>

                                                <div id="services-container-{{ $quotationItem->id }}" class="max-h-80 overflow-y-auto space-y-3 pr-2 custom-scrollbar">
                                                    @foreach($services as $service)
                                                        <div class="service-wrapper-{{ $quotationItem->id }}-{{ $service->id }}">
                                                            <label class="service-item-{{ $quotationItem->id }} flex items-center justify-between p-5 bg-white border-2 border-gray-50 rounded-[1.5rem] hover:border-[#22AF85]/30 cursor-pointer transition-all active:scale-[0.98] shadow-sm select-none"
                                                                   data-service-name="{{ strtolower($service->name) }}"
                                                                   data-service-category="{{ strtolower($service->category ?? '') }}">
                                                                <div class="flex items-center gap-5">
                                                                    <input type="checkbox" name="items[{{ $quotationItem->id }}][services][]" value="{{ $service->id }}"
                                                                           id="service-{{ $quotationItem->id }}-{{ $service->id }}"
                                                                           data-item-id="{{ $quotationItem->id }}" data-price="{{ $service->price }}" 
                                                                           data-name="{{ $service->name }}" data-category="{{ $service->category ?: '-' }}"
                                                                           onchange="toggleServiceDetail({{ $quotationItem->id }}, {{ $service->id }}); updateItemTotal({{ $quotationItem->id }}); updateSelectedServices({{ $quotationItem->id }});"
                                                                           class="service-checkbox w-6 h-6 rounded-lg border-2 border-gray-100 text-[#22AF85] focus:ring-0 transition-all">
                                                                    <div>
                                                                        <p class="text-sm font-black text-gray-900">{{ $service->name }}</p>
                                                                        <p class="text-[9px] text-[#22AF85] font-black uppercase tracking-widest mt-1">{{ $service->category ?: 'General' }}</p>
                                                                    </div>
                                                                </div>
                                                                <span class="text-sm font-black text-gray-900">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                                                            </label>
                                                            
                                                            <div id="detail-{{ $quotationItem->id }}-{{ $service->id }}" class="hidden mt-3 mx-4 p-6 bg-[#22AF85]/5 border-2 border-dashed border-[#22AF85]/20 rounded-[1.5rem]">
                                                                <label class="block text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em] mb-3">Detail Instruksi Jasa</label>
                                                                <textarea name="items[{{ $quotationItem->id }}][service_details][{{ $service->id }}]"
                                                                          id="detail-input-{{ $quotationItem->id }}-{{ $service->id }}"
                                                                          oninput="updateSelectedServices({{ $quotationItem->id }})"
                                                                          rows="2" class="w-full px-5 py-4 rounded-xl border-0 focus:ring-2 focus:ring-[#22AF85] text-xs font-bold text-gray-900 bg-white shadow-inner"
                                                                          placeholder="Contoh: Jahit Pola V / Recolor warna navy pekat..."></textarea>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                {{-- Custom Service Engine --}}
                                                <div class="p-6 bg-gray-900 rounded-[2rem] border-2 border-gray-800 shadow-xl">
                                                    <div class="flex justify-between items-center mb-4">
                                                        <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-[0.2em]">Custom Workshop Service</span>
                                                        <button type="button" onclick="toggleCustomService({{ $quotationItem->id }})"
                                                                class="px-4 py-2 bg-[#22AF85] text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:opacity-90 transition shadow-lg shadow-green-900/20">
                                                            + Add New Service
                                                        </button>
                                                    </div>
                                                    
                                                    <div id="custom-service-{{ $quotationItem->id }}" class="hidden space-y-4 pt-4 border-t border-gray-800">
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <input type="text" id="custom-name-{{ $quotationItem->id }}" placeholder="Nama Layanan"
                                                                   class="px-5 py-3 rounded-xl border-0 bg-gray-800 text-white text-xs font-bold focus:ring-2 focus:ring-[#22AF85]">
                                                            <input type="number" id="custom-price-{{ $quotationItem->id }}" placeholder="Harga (IDR)"
                                                                   class="px-5 py-3 rounded-xl border-0 bg-gray-800 text-white text-xs font-bold focus:ring-2 focus:ring-[#22AF85]">
                                                        </div>
                                                        <textarea id="custom-description-{{ $quotationItem->id }}" placeholder="Detail teknis..."
                                                                  class="w-full px-5 py-3 rounded-xl border-0 bg-gray-800 text-white text-xs font-bold focus:ring-2 focus:ring-[#22AF85]" rows="2"></textarea>
                                                        <button type="button" onclick="addCustomService({{ $quotationItem->id }})"
                                                                class="w-full py-4 bg-[#FFC232] text-gray-900 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest hover:opacity-90 transition">
                                                            Append to Task List
                                                        </button>
                                                    </div>

                                                    <div id="selected-summary-{{ $quotationItem->id }}" class="mt-6 space-y-2 hidden">
                                                        <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3">Service Pipeline:</p>
                                                        <div id="selected-list-{{ $quotationItem->id }}" class="space-y-2"></div>
                                                    </div>

                                                    <div class="mt-8 pt-6 border-t border-gray-800 flex justify-between items-center">
                                                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Subtotal Item #{{ $quotationItem->item_number }}</span>
                                                        <span class="text-2xl font-black text-white" id="item-subtotal-{{ $quotationItem->id }}">Rp 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="items[{{ $quotationItem->id }}][quotation_item_id]" value="{{ $quotationItem->id }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Financial Finalization --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 pt-10 border-t border-gray-100">
                        {{-- Promo & Voucher --}}
                        <div class="p-8 bg-gray-50 rounded-[2.5rem] border-2 border-gray-100">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Redeem Boutique Voucher</label>
                            <div class="flex gap-4">
                                <input type="text" name="promo_code" id="promo-code-input" 
                                       class="flex-1 px-6 py-4 rounded-2xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-sm font-mono font-black text-gray-900 bg-white shadow-sm transition-all uppercase" 
                                       placeholder="COUPON CODE">
                                <button type="button" onclick="validatePromo()" id="btn-apply-promo" 
                                        class="px-10 py-4 bg-gray-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-gray-200">
                                    Apply
                                </button>
                            </div>
                            <div id="promo-status" class="mt-4 hidden">
                                <p class="text-xs font-bold" id="promo-message"></p>
                            </div>
                        </div>

                        {{-- Final Pricing & DP --}}
                        <div class="space-y-6">
                            <div class="p-8 bg-gray-900 rounded-[2.5rem] shadow-2xl text-white">
                                <div class="flex justify-between items-center mb-6">
                                    <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-widest">Grand Total Amount</span>
                                    <span class="text-4xl font-black tracking-tighter" id="grand-total">Rp 0</span>
                                </div>
                                <div class="space-y-4 pt-6 border-t border-gray-800">
                                    <div class="flex justify-between items-center">
                                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Commitment Fee (DP) *</label>
                                        <div class="relative w-48">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-black text-gray-500">Rp</span>
                                            <input type="number" name="dp_amount" id="dp-amount-input" value="0" min="0" required
                                                   class="w-full pl-12 pr-4 py-3 rounded-xl border-0 bg-gray-800 text-sm font-black text-white focus:ring-2 focus:ring-[#22AF85]">
                                        </div>
                                    </div>
                                    <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest text-right">* Minimum 30% Commitment: <span class="text-[#22AF85]" id="dp-suggestion">Rp 0</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-6 pt-10">
                        <button type="button" onclick="closeSpkModal()" 
                                class="flex-1 px-10 py-6 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-xs rounded-[2rem] transition-all duration-300">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-[2] px-10 py-6 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-[0.2em] text-xs rounded-[2rem] shadow-[0_20px_40px_rgba(255,194,50,0.3)] transition-all duration-300 transform hover:-translate-y-2">
                            Generate Production Order (SPK)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    {{-- Modal: Set Follow Up --}}
    <div id="followUpModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
        <div class="relative top-20 mx-auto p-0 border-0 w-full max-w-md shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white overflow-hidden">
            <div class="bg-gray-50/80 px-8 py-6 flex justify-between items-center border-b border-gray-100">
                <div>
                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-tighter">Schedule Follow Up</h3>
                    <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mt-1">Nurturing Retention</p>
                </div>
                <button onclick="closeFollowUpModal()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-8">
                <form action="{{ route('cs.leads.set-follow-up', $lead->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Next Contact Date & Time *</label>
                        <div class="relative group">
                            <input type="datetime-local" name="next_follow_up_at" required min="{{ date('Y-m-d\TH:i') }}" 
                                   class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Strategi & Catatan</label>
                        <textarea name="notes" rows="4" 
                                  class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30 placeholder-gray-300" 
                                  placeholder="Rencana pembicaraan atau poin penting follow up..."></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" onclick="closeFollowUpModal()" 
                                class="flex-1 px-8 py-4 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-[10px] rounded-2xl transition-all">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-[2] px-8 py-4 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-widest text-[10px] rounded-2xl shadow-xl shadow-yellow-100 transition-all transform hover:-translate-y-1">
                            Save Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Handover to Workshop --}}
    <div id="handoverModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
        <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-3xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white mb-20 overflow-hidden">
            {{-- Modal Header --}}
            <div class="bg-gray-50/80 px-10 py-8 flex justify-between items-center border-b border-gray-100">
                <div>
                    <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Gudang Handover</h3>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="w-12 h-1.5 bg-[#22AF85] rounded-full"></div>
                        <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Warehouse Entry Control</p>
                    </div>
                </div>
                <button onclick="closeHandoverModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-10">
                @if($lead->spk && count($lead->spk->items ?? []) > 0)
                    <form action="{{ route('cs.spk.hand-to-workshop', $lead->spk->id) }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                        @csrf
                        <div class="p-6 bg-[#22AF85]/5 border-2 border-dashed border-[#22AF85]/20 rounded-3xl">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-[#22AF85] rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg shadow-green-100">
                                    📦
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-900 uppercase tracking-tighter">Logistics Confirmation</p>
                                    <p class="text-[10px] text-[#22AF85] font-black uppercase tracking-widest">Converting {{ count($lead->spk->items ?? []) }} SPK Items into Work Orders</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            @foreach($lead->spk->items as $spkItem)
                                <div class="bg-gray-50/50 rounded-[2.5rem] p-8 border-2 border-gray-100 group">
                                    <div class="flex justify-between items-start mb-8 pb-6 border-b border-gray-100">
                                        <div class="flex items-center gap-5">
                                            <div class="text-4xl bg-white w-16 h-16 rounded-2xl flex items-center justify-center shadow-sm border border-gray-100">{{ $spkItem->category_icon }}</div>
                                            <div>
                                                <h5 class="text-xl font-black text-gray-900">Item #{{ $spkItem->item_number }}</h5>
                                                <p class="text-[10px] text-[#22AF85] font-black uppercase tracking-widest mt-1">{{ $spkItem->category }} | {{ $spkItem->shoe_brand ?: 'Generic' }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-1">Item Value</p>
                                            <p class="text-lg font-black text-gray-900">Rp {{ number_format($spkItem->item_total_price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        {{-- Physical Details --}}
                                        <div class="space-y-4">
                                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Physical Specs Verification</label>
                                            <div class="grid grid-cols-2 gap-4">
                                                <input type="text" name="items[{{ $spkItem->id }}][shoe_brand]" value="{{ $spkItem->shoe_brand }}" placeholder="Merk"
                                                       class="px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm transition-all focus:bg-white">
                                                <input type="text" name="items[{{ $spkItem->id }}][shoe_type]" value="{{ $spkItem->shoe_type }}" placeholder="Tipe"
                                                       class="px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm transition-all focus:bg-white">
                                                <input type="text" name="items[{{ $spkItem->id }}][shoe_color]" value="{{ $spkItem->shoe_color }}" placeholder="Warna"
                                                       class="px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm transition-all focus:bg-white">
                                                <input type="text" name="items[{{ $spkItem->id }}][shoe_size]" value="{{ $spkItem->shoe_size }}" placeholder="Ukuran"
                                                       class="px-5 py-3 rounded-xl border-2 border-white focus:border-[#22AF85] focus:ring-0 text-xs font-bold text-gray-900 bg-white shadow-sm transition-all focus:bg-white">
                                            </div>
                                            <div class="pt-4">
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Reference Photo Attachment</label>
                                                <input type="file" name="items[{{ $spkItem->id }}][ref_photo]" accept="image/*" 
                                                       class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-[#22AF85]/10 file:text-[#22AF85] hover:file:bg-[#22AF85]/20 cursor-pointer">
                                            </div>
                                        </div>

                                        {{-- Service Pipeline --}}
                                        <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm space-y-4">
                                            <div class="flex justify-between items-center mb-2">
                                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Validated Service Pipeline</label>
                                                <span class="text-[9px] font-black text-[#22AF85] uppercase tracking-widest">{{ count($spkItem->services ?? []) }} Jasa</span>
                                            </div>
                                            <div class="space-y-3 max-h-[200px] overflow-y-auto">
                                                @forelse(($spkItem->services ?? []) as $service)
                                                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:border-[#22AF85]/30 transition-all">
                                                        <div class="flex items-start justify-between gap-3">
                                                            <div class="flex-1">
                                                                <div class="flex items-center gap-2 mb-1">
                                                                    <div class="w-1.5 h-1.5 rounded-full bg-[#22AF85]"></div>
                                                                    <span class="text-[11px] font-black text-gray-900 uppercase tracking-wide">{{ $service['name'] }}</span>
                                                                    @if(!empty($service['is_custom']))
                                                                        <span class="px-2 py-0.5 bg-[#FFC232] text-gray-900 rounded text-[8px] font-black uppercase tracking-widest">Custom</span>
                                                                    @endif
                                                                </div>
                                                                @if(!empty($service['manual_detail']) || !empty($service['description']))
                                                                    <p class="text-[10px] text-gray-500 font-medium pl-3.5 italic leading-relaxed">
                                                                        "{{ $service['manual_detail'] ?? $service['description'] ?? '' }}"
                                                                    </p>
                                                                @endif
                                                            </div>
                                                            <div class="text-right flex-shrink-0">
                                                                <span class="text-xs font-black text-[#22AF85]">Rp {{ number_format($service['price'] ?? 0, 0, ',', '.') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-center py-6 text-gray-400">
                                                        <p class="text-[10px] font-bold uppercase tracking-widest">Tidak ada layanan terpilih</p>
                                                    </div>
                                                @endforelse
                                            </div>
                                            {{-- Service Subtotal --}}
                                            @php
                                                $serviceSubtotal = collect($spkItem->services ?? [])->sum('price');
                                            @endphp
                                            <div class="pt-4 border-t border-dashed border-gray-200 flex justify-between items-center">
                                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Subtotal Jasa</span>
                                                <span class="text-sm font-black text-gray-900">Rp {{ number_format($serviceSubtotal, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="items[{{ $spkItem->id }}][item_type]" value="{{ $spkItem->category_prefix }}">
                                    <input type="hidden" name="items[{{ $spkItem->id }}][spk_item_id]" value="{{ $spkItem->id }}">
                                </div>
                            @endforeach
                        </div>

                        {{-- Warehouse Finalization --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 pt-10 border-t border-gray-100 items-center">
                            <div class="p-8 bg-gray-900 rounded-[2.5rem] shadow-2xl text-white">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Total Handover Value</p>
                                        <h4 class="text-3xl font-black tracking-tighter">Rp {{ number_format($lead->spk->total_price, 0, ',', '.') }}</h4>
                                    </div>
                                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-3xl">📥</div>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <button type="button" onclick="closeHandoverModal()" 
                                        class="flex-1 px-8 py-5 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-xs rounded-3xl transition-all duration-300">
                                    Batal
                                </button>
                                <button type="submit" 
                                        class="flex-[2] px-8 py-5 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-widest text-xs rounded-3xl shadow-xl shadow-yellow-100 transition-all transform hover:-translate-y-1">
                                    Relay to Warehouse
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="p-10 text-center">
                        <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">⚠️</div>
                        <h4 class="text-xl font-black text-gray-900 uppercase tracking-tighter">Ready SPK Required</h4>
                        <p class="text-sm text-gray-500 font-bold mt-2">Generate and accept an SPK first before physical handover to warehouse.</p>
                        <button onclick="closeHandoverModal()" class="mt-8 px-10 py-4 bg-gray-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest">Understood</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Follow Up Modal
        function openFollowUpModal() {
            document.getElementById('followUpModal').classList.remove('hidden');
        }
        function closeFollowUpModal() {
            document.getElementById('followUpModal').classList.add('hidden');
        }
    </script>



{{-- Modal: Edit Detail Barang & Layanan (Governed Edit) --}}
<div id="editItemModal" class="hidden fixed inset-0 bg-gray-900/80 backdrop-blur-md overflow-y-auto h-full w-full z-[100] transition-all duration-300">
    <div class="relative top-10 mx-auto p-0 border-0 w-full max-w-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] rounded-[2.5rem] bg-white mb-20 overflow-hidden">
        {{-- Modal Header --}}
        <div class="bg-gray-50/80 px-10 py-8 flex justify-between items-center border-b border-gray-100">
            <div>
                <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">Detail Transaksi</h3>
                <div class="flex items-center gap-2 mt-2">
                    <div class="w-12 h-1.5 bg-[#22AF85] rounded-full"></div>
                    <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Sync to Master Production</p>
                </div>
            </div>
            <button onclick="closeEditItemModal()" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border-2 border-gray-100 text-gray-400 hover:text-gray-900 hover:border-gray-900 transition-all duration-300 group">
                <svg class="w-6 h-6 group-rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-10">
            <div id="lockedItemWarning" class="hidden mb-10 p-6 bg-red-50 border-2 border-red-100 rounded-[2rem] flex items-start gap-5">
                <div class="w-12 h-12 flex-shrink-0 bg-red-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-red-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m0 0v2m0-2h2m-2 0H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-red-900 font-black text-sm uppercase tracking-widest mb-1.5 text-red-500">Data Terkunci (Locked)</p>
                    <p class="text-gray-600 text-xs font-bold leading-relaxed">
                        Hanya Admin yang dapat merevisi data pada tahap ini karena SPK/Work Order sudah masuk ke antrean workshop.
                    </p>
                </div>
            </div>

            <form id="editItemForm" method="POST" class="space-y-8">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Kategori Barang *</label>
                            <select name="category" id="item_category" required class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                                <option value="">Pilih Kategori...</option>
                                <option value="Sepatu">Sepatu</option>
                                <option value="Tas">Tas</option>
                                <option value="Dompet">Dompet</option>
                                <option value="Topi">Topi</option>
                                <option value="Lainnya">Lainnya...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Model / Tipe</label>
                            <select name="shoe_type" id="item_type" class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                                <option value="">Pilih Tipe...</option>
                                <option value="Casual">Casual</option>
                                <option value="Sneakers">Sneakers</option>
                                <option value="Outdoor">Outdoor</option>
                                <option value="Sport">Sport</option>
                                <option value="Formal">Formal</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Merek (Brand)</label>
                            <select name="shoe_brand" id="item_brand" class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                                <option value="">Pilih Brand...</option>
                                <option value="Nike">Nike</option>
                                <option value="Adidas">Adidas</option>
                                <option value="New Balance">New Balance</option>
                                <option value="Converse">Converse</option>
                                <option value="Lainnya">Lainnya...</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Size</label>
                                <input type="text" name="shoe_size" id="item_size" class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Warna</label>
                                <input type="text" name="shoe_color" id="item_color" class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-gray-50 rounded-[2rem] border-2 border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Master Layanan & Biaya</label>
                        <span class="px-3 py-1 bg-[#22AF85]/10 text-[#22AF85] rounded-lg text-[9px] font-black uppercase tracking-widest">Financial Sync Active</span>
                    </div>
                    
                    <div id="service_edit_checklist" class="max-h-[300px] overflow-y-auto mb-6 pr-2 space-y-3 custom-scrollbar">
                        @php $currentCategory = ''; @endphp
                        @foreach($services as $service)
                            @if($currentCategory !== $service->category)
                                <p class="text-[10px] font-black text-gray-900 uppercase tracking-widest mt-6 mb-3 px-1 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#22AF85]"></span>
                                    {{ $service->category }}
                                </p>
                                @php $currentCategory = $service->category; @endphp
                            @endif
                            <div class="bg-white rounded-2xl border-2 border-white hover:border-[#22AF85]/20 p-4 transition duration-300 group shadow-sm">
                                <label class="flex items-center justify-between cursor-pointer">
                                    <div class="flex items-center gap-4">
                                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" 
                                               class="service-edit-checkbox w-5 h-5 rounded-lg border-2 border-gray-100 text-[#22AF85] focus:ring-0"
                                               data-price="{{ $service->price }}" 
                                               onchange="toggleServiceDetail(this); calculateEditTotal()">
                                        <span class="text-xs font-bold text-gray-700 group-hover:text-gray-900 transition">{{ $service->name }}</span>
                                    </div>
                                    <span class="text-xs font-black text-[#22AF85]">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                                </label>
                                <div id="detail_container_{{ $service->id }}" class="hidden mt-4 pt-4 border-t border-dashed border-gray-100">
                                    <input type="text" name="service_details[{{ $service->id }}]" id="service_detail_{{ $service->id }}"
                                           class="w-full px-5 py-3 text-[11px] border-2 border-gray-50 rounded-xl bg-gray-50 focus:bg-white focus:border-[#22AF85] focus:ring-0 transition"
                                           placeholder="Catatan pengerjaan khusus (misal: warna cat spesifik)...">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Custom Services --}}
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Layanan Tambahan (Custom)</span>
                            <button type="button" onclick="addCustomServiceRow()" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition shadow-lg shadow-gray-200">
                                + Tambah Layanan
                            </button>
                        </div>
                        <div id="edit_custom_services_container" class="space-y-3"></div>
                    </div>

                    <div class="flex items-center justify-between pt-8 border-t border-gray-200 mt-8">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Total Biaya Baru (IDR)</span>
                            <p class="text-xs text-gray-400 font-bold italic">Harga otomatis terakumulasi</p>
                        </div>
                        <div class="relative">
                            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-sm font-black text-gray-400">Rp</span>
                            <input type="number" name="item_total_price" id="item_total_price" 
                                   class="w-48 pl-14 pr-6 py-4 rounded-2xl border-0 bg-white text-xl font-black text-gray-900 shadow-inner"
                                   readonly>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Catatan Khusus Workshop</label>
                    <textarea name="item_notes" id="item_notes" rows="3" class="w-full px-6 py-4 rounded-2xl border-2 border-gray-100 focus:border-[#22AF85] focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-gray-50/30" placeholder="Informasi teknis tambahan..."></textarea>
                </div>

                <div class="pt-8 border-t border-gray-100">
                    <label class="block text-[10px] font-black text-red-500 uppercase tracking-[0.2em] mb-3">Alasan Revisi & Perubahan *</label>
                    <textarea name="revision_reason" id="item_revision_reason" rows="3" required
                              class="w-full px-6 py-4 rounded-3xl border-2 border-red-100 focus:border-red-400 focus:ring-0 text-sm font-bold text-gray-900 transition-all bg-red-50/10 placeholder-red-200"
                              placeholder="Mengapa data barang atau harga diubah?"></textarea>
                    <p class="mt-3 text-[9px] text-gray-400 font-bold uppercase tracking-widest text-center">Setiap revisi akan disinkronasikan ke SPK dan memicu log audit.</p>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeEditItemModal()" class="flex-1 px-8 py-5 bg-gray-100 hover:bg-gray-200 text-gray-500 font-black uppercase tracking-widest text-xs rounded-3xl transition-all duration-300">Batal</button>
                    <button type="submit" class="flex-[2] px-8 py-5 bg-[#FFC232] hover:bg-[#FFC232]/90 text-gray-900 font-black uppercase tracking-widest text-xs rounded-3xl shadow-xl shadow-yellow-100 transition-all duration-300 transform hover:-translate-y-1">Update & Sync SPK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal() {
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Edit Item Modal
    function openEditItemModal(itemId, btn) {
        const item = JSON.parse(btn.getAttribute('data-item'));
        const modal = document.getElementById('editItemModal');
        const form = document.getElementById('editItemForm');
        const warning = document.getElementById('lockedItemWarning');
        const reasonField = document.getElementById('item_revision_reason');
        
        // Reset form action
        form.action = `/cs/items/${itemId}`;
        
        // Populate fields
        document.getElementById('item_category').value = item.category || '';
        document.getElementById('item_brand').value = item.shoe_brand || '';
        document.getElementById('item_type').value = item.shoe_type || '';
        document.getElementById('item_size').value = item.shoe_size || '';
        document.getElementById('item_color').value = item.shoe_color || '';
        document.getElementById('item_notes').value = item.item_notes || '';
        
        // Populate services checkboxes & details
        const itemServices = item.services || [];
        const itemServiceIds = itemServices.filter(s => s.id).map(s => String(s.id));
        const itemServiceDetails = {};
        itemServices.forEach(s => { if(s.id) itemServiceDetails[s.id] = s.manual_detail || ''; });

        document.querySelectorAll('.service-edit-checkbox').forEach(cb => {
            const isChecked = itemServiceIds.includes(String(cb.value));
            cb.checked = isChecked;
            
            // Handle Detail Input
            const container = document.getElementById(`detail_container_${cb.value}`);
            const input = document.getElementById(`service_detail_${cb.value}`);
            if (isChecked) {
                container.classList.remove('hidden');
                input.value = itemServiceDetails[cb.value] || '';
            } else {
                container.classList.add('hidden');
                input.value = '';
            }
            cb.setAttribute('onchange', 'toggleEditItemServiceDetail(this); calculateEditTotal();');
        });

        // Handle Custom Services
        const customContainer = document.getElementById('edit_custom_services_container');
        customContainer.innerHTML = '';
        itemServices.forEach(s => {
            if (!s.id) {
                addCustomServiceRow(s.name, s.price, s.manual_detail);
            }
        });
        
        calculateEditTotal();
        
        // Check locking status
        const isLocked = @json(in_array($lead->status, ['CONVERTED', 'LOST']));
        
        if (isLocked) {
            warning.classList.remove('hidden');
            reasonField.setAttribute('required', 'required');
        } else {
            warning.classList.add('hidden');
            reasonField.removeAttribute('required');
        }
        
        modal.classList.remove('hidden');
    }

    function addCustomServiceRow(name = '', price = 0, detail = '') {
        const container = document.getElementById('edit_custom_services_container');
        const rowId = 'custom_' + Date.now() + Math.random().toString(36).substr(2, 5);
        
        const html = `
            <div id="${rowId}" class="bg-blue-50/50 p-2 rounded-lg border border-blue-100">
                <div class="grid grid-cols-12 gap-2 mb-2">
                    <div class="col-span-11 grid grid-cols-2 gap-2">
                        <input type="text" name="custom_service_names[]" value="${name}" required class="px-2 py-1 text-[10px] rounded border-gray-200" placeholder="Nama Layanan Kustom">
                        <input type="number" name="custom_service_prices[]" value="${price}" required class="px-2 py-1 text-[10px] rounded border-gray-200 custom-price-input" placeholder="Harga" oninput="calculateEditTotal()">
                    </div>
                    <div class="col-span-1 flex items-center justify-center">
                        <button type="button" onclick="document.getElementById('${rowId}').remove(); calculateEditTotal()" class="text-red-400 hover:text-red-600">×</button>
                    </div>
                </div>
                <input type="text" name="custom_service_descriptions[]" value="${detail}" class="w-full px-2 py-1 text-[10px] rounded border-gray-200 bg-white/50" placeholder="Keterangan kustom...">
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function calculateEditTotal() {
        let total = 0;
        const modal = document.getElementById('editItemModal');
        // From checkboxes
        modal.querySelectorAll('.service-edit-checkbox:checked').forEach(cb => {
            total += parseFloat(cb.getAttribute('data-price')) || 0;
        });
        // From custom services
        modal.querySelectorAll('.custom-price-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('item_total_price').value = Math.round(total);
    }

    function toggleEditItemServiceDetail(cb) {
        const container = document.getElementById(`detail_container_${cb.value}`);
        const input = document.getElementById(`service_detail_${cb.value}`);
        if (cb.checked) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
            input.value = '';
        }
        calculateEditTotal();
    }

    function closeEditItemModal() {
        document.getElementById('editItemModal').classList.add('hidden');
    }

    // Activity Modal
    function openActivityModal() {
        document.getElementById('activityModal').classList.remove('hidden');
    }
    function closeActivityModal() {
        document.getElementById('activityModal').classList.add('hidden');
    }

    // Follow Up Modal
    function openFollowUpModal() {
        document.getElementById('followUpModal').classList.remove('hidden');
    }
    function closeFollowUpModal() {
        document.getElementById('followUpModal').classList.add('hidden');
    }

    // Quotation Modal
    function openQuotationModal() {
        document.getElementById('quotationModal').classList.remove('hidden');
    }
    function closeQuotationModal() {
        document.getElementById('quotationModal').classList.add('hidden');
    }

    function autoFillPrice(input) {
        const val = input.value;
        const options = document.getElementById('service-list').options;
        for (let i = 0; i < options.length; i++) {
            if (options[i].value === val) {
                const price = options[i].getAttribute('data-price');
                const row = input.closest('.quotation-item');
                row.querySelector('.service-price-input').value = price;
                break;
            }
        }
    }

    let itemIndex = 1;
    function addQuotationItem() {
        const container = document.getElementById('quotation-items');
        const newItem = `
            <div class="quotation-item border rounded p-3 bg-gray-50">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-5 relative">
                        <input type="text" name="items[${itemIndex}][service_name]" list="service-list" required class="service-name-input w-full px-2 py-1 border rounded text-sm" placeholder="Nama Service" onchange="autoFillPrice(this)">
                    </div>
                    <div class="col-span-2">
                        <input type="number" name="items[${itemIndex}][qty]" value="1" min="1" required class="w-full px-2 py-1 border rounded text-sm" placeholder="Qty">
                    </div>
                    <div class="col-span-4">
                        <input type="number" name="items[${itemIndex}][price]" required class="service-price-input w-full px-2 py-1 border rounded text-sm" placeholder="Harga">
                    </div>
                    <div class="col-span-1 flex items-center justify-center">
                        <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <input type="text" name="items[${itemIndex}][description]" class="w-full px-2 py-1 border rounded text-sm" placeholder="Deskripsi (opsional)">
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newItem);
        itemIndex++;
    }

    function removeItem(button) {
        const items = document.querySelectorAll('.quotation-item');
        if (items.length > 1) {
            button.closest('.quotation-item').remove();
        } else {
            alert('Minimal harus ada 1 item!');
        }
    }

    // Handover Modal
    function openHandoverModal() {
        document.getElementById('handoverModal').classList.remove('hidden');
    }
    function closeHandoverModal() {
        document.getElementById('handoverModal').classList.add('hidden');
    }

    // SPK Modal
    function openSpkModal() {
        document.getElementById('spkModal').classList.remove('hidden');
    }
    function closeSpkModal() {
        document.getElementById('spkModal').classList.add('hidden');
    }

    // Quick Actions
    function moveToKonsultasi() {
        const notes = prompt('Catatan untuk pindah ke Konsultasi:');
        if (notes !== null) {
            fetch("{{ route('cs.leads.move-konsultasi', $lead->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ notes: notes })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    }

    function moveToClosing() {
        if (confirm('Pindahkan lead ke Closing?')) {
            fetch("{{ route('cs.leads.move-closing', $lead->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    }

    function rejectQuotation(quotationId) {
        const reason = prompt('Alasan penolakan:');
        if (reason !== null && reason.trim() !== '') {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/cs/quotations/${quotationId}/reject`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';
            
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'rejection_reason';
            reasonInput.value = reason;
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            form.appendChild(reasonInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function markLost() {
        const reason = prompt('Alasan lead LOST:');
        if (reason !== null && reason.trim() !== '') {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('cs.leads.mark-lost', $lead->id) }}";
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'lost_reason';
            reasonInput.value = reason;
            
            form.appendChild(csrfInput);
            form.appendChild(reasonInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // SPK Modal Helper Functions
    function filterServices(itemId) {
        const input = document.getElementById(`search-services-${itemId}`);
        const filter = input.value.toLowerCase();
        const wrappers = document.querySelectorAll(`[class^="service-wrapper-${itemId}-"]`);
        
        wrappers.forEach(wrapper => {
            const label = wrapper.querySelector('label');
            const name = label.getAttribute('data-service-name');
            const cat = label.getAttribute('data-service-category');
            if (name.includes(filter) || cat.includes(filter)) {
                wrapper.classList.remove('hidden');
            } else {
                wrapper.classList.add('hidden');
            }
        });
    }

    function toggleServiceDetail(itemId, serviceId) {
        const cb = document.getElementById(`service-${itemId}-${serviceId}`);
        const detail = document.getElementById(`detail-${itemId}-${serviceId}`);
        if (cb.checked) {
            detail.classList.remove('hidden');
        } else {
            detail.classList.add('hidden');
            const input = document.getElementById(`detail-input-${itemId}-${serviceId}`);
            if(input) input.value = '';
        }
    }

    function updateItemTotal(itemId) {
        let total = 0;
        const modal = document.getElementById('spkModal');
        modal.querySelectorAll(`.service-checkbox[data-item-id="${itemId}"]:checked`).forEach(cb => {
            total += parseFloat(cb.getAttribute('data-price')) || 0;
        });

        const customItems = document.querySelectorAll(`#selected-list-${itemId} .custom-item-row`);
        customItems.forEach(row => {
            total += parseFloat(row.getAttribute('data-price')) || 0;
        });

        document.getElementById(`item-subtotal-${itemId}`).innerText = 'Rp ' + Math.round(total).toLocaleString('id-ID');
        updateGrandTotal();
    }

    function updateSelectedServices(itemId) {
        const list = document.getElementById(`selected-list-${itemId}`);
        const summaryContainer = document.getElementById(`selected-summary-${itemId}`);
        const checkedBoxes = document.querySelectorAll(`.service-checkbox[data-item-id="${itemId}"]:checked`);
        const customRows = list.querySelectorAll('.custom-item-row');
        
        if (checkedBoxes.length === 0 && customRows.length === 0) {
            summaryContainer.classList.add('hidden');
            return;
        }
        summaryContainer.classList.remove('hidden');
        
        // Collect custom rows HTML to preserve them
        let customHtml = '';
        customRows.forEach(row => {
            customHtml += row.outerHTML;
        });

        let html = '';
        checkedBoxes.forEach(cb => {
            const serviceId = cb.value;
            const name = cb.getAttribute('data-name');
            const price = parseFloat(cb.getAttribute('data-price')).toLocaleString('id-ID');
            const detailInput = document.getElementById(`detail-input-${itemId}-${serviceId}`);
            const detail = detailInput ? detailInput.value : '';
            
            html += `
                <div class="flex justify-between items-start p-3 bg-white/5 rounded-xl border border-gray-800">
                    <div>
                        <p class="text-[10px] font-black text-white uppercase">${name}</p>
                        ${detail ? `<p class="text-[9px] text-[#22AF85] italic mt-1">"${detail}"</p>` : ''}
                    </div>
                    <span class="text-[10px] font-black text-gray-400">Rp ${price}</span>
                </div>
            `;
        });
        
        list.innerHTML = html + customHtml;
    }

    function toggleCustomService(itemId) {
        const div = document.getElementById(`custom-service-${itemId}`);
        div.classList.toggle('hidden');
    }

    function addCustomService(itemId) {
        const nameInput = document.getElementById(`custom-name-${itemId}`);
        const priceInput = document.getElementById(`custom-price-${itemId}`);
        const descInput = document.getElementById(`custom-description-${itemId}`);
        
        const name = nameInput.value.trim();
        const price = parseFloat(priceInput.value) || 0;
        const desc = descInput.value.trim();
        
        if (!name || price <= 0) {
            alert('Nama dan Harga layanan kustom harus diisi!');
            return;
        }
        
        const list = document.getElementById(`selected-list-${itemId}`);
        const summaryContainer = document.getElementById(`selected-summary-${itemId}`);
        summaryContainer.classList.remove('hidden');
        
        const rowId = 'cs_' + Date.now();
        const rowHtml = `
            <div id="${rowId}" class="custom-item-row flex justify-between items-start p-3 bg-[#22AF85]/10 rounded-xl border border-[#22AF85]/20 group/row" data-price="${price}">
                <input type="hidden" name="items[${itemId}][custom_services][${rowId}][name]" value="${name}">
                <input type="hidden" name="items[${itemId}][custom_services][${rowId}][price]" value="${price}">
                <input type="hidden" name="items[${itemId}][custom_services][${rowId}][description]" value="${desc}">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#FFC232]"></span>
                        <p class="text-[10px] font-black text-white uppercase">${name}</p>
                    </div>
                    ${desc ? `<p class="text-[9px] text-[#22AF85] italic mt-1">"${desc}"</p>` : ''}
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-black text-[#22AF85]">Rp ${price.toLocaleString('id-ID')}</span>
                    <button type="button" onclick="document.getElementById('${rowId}').remove(); updateItemTotal(${itemId});" 
                            class="text-gray-500 hover:text-red-400 transition font-black text-lg">×</button>
                </div>
            </div>
        `;
        
        list.insertAdjacentHTML('beforeend', rowHtml);
        
        nameInput.value = '';
        priceInput.value = '';
        descInput.value = '';
        document.getElementById(`custom-service-${itemId}`).classList.add('hidden');
        
        updateItemTotal(itemId);
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        const modal = document.getElementById('spkModal');
        modal.querySelectorAll('.service-checkbox:checked').forEach(cb => {
            grandTotal += parseFloat(cb.getAttribute('data-price')) || 0;
        });
        
        modal.querySelectorAll('.custom-item-row').forEach(row => {
            grandTotal += parseFloat(row.getAttribute('data-price')) || 0;
        });
        
        document.getElementById('grand-total').innerText = 'Rp ' + Math.round(grandTotal).toLocaleString('id-ID');
        
        const dpSuggestion = Math.ceil(grandTotal * 0.3);
        document.getElementById('dp-suggestion').innerText = 'Rp ' + dpSuggestion.toLocaleString('id-ID');
        
        const dpInput = document.getElementById('dp-amount-input');
        if (parseFloat(dpInput.value) <= 0) {
            dpInput.value = dpSuggestion;
        }
    }

    // SPK Preview Logic
    function updateSpkPreview() {
        const deliverySelect = document.getElementById('deliveryTypeSelect');
        if (!deliverySelect) return;
        const deliveryCode = deliverySelect.options[deliverySelect.selectedIndex].getAttribute('data-code');
        const dateStr = "{{ date('ym-d') }}";
        const csCode = document.getElementById('manualCsInput').value.toUpperCase() || '??';
        
        const previewText = `${deliveryCode}-${dateStr}-XXXX-${csCode}`;
        document.getElementById('spkPreview').innerText = previewText;
    }

    // Promo Validation Logic
    async function validatePromo() {
        const codeInput = document.getElementById('promo-code-input');
        if (!codeInput) return;
        const code = codeInput.value.trim();
        const statusDiv = document.getElementById('promo-status');
        const messageP = document.getElementById('promo-message');
        const btnApply = document.getElementById('btn-apply-promo');

        if (!code) {
            alert('Masukkan kode promo!');
            return;
        }

        btnApply.disabled = true;
        btnApply.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Checking...';
        statusDiv.classList.add('hidden');

        try {
            const checkboxes = document.querySelectorAll('.service-checkbox:checked');
            let subtotal = 0;
            let serviceIds = [];
            checkboxes.forEach(cb => {
                subtotal += parseFloat(cb.dataset.price) || 0;
                if (cb.dataset.serviceId) {
                    serviceIds.push(cb.dataset.serviceId);
                }
            });

            if (subtotal <= 0) {
                alert('Pilih setidaknya satu layanan terlebih dahulu!');
                btnApply.disabled = false;
                btnApply.innerText = 'Apply';
                return;
            }

            const response = await fetch('/api/cs/promos/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    code: code,
                    total_amount: subtotal,
                    service_ids: serviceIds
                })
            });

            const result = await response.json();
            statusDiv.classList.remove('hidden');
            if (result.valid) {
                messageP.className = 'text-xs font-semibold text-green-600';
                messageP.innerHTML = `✅ Promo Berhasil! Diskon: <strong>Rp ${result.discount.toLocaleString('id-ID')}</strong>`;
                codeInput.classList.add('border-green-500');
                codeInput.classList.remove('border-red-500');
                const grandTotal = subtotal - result.discount;
                document.getElementById('grand-total').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
                const dpSuggestion = Math.ceil(grandTotal * 0.3);
                document.getElementById('dp-suggestion').textContent = 'Saran (30%): Rp ' + dpSuggestion.toLocaleString('id-ID');
                document.getElementById('dp-amount-input').value = dpSuggestion;
            } else {
                messageP.className = 'text-xs font-semibold text-red-600';
                messageP.innerText = '❌ ' + result.message;
                codeInput.classList.add('border-red-500');
                codeInput.classList.remove('border-green-500');
                document.getElementById('grand-total').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            }
        } catch (error) {
            console.error('Error validating promo:', error);
            alert('Gagal memvalidasi promo. Silakan coba lagi.');
        } finally {
            btnApply.disabled = false;
            btnApply.innerText = 'Apply';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('deliveryTypeSelect')) {
            updateSpkPreview();
        }
    });
</script>
</x-app-layout>
