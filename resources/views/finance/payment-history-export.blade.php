<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Protokol #{{ $order->spk_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: #fdfdfd;
            color: #111827;
        }

        .export-container {
            width: 210mm;
            min-height: 297mm;
            margin: 40px auto;
            background: white;
            padding: 20mm;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
            position: relative;
            border: 1px solid #f1f5f9;
        }

        @media print {
            @page {
                size: A4;
                margin: 0;
            }
            body {
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .export-container {
                width: 100%;
                min-height: 100vh;
                margin: 0;
                box-shadow: none;
                padding: 15mm;
                border: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 antialiased">
    
    <!-- Print Controls -->
    <div class="no-print fixed top-6 right-6 z-50 flex gap-3">
        <button onclick="window.print()" class="bg-gray-900 hover:bg-black text-white font-black py-3 px-6 rounded-2xl shadow-2xl flex items-center gap-2.5 transition-all active:scale-95 text-xs uppercase tracking-widest">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Kuitansi
        </button>
        <button onclick="window.close()" class="bg-white hover:bg-gray-50 text-gray-400 font-bold py-3 px-6 rounded-2xl shadow-sm border border-gray-100 transition-all text-xs uppercase tracking-widest">
            Close
        </button>
    </div>

    <div class="export-container">
        <!-- Header -->
        <div class="border-b-4 border-gray-900 pb-10 mb-12 flex justify-between items-end">
            <div>
                <h1 class="text-4xl font-black text-gray-900 italic tracking-tighter leading-none">RIWAYAT TRANSAKSI</h1>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4em] mt-3 italic">Divisi: Audit Keuangan & Kepatuhan</p>
            </div>
            <div class="text-right">
                <div class="text-[9px] font-black text-gray-300 uppercase tracking-widest leading-none mb-1 italic">Protokol Dihasilkan</div>
                <div class="text-sm font-black text-gray-900 italic leading-none tracking-tight">{{ now()->format('M d, Y / H:i') }}</div>
            </div>
        </div>

        <!-- Meta Info Grid -->
        <div class="grid grid-cols-2 gap-12 mb-16 italic">
            <div class="bg-gray-50 rounded-[2rem] p-8 border border-gray-100 flex flex-col gap-6">
                <div>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Identitas Protokol</h3>
                    <div class="text-2xl font-black text-gray-900 tracking-tighter">{{ $order->spk_number }}</div>
                    <div class="text-[10px] font-black text-[#22AF85] uppercase tracking-widest mt-1.5 opacity-80">{{ str_replace('_', ' ', $order->status->value) }} Sector</div>
                </div>
                <div>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Registrasi</h3>
                    <div class="text-sm font-black text-gray-900">{{ $order->entry_date?->format('d M / Y') ?? '-' }}</div>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-[2rem] p-8 border border-gray-100 flex flex-col gap-6">
                <div>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Detail Entitas</h3>
                    <div class="text-2xl font-black text-gray-900 tracking-tighter">{{ $order->customer_name }}</div>
                    <div class="text-[10px] font-black text-gray-400 mt-1.5">{{ $order->customer_phone }}</div>
                </div>
                <div>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Aset Objek</h3>
                    <div class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $order->shoe_brand }} · {{ $order->shoe_color }}</div>
                </div>
            </div>
        </div>

        <!-- Financial Summary Cards -->
        <div class="grid grid-cols-3 gap-6 mb-16">
            <div class="bg-gray-900 text-white rounded-[2rem] p-8 shadow-xl relative overflow-hidden group">
                <div class="absolute inset-0 bg-white/5 translate-x-full group-hover:translate-x-0 transition-transform"></div>
                <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-4 opacity-50 italic">Total Nilai</p>
                <div class="text-2xl font-black italic tracking-tighter tabular-nums">Rp {{ number_format($order->total_transaksi, 0, ',', '.') }}</div>
            </div>
            <div class="bg-[#22AF85] text-white rounded-[2rem] p-8 shadow-xl relative overflow-hidden group">
                <div class="absolute inset-0 bg-white/10 translate-x-full group-hover:translate-x-0 transition-transform"></div>
                <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-4 opacity-70 italic">Terbayar</p>
                <div class="text-2xl font-black italic tracking-tighter tabular-nums">Rp {{ number_format($order->total_paid, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-gray-100 rounded-[2rem] p-8 shadow-lg group">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-4 text-gray-400 italic">Sisa</p>
                <div class="text-2xl font-black italic tracking-tighter tabular-nums {{ $order->sisa_tagihan > 0 ? 'text-rose-600' : 'text-gray-300' }}">Rp {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Detailed History -->
        <div class="mb-16 italic">
            <h3 class="text-[11px] font-black text-gray-900 uppercase tracking-[0.3em] mb-8 italic">Protokol Riwayat Pembayaran</h3>
            
            <div class="space-y-8">
                @forelse($order->payments as $payment)
                    <div class="relative pl-12">
                        <div class="absolute left-0 top-0 bottom-0 w-px bg-gray-100"></div>
                        <div class="absolute left-[-4px] top-2 w-2 h-2 rounded-full bg-gray-900"></div>
                        
                        <div class="bg-white rounded-[1.5rem] p-6 border border-gray-100 hover:border-[#22AF85]/30 transition-all group">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-[9px] font-black text-[#22AF85] uppercase tracking-widest mb-1 italic">
                                        {{ $payment->type === 'BEFORE' ? 'PEMBAYARAN AWAL / DP' : 'PELUNASAN AKHIR' }}
                                    </div>
                                    <div class="text-xl font-black text-gray-900 tracking-tighter uppercase">{{ $payment->payment_method }}</div>
                                    <div class="text-[10px] text-gray-400 font-black mt-1 uppercase tracking-wider">{{ $payment->paid_at->format('M d, Y / H:i') }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-black text-[#22AF85] tracking-tighter tabular-nums group-hover:scale-110 transition-transform origin-right">Rp {{ number_format($payment->amount_total, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            
                            @if($payment->notes)
                                <div class="mt-4 p-4 bg-gray-50 rounded-xl border-l-2 border-gray-200">
                                    <p class="text-[10px] text-gray-500 font-medium leading-relaxed uppercase tracking-tight italic">"{{ $payment->notes }}"</p>
                                </div>
                            @endif
                            
                            <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center text-[9px] font-black text-gray-300 uppercase tracking-widest italic group-hover:text-gray-400 transition-colors">
                                <span>PIC: {{ $payment->pic->name ?? 'SYSTEM' }}</span>
                                @if($payment->proof_image)
                                    <span class="text-[#22AF85]">✓ Bukti Terverifikasi</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-100 relative overflow-hidden">
                        <div class="absolute inset-0 bg-white/50 blur-xl"></div>
                        <div class="relative z-10">
                            <div class="text-4xl mb-4">📭</div>
                            <p class="text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] italic leading-loose">No liquidity injections<br>detected in current protocol sector.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Settlement Indicator -->
        @if($order->sisa_tagihan <= 0 && $order->payments->count() > 0)
            <div class="flex justify-center mb-16">
                <div class="border-[6px] border-[#22AF85] text-[#22AF85] font-black px-12 py-5 rounded-[2.5rem] uppercase tracking-[0.6em] text-3xl italic transform rotate-[-2deg] shadow-xl shadow-emerald-50">
                    SETTLED
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="mt-auto pt-16 border-t border-gray-100 flex justify-between items-start">
            <div class="text-[10px] text-gray-300 font-black uppercase tracking-widest italic leading-relaxed max-w-sm">
                Dokumen ini adalah verifikasi resmi dari pelunasan aset. Semua rincian pembayaran telah diaudit dan difinalisasi sesuai standar protokol.
            </div>
            <div class="text-right">
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic leading-none mb-1">Pusat Otorisasi</div>
                <div class="text-sm font-black text-gray-900 italic tracking-tighter">EXCELLENCE WORKSHOP FINANCE</div>
            </div>
        </div>
    </div>
</body>
</html>
