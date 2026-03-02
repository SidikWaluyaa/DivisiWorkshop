<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <title>Invoice Premium #{{ $order->spk_number }}</title>
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
            .desktop-header { display: grid !important; }
            .mobile-label { display: none !important; }
            .item-row { display: grid !important; grid-template-columns: repeat(12, minmax(0, 1fr)) !important; padding: 12px 32px !important; gap: 0 !important; }
            .summary-section { flex-direction: row !important; align-items: flex-end !important; }
            .totals-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
            .footer-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
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
                    <p class="text-xl sm:text-[24px] font-light tracking-wide opacity-90 mt-0.5 italic leading-none">Payment Invoices</p>
                </div>
            </div>

            <!-- Header Info Card -->
            <div class="floating-card w-full sm:w-[480px] bg-white p-6 sm:p-8 z-20 border border-gray-100 flex flex-col mt-6 sm:mt-0 sm:absolute sm:top-6 sm:right-10">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 w-full">
                    <div>
                        <p class="label-text text-[#22AF85]">Customer Name</p>
                        <div class="info-box">
                            <span class="text-xs font-black text-gray-900 italic truncate">{{ $order->customer_name }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="label-text text-gray-400">Shipment Method</p>
                        <div class="info-box">
                            <span class="text-xs font-black text-gray-900 uppercase italic">{{ $order->shipping_type ?? 'OFFLINE' }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="label-text text-gray-400">Invoice Number</p>
                        <div class="info-box overflow-hidden">
                            <span class="text-[10px] sm:text-xs font-black text-gray-900 tracking-tight uppercase">{{ $order->spk_number }}</span>
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
            <!-- Items Table -->
            <div class="bg-white rounded-3xl sm:rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden flex flex-col min-h-[300px]">
                <!-- Desktop Header -->
                <div class="desktop-header hidden sm:grid grid-cols-12 bg-[#22AF85] text-white py-3 px-8 text-[10px] font-black uppercase tracking-widest italic">
                    <div class="col-span-1">No</div>
                    <div class="col-span-3">Item Details</div>
                    <div class="col-span-5">Service Description</div>
                    <div class="col-span-1 text-center">Qty</div>
                    <div class="col-span-2 text-right">Total</div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($order->workOrderServices as $index => $detail)
                    <div class="item-row grid grid-cols-1 sm:grid-cols-12 p-5 sm:py-5 sm:px-8 hover:bg-gray-50/50 transition-colors gap-3 sm:gap-0">
                        <!-- No -->
                        <div class="hidden sm:flex col-span-1 items-center">
                            <span class="text-xs font-black text-gray-400 italic">{{ $index + 1 }}</span>
                        </div>

                        <!-- Item Details -->
                        <div class="col-span-1 sm:col-span-3 flex flex-col">
                            <span class="mobile-label sm:hidden text-[8px] font-black text-gray-300 uppercase italic">Item</span>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5 italic">{{ $order->shoe_type }}</p>
                            <p class="text-xs font-black text-gray-900 uppercase italic tracking-tight">{{ $order->shoe_brand }}</p>
                        </div>

                        <!-- Service Details -->
                        <div class="col-span-1 sm:col-span-5 flex flex-col">
                            <span class="mobile-label sm:hidden text-[8px] font-black text-gray-300 uppercase italic">Service</span>
                            <p class="text-xs font-black text-gray-700 uppercase italic tracking-tight leading-tight">
                                {{ $detail->custom_service_name ?? ($detail->service ? $detail->service->name : 'General Service') }}
                            </p>
                        </div>

                        <!-- Qty -->
                        <div class="col-span-1 sm:col-span-1 flex sm:justify-center items-center gap-2">
                            <span class="mobile-label sm:hidden text-[8px] font-black text-gray-300 uppercase italic">Qty:</span>
                            <span class="text-[11px] font-black text-gray-500 italic tabular-nums">1</span>
                        </div>

                        <!-- Total -->
                        <div class="col-span-1 sm:col-span-2 flex sm:block justify-between items-center sm:text-right pt-2 sm:pt-0 border-t border-gray-50 sm:border-0">
                            <span class="mobile-label sm:hidden text-[9px] font-black text-gray-400 uppercase italic text-right">Subtotal</span>
                            <span class="text-sm sm:text-base font-black text-gray-900 italic tabular-nums tracking-tighter">
                                Rp. {{ number_format($detail->cost, 0, ',', '.') }}
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
                            <p class="text-sm md:text-base font-black text-gray-900 italic tabular-nums leading-none tracking-tighter">Rp. {{ number_format($order->total_transaksi + $order->discount - $order->shipping_cost, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic">Shipping</p>
                            <p class="text-sm md:text-base font-black text-gray-900 italic tabular-nums leading-none tracking-tighter">Rp. {{ number_format($order->shipping_cost, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic">DP / Paid</p>
                            <p class="text-sm md:text-base font-black text-[#22AF85] italic tabular-nums leading-none tracking-tighter">Rp. {{ number_format($order->payments->sum('amount_total'), 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic">Discount</p>
                            <p class="text-sm md:text-base font-black text-red-500 italic tabular-nums leading-none tracking-tighter">- Rp. {{ number_format($order->discount, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Grand Total / Remaining -->
                    <div class="flex flex-col sm:flex-row items-center justify-end gap-4 md:gap-8">
                        <p class="text-[10px] font-black text-gray-900 uppercase tracking-[0.2em] italic">Sisa Bayar</p>
                        <div class="bg-[#22AF85] px-8 md:px-12 py-4 rounded-2xl md:rounded-[1.5rem] shadow-2xl w-full sm:w-auto text-center min-w-[240px]">
                            <p class="text-white font-black italic text-xl md:text-2xl tabular-nums tracking-tighter leading-none">Rp. {{ number_format($order->sisa_tagihan, 0, ',', '.') }}</p>
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
</body>
</html>
