<div class="p-8 space-y-8 bg-[#FBFBFB] min-h-screen relative font-sans print:bg-white print:p-0">
    <!-- Global Print Guard -->
    <style>
        @media print {
            nav, header, aside, [role="navigation"], .main-header, .sidebar, .no-print { display: none !important; }
            .print\:hidden { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .report-card { border: 1px solid #eee !important; box-shadow: none !important; }
        }
    </style>

    <!-- Header: Ultra Slim Premium -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 max-w-[1600px] mx-auto">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <div class="w-1.5 h-6 bg-[#FFC232] rounded-full"></div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">PUSAT <span class="text-[#22AF85]">LAPORAN</span> GUDANG</h1>
            </div>
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] ml-5">REKAPITULASI & AUDIT MATERIAL WORKSHOP</p>
        </div>
        
        <div class="flex items-center gap-3 no-print">
            <button onclick="window.print()" class="px-6 py-2.5 bg-gray-900 text-white font-black text-[11px] rounded-xl shadow-lg hover:bg-black transition-all uppercase tracking-widest flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                CETAK REKAP
            </button>
        </div>
    </div>

    <!-- Navigation Tabs & Filter Bar -->
    <div class="max-w-[1600px] mx-auto space-y-6 no-print">
        <div class="flex items-center space-x-1 bg-gray-100 p-1 rounded-2xl w-fit">
            <button wire:click="setTab('purchase')" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $tab === 'purchase' ? 'bg-white shadow-sm text-[#22AF85]' : 'text-gray-400 hover:text-gray-600' }}">REKAP BELANJA</button>
            <button wire:click="setTab('disbursement')" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $tab === 'disbursement' ? 'bg-white shadow-sm text-rose-500' : 'text-gray-400 hover:text-gray-600' }}">REKAP KELUAR</button>
            <button wire:click="setTab('mutation')" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $tab === 'mutation' ? 'bg-white shadow-sm text-blue-500' : 'text-gray-400 hover:text-gray-600' }}">REKAP MUTASI</button>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Dari Tanggal</label>
                <input type="date" wire:model.live="startDate" class="w-full px-4 py-2.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 font-black text-gray-700 text-xs uppercase">
            </div>
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Sampai Tanggal</label>
                <input type="date" wire:model.live="endDate" class="w-full px-4 py-2.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 font-black text-gray-700 text-xs uppercase">
            </div>
            
            @if($tab === 'purchase')
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Status Belanja</label>
                <select wire:model.live="purchaseStatus" class="w-full px-4 py-2.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 font-black text-gray-700 text-xs uppercase">
                    <option value="">SEMUA STATUS</option>
                    <option value="PENDING">PENDING</option>
                    <option value="PROCESSING">PROSES</option>
                    <option value="COMPLETED">SELESAI</option>
                </select>
            </div>
            @elseif($tab === 'mutation')
            <div class="space-y-1.5">
                <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Pilih Material</label>
                <select wire:model.live="materialId" class="w-full px-4 py-2.5 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:ring-4 focus:ring-[#22AF85]/5 font-black text-gray-700 text-xs uppercase">
                    <option value="">SEMUA MATERIAL</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}">{{ $material->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>

    <!-- REPORT CONTENT AREA -->
    <div class="max-w-[1600px] mx-auto space-y-8 print:mt-0">
        <!-- Print Header: Only visible on print -->
        <div class="hidden print:block border-b-4 border-gray-900 pb-6 mb-8">
            <div class="flex justify-between items-end">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 uppercase">LAPORAN REKAPITULASI {{ strtoupper($tab) }} GUDANG</h2>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-1">PERIODE: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">WAKTU CETAK: {{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Summary Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 print:grid-cols-3">
            @if($tab === 'purchase')
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4 report-card">
                    <div class="w-12 h-12 bg-[#22AF85]/10 rounded-2xl flex items-center justify-center text-[#22AF85] no-print">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-gray-300 uppercase tracking-widest">TOTAL NOMINAL BELANJA</div>
                        <div class="text-2xl font-black text-gray-900 tracking-tight mt-1">Rp {{ number_format($summary['total_amount'], 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4 report-card">
                    <div class="w-12 h-12 bg-[#FFC232]/10 rounded-2xl flex items-center justify-center text-[#FFC232] no-print">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-gray-300 uppercase tracking-widest">JUMLAH TRANSAKSI</div>
                        <div class="text-2xl font-black text-gray-900 tracking-tight mt-1">{{ $summary['total_transactions'] }} <small class="text-[10px] text-gray-400">Nota</small></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4 report-card">
                    <div class="w-12 h-12 bg-[#22AF85]/10 rounded-2xl flex items-center justify-center text-[#22AF85] no-print">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-gray-300 uppercase tracking-widest">TOTAL ITEM TERBELI</div>
                        <div class="text-2xl font-black text-gray-900 tracking-tight mt-1">{{ $summary['total_items'] }} <small class="text-[10px] text-gray-400">Pcs</small></div>
                    </div>
                </div>
            @elseif($tab === 'disbursement')
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4 report-card">
                    <div class="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 no-print">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-gray-300 uppercase tracking-widest">TOTAL KUANTITAS KELUAR</div>
                        <div class="text-2xl font-black text-rose-500 tracking-tight mt-1">{{ $summary['total_qty_out'] }} <small class="text-[10px] text-gray-400">Unit</small></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4 report-card">
                    <div class="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 no-print">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-gray-300 uppercase tracking-widest">TRANSAKSI DISTRIBUSI</div>
                        <div class="text-2xl font-black text-gray-900 tracking-tight mt-1">{{ $summary['total_transactions'] }} <small class="text-[10px] text-gray-400">Slip</small></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4 report-card">
                    <div class="w-12 h-12 bg-gray-900/10 rounded-2xl flex items-center justify-center text-gray-900 no-print">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-gray-300 uppercase tracking-widest">ESTIMASI NILAI MATERIAL</div>
                        <div class="text-2xl font-black text-gray-900 tracking-tight mt-1">Rp {{ number_format($summary['total_estimated_value'], 0, ',', '.') }}</div>
                    </div>
                </div>
            @elseif($tab === 'mutation')
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4 report-card">
                    <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 no-print">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-gray-300 uppercase tracking-widest">TOTAL PERGERAKAN STOK</div>
                        <div class="text-2xl font-black text-gray-900 tracking-tight mt-1">{{ $summary['total_mutations'] }} <small class="text-[10px] text-gray-400">Event</small></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4 report-card">
                    <div class="w-12 h-12 bg-[#22AF85]/10 rounded-2xl flex items-center justify-center text-[#22AF85] no-print">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-gray-300 uppercase tracking-widest">TOTAL MASUK (IN)</div>
                        <div class="text-2xl font-black text-[#22AF85] tracking-tight mt-1">{{ number_format($summary['total_in'], 0, ',', '.') }} <small class="text-[10px] text-gray-400">Unit</small></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center space-x-4 report-card">
                    <div class="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 no-print">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                    </div>
                    <div>
                        <div class="text-[9px] font-black text-gray-300 uppercase tracking-widest">TOTAL KELUAR (OUT)</div>
                        <div class="text-2xl font-black text-rose-500 tracking-tight mt-1">{{ number_format($summary['total_out'], 0, ',', '.') }} <small class="text-[10px] text-gray-400">Unit</small></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Data Table: Ultra Slim -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden report-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 print:bg-white print:border-b">
                            <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] w-32">TANGGAL</th>
                            @if($tab === 'purchase')
                                <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">NOMOR NOTA / REF</th>
                                <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">STATUS</th>
                                <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">TOTAL NOMINAL</th>
                            @elseif($tab === 'disbursement')
                                <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">NOMOR SLIP / REF</th>
                                <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">TOTAL ITEM</th>
                                <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">EST. VALUE</th>
                            @elseif($tab === 'mutation')
                                <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">NAMA MATERIAL</th>
                                <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">TIPE</th>
                                <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">QTY</th>
                                <th class="px-8 py-5 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">CATATAN</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($data as $row)
                        <tr class="group hover:bg-[#FBFBFB] transition-all">
                            <td class="px-8 py-4">
                                <span class="text-xs font-black text-gray-900 uppercase tracking-tight">{{ $row->created_at->format('d/m/Y') }}</span>
                            </td>
                            @if($tab === 'purchase')
                                <td class="px-8 py-4">
                                    <p class="text-[11px] font-black text-gray-900 uppercase leading-none">{{ $row->purchase_number }}</p>
                                    @if($row->external_reference)
                                        <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1.5 italic">REF: {{ $row->external_reference }}</p>
                                    @endif
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-3 py-1 bg-gray-50 text-gray-400 rounded-full text-[8px] font-black uppercase tracking-widest border border-gray-100">
                                        {{ $row->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <span class="text-sm font-black text-gray-900">Rp {{ number_format($row->total_amount, 0, ',', '.') }}</span>
                                </td>
                            @elseif($tab === 'disbursement')
                                <td class="px-8 py-4">
                                    <p class="text-[11px] font-black text-gray-900 uppercase leading-none">{{ $row->disbursement_number }}</p>
                                    @if($row->external_reference)
                                        <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1.5 italic">REF: {{ $row->external_reference }}</p>
                                    @endif
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="text-sm font-black text-gray-900">{{ $row->items->sum('quantity') }}</span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <span class="text-sm font-black text-gray-900">Rp {{ number_format($row->total_amount, 0, ',', '.') }}</span>
                                </td>
                            @elseif($tab === 'mutation')
                                <td class="px-8 py-4">
                                    <p class="text-[11px] font-black text-gray-800 uppercase leading-none">{{ $row->material->name }}</p>
                                    <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1.5 italic">{{ $row->material->unit }}</p>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    @if($row->type == 'IN')
                                        <span class="text-[9px] font-black text-[#22AF85] uppercase tracking-widest">MASUK</span>
                                    @else
                                        <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest">KELUAR</span>
                                    @endif
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="text-sm font-black {{ $row->type == 'IN' ? 'text-[#22AF85]' : 'text-rose-500' }}">
                                        {{ $row->type == 'IN' ? '+' : '-' }} {{ number_format($row->quantity, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase italic">{{ $row->notes ?: '-' }}</span>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center opacity-20">
                                <p class="text-xl font-black text-gray-900 uppercase tracking-widest">BELUM ADA DATA PADA PERIODE INI</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer for Print: Signatures -->
        <div class="hidden print:grid grid-cols-2 gap-12 mt-16 text-center">
            <div class="space-y-20">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">KEPALA GUDANG / WAREHOUSE MANAGER</p>
                <div class="space-y-1">
                    <div class="w-48 h-px bg-gray-900 mx-auto"></div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase italic">Tanda Tangan & Nama Terang</p>
                </div>
            </div>
            <div class="space-y-20">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">OWNER / DIREKSI</p>
                <div class="space-y-1">
                    <div class="w-48 h-px bg-gray-900 mx-auto"></div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase italic">Tanda Tangan & Nama Terang</p>
                </div>
            </div>
        </div>
    </div>
</div>
