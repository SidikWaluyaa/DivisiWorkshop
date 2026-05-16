<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC]">
        {{-- Premium Header --}}
        <div class="bg-white/90 shadow-2xl border-b border-gray-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="flex items-center gap-6">
                    <div class="p-4 bg-purple-600 rounded-[1.5rem] shadow-[0_10px_30px_-10px_rgba(147,51,234,0.5)] transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="text-[10px] font-black bg-purple-50 text-purple-600 px-2 py-0.5 rounded-md uppercase tracking-widest italic border border-purple-100">REKONSILIASI</span>
                            <h1 class="text-4xl font-black text-gray-900 tracking-tighter leading-none italic uppercase">Verifikasi Mutasi</h1>
                        </div>
                        <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-70">Cocokkan Pembayaran Manual Dengan Mutasi Bank</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="max-w-7xl mx-auto px-6 pt-6">
            <div class="bg-emerald-50 border-2 border-emerald-200 text-[#1B8A68] px-6 py-4 rounded-2xl font-black text-sm italic flex items-center gap-3 shadow-lg shadow-emerald-50">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        </div>
        @endif
        @if(session('error'))
        <div class="max-w-7xl mx-auto px-6 pt-6">
            <div class="bg-red-50 border-2 border-red-200 text-red-700 px-6 py-4 rounded-2xl font-black text-sm italic flex items-center gap-3 shadow-lg shadow-red-50">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
        </div>
        @endif

        {{-- Legend --}}
        <div class="max-w-7xl mx-auto px-6 pt-8">
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                    <span class="text-[10px] font-black text-gray-600 uppercase tracking-wider italic">Exact Match — Auto</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <span class="text-[10px] font-black text-gray-600 uppercase tracking-wider italic">Invoice Sama, Nominal Beda — Pilih Manual</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <span class="w-3 h-3 rounded-full bg-gray-300"></span>
                    <span class="text-[10px] font-black text-gray-600 uppercase tracking-wider italic">Tidak Ada Mutasi — Belum Bisa Verifikasi</span>
                </div>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="max-w-7xl mx-auto px-6 pt-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 bg-white p-2 rounded-[2rem] shadow-sm border border-gray-100">
                {{-- Tabs --}}
                <div class="flex items-center gap-1 p-1 bg-gray-50 rounded-[1.8rem] w-full md:w-auto">
                    <a href="{{ route('finance.verifications.index', ['tab' => 'candidates']) }}" class="flex-1 md:flex-none px-8 py-3 rounded-[1.5rem] text-[11px] font-black uppercase tracking-widest italic transition-all {{ $tab === 'candidates' ? 'bg-white text-purple-600 shadow-sm border border-gray-100' : 'text-gray-400 hover:text-gray-600' }}">
                        Menunggu ({{ $candidatesCount ?? 0 }})
                    </a>
                    <a href="{{ route('finance.verifications.index', ['tab' => 'history']) }}" class="flex-1 md:flex-none px-8 py-3 rounded-[1.5rem] text-[11px] font-black uppercase tracking-widest italic transition-all {{ $tab === 'history' ? 'bg-white text-purple-600 shadow-sm border border-gray-100' : 'text-gray-400 hover:text-gray-600' }}">
                        Riwayat
                    </a>
                </div>

                <form action="{{ route('finance.verifications.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto pr-4">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    
                    @if($tab === 'candidates')
                    <select name="match_type" onchange="this.form.submit()" class="w-full md:w-auto px-5 py-3 bg-transparent border-none text-[11px] font-black uppercase tracking-wider italic text-gray-600 focus:ring-0 cursor-pointer">
                        <option value="">Semua Tipe Match</option>
                        <option value="exact" {{ request('match_type') === 'exact' ? 'selected' : '' }}>🟣 Exact Match</option>
                        <option value="partial" {{ request('match_type') === 'partial' ? 'selected' : '' }}>🟡 Partial Match</option>
                        <option value="none" {{ request('match_type') === 'none' ? 'selected' : '' }}>⚪ Tidak Ada Match</option>
                    </select>
                    @endif

                    <div class="relative w-full md:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Invoice..." class="w-full pl-10 pr-4 py-3 bg-gray-50 border-none rounded-xl text-sm font-bold italic text-gray-700 focus:ring-0 placeholder-gray-400">
                        <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </form>
            </div>
        </div>

        {{-- Verification Content --}}
        <div class="max-w-7xl mx-auto px-6 py-6 pb-20">
            @if($tab === 'candidates')
                {{-- CANDIDATES TAB --}}
                <div class="space-y-6">
                    @forelse($candidates as $candidate)
                        @php
                            $payment = $candidate['payment'];
                            $matchType = $candidate['match_type'];
                            $mutation = $candidate['mutation'];
                            $partialMutations = $candidate['partial_mutations'];

                            $borderColor = match($matchType) {
                                'exact' => 'border-l-purple-500 bg-purple-50/10',
                                'partial' => 'border-l-amber-500 bg-amber-50/10',
                                default => 'border-l-gray-300',
                            };
                        @endphp
                        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 border-l-4 {{ $borderColor }} shadow-2xl overflow-hidden relative group hover:-translate-y-1 transition-all duration-500">
                            <div class="flex flex-col lg:flex-row gap-8">
                                {{-- LEFT: Payment Info --}}
                                <div class="flex-1">
                                    <div class="flex items-center gap-4 mb-5">
                                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-inner {{ $matchType === 'exact' ? 'bg-purple-100 text-purple-600' : ($matchType === 'partial' ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-400') }}">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-gray-900 italic uppercase tracking-tighter">{{ $payment->invoice->invoice_number ?? '-' }}</div>
                                            <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic opacity-60">{{ $payment->invoice->customer->name ?? '-' }}</div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-3 gap-4 p-5 bg-gray-50 rounded-2xl border border-gray-100">
                                        <div>
                                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider italic block mb-1">Pembayaran</span>
                                            <span class="text-lg font-black text-gray-900 italic tabular-nums tracking-tighter">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider italic block mb-1">Tanggal</span>
                                            <span class="text-sm font-black text-gray-700 italic">{{ $payment->payment_date->format('d M Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-wider italic block mb-1">Oleh</span>
                                            <span class="text-sm font-black text-gray-700 italic">{{ $payment->creator->name ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- DIVIDER --}}
                                <div class="hidden lg:flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                    </div>
                                </div>

                                {{-- RIGHT: Mutation Match --}}
                                <div class="flex-1">
                                    @if($matchType === 'exact')
                                        {{-- EXACT MATCH --}}
                                        <div class="p-6 bg-purple-50 rounded-2xl border-2 border-purple-200 mb-4">
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                                                <span class="text-[10px] font-black text-purple-700 uppercase tracking-[0.2em] italic">Exact Match!</span>
                                            </div>
                                            <div class="space-y-2">
                                                <div class="flex justify-between"><span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">Mutasi</span><span class="text-sm font-black text-purple-700 italic tabular-nums">Rp {{ number_format($mutation->amount, 0, ',', '.') }}</span></div>
                                                <div class="flex justify-between"><span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">Bank</span><span class="text-xs font-black text-gray-700 italic">{{ $mutation->bank_code ?: '-' }}</span></div>
                                                <div class="flex justify-between"><span class="text-[10px] text-gray-500 font-black italic uppercase tracking-wider">Tanggal</span><span class="text-xs font-black text-gray-700 italic">{{ $mutation->transaction_date->format('d M Y') }}</span></div>
                                            </div>
                                        </div>
                                        <form action="{{ route('finance.verifications.verify', $payment->id) }}" method="POST" onsubmit="return confirm('Verifikasi pembayaran ini?')">
                                            @csrf
                                            <input type="hidden" name="mutation_id" value="{{ $mutation->id }}">
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.15em] italic shadow-xl shadow-purple-100 transition-all hover:-translate-y-0.5 active:scale-95">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                                Verify (Auto Match)
                                            </button>
                                        </form>

                                    @elseif($matchType === 'partial')
                                        {{-- PARTIAL MATCH — Same invoice, different amount --}}
                                        <div class="p-6 bg-amber-50 rounded-2xl border-2 border-amber-200 mb-4">
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                                <span class="text-[10px] font-black text-amber-700 uppercase tracking-[0.2em] italic">Invoice Cocok — Nominal Beda</span>
                                            </div>
                                            <p class="text-[10px] text-amber-800/70 italic font-bold leading-relaxed">Pilih mutasi yang sesuai. Selisih nominal akan dicatat.</p>
                                        </div>

                                        <form action="{{ route('finance.verifications.verify', $payment->id) }}" method="POST" onsubmit="return confirm('Nominal berbeda! Yakin ingin verifikasi?')">
                                            @csrf
                                            <div class="mb-4">
                                                <select name="mutation_id" required class="w-full px-5 py-4 bg-white border-2 border-amber-200 rounded-xl text-xs font-black italic tracking-tight text-gray-700 focus:border-amber-400 focus:ring-4 focus:ring-amber-500/5 outline-none appearance-none">
                                                    <option value="">— Pilih mutasi —</option>
                                                    @foreach($partialMutations as $pm)
                                                        @php $selisih = abs((float)$payment->amount - (float)$pm->amount); @endphp
                                                        <option value="{{ $pm->id }}">
                                                            Rp {{ number_format($pm->amount, 0, ',', '.') }} • {{ $pm->transaction_date->format('d/m/Y') }} • {{ $pm->bank_code ?: '-' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.15em] italic shadow-xl shadow-amber-100 transition-all hover:-translate-y-0.5 active:scale-95">
                                                Verify Manual
                                            </button>
                                        </form>

                                    @else
                                        {{-- NO MATCH --}}
                                        <div class="p-6 bg-gray-50 rounded-2xl border-2 border-gray-100 h-full flex flex-col items-center justify-center text-center">
                                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-3xl mb-4 shadow-sm">🔍</div>
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] italic block mb-2">No Match</span>
                                            <p class="text-[10px] text-gray-400 italic font-bold leading-relaxed max-w-[250px]">Import mutasi <strong class="text-gray-600">{{ $payment->invoice->invoice_number ?? '-' }}</strong> terlebih dahulu.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden px-10 py-40 text-center">
                            <div class="w-32 h-32 bg-[#F8FAFC] rounded-[2.5rem] flex items-center justify-center text-6xl mb-8 shadow-inner border border-gray-100 mx-auto filter grayscale opacity-20">🛡️</div>
                            <h3 class="text-3xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">Semua Terverifikasi</h3>
                            <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-60">Tidak ada pembayaran yang menunggu verifikasi</p>
                        </div>
                    @endforelse
                </div>
            @else
                {{-- HISTORY TAB --}}
                <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden relative">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#F8FAFC] border-b border-gray-100">
                                    <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Invoice & Pelanggan</th>
                                    <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Nominal Verifikasi</th>
                                    <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Data Mutasi</th>
                                    <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic">Diverifikasi Oleh</th>
                                    <th class="px-10 py-8 text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($history as $v)
                                    <tr class="hover:bg-[#F8FAFC] transition-all duration-300">
                                        <td class="px-10 py-6">
                                            <div class="text-sm font-black text-gray-900 italic uppercase tracking-tighter">{{ $v->payment->invoice->invoice_number ?? '-' }}</div>
                                            <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest italic opacity-60">{{ $v->payment->invoice->customer->name ?? '-' }}</div>
                                        </td>
                                        <td class="px-10 py-6">
                                            <div class="text-lg font-black text-[#1B8A68] italic tabular-nums tracking-tighter">Rp {{ number_format($v->payment->amount, 0, ',', '.') }}</div>
                                            @if($v->notes)
                                                <div class="text-[9px] text-amber-600 font-bold italic mt-1">{{ $v->notes }}</div>
                                            @endif
                                        </td>
                                        <td class="px-10 py-6">
                                            <div class="text-xs font-black text-gray-700 italic">{{ $v->mutation->bank_code }} • {{ $v->mutation->transaction_date->format('d/m/Y') }}</div>
                                            <div class="text-[9px] text-gray-400 font-bold italic mt-1 truncate max-w-[150px]">{{ $v->mutation->description }}</div>
                                        </td>
                                        <td class="px-10 py-6">
                                            <div class="text-xs font-black text-gray-700 italic">{{ $v->verifier->name ?? '-' }}</div>
                                            <div class="text-[9px] text-gray-400 font-bold italic mt-1">{{ $v->verified_at->format('d/m/Y H:i') }}</div>
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            <form action="{{ route('finance.verifications.unverify', $v->id) }}" method="POST" onsubmit="return confirm('PERHATIAN: Membatalkan verifikasi akan membebaskan mutasi bank dan mengubah status invoice kembali menjadi belum lunas. Lanjutkan?')">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl font-black text-[10px] uppercase tracking-widest italic transition-all border border-red-100">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Batalkan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-10 py-20 text-center">
                                            <div class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-60">Belum ada riwayat verifikasi</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($history->hasPages())
                        <div class="px-10 py-10 border-t border-gray-50 bg-[#F8FAFC]/50">
                            {{ $history->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
