<x-app-layout>
    <div class="min-h-screen bg-[#F8FAFC]" x-data="{ 
        showVerifyModal: false, 
        selectedPayment: {}, 
        editAmount: 0, 
        editDate: '', 
        editMethod: '',
        editNotes: '',
        openVerifyModal(payment) {
            this.selectedPayment = payment;
            this.editAmount = payment.amount_total;
            // Format date to YYYY-MM-DD for input[type=date]
            let d = new Date(payment.paid_at);
            this.editDate = d.toISOString().split('T')[0];
            this.editMethod = payment.payment_method;
            this.editNotes = payment.notes;
            this.showVerifyModal = true;
        }
    }">
        {{-- Elite Premium Header --}}
        <div class="bg-white/90 shadow-2xl border-b border-gray-100 sticky top-0 z-40 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 sm:py-8">
                <div class="flex flex-col gap-5 sm:gap-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 sm:gap-6">
                            <div class="p-2.5 sm:p-4 bg-orange-500 rounded-xl sm:rounded-[1.5rem] shadow-[0_10px_30px_-10px_rgba(249,115,22,0.5)] transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                                <svg class="w-5 h-5 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 sm:gap-3 mb-0.5 sm:mb-1">
                                    <span class="text-[8px] sm:text-[10px] font-black bg-orange-50 text-orange-600 px-1.5 sm:px-2 py-0.5 rounded-md uppercase tracking-widest italic border border-orange-100 animate-pulse">AWAITING VERIFICATION</span>
                                    <h1 class="text-xl sm:text-4xl font-black text-gray-900 tracking-tighter leading-none italic uppercase">Audit Pembayaran CS</h1>
                                </div>
                                <p class="text-gray-400 text-[9px] sm:text-[11px] font-black uppercase tracking-[0.2em] sm:tracking-[0.3em] italic opacity-70 hidden sm:block">Verifikasi Uang Masuk dari Tim Customer Service</p>
                            </div>
                        </div>

                        {{-- Tab Navigation --}}
                        <div class="flex bg-gray-100 p-1 rounded-2xl border border-gray-200 shadow-inner">
                            <a href="{{ route('finance.cs-verification') }}" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest bg-white text-orange-600 rounded-xl shadow-md border border-orange-100 italic">Pending</a>
                            <a href="{{ route('finance.cs-verification.history') }}" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 transition-all italic">History</a>
                        </div>
                    </div>

                    {{-- Search Form --}}
                    <form action="{{ route('finance.cs-verification') }}" method="GET" class="flex items-center gap-3">
                        <div class="relative group/search flex-1">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}" 
                                   placeholder="Cari SPK atau Nama Pelanggan..." 
                                   class="w-full pl-12 pr-6 py-3 sm:py-4 bg-gray-50 border-2 border-transparent rounded-xl sm:rounded-[2rem] focus:bg-white focus:border-orange-500/20 focus:ring-4 focus:ring-orange-500/5 text-sm font-black italic tracking-tight placeholder-gray-300 transition-all duration-500 shadow-inner">
                            <svg class="w-5 h-5 text-gray-300 absolute left-4 sm:left-6 top-1/2 -translate-y-1/2 group-focus-within/search:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <button type="submit" class="px-8 py-4 bg-gray-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl active:scale-95">Filter</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Main Content Section --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-12">
            
            <div class="grid grid-cols-1 gap-6">
                @forelse($payments as $payment)
                    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden group hover:border-orange-500/30 transition-all duration-500">
                        <div class="flex flex-col lg:flex-row">
                            {{-- Evidence Section --}}
                            <div class="lg:w-64 bg-gray-50 relative overflow-hidden group/img">
                                @if($payment->proof_image)
                                    <img src="{{ asset($payment->proof_image) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover/img:scale-110">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                        <button onclick="showProofLightbox('{{ asset($payment->proof_image) }}')" class="p-3 bg-white rounded-full text-gray-900 shadow-2xl transform translate-y-4 group-hover/img:translate-y-0 transition-transform">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </button>
                                    </div>
                                @else
                                    <div class="w-full h-48 lg:h-full flex flex-col items-center justify-center text-gray-300">
                                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-[9px] font-black uppercase tracking-widest">No Evidence</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Info Section --}}
                            <div class="flex-1 p-8 lg:p-10 flex flex-col justify-between">
                                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 mb-8">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="px-2 py-1 bg-gray-900 text-white text-[9px] font-black rounded-lg uppercase tracking-widest italic">{{ $payment->payment_method }}</span>
                                            <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">TRX-{{ $payment->id }}</span>
                                        </div>
                                        <h2 class="text-3xl font-black text-gray-900 tracking-tighter italic uppercase leading-none mb-2">
                                            @if(strtolower($payment->type) === 'before')
                                                DP dari CS
                                            @else
                                                Lunas di Awal
                                            @endif
                                        </h2>
                                        <div class="flex items-center gap-4 text-[11px] font-black text-gray-400 uppercase tracking-widest italic">
                                            <span>📅 {{ $payment->paid_at->format('d M Y • H:i') }}</span>
                                            <span class="text-gray-200">/</span>
                                            <span class="text-orange-500">PIC: {{ $payment->pic->name ?? 'CS' }}</span>
                                        </div>
                                    </div>

                                    <div class="text-left lg:text-right">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">NOMINAL SETORAN</p>
                                        <p class="text-4xl font-black text-gray-900 italic tracking-tighter leading-none">Rp {{ number_format($payment->amount_total, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-8 border-t border-gray-50">
                                    <div class="space-y-1">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">NOMOR SPK</p>
                                        <a href="{{ route('finance.show', $payment->work_order_id) }}" class="text-sm font-black text-[#1B8A68] hover:underline italic">{{ $payment->spk_number_snapshot }}</a>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">PELANGGAN</p>
                                        <p class="text-sm font-black text-gray-900 italic">{{ $payment->customer_name_snapshot }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">CATATAN CS</p>
                                        <p class="text-xs text-gray-500 font-medium italic">"{{ $payment->notes }}"</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Section --}}
                            <div class="lg:w-64 bg-gray-50 p-8 flex flex-col items-center justify-center border-l border-gray-100">
                                <button type="button" 
                                        @click="openVerifyModal({{ json_encode($payment) }})"
                                        class="w-full group/btn relative inline-flex flex-col items-center gap-3 px-8 py-10 bg-white hover:bg-orange-500 border-2 border-orange-500/20 hover:border-orange-500 rounded-3xl transition-all duration-500 shadow-xl shadow-gray-200/50 hover:shadow-orange-200 active:scale-95">
                                    <div class="w-12 h-12 rounded-full bg-orange-50 text-orange-500 group-hover/btn:bg-white group-hover/btn:scale-110 transition-all flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="text-[11px] font-black text-orange-600 group-hover/btn:text-white uppercase tracking-[0.2em] italic">VERIFIKASI</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-[3rem] p-24 text-center border-2 border-dashed border-gray-100 shadow-inner">
                        <div class="w-32 h-32 bg-gray-50 rounded-[2.5rem] flex items-center justify-center text-6xl mb-8 mx-auto grayscale opacity-20 filter">🛡️</div>
                        <h3 class="text-3xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic leading-none">Semua Beres!</h3>
                        <p class="text-gray-400 text-[11px] font-black uppercase tracking-[0.3em] italic opacity-60">Tidak ada pembayaran CS yang menunggu verifikasi.</p>
                    </div>
                @endforelse

                @if($payments->hasPages())
                    <div class="py-12 flex justify-center">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Verification Modal --}}
        <div x-show="showVerifyModal" 
             class="fixed inset-0 z-[100] overflow-y-auto" 
             x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showVerifyModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 transition-opacity bg-gray-900/90 backdrop-blur-sm" 
                     @click="showVerifyModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="showVerifyModal" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                    
                    <form :action="'{{ route('finance.verify-order-payment', ['id' => ':id']) }}'.replace(':id', selectedPayment.id)" method="POST">
                        @csrf
                        <div class="bg-white px-8 pt-8 pb-6">
                            <div class="flex items-center justify-between mb-8">
                                <div>
                                    <h3 class="text-2xl font-black text-gray-900 tracking-tighter italic uppercase">Audit Verifikasi CS</h3>
                                    <p class="text-xs text-gray-500 font-bold tracking-widest uppercase mt-1">Konfirmasi Mutasi & Catatan</p>
                                </div>
                                <button type="button" @click="showVerifyModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>

                            <div class="space-y-6">
                                {{-- Nominal --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nominal Setoran (Koreksi jika perlu)</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-black italic">Rp</span>
                                        <input type="number" name="amount_total" x-model="editAmount" required
                                               class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-orange-500 focus:ring-0 transition-all text-xl font-black text-gray-900 italic">
                                    </div>
                                </div>

                                 {{-- Tanggal --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Bayar Sesuai Mutasi</label>
                                    <input type="date" name="paid_at" x-model="editDate" required
                                           class="w-full px-4 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-orange-500 focus:ring-0 transition-all font-bold text-gray-900">
                                </div>

                                {{-- Metode Pembayaran --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Metode Pembayaran (Dropdown Koreksi)</label>
                                    <select name="payment_method" x-model="editMethod" required
                                            class="w-full px-4 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-orange-500 focus:ring-0 transition-all font-bold text-gray-900 appearance-none">
                                        <option value="BCA">Transfer BCA</option>
                                        <option value="MANDIRI">Transfer Mandiri</option>
                                        <option value="QRIS">QRIS</option>
                                        <option value="TUNAI">Tunai / Cash</option>
                                        <option value="EDC">Mesin EDC</option>
                                    </select>
                                </div>

                                {{-- Catatan --}}
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Catatan Verifikasi Finance</label>
                                    <textarea name="notes" x-model="editNotes" rows="3"
                                              class="w-full px-4 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-orange-500 focus:ring-0 transition-all font-bold text-gray-900"
                                              placeholder="Contoh: Sudah masuk mutasi BCA jam 10 pagi..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-8 py-6 flex flex-col gap-3">
                            <button type="submit" 
                                    class="w-full bg-gray-900 hover:bg-orange-600 text-white font-black italic py-4 rounded-2xl shadow-lg transition-all tracking-tighter uppercase flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Konfirmasi & Verifikasi Uang
                            </button>
                            <button type="button" @click="showVerifyModal = false"
                                    class="w-full bg-white text-gray-500 font-black py-4 rounded-2xl hover:bg-gray-100 transition-all tracking-tighter uppercase text-sm italic">
                                Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Evidence Lightbox --}}
    <div id="proofLightbox" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/95 backdrop-blur-2xl transition-all duration-500 opacity-0 scale-95" onclick="closeProofLightbox()">
        <div class="max-w-4xl w-full h-full flex flex-col items-center justify-center gap-8" onclick="event.stopPropagation()">
            <img id="lightboxImage" src="" class="max-w-full max-h-[80vh] rounded-[2rem] shadow-[0_0_80px_rgba(255,255,255,0.1)] border-4 border-white/10 object-contain">
            <button onclick="closeProofLightbox()" class="px-12 py-5 bg-white text-gray-900 rounded-[2rem] font-black text-xs uppercase tracking-widest hover:bg-orange-500 hover:text-white transition-all shadow-2xl active:scale-95">Tutup Preview</button>
        </div>
    </div>

    @push('scripts')
    <script>
        function showProofLightbox(src) {
            const lightbox = document.getElementById('proofLightbox');
            const img = document.getElementById('lightboxImage');
            img.src = src;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            setTimeout(() => {
                lightbox.classList.remove('opacity-0', 'scale-95');
            }, 10);
        }

        function closeProofLightbox() {
            const lightbox = document.getElementById('proofLightbox');
            lightbox.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                lightbox.classList.add('hidden');
                lightbox.classList.remove('flex');
            }, 500);
        }
    </script>
    @endpush
</x-app-layout>
