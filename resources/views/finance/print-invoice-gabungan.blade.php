<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        }

        .floating-card {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 2rem;
        }

        .info-box {
            background-color: #F8FAFC;
            border-radius: 0.75rem;
            border: 1px solid #F1F5F9;
            padding: 0.4rem 0.8rem;
            min-height: 34px;
            display: flex;
            align-items: center;
        }

        .label-text {
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 4px;
            font-style: italic;
        }

        .btn-premium {
            background: #22AF85;
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
            box-shadow: 0 10px 20px rgba(34, 175, 133, 0.2);
            transition: all 0.3s ease;
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
        <div class="topo-bg min-h-[160px] md:h-40 w-full px-6 md:px-10 pt-8 pb-32 md:pb-0 flex flex-col md:flex-row items-start justify-between box-border relative">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-white rounded-xl md:rounded-2xl flex items-center justify-center shadow-2xl transform -rotate-6">
                    <svg class="w-6 h-6 md:w-8 md:h-8 text-[#22AF85]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                        <circle cx="12" cy="12" r="5"/>
                    </svg>
                </div>
                <div class="text-white mt-1">
                    <h1 class="text-base md:text-lg font-black tracking-tight leading-none uppercase italic">Shoe Workshop</h1>
                    <p class="text-xl md:text-[24px] font-light tracking-wide opacity-90 mt-0.5 italic leading-none">Comb. Invoice</p>
                </div>
            </div>

            <!-- Header Info Card (Floating on Desktop, In-flow on Mobile) -->
            <div class="w-full md:w-[480px] bg-white floating-card p-6 md:p-8 z-20 border border-gray-100 flex flex-col mt-6 md:mt-0 md:absolute md:top-6 md:right-10">
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
                            <span class="text-[10px] md:text-xs font-black text-gray-900 tracking-tight uppercase">{{ $invoice->invoice_number }}</span>
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
        <div class="flex-1 px-4 md:px-10 pt-6 md:pt-28 pb-6 bg-[#F8FAFC] flex flex-col">
            <!-- Items Container -->
            <div class="bg-white rounded-3xl md:rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden flex flex-col min-h-[300px]">
                <!-- Table Header (Desktop Only) -->
                <div class="hidden md:grid grid-cols-12 bg-[#22AF85] text-white py-3 px-8 text-[10px] font-black uppercase tracking-widest italic">
                    <div class="col-span-1">No</div>
                    <div class="col-span-3">SPK Number</div>
                    <div class="col-span-5">Items & Services</div>
                    <div class="col-span-3 text-right">Subtotal</div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($invoice->workOrders as $index => $item)
                    <div class="grid grid-cols-1 md:grid-cols-12 p-5 md:py-5 md:px-8 hover:bg-gray-50/50 transition-colors gap-4 md:gap-0">
                        <!-- No & SPK (Combined on Mobile) -->
                        <div class="col-span-1 md:col-span-4 flex items-center gap-4">
                            <span class="text-xs font-black text-gray-400 italic md:w-8">{{ $index + 1 }}</span>
                            <div class="flex-1">
                                <span class="hidden md:inline-block text-[10px] font-black text-gray-800 uppercase italic bg-gray-100 border border-gray-200 px-2 py-1 rounded">
                                    {{ $item->spk_number }}
                                </span>
                                <!-- Mobile SPK Badge -->
                                <div class="md:hidden flex flex-col gap-1">
                                    <span class="text-[8px] font-black text-gray-300 uppercase italic tracking-widest">SPK NUMBER</span>
                                    <span class="text-[11px] font-black text-gray-900 bg-gray-50 px-2 py-1 rounded-lg border w-fit italic">{{ $item->spk_number }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="col-span-1 md:col-span-5">
                            <p class="text-xs font-black text-gray-900 uppercase italic tracking-tight mb-2">
                                {{ $item->shoe_brand }} {{ $item->shoe_type }}
                            </p>
                            <ul class="space-y-1.5 md:space-y-1">
                                @foreach($item->workOrderServices as $serviceLine)
                                    <li class="text-[10px] font-bold text-gray-600 italic flex items-start gap-1">
                                        <span class="text-[#22AF85]">•</span>
                                        <span class="flex-1">{{ $serviceLine->custom_service_name ?? ($serviceLine->service->name ?? 'Service') }}</span>
                                        <span class="text-gray-400 font-normal tabular-nums ml-1">(Rp {{ number_format($serviceLine->cost, 0, ',', '.') }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Subtotal -->
                        <div class="col-span-1 md:col-span-3 flex md:block justify-between items-center md:text-right pt-3 md:pt-0 border-t border-gray-50 md:border-0">
                            <span class="md:hidden text-[9px] font-black text-gray-400 uppercase italic">Subtotal</span>
                            <span class="text-sm md:text-base font-black text-gray-900 italic tabular-nums tracking-tighter">
                                Rp. {{ number_format($item->total_transaksi, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="flex-1 bg-transparent hidden md:block"></div>
            </div>

            <!-- Summary Section -->
            <div class="mt-8 flex flex-col lg:flex-row justify-between items-stretch lg:items-end gap-8">
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
                <div class="flex-1 flex flex-col gap-6 md:gap-8">
                    <div class="grid grid-cols-2 gap-y-5 gap-x-8 px-2 md:px-0">
                        <div class="text-right">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic">Subtotal</p>
                            <p class="text-sm md:text-base font-black text-gray-900 italic tabular-nums leading-none tracking-tighter">Rp. {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic">Shipping</p>
                            <p class="text-sm md:text-base font-black text-gray-900 italic tabular-nums leading-none tracking-tighter">Rp. {{ number_format($invoice->shipping_cost ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic">DP / Paid</p>
                            <p class="text-sm md:text-base font-black text-[#22AF85] italic tabular-nums leading-none tracking-tighter">Rp. {{ number_format($invoice->paid_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic">Discount</p>
                            <p class="text-sm md:text-base font-black text-red-500 italic tabular-nums leading-none tracking-tighter">- Rp. {{ number_format($invoice->discount, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Grand Total / Remaining -->
                    <div class="flex flex-col sm:flex-row items-center justify-end gap-4 md:gap-8 relative">
                        <!-- Lunas Stamp -->
                        @php
                            $remaining = ($invoice->total_amount + ($invoice->shipping_cost ?? 0)) - $invoice->paid_amount - $invoice->discount;
                        @endphp
                        @if($remaining <= 0 && $invoice->total_amount > 0)
                        <div class="absolute -top-14 sm:right-52 right-auto left-4 sm:left-auto transform -rotate-12 z-30 pointer-events-none">
                            <div class="border-4 border-emerald-500 text-emerald-500 px-6 py-2 rounded-xl font-black text-3xl tracking-widest uppercase shadow-lg bg-white/50 backdrop-blur-md">LUNAS</div>
                        </div>
                        @endif

                        <p class="text-[10px] font-black text-gray-900 uppercase tracking-[0.2em] italic">Sisa Bayar</p>
                        <div class="bg-[#22AF85] px-8 md:px-12 py-4 rounded-2xl md:rounded-[1.5rem] shadow-2xl w-full sm:w-auto text-center min-w-[240px]">
                            <p class="text-white font-black italic text-xl md:text-2xl tabular-nums tracking-tighter leading-none">Rp. {{ number_format($remaining, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Designer Footer Strip -->
        <div class="px-6 md:px-10 pb-10 pt-6 mt-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
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
                    <p class="text-[10px] md:text-[11px] text-gray-800 font-bold leading-relaxed italic">
                        <span class="font-black text-[#22AF85] uppercase tracking-[0.2em] block mb-2 italic">Cheers to the memories, stories, and miles</span>
                        we've covered together! Your loyalty to Shoe Workshop makes every repair more than a service — it's a shared experience.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
