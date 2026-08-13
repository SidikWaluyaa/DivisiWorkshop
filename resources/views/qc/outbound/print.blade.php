<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan Outbound #{{ $manifest->manifest_number }} — ShoeWorkshop</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: #cbd5e1;
            color: #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .print-paper {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #ffffff;
            padding: 14mm 16mm;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            border-radius: 6px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0 !important;
            }

            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .print-paper {
                width: 210mm !important;
                min-height: 297mm !important;
                margin: 0 !important;
                padding: 10mm 12mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .page-break-avoid {
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

    {{-- FLOATING TOP ACTION BAR (HIDDEN ON PRINT) --}}
    <div class="no-print fixed top-5 left-1/2 transform -translate-x-1/2 z-50 bg-slate-900/95 text-white px-6 py-3 rounded-2xl shadow-2xl backdrop-blur-xl border border-slate-700 flex items-center gap-4">
        <button onclick="window.history.back()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </button>

        <span class="text-xs font-mono font-bold text-teal-400">#{{ $manifest->manifest_number }}</span>

        <button onclick="window.print()" class="px-5 py-2 bg-[#22AF85] hover:bg-emerald-600 text-white text-xs font-black rounded-xl shadow-lg transition-all active:scale-95 flex items-center gap-2 cursor-pointer uppercase tracking-wider">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Surat Jalan
        </button>
    </div>

    {{-- A4 DOCUMENT SHEET --}}
    <div class="print-paper">
        <div>
            @php
                $totalSpkCount = $manifest->workOrders->count();
                $totalSvcCount = $manifest->workOrders->sum(function($item) {
                    return $item->workOrderServices?->count() ?? 0;
                });
            @endphp

            {{-- 1. CORPORATE HEADER --}}
            <div style="border-bottom: 2.5px solid #0f172a; padding-bottom: 12px; margin-bottom: 14px;">
                <table style="width: 100%; border: none !important; border-collapse: collapse;">
                    <tr style="border: none !important;">
                        <td style="border: none !important; vertical-align: top; width: 62%;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <span style="background: #22AF85; color: #ffffff; font-weight: 900; font-size: 11px; padding: 3px 10px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1px;">
                                    ShoeWorkshop
                                </span>
                                <span style="font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
                                    Pusat QC & Workshop Hijau
                                </span>
                            </div>
                            <h1 style="font-size: 18px; font-weight: 900; margin: 4px 0 2px 0; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px;">
                                SURAT JALAN MANIFEST OUTBOUND
                            </h1>
                            <p style="font-size: 9px; font-weight: 600; color: #475569; margin: 0;">
                                Dokumen Resmi Serah Terima Barang dari QC Workshop ke Gudang Utama
                            </p>
                        </td>
                        <td style="border: none !important; vertical-align: top; text-align: right; width: 230px;">
                            {{-- Manifest Number Box --}}
                            <div style="background: #f8fafc; border: 1.5px solid #0f172a; border-radius: 8px; padding: 8px 12px; text-align: right; display: inline-block; width: 100%;">
                                <span style="font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; display: block; text-align: right;">NOMOR MANIFEST</span>
                                <span style="font-family: monospace; font-weight: 900; font-size: 14px; color: #0f172a; display: block; margin-top: 2px; text-align: right; letter-spacing: 0.5px;">{{ $manifest->manifest_number }}</span>
                            </div>
                            <p style="font-size: 9px; font-weight: 700; color: #475569; margin-top: 4px; text-align: right;">
                                TGL KIRIM: {{ $manifest->dispatched_at ? $manifest->dispatched_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }} WIB
                            </p>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- 2. HIGHLIGHT STAT CARDS --}}
            <table style="width: 100%; border-collapse: separate; border-spacing: 8px; margin-bottom: 12px; margin-left: -8px; margin-right: -8px;">
                <tr style="border: none !important;">
                    <td style="background: #f1f5f9; border: 1.5px solid #0f172a; color: #0f172a; padding: 8px 12px; border-radius: 8px; width: 33%;">
                        <span style="font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; color: #475569; display: block;">TOTAL MUATAN SPK</span>
                        <span style="font-size: 12px; font-weight: 900; color: #0f172a; display: block; margin-top: 2px;">📦 {{ $totalSpkCount }} SPK (Unit Sepatu)</span>
                    </td>
                    <td style="background: #ecfdf5; border: 1.5px solid #059669; color: #065f46; padding: 8px 12px; border-radius: 8px; width: 33%;">
                        <span style="font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; color: #047857; display: block;">TOTAL JASA / LAYANAN</span>
                        <span style="font-size: 12px; font-weight: 900; color: #065f46; display: block; margin-top: 2px;">🔨 {{ $totalSvcCount }} Layanan Pengerjaan</span>
                    </td>
                    <td style="background: #f0f9ff; border: 1.5px solid #0284c7; color: #075985; padding: 8px 12px; border-radius: 8px; width: 33%;">
                        <span style="font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; color: #0369a1; display: block;">RUTE MANIFEST</span>
                        <span style="font-size: 12px; font-weight: 900; color: #075985; display: block; margin-top: 2px;">🚚 QC WORKSHOP ➔ GUDANG</span>
                    </td>
                </tr>
            </table>

            {{-- 3. METADATA INFO CARDS --}}
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

            {{-- 4. ITEMS TABLE --}}
            <div style="margin-bottom: 12px;">
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

            {{-- 5. CATATAN DISPATCHER BOX --}}
            @if($manifest->notes)
                <div style="margin-top: 12px; margin-bottom: 16px; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; background-color: #f8fafc;">
                    <span style="font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; display: block;">📝 CATATAN PENGIRIMAN DISPATCHER:</span>
                    <p style="font-size: 10px; font-weight: 700; font-style: italic; color: #0f172a; margin: 3px 0 0 0; line-height: 1.4;">"{{ $manifest->notes }}"</p>
                </div>
            @endif

            {{-- 6. LEMBAR PENGESAHAN SIGNATURES --}}
            <div class="page-break-avoid" style="margin-top: 20px;">
                <p style="font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.8px; text-align: center; margin-bottom: 10px; color: #475569;">
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
        </div>

        {{-- 7. FOOTER BAR --}}
        <div style="font-size: 8px; color: #64748b; font-weight: 600; display: flex; justify-content: space-between; border-top: 1px solid #cbd5e1; padding-top: 6px; margin-top: 16px;">
            <span>ShoeWorkshop Management System • Dokumen Resmi Pengiriman Outbound</span>
            <span>Dicetak pada: {{ now()->format('d/m/Y H:i') }} WIB • Halaman 1/1</span>
        </div>
    </div>

</body>
</html>
