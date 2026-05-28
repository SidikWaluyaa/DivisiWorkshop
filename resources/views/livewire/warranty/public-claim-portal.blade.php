<div>
    <style>
        /* ══════════════════════════════════════
           UPLOAD ZONE — STATES:
           1. Empty   — dashed border placeholder
           2. Loading — spinner overlay
           3. Filled  — image preview + change btn
        ══════════════════════════════════════ */

        /* Wrapper: fixed aspect-ratio so it never collapses */
        .upload-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 4 / 3;
            border-radius: 1rem;
            overflow: hidden;
            cursor: pointer;
            /* default: empty dashed state */
            border: 2px dashed #d1d5db;
            background-color: #f9fafb;
            transition: border-color 0.2s, background-color 0.2s, box-shadow 0.2s;
        }
        .upload-wrap:hover {
            border-color: #10b981;
            background-color: #f0fdf4;
            box-shadow: 0 0 0 4px rgba(16,185,129,0.09);
        }
        /* When filled, remove dashed border */
        .upload-wrap.is-filled {
            border: none;
        }

        /* ── Layer: Empty placeholder (centered content) ── */
        .upload-empty {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem;
            text-align: center;
            pointer-events: none;
        }

        /* ── Layer: Loading spinner ── */
        .upload-loading {
            position: absolute;
            inset: 0;
            z-index: 20;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(8px);
        }

        /* ── Layer: Preview image (fills the box) ── */
        .upload-preview {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        /* ── Layer: Hover-reveal change button ── */
        .upload-change-overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            background: rgba(0,0,0,0);
            transition: background 0.25s;
        }
        .upload-wrap:hover .upload-change-overlay {
            background: rgba(0,0,0,0.52);
        }
        .upload-change-btn {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.4rem 0.875rem;
            border-radius: 9999px;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(4px);
            font-size: 0.7rem;
            font-weight: 800;
            color: #111827;
            opacity: 0;
            transform: translateY(6px);
            transition: opacity 0.2s, transform 0.2s;
            white-space: nowrap;
            pointer-events: none;
        }
        .upload-wrap:hover .upload-change-btn {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── Checkmark badge (top-right corner when filled) ── */
        .upload-check-badge {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            z-index: 3;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 9999px;
            background: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.25);
            animation: pop-badge 0.3s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        @keyframes pop-badge {
            from { transform: scale(0); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }

        /* ── Step connector line ── */
        .step-line {
            flex: 1;
            height: 2px;
            background: #e5e7eb;
            border-radius: 9999px;
            transition: background 0.3s;
        }
        .step-line.active { background: #10b981; }

        /* ── Warranty badge pulse ── */
        @keyframes warranty-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.35); }
            50%       { box-shadow: 0 0 0 8px rgba(16,185,129,0); }
        }
        .warranty-badge-pulse { animation: warranty-pulse 2.4s ease-in-out infinite; }

        /* ── Slide in ── */
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .slide-up { animation: slide-up 0.45s cubic-bezier(0.22,1,0.36,1) both; }

        /* ── Success confetti feel ── */
        @keyframes pop-in {
            0%   { opacity: 0; transform: scale(0.6); }
            70%  { transform: scale(1.08); }
            100% { opacity: 1; transform: scale(1); }
        }
        .pop-in { animation: pop-in 0.5s cubic-bezier(0.34,1.56,0.64,1) both; }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.875rem;
            background-color: #f9fafb;
            font-size: 0.9375rem;
            font-weight: 600;
            color: #111827;
            transition: all 0.2s;
            outline: none;
        }
        .form-input:focus {
            border-color: #10b981;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(16,185,129,0.12);
        }
        .form-input::placeholder { color: #9ca3af; font-weight: 400; }
    </style>

    {{-- ══════════════════════════════════════════════════════════
         MAIN CARD
    ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-t-8
                {{ $step === 1 ? 'border-emerald-500' : ($step === 2 ? 'border-amber-500' : 'border-emerald-500') }}">

        {{-- ── TOP HEADER BAND ── --}}
        <div class="px-6 pt-7 pb-5 {{ $step === 2 ? 'bg-gradient-to-br from-amber-50 to-orange-50' : 'bg-gradient-to-br from-emerald-50 to-teal-50' }}">
            <div class="flex items-center gap-3.5">
                {{-- Icon badge --}}
                <div class="shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center shadow-md
                            {{ $step === 3 ? 'bg-emerald-500' : ($step === 2 ? 'bg-amber-500' : 'bg-emerald-500') }}">
                    @if($step === 3)
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @elseif($step === 2)
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @else
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-lg font-black text-gray-900 leading-tight tracking-tight">
                        @if($step === 1) Klaim Garansi Mandiri
                        @elseif($step === 2) Form Pengajuan Klaim
                        @else Pengajuan Terkirim!
                        @endif
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">
                        @if($step === 1) Verifikasi SPK & nomor WhatsApp Anda terlebih dahulu
                        @elseif($step === 2) Lengkapi detail keluhan dan unggah bukti foto
                        @else Tim CX kami akan segera memproses klaim Anda
                        @endif
                    </p>
                </div>
            </div>

            {{-- Step Progress Bar --}}
            @if($step < 3)
            <div class="mt-5 flex items-center gap-2">
                {{-- Step 1 --}}
                <div class="flex flex-col items-center gap-1">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-black
                                {{ $step >= 1 ? 'bg-emerald-500 text-white shadow-md shadow-emerald-200' : 'bg-gray-200 text-gray-400' }}">
                        @if($step > 1) <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @else 1 @endif
                    </div>
                    <span class="text-[9px] font-bold uppercase tracking-wide {{ $step >= 1 ? 'text-emerald-600' : 'text-gray-400' }}">Verifikasi</span>
                </div>
                <div class="step-line {{ $step >= 2 ? 'active' : '' }} mb-4"></div>
                {{-- Step 2 --}}
                <div class="flex flex-col items-center gap-1">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-black
                                {{ $step >= 2 ? 'bg-amber-500 text-white shadow-md shadow-amber-200' : 'bg-gray-200 text-gray-400' }}">2</div>
                    <span class="text-[9px] font-bold uppercase tracking-wide {{ $step >= 2 ? 'text-amber-600' : 'text-gray-400' }}">Detail Klaim</span>
                </div>
                <div class="step-line mb-4"></div>
                {{-- Step 3 --}}
                <div class="flex flex-col items-center gap-1">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-black bg-gray-200 text-gray-400">3</div>
                    <span class="text-[9px] font-bold uppercase tracking-wide text-gray-400">Selesai</span>
                </div>
            </div>
            @endif
        </div>

        {{-- ── BODY ── --}}
        <div class="px-6 pb-7 pt-5">

            {{-- ── ERROR ALERT ── --}}
            @if(session()->has('error'))
                <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl flex items-start gap-3 slide-up">
                    <div class="shrink-0 w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-red-700 uppercase tracking-wider mb-0.5">Verifikasi Gagal</p>
                        <p class="text-sm text-red-600">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- ════════════════════════════
                 STEP 1 — VERIFICATION
            ════════════════════════════ --}}
            @if($step === 1)
            <form wire:submit.prevent="checkWarranty" class="space-y-5 slide-up">

                {{-- SPK Number --}}
                <div>
                    <label for="spk_number" class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-widest">
                        Nomor SPK / Order
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                        </span>
                        <input wire:model.defer="spk_number"
                               type="text"
                               id="spk_number"
                               placeholder="Contoh: S-2602-16-0009-SW"
                               class="form-input font-mono"
                               autocomplete="off">
                    </div>
                    @error('spk_number')
                        <p class="text-red-500 text-xs font-semibold mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- WhatsApp Number --}}
                <div>
                    <label for="customer_phone" class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-widest">
                        Nomor WhatsApp
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </span>
                        <input wire:model.defer="customer_phone"
                               type="tel"
                               id="customer_phone"
                               placeholder="Contoh: 08123456789 atau 6281234..."
                               class="form-input">
                    </div>
                    @error('customer_phone')
                        <p class="text-red-500 text-xs font-semibold mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Info box --}}
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3.5 flex gap-3">
                    <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <p class="text-xs text-blue-700 leading-relaxed">Masukkan nomor SPK dan nomor WhatsApp yang sama seperti saat melakukan pemesanan. Data akan dicocokkan secara otomatis.</p>
                </div>

                {{-- CTA Button --}}
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-black py-4 px-6 rounded-xl transition-all duration-300 flex items-center justify-center gap-3 text-base shadow-lg shadow-emerald-200 hover:shadow-emerald-300 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="checkWarranty">
                        Verifikasi Garansi Saya
                    </span>
                    <svg wire:loading.remove wire:target="checkWarranty" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    <span wire:loading wire:target="checkWarranty" class="flex items-center gap-2.5">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memverifikasi...
                    </span>
                </button>

            </form>
            @endif

            {{-- ════════════════════════════
                 STEP 2 — CLAIM FORM
            ════════════════════════════ --}}
            @if($step === 2)
            <div class="slide-up space-y-5">

                {{-- Verified warranty card --}}
                <div class="warranty-badge-pulse bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl p-4 text-white">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-black uppercase tracking-widest opacity-80">Garansi Terverifikasi ✓</span>
                        <span class="text-[10px] font-black bg-white/20 px-2.5 py-1 rounded-full">AKTIF</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-[9px] opacity-70 uppercase tracking-wider font-bold mb-0.5">Nama Pemilik</p>
                            <p class="font-black text-sm leading-tight">{{ $order_details['customer_name'] }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] opacity-70 uppercase tracking-wider font-bold mb-0.5">Sepatu</p>
                            <p class="font-black text-sm leading-tight">{{ $order_details['shoe_brand'] }}
                                @if($order_details['shoe_color']) · {{ $order_details['shoe_color'] }}@endif
                            </p>
                        </div>
                        <div class="col-span-2 pt-2 border-t border-white/20">
                            <p class="text-[9px] opacity-70 uppercase tracking-wider font-bold mb-0.5">Berlaku Hingga</p>
                            <p class="font-black text-sm">{{ $order_details['warranty_expires_at'] }}
                                <span class="font-medium opacity-80 text-xs">
                                    (Sisa {{ (int) $order_details['days_left'] }} hari)
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="submitClaim" class="space-y-5">

                    {{-- Problem Description --}}
                    <div>
                        <label for="problem_description" class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-widest">
                            Deskripsi Masalah / Kerusakan
                        </label>
                        <textarea wire:model.defer="problem_description"
                                  id="problem_description"
                                  rows="3"
                                  placeholder="Jelaskan detail keluhan, bagian sepatu yang bermasalah, kapan terjadi, dll..."
                                  class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-50 text-sm text-gray-800 font-medium transition-all outline-none placeholder-gray-400 resize-none leading-relaxed"></textarea>
                        @error('problem_description')
                            <p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ══ Photo Uploads — 2-column grid ══ --}}
                    <div class="grid grid-cols-2 gap-3">

                        {{-- ── Foto Kerusakan ── --}}
                        <div>
                            <p class="text-[10px] font-black text-gray-500 mb-2 uppercase tracking-widest flex items-center gap-1">
                                <span class="inline-block w-3.5 h-3.5 bg-red-500 text-white rounded-full text-[8px] font-black text-center leading-3.5 shrink-0">!</span>
                                Foto Kerusakan
                            </p>
                            <label class="upload-wrap {{ $problem_photo ? 'is-filled' : '' }} block">

                                {{-- STATE 1: Empty --}}
                                @if (!$problem_photo)
                                <div class="upload-empty" wire:loading.remove wire:target="problem_photo">
                                    <div class="w-9 h-9 bg-emerald-100 text-emerald-500 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="text-[10px] font-bold text-gray-600 leading-tight">Ketuk untuk<br>pilih foto</p>
                                    <p class="text-[9px] text-gray-400">JPG/PNG · 5MB</p>
                                </div>
                                @endif

                                {{-- STATE 2: Loading --}}
                                <div wire:loading wire:target="problem_photo" class="upload-loading">
                                    <svg class="animate-spin h-8 w-8 text-emerald-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Mengunggah...</span>
                                </div>

                                {{-- STATE 3: Filled with preview --}}
                                @if ($problem_photo)
                                <img src="{{ $problem_photo->temporaryUrl() }}"
                                     class="upload-preview"
                                     alt="Preview foto kerusakan">
                                {{-- Green checkmark badge --}}
                                <div class="upload-check-badge">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                {{-- Hover: change button --}}
                                <div class="upload-change-overlay">
                                    <span class="upload-change-btn">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Ganti Foto
                                    </span>
                                </div>
                                @endif

                                <input wire:model="problem_photo" type="file" class="hidden" accept="image/*">
                            </label>
                            @error('problem_photo')
                                <p class="text-red-500 text-[10px] font-semibold mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- ── Foto Google Review ── --}}
                        <div>
                            <p class="text-[10px] font-black text-gray-500 mb-2 uppercase tracking-widest flex items-center gap-1">
                                <span class="inline-block shrink-0">
                                    <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </span>
                                Google Review
                            </p>
                            <label class="upload-wrap {{ $google_review_photo ? 'is-filled' : '' }} block">

                                {{-- STATE 1: Empty --}}
                                @if (!$google_review_photo)
                                <div class="upload-empty" wire:loading.remove wire:target="google_review_photo">
                                    <div class="w-9 h-9 bg-yellow-100 text-yellow-500 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    </div>
                                    <p class="text-[10px] font-bold text-gray-600 leading-tight">Screenshot<br>ulasan Google</p>
                                    <p class="text-[9px] text-gray-400">Wajib · 5MB</p>
                                </div>
                                @endif

                                {{-- STATE 2: Loading --}}
                                <div wire:loading wire:target="google_review_photo" class="upload-loading">
                                    <svg class="animate-spin h-8 w-8 text-amber-400" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest">Mengunggah...</span>
                                </div>

                                {{-- STATE 3: Filled with preview --}}
                                @if ($google_review_photo)
                                <img src="{{ $google_review_photo->temporaryUrl() }}"
                                     class="upload-preview"
                                     alt="Preview Google Review">
                                {{-- Green checkmark badge --}}
                                <div class="upload-check-badge">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                {{-- Hover: change button --}}
                                <div class="upload-change-overlay">
                                    <span class="upload-change-btn">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        Ganti Foto
                                    </span>
                                </div>
                                @endif

                                <input wire:model="google_review_photo" type="file" class="hidden" accept="image/*">
                            </label>
                            @error('google_review_photo')
                                <p class="text-red-500 text-[10px] font-semibold mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>{{-- /grid 2-col --}}

                    {{-- Upload note --}}
                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 flex gap-2.5 items-start">
                        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <p class="text-[11px] text-amber-700 leading-relaxed font-medium">Foto Google Review <strong>wajib</strong> menyertakan ulasan yang Anda buat untuk Workshop kami. Foto tanpa review tidak akan diproses.</p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-3 pt-1">
                        <button type="button"
                                wire:click="resetPortal"
                                class="w-1/3 py-4 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-bold text-sm transition-all">
                            ← Kembali
                        </button>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="w-2/3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-black py-4 px-5 rounded-xl transition-all duration-300 flex items-center justify-center gap-2.5 text-sm shadow-lg shadow-amber-200 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="submitClaim">Kirim Pengajuan</span>
                            <svg wire:loading.remove wire:target="submitClaim" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span wire:loading wire:target="submitClaim" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mengirim...
                            </span>
                        </button>
                    </div>

                </form>
            </div>
            @endif

            {{-- ════════════════════════════
                 STEP 3 — SUCCESS
            ════════════════════════════ --}}
            @if($step === 3)
            <div class="text-center py-4 slide-up">
                {{-- Success icon --}}
                <div class="pop-in inline-flex w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-500 text-white rounded-full items-center justify-center shadow-xl shadow-emerald-200 mb-5">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-2">Klaim Berhasil Dikirim!</h2>
                <p class="text-sm text-gray-500 max-w-xs mx-auto leading-relaxed mb-6">
                    Pengajuan klaim garansi Anda telah kami terima dan sedang dalam antrean peninjauan.
                </p>

                {{-- Next steps --}}
                <div class="text-left space-y-3 mb-7">
                    <div class="flex items-start gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="shrink-0 w-7 h-7 bg-emerald-500 text-white rounded-lg flex items-center justify-center text-xs font-black">1</span>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Tinjauan Divisi CX</p>
                            <p class="text-xs text-gray-500 mt-0.5">Dokumen Anda akan diverifikasi dalam maks. <strong>4 jam kerja</strong>.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="shrink-0 w-7 h-7 bg-emerald-500 text-white rounded-lg flex items-center justify-center text-xs font-black">2</span>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Notifikasi WhatsApp</p>
                            <p class="text-xs text-gray-500 mt-0.5">Keputusan diterima/ditolak akan dikirim via <strong>WhatsApp otomatis</strong>.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="shrink-0 w-7 h-7 bg-emerald-500 text-white rounded-lg flex items-center justify-center text-xs font-black">3</span>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Proses Perbaikan</p>
                            <p class="text-xs text-gray-500 mt-0.5">Jika disetujui, Anda akan mendapat panduan <strong>penyerahan sepatu</strong>.</p>
                        </div>
                    </div>
                </div>

                <button type="button"
                        wire:click="resetPortal"
                        class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-black py-4 px-6 rounded-xl transition-all duration-300 shadow-lg shadow-emerald-200 hover:-translate-y-0.5">
                    Ajukan Klaim SPK Lainnya
                </button>

                <a href="{{ route('tracking.index') }}" class="mt-3 block text-sm text-gray-400 hover:text-gray-600 transition-colors font-medium">
                    ← Kembali ke Lacak Pesanan
                </a>
            </div>
            @endif

        </div>{{-- /body --}}
    </div>{{-- /main card --}}

</div>
