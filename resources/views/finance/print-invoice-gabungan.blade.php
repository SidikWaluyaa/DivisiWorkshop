<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Gabungan #{{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: #cbd5e1;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .invoice-paper {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: white;
            position: relative;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            overflow: hidden;
            box-shadow: 0 10px 50px rgba(0,0,0,0.1);
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

        @media (max-width: 210mm) {
            .invoice-paper {
                width: 100%;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
            }
            body { background: white; }
        }

        .floating-card {
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            border-radius: 3rem;
        }

        .table-clip {
            border-bottom-left-radius: 2.5rem;
            border-bottom-right-radius: 2.5rem;
        }

        .info-box {
            background-color: #F8FAFC;
            border-radius: 0.75rem;
            border: 1px solid #F1F5F9;
            padding: 0.4rem 1rem;
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
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(34, 175, 133, 0.3);
            background: #1A8A6A;
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
        @if(!isset($is_public))
        <button onclick="window.close()" class="bg-white hover:bg-gray-50 text-gray-400 font-bold py-2 px-6 rounded-full shadow-lg border border-gray-100 transition-all text-[9px] uppercase tracking-widest">
            Keluar
        </button>
        @endif
    </div>

    <div class="invoice-paper">
        <!-- Header -->
        <div class="topo-bg h-32 w-full px-10 pt-8 flex items-start box-border relative">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-xl transform -rotate-6">
                    <svg class="w-8 h-8 text-[#22AF85]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                        <circle cx="12" cy="12" r="5"/>
                    </svg>
                </div>
                <div class="text-white mt-1">
                    <h1 class="text-lg font-black tracking-tight leading-none uppercase italic">Shoe Workshop</h1>
                    <p class="text-[24px] font-light tracking-wide opacity-90 mt-0.5 italic leading-none">Comb. Invoice</p>
                </div>
            </div>

            <!-- Floating Card -->
            <div class="absolute top-6 right-10 w-[440px] bg-white floating-card p-7 z-20 border border-gray-50 flex flex-col md:flex-row md:items-start gap-0">
                <div class="grid grid-cols-2 gap-x-8 gap-y-5 w-full">
                    <div>
                        <p class="label-text text-[#22AF85]">Dear Our Beloved Customer</p>
                        <div class="info-box">
                            <span class="text-xs font-black text-gray-900 italic tracking-tight">{{ $invoice->customer->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="label-text text-gray-400">Generated Date</p>
                        <div class="info-box">
                            <span class="text-[10px] font-bold text-gray-500 tracking-tight">{{ now()->format('d-M-Y H:i:s') }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="label-text text-gray-400">Invoice Number</p>
                        <div class="info-box overflow-hidden">
                            <span class="text-xs font-black text-gray-900 tracking-tight uppercase">{{ $invoice->invoice_number }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="label-text text-gray-400">Shipment</p>
                        <div class="info-box">
                            <span class="text-xs font-black text-gray-900 uppercase italic">{{ $invoice->delivery_type ?? 'OFFLINE' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Body -->
        <div class="flex-1 px-10 pt-20 pb-4 bg-[#F8FAFC] flex flex-col">
            <!-- Table -->
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden table-clip flex flex-col min-h-[360px]">
                <table class="w-full text-left">
                    <thead class="bg-[#22AF85] text-white">
                        <tr>
                            <th class="py-3 px-6 text-[9px] font-black uppercase tracking-[0.2em] italic">No</th>
                            <th class="py-3 px-6 text-[9px] font-black uppercase tracking-[0.2em] italic">SPK Bengkel</th>
                            <th class="py-3 px-6 text-[9px] font-black uppercase tracking-[0.2em] italic">Detail Item & Service</th>
                            <th class="py-3 px-6 text-[9px] font-black uppercase tracking-[0.2em] italic text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($invoice->workOrders as $index => $item)
                        <tr class="hover:bg-gray-50/30 transition-colors align-top">
                            <td class="py-4 px-6 w-12">
                                <span class="text-xs font-black text-gray-500 italic">{{ $index + 1 }}</span>
                            </td>
                            <td class="py-4 px-6 w-32">
                                <span class="text-[10px] font-black text-gray-800 uppercase tracking-tight italic bg-gray-100 border border-gray-200 px-2 py-1 rounded">
                                    {{ $item->spk_number }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-xs font-black text-gray-800 uppercase italic tracking-tight mb-1">
                                    {{ $item->shoe_brand }} {{ $item->shoe_type }}
                                </p>
                                <ul class="list-none p-0 m-0 space-y-1">
                                    @if(count($item->workOrderServices) > 0)
                                        @foreach($item->workOrderServices as $serviceLine)
                                            <li class="text-[10px] font-bold text-gray-600 tracking-tight italic">
                                                <span class="text-gray-400 mr-1">-</span>
                                                {{ $serviceLine->custom_service_name ?? ($serviceLine->service->name ?? 'Layanan') }} 
                                                <span class="text-gray-400 font-normal ml-1"> (Rp {{ number_format($serviceLine->cost, 0, ',', '.') }})</span>
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="text-[10px] font-bold text-gray-400 italic">- Belum ada pengerjaan -</li>
                                    @endif
                                </ul>
                            </td>
                            <td class="py-4 px-6 text-right font-black text-gray-900 italic tabular-nums text-sm tracking-tighter text-nowrap align-bottom">
                                Rp. {{ number_format($item->total_transaksi, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex-1 bg-transparent"></div>
            </div>

            <!-- Summary -->
            <div class="mt-6 flex flex-col md:flex-row justify-between items-end gap-6">
                <!-- Payment Method -->
                <div class="w-full md:w-[350px] bg-white rounded-[1.75rem] p-5 shadow-lg border border-gray-100 flex flex-col justify-between h-36 relative overflow-hidden">
                    <div class="bg-[#22AF85] -mt-5 -ml-5 -mr-5 px-5 py-2.5 mb-4">
                        <p class="text-white text-[8px] font-black uppercase tracking-[0.2em] italic">Payment Method</p>
                    </div>
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-6 bg-slate-50 flex items-center justify-center text-[8px] font-black italic text-blue-900 border rounded shadow-sm">BCA</div>
                            <div>
                                <p class="text-[11px] font-black text-gray-900 italic leading-none tracking-tight">8100978521</p>
                                <p class="text-[7px] font-bold text-gray-400 uppercase tracking-widest mt-1 italic leading-none">PT. Terang Garam Solusindo</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-6 bg-slate-50 flex items-center justify-center text-[8px] font-black italic text-blue-700 border rounded shadow-sm">Mandiri</div>
                            <div>
                                <p class="text-[11px] font-black text-gray-900 italic leading-none tracking-tight">1300030119047</p>
                                <p class="text-[7px] font-bold text-gray-400 uppercase tracking-widest mt-1 italic leading-none">PT. Terang Garam Solusindo</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Totals -->
                <div class="flex-1 flex flex-col gap-6 pb-1">
                    <div class="grid grid-cols-2 gap-x-8 gap-y-4 px-4 pr-6">
                        <div class="text-right">
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic leading-none">Subtotal</p>
                            <p class="text-sm font-black text-gray-800 italic tracking-tighter tabular-nums leading-none">Rp. {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic leading-none">Shipping Cost</p>
                            <p class="text-sm font-black text-gray-800 italic tracking-tighter tabular-nums leading-none">Rp. {{ number_format($invoice->shipping_cost ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic leading-none">DP / Paid</p>
                            <p class="text-sm font-black text-[#22AF85] italic tracking-tighter tabular-nums leading-none">Rp. {{ number_format($invoice->paid_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            @if($invoice->discount > 0)
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic leading-none">Discount</p>
                                <p class="text-sm font-black text-red-500 italic tracking-tighter tabular-nums leading-none">- Rp. {{ number_format($invoice->discount, 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-8 pr-4 relative">
                        <!-- Lunas Stamp overlay -->
                        @php
                            $remaining = $invoice->total_amount + ($invoice->shipping_cost ?? 0) - $invoice->paid_amount - $invoice->discount;
                        @endphp
                        @if( $remaining <= 0 && $invoice->total_amount > 0 )
                        <div class="absolute -top-12 right-24 transform -rotate-12 opacity-80 z-30">
                            <div class="border-4 border-emerald-500 text-emerald-500 px-6 py-2 rounded-lg font-black text-2xl tracking-widest uppercase inline-block shadow-sm background-white/50 backdrop-blur-sm">LUNAS</div>
                        </div>
                        @endif

                        <p class="text-[9px] font-black text-gray-900 uppercase tracking-[0.2em] italic leading-none">Sisa Bayar</p>
                        <div class="bg-[#22AF85] px-10 py-3 rounded-[1rem] shadow-xl min-w-[220px] text-center">
                            <p class="text-white font-black italic text-xl tabular-nums tracking-tighter leading-none">Rp. {{ number_format($remaining, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Designer Footer Strip -->
        <div class="px-10 pb-10 pt-4 mt-auto">
            <div class="flex flex-col md:flex-row items-stretch gap-8 h-auto md:h-28">
                <div class="w-full md:w-1/2 h-full">
                    <div class="bg-[#FFC232] rounded-[1.75rem] p-6 flex flex-col justify-center gap-4 shadow-xl relative overflow-hidden group h-full">
                        <div class="flex items-center gap-6 relative z-10">
                            <p class="text-[8px] font-black text-[#8B6B1B] uppercase tracking-[0.2em] italic leading-none">Shipping Partner</p>
                            <div class="flex gap-4 items-center">
                                <span class="text-[9.5px] font-black italic text-gray-900">TIKI</span>
                                <span class="text-[9.5px] font-black italic text-blue-900">Lion parcel</span>
                                <span class="text-[10px] font-black italic text-[#22AF85]">PCP EXPRESS</span>
                            </div>
                        </div>
                        <div class="h-px bg-white/25 relative z-10"></div>
                        <div class="flex items-center gap-8 relative z-10">
                            <p class="text-[8px] font-black text-[#8B6B1B] uppercase tracking-[0.2em] italic leading-none">Stay updated</p>
                            <div class="flex gap-5 items-center">
                                <span class="flex items-center gap-1.5 text-[9.5px] font-black text-gray-900 italic">
                                    <div class="w-3 h-3 rounded bg-gray-900 shadow-sm"></div> Shoe Workshop
                                </span>
                                <span class="flex items-center gap-1.5 text-[9.5px] font-black text-gray-900 italic">
                                    <div class="w-3 h-3 rounded bg-gray-900 shadow-sm"></div> shoe_workshop
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2 flex items-center pr-2">
                    <div class="relative pl-7 h-full flex flex-col justify-center">
                        <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-[#22AF85] via-emerald-400 to-[#22AF85]/10 rounded-full shadow-sm"></div>
                        <p class="text-[9.5px] text-gray-800 font-bold leading-relaxed italic pr-4">
                            <span class="font-black text-[#22AF85] uppercase tracking-[0.2em] text-[9.5px] block mb-2 italic">Cheers to the memories, stories, and miles</span>
                            we've covered together! Your loyalty to Shoe Workshop makes every repair more than a service — it's a shared experience.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
