<div class="space-y-6" x-data="{ fullImage: null }">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[10px] font-black uppercase tracking-widest">
                    Finance Module
                </span>
                <span class="text-xs text-gray-400 font-medium">Customer Direct Upload</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight mt-1">
                Verifikasi Bukti Bayar Customer
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Review dan validasi bukti transfer mandiri yang diunggah customer via scan QR Invoice.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ url('/konfirmasi-pembayaran') }}" target="_blank"
               class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 active:scale-95 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Buka Form Customer
            </a>
        </div>
    </div>

    {{-- Filter & Tab Bar --}}
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        {{-- Tabs --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
            <button type="button" 
                    wire:click="setTab('pending')"
                    class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer
                           {{ $activeTab === 'pending' ? 'bg-[#F5C518] text-slate-950 shadow-md shadow-amber-500/20' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                <span>Menunggu Verifikasi</span>
                @if($pendingCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'pending' ? 'bg-slate-950 text-[#F5C518]' : 'bg-amber-500 text-white animate-pulse' }}">
                        {{ $pendingCount }}
                    </span>
                @endif
            </button>

            <button type="button" 
                    wire:click="setTab('verified')"
                    class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer
                           {{ $activeTab === 'verified' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                <span>Terverifikasi (Lolos)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'verified' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300' }}">
                    {{ $verifiedCount }}
                </span>
            </button>

            <button type="button" 
                    wire:click="setTab('rejected')"
                    class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 cursor-pointer
                           {{ $activeTab === 'rejected' ? 'bg-rose-600 text-white shadow-md shadow-rose-600/20' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200' }}">
                <span>Ditolak</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $activeTab === 'rejected' ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300' }}">
                    {{ $rejectedCount }}
                </span>
            </button>
        </div>

        {{-- Search Input --}}
        <div class="relative w-full sm:w-72">
            <input type="text" 
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari Invoice, SPK, Customer..."
                   class="w-full text-xs bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl pl-9 pr-4 py-2.5 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-[#F5C518]/20 focus:border-[#F5C518] outline-none">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    {{-- Table of Payments --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-900/60 border-b border-gray-100 dark:border-gray-700 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400">
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Bukti Transfer</th>
                        <th class="py-3.5 px-4">Invoice & SPK</th>
                        <th class="py-3.5 px-4">Pelanggan</th>
                        <th class="py-3.5 px-4">Nominal Transfer</th>
                        <th class="py-3.5 px-4">Rekening Tujuan</th>
                        <th class="py-3.5 px-4">Status & Waktu</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                    @forelse($payments as $index => $pay)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-750/50 transition-colors">
                            {{-- No --}}
                            <td class="py-4 px-4 text-center font-bold text-gray-400">
                                {{ $payments->firstItem() + $index }}
                            </td>

                            {{-- Bukti Struk Thumbnail --}}
                            <td class="py-4 px-4">
                                @if($pay->proof_image)
                                    <div class="relative group cursor-pointer w-16 h-16 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm"
                                         @click="fullImage = '{{ Storage::url($pay->proof_image) }}'">
                                        <img src="{{ Storage::url($pay->proof_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-200">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-[10px] font-bold">
                                            🔍 Zoom
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-[10px]">Tanpa Foto</span>
                                @endif
                            </td>

                            {{-- Invoice & SPK --}}
                            <td class="py-4 px-4 space-y-1">
                                @if($pay->invoice)
                                    <a href="{{ route('finance.invoices.show', $pay->invoice->id) }}" target="_blank"
                                       class="font-black text-blue-600 dark:text-blue-400 hover:underline uppercase block text-xs">
                                        {{ $pay->invoice->invoice_number }} ↗
                                    </a>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 block font-mono">
                                        {{ $pay->spk_number_snapshot }}
                                    </span>
                                @else
                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $pay->spk_number_snapshot }}</span>
                                @endif
                            </td>

                            {{-- Pelanggan --}}
                            <td class="py-4 px-4">
                                <span class="font-bold text-gray-800 dark:text-gray-200 block">
                                    {{ $pay->customer_name_snapshot ?? ($pay->invoice->customer->name ?? '-') }}
                                </span>
                                <span class="text-[10px] text-gray-400 block">
                                    {{ $pay->customer_phone_snapshot ?? ($pay->invoice->customer->phone ?? '-') }}
                                </span>
                            </td>

                            {{-- Nominal --}}
                            <td class="py-4 px-4">
                                <span class="font-black text-emerald-600 dark:text-emerald-400 text-sm block">
                                    Rp {{ number_format($pay->amount_total, 0, ',', '.') }}
                                </span>
                                @if($pay->invoice)
                                    <span class="text-[10px] text-gray-400 block">
                                        Total Tagihan: Rp {{ number_format($pay->invoice->total_amount, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Rekening Tujuan --}}
                            <td class="py-4 px-4">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase 
                                      {{ $pay->payment_method === 'BCA' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                    {{ $pay->payment_method }}
                                </span>
                            </td>

                            {{-- Status & Waktu --}}
                            <td class="py-4 px-4 space-y-1">
                                @if(str_contains($pay->notes, '[DITOLAK FINANCE'))
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 rounded text-[10px] font-black uppercase block w-fit">
                                        Ditolak
                                    </span>
                                @elseif($pay->is_verified)
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded text-[10px] font-black uppercase block w-fit">
                                        ✓ Terverifikasi
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 rounded text-[10px] font-black uppercase animate-pulse block w-fit">
                                        Menunggu
                                    </span>
                                @endif
                                <span class="text-[10px] text-gray-400 block">
                                    {{ $pay->paid_at ? $pay->paid_at->format('d/m/Y H:i') : '-' }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="py-4 px-4 text-right">
                                @if(!$pay->is_verified && !str_contains($pay->notes, '[DITOLAK FINANCE'))
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" 
                                                wire:click="approvePayment({{ $pay->id }})"
                                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[11px] rounded-lg transition-all shadow-sm active:scale-95 flex items-center gap-1 cursor-pointer">
                                            ✓ Terima
                                        </button>
                                        <button type="button" 
                                                wire:click="openRejectModal({{ $pay->id }})"
                                                class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] rounded-lg transition-all shadow-sm active:scale-95 cursor-pointer">
                                            ✕ Tolak
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-[10px] italic">
                                        {{ $pay->pic->name ?? '-' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400 text-xs">
                                Tidak ada data bukti pembayaran pada tab ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    {{-- Fullscreen Image Preview Lightbox --}}
    <div x-show="fullImage" 
         x-cloak 
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="fullImage = null"
         @keydown.escape.window="fullImage = null">
        <div class="relative max-w-3xl max-h-[90vh] bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-700">
            <button type="button" 
                    @click="fullImage = null"
                    class="absolute top-3 right-3 p-2 bg-black/60 text-white rounded-full hover:bg-black transition-colors z-10">
                ✕
            </button>
            <img :src="fullImage" class="max-w-full max-h-[85vh] object-contain mx-auto">
        </div>
    </div>

    {{-- Reject Reason Modal --}}
    @if($rejectModalOpen)
        <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full shadow-2xl border border-gray-200 dark:border-gray-700 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3">
                    <h3 class="font-black text-sm uppercase tracking-wider text-rose-600">Tolak Bukti Pembayaran</h3>
                    <button type="button" wire:click="closeRejectModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model.defer="rejectReason" 
                              rows="3" 
                              placeholder="Misal: Nominal tidak sesuai dengan mutasi, struk buram/tidak terbaca..."
                              class="w-full text-xs p-3 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none"></textarea>
                    @error('rejectReason') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeRejectModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold">
                        Batal
                    </button>
                    <button type="button" wire:click="confirmRejectPayment" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black uppercase tracking-wider">
                        Konfirmasi Tolak
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
