<div class="container mx-auto px-4 py-8 sm:py-12 max-w-2xl" 
     x-data="{
         scanMode: 'file', // 'camera' or 'file'
         cameraActive: false,
         html5QrCode: null,
         fileName: '',
         previewUrl: null,
         rawAmount: @entangle('amount'),
         formattedAmount: '',
         isScanning: false,

         formatRupiah(val) {
             let numberString = val.replace(/[^,\d]/g, '').toString();
             let split = numberString.split(',');
             let sisa = split[0].length % 3;
             let rupiah = split[0].substr(0, sisa);
             let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

             if (ribuan) {
                 let separator = sisa ? '.' : '';
                 rupiah += separator + ribuan.join('.');
             }

             rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
             return rupiah ? 'Rp ' + rupiah : '';
         },

         onAmountInput(e) {
             let clean = e.target.value.replace(/[^0-9]/g, '');
             this.rawAmount = clean;
             this.formattedAmount = clean ? this.formatRupiah(clean) : '';
         },

         startCamera() {
             this.scanMode = 'camera';
             this.cameraActive = true;
             this.$nextTick(() => {
                 if (!this.html5QrCode) {
                     this.html5QrCode = new Html5Qrcode('qr-reader-portal');
                 }
                 const config = { fps: 10, qrbox: { width: 220, height: 220 } };
                 this.html5QrCode.start(
                     { facingMode: 'environment' },
                     config,
                     (decodedText) => {
                         this.stopCamera();
                         @this.loadInvoice(decodedText);
                     },
                     (errorMessage) => {
                         // Scanning frame error, keep silent
                     }
                 ).catch(err => {
                     console.error('Camera start error:', err);
                     alert('Tidak dapat mengakses kamera. Pastikan izin kamera aktif.');
                     this.cameraActive = false;
                 });
             });
         },

         stopCamera() {
             if (this.html5QrCode && this.cameraActive) {
                 this.html5QrCode.stop().then(() => {
                     this.cameraActive = false;
                 }).catch(err => console.error(err));
             } else {
                 this.cameraActive = false;
             }
         },

         scanQrFile(e) {
             const file = e.target.files[0];
             if (!file) return;
             this.isScanning = true;
             const html5QrCode = new Html5Qrcode('qr-file-processor');
             html5QrCode.scanFile(file, true)
                 .then(decodedText => {
                     this.isScanning = false;
                     @this.loadInvoice(decodedText);
                 })
                 .catch(err => {
                     this.isScanning = false;
                     console.error('File scan error:', err);
                     alert('Tidak menemukan QR Code yang valid pada gambar tersebut. Pastikan gambar QR jelas.');
                 });
         },

         copyText(text) {
             navigator.clipboard.writeText(text);
             Swal.fire({
                 toast: true,
                 position: 'top-end',
                 icon: 'success',
                 title: 'Nomor rekening berhasil disalin!',
                 showConfirmButton: false,
                 timer: 2000,
                 background: '#1E293B',
                 color: '#F8FAFC'
             });
         }
     }"
     x-init="
         if (rawAmount) {
             formattedAmount = formatRupiah(rawAmount.toString());
         }
     ">

    {{-- Header Branding --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#F5C518] to-amber-300 text-slate-950 font-black text-xl shadow-lg shadow-amber-500/20 mb-3 transform -rotate-3">
            SW
        </div>
        <h1 class="text-2xl sm:text-3xl font-black font-poppins text-white tracking-tight">
            Konfirmasi Pembayaran
        </h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
            Unggah bukti transfer Anda untuk verifikasi otomatis oleh tim Finance
        </p>
    </div>

    {{-- Hidden element for file scanning --}}
    <div id="qr-file-processor" style="display: none;"></div>

    @if($isSubmitted)
        {{-- SUCCESS STATE CARD --}}
        <div class="bg-slate-800/80 backdrop-blur-xl border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl text-center space-y-6 animate-fade-in">
            <div class="w-16 h-16 bg-emerald-500/10 border-2 border-emerald-500 text-emerald-400 rounded-full flex items-center justify-center mx-auto text-3xl shadow-lg shadow-emerald-500/20 animate-bounce">
                ✓
            </div>
            
            <div class="space-y-2">
                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-500/30">
                    Bukti Pembayaran Terkirim
                </span>
                <h2 class="text-xl sm:text-2xl font-black font-poppins text-white">
                    Terima Kasih!
                </h2>
                <p class="text-xs text-slate-300 max-w-md mx-auto leading-relaxed">
                    Bukti pembayaran untuk Invoice <strong class="text-emerald-400 font-mono">{{ $invoice->invoice_number }}</strong> telah kami terima dan sedang dalam antrean verifikasi oleh tim Finance.
                </p>
            </div>

            <div class="bg-slate-900/90 rounded-2xl p-4 border border-slate-700/60 text-left space-y-2.5 text-xs">
                <div class="flex justify-between items-center text-slate-400">
                    <span>Nominal Ditransfer:</span>
                    <strong class="text-[#F5C518] text-sm font-black">Rp {{ number_format(preg_replace('/[^0-9]/', '', $amount), 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between items-center text-slate-400">
                    <span>Bank Tujuan:</span>
                    <span class="text-slate-200 font-bold">{{ $payment_method }}</span>
                </div>
                <div class="flex justify-between items-center text-slate-400">
                    <span>Waktu Kirim:</span>
                    <span class="text-slate-300">{{ now()->format('d M Y, H:i') }} WIB</span>
                </div>
            </div>

            <div class="pt-2 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ url('/api/invoice_share_grouped.php?token=' . urlencode($invoice->invoice_number)) }}" target="_blank"
                   class="px-6 py-3 bg-[#F5C518] hover:bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-amber-500/20 active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Lihat Status Invoice
                </a>
                <button type="button" wire:click="resetInvoice"
                        class="px-6 py-3 bg-slate-700/80 hover:bg-slate-700 text-slate-200 font-bold text-xs uppercase tracking-wider rounded-xl transition-all active:scale-95">
                    Kirim Bukti Lain
                </button>
            </div>
        </div>

    @elseif(!$invoice)
        {{-- STEP 1: SCAN OR UPLOAD QR CODE INVOICE --}}
        <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-700/80 pb-4">
                <div class="flex items-center gap-2">
                    <span class="flex h-6 w-6 rounded-full bg-[#F5C518] text-slate-950 items-center justify-center text-xs font-black">1</span>
                    <h2 class="text-sm sm:text-base font-bold text-white uppercase tracking-wider">Identifikasi Invoice</h2>
                </div>
                <span class="text-[10px] text-slate-400 font-medium italic">Gunakan QR Code dari Invoice</span>
            </div>

            {{-- Tab Mode Switcher --}}
            <div class="grid grid-cols-2 gap-2 p-1 bg-slate-900/90 rounded-2xl border border-slate-700/80">
                <button type="button" 
                        @click="stopCamera(); scanMode = 'file';"
                        :class="scanMode === 'file' ? 'bg-[#F5C518] text-slate-950 font-black shadow-md' : 'text-slate-400 hover:text-white font-medium'"
                        class="py-2.5 px-3 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Upload Gambar QR
                </button>
                <button type="button" 
                        @click="startCamera();"
                        :class="scanMode === 'camera' ? 'bg-[#F5C518] text-slate-950 font-black shadow-md' : 'text-slate-400 hover:text-white font-medium'"
                        class="py-2.5 px-3 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Scan Kamera Live
                </button>
            </div>

            {{-- Mode: Upload Gambar QR --}}
            <div x-show="scanMode === 'file'" class="space-y-4">
                <label class="relative flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-slate-600 hover:border-[#F5C518] rounded-2xl bg-slate-900/50 hover:bg-slate-900 cursor-pointer transition-all group overflow-hidden">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                        <div class="w-12 h-12 mb-3 rounded-2xl bg-slate-800 text-slate-300 group-hover:text-[#F5C518] group-hover:scale-110 flex items-center justify-center transition-all border border-slate-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <p class="text-xs sm:text-sm font-bold text-slate-200">
                            Pilih atau Tarik File Gambar QR Code
                        </p>
                        <p class="text-[10px] text-slate-500 mt-1">
                            Format JPG, PNG dari hasil download Invoice halaman 2
                        </p>
                        <span x-show="isScanning" class="mt-2 text-xs font-bold text-[#F5C518] animate-pulse">
                            Sedang memproses QR...
                        </span>
                    </div>
                    <input type="file" accept="image/*" class="hidden" @change="scanQrFile($event)">
                </label>
            </div>

            {{-- Mode: Live Camera Scanner --}}
            <div x-show="scanMode === 'camera'" class="space-y-3" style="display: none;">
                <div class="relative bg-black rounded-2xl overflow-hidden border border-slate-700 min-h-[260px] flex items-center justify-center">
                    <div id="qr-reader-portal" class="w-full"></div>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400 italic">Arahkan kamera ke QR Code Invoice</span>
                    <button type="button" @click="stopCamera(); scanMode = 'file';" class="text-red-400 hover:underline font-bold">
                        Tutup Kamera
                    </button>
                </div>
            </div>

            {{-- Manual Token Fallback Input --}}
            <div class="pt-2 border-t border-slate-700/60">
                <details class="text-xs text-slate-400 group">
                    <summary class="cursor-pointer hover:text-slate-200 font-bold transition-colors list-none flex items-center justify-between">
                        <span>Atau input Nomor Invoice / SPK Manual</span>
                        <span class="text-[10px] text-slate-500 group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="mt-3 flex gap-2">
                        <input type="text" 
                               wire:model.defer="token"
                               placeholder="Contoh: INV-260829-014F"
                               class="flex-1 bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white uppercase focus:border-[#F5C518] focus:ring-1 focus:ring-[#F5C518] outline-none">
                        <button type="button" 
                                wire:click="loadInvoice($wire.token)"
                                class="px-4 py-2 bg-[#F5C518] hover:bg-amber-400 text-slate-950 font-black text-xs uppercase rounded-xl transition-all active:scale-95">
                            Cari
                        </button>
                    </div>
                </details>
            </div>
        </div>

    @else
        {{-- STEP 2: INVOICE LOADED & PAYMENT FORM --}}
        <div class="space-y-6">
            {{-- Invoice Overview Card --}}
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 rounded-3xl p-5 sm:p-6 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#F5C518]/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-700/70 pb-4 mb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-[#F5C518] bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                                {{ $invoice->status ?? 'Belum Bayar' }}
                            </span>
                            <span class="text-xs text-slate-400 font-mono">{{ $invoice->created_at?->format('d M Y') }}</span>
                        </div>
                        <h2 class="text-base sm:text-lg font-black text-white uppercase tracking-tight mt-1">
                            {{ $invoice->invoice_number }}
                        </h2>
                    </div>

                    <div class="text-left sm:text-right">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Pelanggan:</span>
                        <span class="text-sm font-black text-slate-200">{{ $invoice->customer->name ?? 'N/A' }}</span>
                    </div>
                </div>

                {{-- Shoes / SPK List --}}
                <div class="space-y-2 mb-4">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Item Sepatu & Layanan:</span>
                    <div class="grid grid-cols-1 gap-2 max-h-40 overflow-y-auto pr-1">
                        @foreach($invoice->workOrders as $wo)
                            <div class="bg-slate-950/60 rounded-xl p-2.5 border border-slate-800 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-slate-200">{{ $wo->shoe_brand }} {{ $wo->shoe_type }}</span>
                                    <span class="text-[10px] text-slate-400 block font-mono">{{ $wo->spk_number }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[11px] text-slate-300 font-medium">
                                        {{ $wo->workOrderServices->pluck('service.name')->filter()->join(', ') ?: 'Reparasi/Treatment' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Financial Summary Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-3 border-t border-slate-700/70 text-xs">
                    <div>
                        <span class="text-[9px] text-slate-400 uppercase block">Total Tagihan</span>
                        <span class="font-black text-slate-100 text-sm">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] text-slate-400 uppercase block">Sudah Dibayar</span>
                        <span class="font-black text-emerald-400 text-sm">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-span-2 sm:col-span-1 bg-amber-500/10 border border-amber-500/20 rounded-xl p-2 text-right">
                        <span class="text-[9px] text-amber-300 uppercase font-black block">Sisa Tagihan</span>
                        <span class="font-black text-[#F5C518] text-sm sm:text-base">
                            Rp {{ number_format(max(0, $invoice->total_amount - $invoice->paid_amount), 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Switch Invoice Link --}}
                <div class="mt-4 text-right">
                    <button type="button" wire:click="resetInvoice" class="text-[11px] text-slate-400 hover:text-white underline transition-colors">
                        Ganti / Scan Invoice Lain
                    </button>
                </div>
            </div>

            {{-- Payment Submission Form --}}
            <form wire:submit.prevent="submitPayment" class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
                <div class="flex items-center gap-2 border-b border-slate-700/80 pb-4">
                    <span class="flex h-6 w-6 rounded-full bg-[#F5C518] text-slate-950 items-center justify-center text-xs font-black">2</span>
                    <h2 class="text-sm sm:text-base font-bold text-white uppercase tracking-wider">Form Bukti Pembayaran</h2>
                </div>

                {{-- 1. Input Nominal Transfer --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Nominal Transfer <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               x-model="formattedAmount"
                               @input="onAmountInput($event)"
                               placeholder="Contoh: Rp 150.000"
                               class="w-full bg-slate-900 border border-slate-700 rounded-2xl py-3 px-4 text-white text-base font-black tracking-wide focus:border-[#F5C518] focus:ring-2 focus:ring-[#F5C518]/20 outline-none transition-all placeholder:font-normal placeholder:text-slate-600">
                    </div>
                    @error('amount') <span class="text-red-400 text-xs font-medium">{{ $message }}</span> @enderror
                    <p class="text-[10px] text-slate-400 italic">Ketikkan nominal yang Anda transfer sesuai struk pembayaran.</p>
                </div>

                {{-- 2. Rekening Tujuan Bank --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Rekening Bank Tujuan <span class="text-red-400">*</span>
                    </label>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- BCA --}}
                        <label class="relative flex items-start p-3.5 rounded-2xl border cursor-pointer transition-all"
                               :class="$wire.payment_method === 'BCA' ? 'bg-blue-500/10 border-blue-500 text-white' : 'bg-slate-900/80 border-slate-700 text-slate-400 hover:border-slate-600'">
                            <input type="radio" wire:model="payment_method" value="BCA" class="sr-only">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-black text-xs text-blue-400">BANK BCA</span>
                                    <button type="button" @click.stop="copyText('8100978521')" class="text-[10px] text-slate-400 hover:text-white bg-slate-800 px-2 py-0.5 rounded border border-slate-700">
                                        Salin
                                    </button>
                                </div>
                                <p class="text-sm font-black font-mono mt-1 text-slate-200">8100978521</p>
                                <p class="text-[9px] text-slate-400 uppercase mt-0.5">a.n PT. Terang Garam Solusindo</p>
                            </div>
                        </label>

                        {{-- Mandiri --}}
                        <label class="relative flex items-start p-3.5 rounded-2xl border cursor-pointer transition-all"
                               :class="$wire.payment_method === 'Mandiri' ? 'bg-amber-500/10 border-amber-500 text-white' : 'bg-slate-900/80 border-slate-700 text-slate-400 hover:border-slate-600'">
                            <input type="radio" wire:model="payment_method" value="Mandiri" class="sr-only">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-black text-xs text-amber-400">BANK MANDIRI</span>
                                    <button type="button" @click.stop="copyText('1300030119047')" class="text-[10px] text-slate-400 hover:text-white bg-slate-800 px-2 py-0.5 rounded border border-slate-700">
                                        Salin
                                    </button>
                                </div>
                                <p class="text-sm font-black font-mono mt-1 text-slate-200">1300030119047</p>
                                <p class="text-[9px] text-slate-400 uppercase mt-0.5">a.n PT. Terang Garam Solusindo</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- 3. Upload File Bukti Transfer --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Foto Bukti Transfer / Struk <span class="text-red-400">*</span>
                    </label>

                    <div class="relative">
                        <label class="flex flex-col items-center justify-center w-full min-h-[140px] border-2 border-dashed border-slate-700 hover:border-[#F5C518] rounded-2xl bg-slate-900/80 cursor-pointer p-4 transition-all">
                            @if ($proof_image)
                                <div class="flex items-center gap-4 w-full">
                                    <img src="{{ $proof_image->temporaryUrl() }}" class="w-20 h-20 object-cover rounded-xl border border-slate-700 shadow-md">
                                    <div class="flex-1 text-left">
                                        <p class="text-xs font-bold text-emerald-400 flex items-center gap-1">
                                            ✓ Foto Bukti Siap Dikirim
                                        </p>
                                        <p class="text-[10px] text-slate-400 mt-1">Klik untuk mengganti foto</p>
                                    </div>
                                </div>
                            @else
                                <div class="text-center space-y-2">
                                    <div class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center mx-auto border border-slate-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-300">Pilih Foto atau Screenshot Transfer</p>
                                    <p class="text-[10px] text-slate-500">Maksimal ukuran file 10MB (JPG/PNG)</p>
                                </div>
                            @endif
                            <input type="file" wire:model="proof_image" accept="image/*" class="hidden">
                        </label>
                    </div>
                    @error('proof_image') <span class="text-red-400 text-xs font-medium">{{ $message }}</span> @enderror
                </div>

                {{-- 4. Catatan Tambahan --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Catatan Tambahan (Opsional)
                    </label>
                    <textarea wire:model.defer="notes" 
                              rows="2" 
                              placeholder="Misal: Pembayaran DP 50%, transfer dari rekening BCA atas nama John Doe"
                              class="w-full bg-slate-900 border border-slate-700 rounded-2xl p-3 text-xs text-slate-200 focus:border-[#F5C518] focus:ring-1 focus:ring-[#F5C518] outline-none placeholder:text-slate-600"></textarea>
                </div>

                {{-- Submit Button --}}
                <div class="pt-2">
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full py-4 px-6 bg-gradient-to-r from-[#F5C518] to-amber-400 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-sm uppercase tracking-widest rounded-2xl shadow-xl shadow-amber-500/20 transition-all transform active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                        <span wire:loading.remove>Kirim Bukti Pembayaran ➔</span>
                        <span wire:loading class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-slate-950" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Mengirim Bukti...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
