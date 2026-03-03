<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <title>Invoice Gabungan #{{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: #cbd5e1;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            overflow-x: hidden;
        }

        .invoice-paper {
            width: 100%;
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        @media screen and (min-width: 210mm) {
            .invoice-paper {
                margin: 20px auto;
                box-shadow: 0 10px 50px rgba(0,0,0,0.1);
            }
        }

        .topo-bg {
            background-color: #22AF85;
            background-image: url("data:image/svg+xml,%3Csvg width='400' height='400' viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 400C100 350 150 250 200 200C250 150 350 100 400 0' stroke='%23ffffff' stroke-width='1.5' fill='none' opacity='0.15'/%3E%3Cpath d='M0 350C80 300 130 220 180 170C230 120 320 80 400 -50' stroke='%23ffffff' stroke-width='1' fill='none' opacity='0.1'/%3E%3Cpath d='M-50 400C50 340 100 240 150 190C200 140 300 90 350 -50' stroke='%23ffffff' stroke-width='1' fill='none' opacity='0.1'/%3E%3Cpath d='M50 400C130 360 180 280 230 230C280 180 370 140 450 10' stroke='%23ffffff' stroke-width='1' fill='none' opacity='0.1'/%3E%3C/svg%3E");
        }

        @media print {
            @page {
                size: A4;
                margin: 0mm !important;
            }
            body { background: white; }
            .invoice-paper {
                margin: 0 !important;
                width: 210mm;
                height: 297mm;
                box-shadow: none;
                overflow: hidden;
            }
            .no-print { display: none !important; }
            
            /* Force Professional Layout for Print regardless of width */
            .topo-bg { height: 160px !important; padding-bottom: 0 !important; }
            .floating-card { position: absolute !important; top: 24px !important; right: 40px !important; width: 440px !important; padding: 24px !important; }
            .content-body { padding-top: 100px !important; }
            .items-grid { display: grid !important; grid-template-columns: repeat(12, minmax(0, 1fr)) !important; }
            .desktop-header { display: grid !important; }
            .mobile-label { display: none !important; }
            .item-row { display: grid !important; grid-template-columns: repeat(12, minmax(0, 1fr)) !important; padding: 12px 32px !important; gap: 0 !important; }
            .summary-section { flex-direction: row !important; align-items: flex-end !important; }
            .totals-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            .footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; height: 110px !important; }
        }

        .floating-card {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 2rem;
        }
        
        /* Utility for fixed printing */
        .items-grid-header { display: none; }
        @media screen and (min-width: 640px) {
            .items-grid-header { display: grid; }
        }
    </style>
</head>
<body class="antialiased font-sans">

    <!-- Control Buttons (Web Only) -->
    <div class="no-print fixed top-4 right-4 z-50 flex gap-2">
        <button onclick="window.print()" class="btn-premium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            {{ isset($is_public) ? 'Download / Simpan PDF' : 'Cetak' }}
        </button>
    </div>

    <div class="invoice-paper">
        <!-- Header Section -->
        <div class="topo-bg min-h-[160px] sm:h-40 w-full px-6 sm:px-10 pt-8 pb-32 sm:pb-0 flex flex-col sm:flex-row items-start justify-between box-border relative">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-xl sm:rounded-2xl flex items-center justify-center shadow-2xl transform -rotate-6">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-[#22AF85]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                        <circle cx="12" cy="12" r="5"/>
                    </svg>
                </div>
                <div class="text-white mt-1">
                    <h1 class="text-base sm:text-lg font-black tracking-tight leading-none uppercase italic">Shoe Workshop</h1>
                    <p class="text-xl sm:text-[24px] font-light tracking-wide opacity-90 mt-0.5 italic leading-none">Comb. Invoice</p>
                </div>
            </div>

            <!-- Header Info Card -->
            <div class="floating-card w-full sm:w-[480px] bg-white p-6 sm:p-8 z-20 border border-gray-100 flex flex-col mt-6 sm:mt-0 sm:absolute sm:top-6 sm:right-10">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 w-full">
                    <div>
                        <p class="label-text text-[#22AF85]">Customer Name</p>
                        <div class="info-box">
                            <span class="text-xs font-black text-gray-900 italic truncate">{{ $invoice->customer->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="label-text text-gray-400">Shipment Method</p>
                        <div class="info-box">
                            <span class="text-xs font-black text-gray-900 uppercase italic">{{ $invoice->delivery_type ?? 'OFFLINE' }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="label-text text-gray-400">Invoice Number</p>
                        <div class="info-box overflow-hidden">
                            <span class="text-[10px] sm:text-xs font-black text-gray-900 tracking-tight uppercase">{{ $invoice->invoice_number }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="label-text text-gray-400">Generated Date</p>
                        <div class="info-box">
                            <span class="text-[9px] font-bold text-gray-500 tabular-nums">{{ now()->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Body -->
        <div class="content-body flex-1 px-4 sm:px-10 pt-6 sm:pt-28 pb-6 bg-[#F8FAFC] flex flex-col">
            <!-- Items Container -->
            <div class="bg-white rounded-3xl sm:rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden flex flex-col min-h-[300px]">
                <!-- Table Header -->
                <div class="desktop-header hidden sm:grid grid-cols-12 bg-[#22AF85] text-white py-3 px-8 text-[10px] font-black uppercase tracking-widest italic">
                    <div class="col-span-1">No</div>
                    <div class="col-span-3">SPK Number</div>
                    <div class="col-span-5">Items & Services</div>
                    <div class="col-span-3 text-right">Subtotal</div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($invoice->workOrders as $index => $item)
                    <div class="item-row grid grid-cols-1 sm:grid-cols-12 p-5 sm:py-5 sm:px-8 hover:bg-gray-50/50 transition-colors gap-4 sm:gap-0">
                        <div class="col-span-1 sm:col-span-4 flex items-center gap-4">
                            <span class="text-xs font-black text-gray-400 italic sm:w-8">{{ $index + 1 }}</span>
                            <div class="flex-1">
                                <span class="hidden sm:inline-block text-[10px] font-black text-gray-800 uppercase italic bg-gray-100 border border-gray-200 px-2 py-1 rounded">
                                    {{ $item->spk_number }}
                                </span>
                                <!-- Mobile SPK Badge -->
                                <div class="mobile-label sm:hidden flex flex-col gap-1">
                                    <span class="text-[8px] font-black text-gray-300 uppercase italic tracking-widest">SPK NUMBER</span>
                                    <span class="text-[11px] font-black text-gray-900 bg-gray-50 px-2 py-1 rounded-lg border w-fit italic">{{ $item->spk_number }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-1 sm:col-span-5">
                            <p class="text-xs font-black text-gray-900 uppercase italic tracking-tight mb-2">
                                {{ $item->shoe_brand }} {{ $item->shoe_type }}
                            </p>
                            <ul class="space-y-1.5 sm:space-y-1">
                                @foreach($item->workOrderServices as $serviceLine)
                                    <li class="text-[10px] font-bold text-gray-600 italic flex items-start gap-1">
                                        <span class="text-[#22AF85]">•</span>
                                        <span class="flex-1">{{ $serviceLine->custom_service_name ?? ($serviceLine->service->name ?? 'Service') }}</span>
                                        <span class="text-gray-400 font-normal tabular-nums ml-1">(Rp {{ number_format($serviceLine->cost, 0, ',', '.') }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="col-span-1 sm:col-span-3 flex sm:block justify-between items-center sm:text-right pt-3 sm:pt-0 border-t border-gray-50 sm:border-0">
                            <span class="mobile-label sm:hidden text-[9px] font-black text-gray-400 uppercase italic">Subtotal</span>
                            <span class="text-sm sm:text-base font-black text-gray-900 italic tabular-nums tracking-tighter">
                                Rp. {{ number_format($item->total_transaksi, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="flex-1 bg-transparent hidden sm:block"></div>
            </div>

            <!-- Summary Section -->
            <div class="summary-section mt-8 flex flex-col lg:flex-row justify-between items-stretch lg:items-end gap-8">
                <!-- Payment Methods -->
                <div class="w-full lg:w-[380px] bg-white rounded-3xl p-6 shadow-xl border border-gray-100 flex flex-col gap-5 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 bg-[#22AF85] h-full"></div>
                    <div>
                        <p class="text-[9px] font-black text-[#22AF85] uppercase tracking-[0.2em] italic mb-4">Payment Methods</p>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-8 bg-slate-50 flex items-center justify-center text-[9px] font-black italic text-blue-900 border rounded shadow-sm">BCA</div>
                                <div>
                                    <p class="text-[12px] font-black text-gray-900 italic tracking-tight">8100978521</p>
                                    <p class="text-[7px] font-bold text-gray-400 uppercase tracking-widest mt-1 italic leading-none">PT. Terang Garam Solusindo</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-8 bg-slate-50 flex items-center justify-center text-[9px] font-black italic text-blue-700 border rounded shadow-sm">Mandiri</div>
                                <div>
                                    <p class="text-[12px] font-black text-gray-900 italic tracking-tight">1300030119047</p>
                                    <p class="text-[7px] font-bold text-gray-400 uppercase tracking-widest mt-1 italic leading-none">PT. Terang Garam Solusindo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Totals Grid -->
                <div class="flex-1 flex flex-col gap-6 sm:gap-8">
                    <div class="totals-grid grid grid-cols-2 gap-y-5 gap-x-8 px-2 sm:px-0">
                        <div class="text-right">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic">Subtotal</p>
                            <p class="text-sm sm:text-base font-black text-gray-900 italic tabular-nums leading-none tracking-tighter">Rp. {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic">Shipping</p>
                            <p class="text-sm sm:text-base font-black text-gray-900 italic tabular-nums leading-none tracking-tighter">Rp. {{ number_format($invoice->shipping_cost ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic">DP / Paid</p>
                            <p class="text-sm sm:text-base font-black text-[#22AF85] italic tabular-nums leading-none tracking-tighter">Rp. {{ number_format($invoice->paid_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic">Discount</p>
                            <p class="text-sm sm:text-base font-black text-red-500 italic tabular-nums leading-none tracking-tighter">- Rp. {{ number_format($invoice->discount, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-end gap-4 sm:gap-8 relative">
                        @php
                            $remaining = ($invoice->total_amount + ($invoice->shipping_cost ?? 0)) - $invoice->paid_amount - $invoice->discount;
                        @endphp
                        @if($remaining <= 0 && $invoice->total_amount > 0)
                        <div class="absolute -top-14 sm:right-52 right-auto left-4 sm:left-auto transform -rotate-12 z-30 pointer-events-none">
                            <div class="border-4 border-emerald-500 text-emerald-500 px-6 py-2 rounded-xl font-black text-3xl tracking-widest uppercase shadow-lg bg-white/50 backdrop-blur-md">LUNAS</div>
                        </div>
                        @endif

                        <p class="text-[10px] font-black text-gray-900 uppercase tracking-[0.2em] italic">Sisa Bayar</p>
                        <div class="bg-[#22AF85] px-8 sm:px-12 py-4 rounded-2xl sm:rounded-[1.5rem] shadow-2xl w-full sm:w-auto text-center min-w-[240px]">
                            <p class="text-white font-black italic text-xl sm:text-2xl tabular-nums tracking-tighter leading-none">Rp. {{ number_format($remaining, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Designer Footer Strip -->
        <div class="px-6 sm:px-10 pb-10 pt-6 mt-auto">
            <div class="footer-grid grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-[#FFC232] rounded-3xl p-6 flex flex-col gap-5 shadow-xl relative overflow-hidden h-full">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                        <p class="text-[9px] font-black text-[#8B6B1B] uppercase italic mb-1 sm:mb-0">Shipping Partner</p>
                        <div class="flex gap-4">
                            <span class="text-[10px] font-black italic text-gray-900">TIKI</span>
                            <span class="text-[10px] font-black italic text-blue-900 lowercase first-letter:uppercase">Lion parcel</span>
                            <span class="text-[10px] font-black italic text-[#22AF85]">PCP EXPRESS</span>
                        </div>
                    </div>
                    <div class="h-px bg-white/30"></div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                        <p class="text-[9px] font-black text-[#8B6B1B] uppercase italic mb-1 sm:mb-0">Social Media</p>
                        <div class="flex gap-5">
                            <span class="flex items-center gap-1.5 text-[10px] font-black text-gray-900 italic">
                                <div class="w-3 h-3 rounded bg-gray-900 shadow-sm"></div> Shoe Workshop
                            </span>
                            <span class="flex items-center gap-1.5 text-[10px] font-black text-gray-900 italic">
                                <div class="w-3 h-3 rounded bg-gray-900 shadow-sm"></div> shoe_workshop
                            </span>
                        </div>
                    </div>
                </div>

                <div class="relative pl-8 flex items-center border-l-4 border-emerald-500/20">
                    <p class="text-[10px] sm:text-[11px] text-gray-800 font-bold leading-relaxed italic">
                        <span class="font-black text-[#22AF85] uppercase tracking-[0.2em] block mb-2 italic">Cheers to the memories, stories, and miles</span>
                        we've covered together! Your loyalty to Shoe Workshop makes every repair more than a service — it's a shared experience.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- PAGE 2: APPENDIX (PHOTOS) -->
    <!-- We check if there are any photos across all SPKs first to avoid an empty blank page -->
    @php
        $hasAnyPhotos = $invoice->workOrders->contains(function($wo) {
            return $wo->warehouseBeforePhotos->isNotEmpty();
        });
    @endphp

    @if($hasAnyPhotos)
    <div class="invoice-paper" style="page-break-before: always; border-top: none;">
        <!-- Simple Header for Appendix -->
        <div class="bg-gray-900 min-h-[100px] w-full px-6 sm:px-10 py-8 flex flex-col justify-center">
            <h2 class="text-white text-xl sm:text-2xl font-black italic tracking-wide">Lampiran Dokumentasi Awal</h2>
            <p class="text-gray-400 text-xs sm:text-sm mt-1 uppercase tracking-widest">INV: {{ $invoice->invoice_number }}</p>
        </div>

        <div class="content-body flex-1 px-4 sm:px-10 py-8 bg-[#F8FAFC]">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($invoice->workOrders as $item)
                    @php
                        // Get the Cover Photo or the first one if no cover exists
                        $coverPhoto = $item->warehouseBeforePhotos->where('is_spk_cover', true)->first() 
                                   ?? $item->warehouseBeforePhotos->first();
                    @endphp
                    
                    @if($coverPhoto)
                    <!-- Photo Card -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col" style="page-break-inside: avoid;">
                        <div class="aspect-square bg-gray-100 relative">
                            <img src="{{ $coverPhoto->photo_url }}" alt="Before {{ $item->spk_number }}" class="w-full h-full object-cover">
                            <!-- Overlay Badge -->
                            <div class="absolute top-3 right-3 bg-black/70 backdrop-blur-sm text-white px-3 py-1 rounded-full text-[9px] font-black tracking-wider uppercase">
                                BEFORE
                            </div>
                        </div>
                        <div class="p-4 flex flex-col items-center text-center bg-white">
                            <span class="text-[10px] font-black text-[#22AF85] uppercase tracking-[0.1em] mb-1 italic">{{ $item->spk_number }}</span>
                            <span class="text-xs font-black text-gray-900 leading-tight">{{ $item->shoe_brand }}</span>
                            <span class="text-[10px] font-bold text-gray-500 mt-0.5 truncate w-full">{{ $item->shoe_type }}</span>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif
</body>
</html>
