<x-workshop-pwa-layout title="Detail Manifest Outbound">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

    .manifest-font {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .topo-header {
        background-color: #0f172a;
        background-image: radial-gradient(#1e293b 1px, transparent 1px);
        background-size: 16px 16px;
    }

    @media print {
        /* 1. Hide web shell & web view */
        nav, header, aside, form, button, 
        .print\:hidden, [role="navigation"], 
        #sidebar, #navbar, input, select, .no-print {
            display: none !important;
        }

        /* 2. Page setup */
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        html, body, main, div {
            background-color: #ffffff !important;
            color: #0f172a !important;
            overflow: visible !important;
            height: auto !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .print-document {
            display: block !important;
            width: 100% !important;
            color: #0f172a !important;
            background: #ffffff !important;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .page-break-inside-avoid {
            page-break-inside: avoid !important;
        }
    }
</style>

{{-- ========================================== --}}
{{-- 1. SCREEN VIEW (Hidden on Print)           --}}
{{-- ========================================== --}}
<div class="py-8 bg-slate-100 dark:bg-gray-900 min-h-screen manifest-font print:hidden">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Navigation & Top Actions --}}
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <a href="{{ route('qc.outbound') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-teal-600 dark:text-gray-400 dark:hover:text-teal-400 transition-colors mb-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Staging Outbound
                </a>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight flex items-center gap-3">
                    Detail Manifest Outbound
                </h1>
            </div>
            
            <div class="flex items-center gap-3 flex-wrap">
                @if($manifest->status === 'SENT')
                    <span class="px-3.5 py-1.5 rounded-xl bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 font-black text-xs uppercase tracking-wider border border-amber-200 dark:border-amber-800">
                        SENT / DIKIRIM
                    </span>
                @else
                    <span class="px-3.5 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 font-black text-xs uppercase tracking-wider border border-emerald-200 dark:border-emerald-800">
                        RECEIVED / DITERIMA GUDANG
                    </span>
                @endif

                <a href="{{ route('qc.outbound.print', $manifest->id) }}" target="_blank" class="inline-flex items-center px-5 py-2.5 bg-slate-900 hover:bg-black text-white dark:bg-teal-600 dark:hover:bg-teal-500 font-black text-xs rounded-xl shadow-lg transition-all active:scale-95 cursor-pointer uppercase tracking-wider">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Surat Jalan
                </a>
            </div>
        </div>

        {{-- WEB PAPER CARD --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-slate-200 dark:border-gray-700 overflow-hidden">
            
            {{-- HEADER BANNER --}}
            <div class="topo-header text-white p-6 sm:p-8 relative">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="px-3 py-1 rounded-lg bg-[#22AF85] text-white font-black text-xs uppercase tracking-widest">
                                ShoeWorkshop
                            </span>
                            <span class="text-xs font-semibold text-slate-400">Pusat QC & Workshop Hijau</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white">
                            SURAT JALAN MANIFEST OUTBOUND
                        </h2>
                        <p class="text-xs text-slate-300 font-medium mt-1">Dokumen Resmi Serah Terima Barang dari QC Workshop ke Gudang Utama</p>
                    </div>

                    {{-- MANIFEST NUMBER BADGE BOX --}}
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 text-right min-w-[220px]">
                        <p class="text-[9px] font-black uppercase tracking-widest text-teal-300">NOMOR MANIFEST</p>
                        <p class="text-lg font-mono font-black tracking-wider text-white mt-0.5">{{ $manifest->manifest_number }}</p>
                        <p class="text-[10px] text-slate-300 font-semibold mt-1">
                            TGL: {{ $manifest->dispatched_at ? $manifest->dispatched_at->translatedFormat('d M Y • H:i') : now()->translatedFormat('d M Y • H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- METADATA INFO CARDS --}}
            <div class="p-6 sm:p-8 space-y-6">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Pengirim Box --}}
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-gray-750 border border-slate-200 dark:border-gray-700">
                        <p class="text-[9px] font-black text-slate-400 dark:text-gray-400 uppercase tracking-widest">PENGIRIM (WORKSHOP QC)</p>
                        <p class="text-sm font-black text-slate-900 dark:text-white mt-1">QC Workshop Hijau</p>
                        <div class="mt-2 text-xs text-slate-600 dark:text-gray-300 font-medium space-y-0.5">
                            <p><span class="font-bold">Dispatcher:</span> {{ $manifest->dispatcher->name ?? 'Admin Workshop' }}</p>
                            <p><span class="font-bold">Lokasi Asal:</span> Staging Outbound Terpadu</p>
                        </div>
                    </div>

                    {{-- Penerima Box --}}
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-gray-750 border border-slate-200 dark:border-gray-700">
                        <p class="text-[9px] font-black text-slate-400 dark:text-gray-400 uppercase tracking-widest">TUJUAN (GUDANG UTAMA)</p>
                        <p class="text-sm font-black text-slate-900 dark:text-white mt-1">Finished Store / Gudang Utama</p>
                        <div class="mt-2 text-xs text-slate-600 dark:text-gray-300 font-medium space-y-0.5">
                            <p><span class="font-bold">Penerima Gudang:</span> {{ $manifest->receiver->name ?? '( Menunggu Konfirmasi )' }}</p>
                            <p><span class="font-bold">Status Serah Terima:</span> 
                                <span class="font-black uppercase {{ $manifest->status === 'RECEIVED' ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $manifest->status === 'RECEIVED' ? 'Diterima Gudang' : 'Dalam Pengiriman' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- TABLE OF SPK ITEMS --}}
                <div class="border border-slate-200 dark:border-gray-700 rounded-2xl overflow-hidden">
                    <div class="px-5 py-3.5 bg-slate-900 text-white font-black text-xs uppercase tracking-wider flex justify-between items-center">
                        <span>Rincian Item SPK Dalam Manifest ({{ $manifest->workOrders->count() }} SPK)</span>
                        <span class="text-[10px] font-normal tracking-normal text-slate-300">QC PASSED</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-gray-700 text-left">
                            <thead class="bg-slate-100 dark:bg-gray-750 text-[10px] font-black text-slate-500 dark:text-gray-400 uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-4 text-center w-12 border-r border-slate-200 dark:border-gray-700">NO</th>
                                    <th class="py-3 px-4 border-r border-slate-200 dark:border-gray-700">NOMOR SPK</th>
                                    <th class="py-3 px-4 border-r border-slate-200 dark:border-gray-700">PELANGGAN</th>
                                    <th class="py-3 px-4 border-r border-slate-200 dark:border-gray-700">BRAND & SEPATU</th>
                                    <th class="py-3 px-4">LAYANAN JASA</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-gray-700 text-xs bg-white dark:bg-gray-800">
                                @forelse($manifest->workOrders as $index => $item)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-gray-750/50 transition-colors">
                                        <td class="py-3.5 px-4 text-center font-bold text-slate-400 border-r border-slate-200 dark:border-gray-700">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="py-3.5 px-4 font-mono font-black text-slate-900 dark:text-white border-r border-slate-200 dark:border-gray-700">
                                            {{ $item->spk_number }}
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-gray-200 border-r border-slate-200 dark:border-gray-700">
                                            {{ $item->customer_name }}
                                        </td>
                                        <td class="py-3.5 px-4 text-slate-600 dark:text-gray-400 border-r border-slate-200 dark:border-gray-700 font-medium">
                                            {{ $item->shoe_brand }} - {{ $item->shoe_type }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($item->workOrderServices as $svc)
                                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                                        {{ $svc->custom_service_name ?? ($svc->service ? $svc->service->name : 'Layanan') }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400 italic">
                                            Tidak ada SPK terhubung dalam manifest ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- CATATAN DISPATCHER --}}
                @if($manifest->notes)
                <div class="p-4 rounded-2xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 text-xs">
                    <p class="font-black text-amber-800 dark:text-amber-400 uppercase tracking-widest text-[9px]">CATATAN PENGIRIMAN DISPATCHER:</p>
                    <p class="text-slate-700 dark:text-gray-300 italic font-medium mt-1">"{{ $manifest->notes }}"</p>
                </div>
                @endif

                {{-- LEMBAR PENGESAHAN & SERAH TERIMA SIGNATURES --}}
                <div class="pt-6 border-t border-slate-200 dark:border-gray-700">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-400 mb-6 text-center">
                        LEMBAR PENGESAHAN & SERAH TERIMA PENGIRIMAN BARANG
                    </p>

                    <div class="grid grid-cols-3 gap-6 text-center text-xs">
                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-750">
                            <p class="font-black text-slate-800 dark:text-gray-200 uppercase text-[10px] tracking-wider">Pengirim (WS QC)</p>
                            <div class="h-20 flex items-end justify-center pb-2">
                                <span class="font-bold text-slate-900 dark:text-white underline">{{ $manifest->dispatcher->name ?? 'Admin Workshop QC' }}</span>
                            </div>
                            <p class="text-[9px] text-slate-400 dark:text-gray-500 font-semibold">Admin Workshop QC</p>
                        </div>

                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-750">
                            <p class="font-black text-slate-800 dark:text-gray-200 uppercase text-[10px] tracking-wider">Kurir / Logistik</p>
                            <div class="h-20 flex items-end justify-center pb-2">
                                <span class="font-bold text-slate-400 dark:text-gray-500">( .................................... )</span>
                            </div>
                            <p class="text-[9px] text-slate-400 dark:text-gray-500 font-semibold">Petugas Driver / Logistik</p>
                        </div>

                        <div class="p-4 rounded-2xl border border-slate-200 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-750">
                            <p class="font-black text-slate-800 dark:text-gray-200 uppercase text-[10px] tracking-wider">Penerima (Gudang)</p>
                            <div class="h-20 flex items-end justify-center pb-2">
                                <span class="font-bold text-slate-900 dark:text-white underline">{{ $manifest->receiver->name ?? '( .................................... )' }}</span>
                            </div>
                            <p class="text-[9px] text-slate-400 dark:text-gray-500 font-semibold">Admin Gudang Utama</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>


{{-- ========================================== --}}
{{-- 2. PRINT-ONLY VIEW (Dedicated Print Page)  --}}
{{-- ========================================== --}}
<div class="hidden print:block print-document manifest-font" style="padding: 0; margin: 0; background: #ffffff; color: #0f172a;">
    
    @php
        $totalSpkCount = $manifest->workOrders->count();
        $totalSvcCount = $manifest->workOrders->sum(function($item) {
            return $item->workOrderServices?->count() ?? 0;
        });
    @endphp

    {{-- Clean Corporate Header Bar --}}
    <div style="border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 16px;">
        <table style="width: 100%; border: none !important; margin: 0; border-collapse: collapse;">
            <tr style="border: none !important;">
                <td style="border: none !important; vertical-align: top; width: 60%;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="background: #22AF85; color: #ffffff; font-weight: 900; font-size: 11px; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">
                            ShoeWorkshop
                        </span>
                        <span style="font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase;">Pusat QC & Workshop Hijau</span>
                    </div>
                    <h1 style="font-size: 18px; font-weight: 900; margin: 4px 0 2px 0; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px;">
                        SURAT JALAN MANIFEST OUTBOUND
                    </h1>
                    <p style="font-size: 9px; color: #475569; margin: 0;">Dokumen Serah Terima Barang dari QC Workshop ke Gudang Utama</p>
                </td>
                <td style="border: none !important; vertical-align: top; text-align: right; width: 220px;">
                    {{-- Manifest Number Box with Solid Dark Border & Crisp Dark Text --}}
                    <div style="background: #f8fafc; border: 1.5px solid #0f172a; border-radius: 8px; padding: 8px 12px; text-align: right; display: inline-block; width: 100%;">
                        <span style="font-size: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; display: block; text-align: right;">NOMOR MANIFEST</span>
                        <span style="font-family: monospace; font-weight: 900; font-size: 14px; color: #0f172a; display: block; margin-top: 2px; text-align: right; letter-spacing: 0.5px;">{{ $manifest->manifest_number }}</span>
                    </div>
                    <p style="font-size: 9px; font-weight: 700; color: #475569; margin-top: 4px; text-align: right;">
                        TGL KIRIM: {{ $manifest->dispatched_at ? $manifest->dispatched_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }} WIB
                    </p>
                </td>
            </tr>
        </table>
    </div>

    {{-- Highlight Stat Cards --}}
    <table style="width: 100%; border-collapse: separate; border-spacing: 8px; margin-bottom: 14px; margin-left: -8px; margin-right: -8px;">
        <tr style="border: none !important;">
            <td style="background: #f1f5f9; border: 1.5px solid #0f172a; color: #0f172a; padding: 8px 12px; border-radius: 8px; width: 33%;">
                <span style="font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; color: #475569; display: block;">TOTAL MUATAN SPK</span>
                <span style="font-size: 13px; font-weight: 900; color: #0f172a; display: block; margin-top: 2px;">📦 {{ $totalSpkCount }} SPK (Unit Sepatu)</span>
            </td>
            <td style="background: #ecfdf5; border: 1.5px solid #059669; color: #065f46; padding: 8px 12px; border-radius: 8px; width: 33%;">
                <span style="font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; color: #047857; display: block;">TOTAL JASA / LAYANAN</span>
                <span style="font-size: 13px; font-weight: 900; color: #065f46; display: block; margin-top: 2px;">🔨 {{ $totalSvcCount }} Layanan Pengerjaan</span>
            </td>
            <td style="background: #f0f9ff; border: 1.5px solid #0284c7; color: #075985; padding: 8px 12px; border-radius: 8px; width: 33%;">
                <span style="font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; color: #0369a1; display: block;">RUTE MANIFEST</span>
                <span style="font-size: 13px; font-weight: 900; color: #075985; display: block; margin-top: 2px;">🚚 QC WORKSHOP ➔ GUDANG</span>
            </td>
        </tr>
    </table>

    {{-- Meta Metadata Info Table --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px;">
        <tr style="border: none !important;">
            <td style="width: 50%; padding: 10px 14px; border-right: 1px solid #cbd5e1; border-top: none; border-bottom: none; border-left: none; vertical-align: top;">
                <p style="font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin: 0 0 4px 0;">PENGIRIM (WORKSHOP QC):</p>
                <p style="font-size: 13px; font-weight: 900; color: #0f172a; margin: 0 0 4px 0;">QC Workshop Hijau</p>
                <p style="font-size: 10px; color: #334155; margin: 0;"><strong>Dispatcher / PIC:</strong> {{ $manifest->dispatcher->name ?? 'Admin Workshop QC' }}</p>
                <p style="font-size: 10px; color: #334155; margin: 2px 0 0 0;"><strong>Stasiun Asal:</strong> Staging Outbound Terpadu</p>
            </td>
            <td style="width: 50%; padding: 10px 14px; border: none; vertical-align: top;">
                <p style="font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin: 0 0 4px 0;">TUJUAN (GUDANG UTAMA):</p>
                <p style="font-size: 13px; font-weight: 900; color: #0f172a; margin: 0 0 4px 0;">Finished Store / Gudang Utama</p>
                <p style="font-size: 10px; color: #334155; margin: 0;"><strong>Penerima Gudang:</strong> {{ $manifest->receiver->name ?? '( Menunggu Konfirmasi Gudang )' }}</p>
                <p style="font-size: 10px; color: #334155; margin: 2px 0 0 0;"><strong>Status Serah Terima:</strong> <strong style="text-transform: uppercase; color: {{ $manifest->status === 'RECEIVED' ? '#059669' : '#d97706' }};">{{ $manifest->status === 'RECEIVED' ? 'DITERIMA GUDANG' : 'SENT / DALAM PENGIRIMAN' }}</strong></p>
            </td>
        </tr>
    </table>

    {{-- Items Table (Without Paraf Terima) --}}
    <div style="margin-bottom: 14px;">
        <p style="font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 6px 0; color: #0f172a;">
            DAFTAR ITEM SPK DALAM MANIFEST:
        </p>
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #cbd5e1; font-size: 10px;">
            <thead>
                <tr style="background-color: #f1f5f9; color: #334155; text-transform: uppercase; font-weight: 900; font-size: 9px; letter-spacing: 0.5px;">
                    <th style="padding: 7px 8px; border: 1px solid #cbd5e1; text-align: center; width: 30px;">NO</th>
                    <th style="padding: 7px 8px; border: 1px solid #cbd5e1; text-align: left; width: 140px;">NOMOR SPK</th>
                    <th style="padding: 7px 8px; border: 1px solid #cbd5e1; text-align: left; width: 140px;">NAMA PELANGGAN</th>
                    <th style="padding: 7px 8px; border: 1px solid #cbd5e1; text-align: left; width: 160px;">BRAND & SEPATU</th>
                    <th style="padding: 7px 8px; border: 1px solid #cbd5e1; text-align: left;">RINCIAN LAYANAN JASA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($manifest->workOrders as $idx => $item)
                    <tr style="border-bottom: 1px solid #cbd5e1;">
                        <td style="padding: 7px 8px; border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #64748b;">{{ $idx + 1 }}</td>
                        <td style="padding: 7px 8px; border: 1px solid #cbd5e1; font-family: monospace; font-weight: 900; font-size: 11px; color: #0f172a;">{{ $item->spk_number }}</td>
                        <td style="padding: 7px 8px; border: 1px solid #cbd5e1; font-weight: 800; color: #1e293b;">{{ $item->customer_name }}</td>
                        <td style="padding: 7px 8px; border: 1px solid #cbd5e1; color: #334155; font-weight: 600;">{{ $item->shoe_brand }} - {{ $item->shoe_type }}</td>
                        <td style="padding: 7px 8px; border: 1px solid #cbd5e1;">
                            <div style="display: flex; flex-wrap: wrap; gap: 3px;">
                                @foreach($item->workOrderServices as $svc)
                                    <span style="display: inline-block; background: #f8fafc; border: 1px solid #94a3b8; color: #0f172a; padding: 2px 6px; font-size: 8px; font-weight: 800; border-radius: 4px;">
                                        {{ $svc->custom_service_name ?? ($svc->service ? $svc->service->name : 'Layanan') }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Standalone Catatan Dispatcher Box --}}
    @if($manifest->notes)
        <div style="margin-top: 14px; margin-bottom: 18px; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; background-color: #f8fafc;">
            <span style="font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; display: block;">📝 CATATAN PENGIRIMAN DISPATCHER:</span>
            <p style="font-size: 10px; font-weight: 700; font-style: italic; color: #0f172a; margin: 3px 0 0 0; line-height: 1.4;">"{{ $manifest->notes }}"</p>
        </div>
    @endif

    {{-- Signatures --}}
    <div class="page-break-inside-avoid" style="margin-top: 24px;">
        <p style="font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; text-align: center; margin-bottom: 12px; color: #475569;">
            LEMBAR PENGESAHAN & SERAH TERIMA PENGIRIMAN BARANG
        </p>
        <table style="width: 100%; text-align: center; font-size: 10px; border-collapse: separate; border-spacing: 10px; margin-left: -10px; margin-right: -10px;">
            <tr style="border: none !important;">
                <td style="width: 33.3%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; vertical-align: top; background: #f8fafc;">
                    <p style="font-weight: 900; text-transform: uppercase; margin: 0 0 45px 0; font-size: 9px; color: #334155;">PENGIRIM (WS QC)</p>
                    <p style="font-weight: 900; text-decoration: underline; margin: 0; color: #0f172a; font-size: 11px;">{{ $manifest->dispatcher->name ?? 'Admin Workshop QC' }}</p>
                    <p style="font-size: 8px; color: #64748b; margin: 2px 0 0 0; font-weight: 600;">Admin Workshop QC</p>
                </td>
                <td style="width: 33.3%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; vertical-align: top; background: #f8fafc;">
                    <p style="font-weight: 900; text-transform: uppercase; margin: 0 0 45px 0; font-size: 9px; color: #334155;">KURIR / LOGISTIK</p>
                    <p style="font-weight: 900; color: #64748b; margin: 0; font-size: 11px;">( .................................... )</p>
                    <p style="font-size: 8px; color: #64748b; margin: 2px 0 0 0; font-weight: 600;">Petugas Driver / Logistik</p>
                </td>
                <td style="width: 33.3%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; vertical-align: top; background: #f8fafc;">
                    <p style="font-weight: 900; text-transform: uppercase; margin: 0 0 45px 0; font-size: 9px; color: #334155;">PENERIMA (GUDANG)</p>
                    <p style="font-weight: 900; text-decoration: underline; margin: 0; color: #0f172a; font-size: 11px;">{{ $manifest->receiver->name ?? '( .................................... )' }}</p>
                    <p style="font-size: 8px; color: #64748b; margin: 2px 0 0 0; font-weight: 600;">Admin Gudang Utama</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div style="margin-top: 14px; font-size: 8px; color: #64748b; font-weight: 600; display: flex; justify-content: space-between; border-top: 1px solid #e2e8f0; padding-top: 6px;">
        <span>ShoeWorkshop Management System • Dokumen Resmi Pengiriman Outbound</span>
        <span>Dicetak pada: {{ now()->format('d/m/Y H:i') }} WIB • Halaman 1/1</span>
    </div>
</div>

</x-workshop-pwa-layout>
