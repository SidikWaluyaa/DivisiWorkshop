<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panduan Ringkas Scan QR Mobile Teknisi - Shoe Workshop</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 10mm 12mm 8mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #1e293b;
            background-color: #ffffff;
            font-size: 10px;
            line-height: 1.35;
        }

        /* HEADER */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #2ebd8a;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .header-logo {
            width: 130px;
            vertical-align: middle;
        }

        .header-logo img {
            max-width: 125px;
            max-height: 42px;
        }

        .header-content {
            vertical-align: middle;
            padding-left: 10px;
        }

        .doc-badge {
            display: inline-block;
            background-color: #0f172a;
            color: #2ebd8a;
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 2px 7px;
            border-radius: 4px;
            margin-bottom: 3px;
        }

        .main-title {
            font-size: 14.5px;
            font-weight: 900;
            color: #0f172a;
            margin: 0;
            line-height: 1.15;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .sub-title {
            font-size: 8.5px;
            color: #64748b;
            margin: 2px 0 0 0;
            font-weight: 500;
        }

        .header-meta {
            text-align: right;
            vertical-align: middle;
            width: 120px;
        }

        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 4px 6px;
            text-align: center;
        }

        .meta-label {
            font-size: 7px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
        }

        .meta-val {
            font-size: 9.5px;
            font-weight: 800;
            color: #059669;
        }

        /* STATUS BADGE BAR */
        .status-bar {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            margin-bottom: 8px;
        }

        .status-cell {
            padding: 5px 7px;
            border-radius: 6px;
            text-align: center;
            font-size: 8px;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .status-antre {
            background-color: #f1f5f9;
            color: #475569;
            border-color: #cbd5e1;
        }

        .status-mulai {
            background-color: #eff6ff;
            color: #1d4ed8;
            border-color: #93c5fd;
        }

        .status-jeda {
            background-color: #fffbeb;
            color: #b45309;
            border-color: #fcd34d;
        }

        .status-selesai {
            background-color: #ecfdf5;
            color: #047857;
            border-color: #6ee7b7;
        }

        /* 4 MAIN STEPS GRID */
        .step-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 6px;
            margin-bottom: 8px;
        }

        .step-row {
            background-color: #ffffff;
        }

        .step-num-cell {
            width: 44px;
            text-align: center;
            vertical-align: middle;
            border: 1.5px solid #e2e8f0;
            border-right: none;
            border-radius: 8px 0 0 8px;
            padding: 8px 4px;
            background-color: #f8fafc;
        }

        .step-badge {
            width: 32px;
            height: 32px;
            line-height: 32px;
            border-radius: 50%;
            display: inline-block;
            font-size: 15px;
            font-weight: 900;
            color: #ffffff;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .step-1-color { background-color: #0284c7; }
        .step-2-color { background-color: #7c3aed; }
        .step-3-color { background-color: #2ebd8a; }
        .step-4-color { background-color: #059669; }

        .step-content-cell {
            vertical-align: middle;
            border: 1.5px solid #e2e8f0;
            border-left: none;
            border-radius: 0 8px 8px 0;
            padding: 7px 10px;
            background-color: #ffffff;
        }

        .step-title {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .step-title .tagline {
            font-size: 8.5px;
            font-weight: 600;
            color: #64748b;
            text-transform: none;
            margin-left: 5px;
        }

        .step-desc {
            font-size: 9px;
            color: #334155;
            line-height: 1.35;
            margin: 0;
        }

        .step-highlight {
            display: inline-block;
            background-color: #f1f5f9;
            color: #0f172a;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 700;
            font-size: 8.5px;
            padding: 1px 4px;
            border-radius: 3px;
            border: 1px solid #e2e8f0;
        }

        .step-tip {
            margin-top: 3px;
            font-size: 8px;
            color: #475569;
            background-color: #f8fafc;
            border-left: 2.5px solid #2ebd8a;
            padding: 2px 6px;
            border-radius: 0 4px 4px 0;
        }

        /* TROUBLESHOOTING / IMPORTANT RULES BOX */
        .rules-card {
            background-color: #fafaf9;
            border: 1.5px dashed #cbd5e1;
            border-radius: 8px;
            padding: 7px 10px;
            margin-bottom: 8px;
        }

        .rules-title {
            font-size: 9.5px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
            padding-bottom: 3px;
            border-bottom: 1px solid #e2e8f0;
        }

        .rules-table {
            width: 100%;
            border-collapse: collapse;
        }

        .rules-table td {
            vertical-align: top;
            padding: 2px 5px;
            font-size: 8.5px;
            color: #334155;
        }

        .rules-icon {
            font-size: 10px;
            font-weight: bold;
            color: #0284c7;
            width: 16px;
            text-align: center;
        }

        /* REASON PRESETS TABLE */
        .reasons-box {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 4px 8px;
            margin-top: 3px;
            font-size: 7.8px;
            color: #92400e;
        }

        /* FOOTER */
        .footer-table {
            width: 100%;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            font-size: 7.5px;
            color: #64748b;
        }
    </style>
</head>
<body>

    {{-- HEADER SECTION --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Shoe Workshop Logo">
                @else
                    <strong style="font-size: 15px; color: #2ebd8a;">SHOE WORKSHOP</strong>
                @endif
            </td>
            <td class="header-content">
                <span class="doc-badge">SOP PRODUKSI & KONTROL TEKNISI #SOP-IT-04</span>
                <h1 class="main-title">PANDUAN RINGKAS SCAN QR MOBILE</h1>
                <p class="sub-title">Standar Operasional Pelacakan Waktu Nyata (Real-Time Tracking) Meja Kerja Teknisi</p>
            </td>
            <td class="header-meta">
                <div class="meta-box">
                    <div class="meta-label">Status Berlaku</div>
                    <div class="meta-val">1 Nov 2026</div>
                    <div style="font-size: 6.5px; color: #94a3b8; margin-top: 1px;">Versi 1.0 (Laminating)</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- STATUS BAR VISUAL GUIDE --}}
    <table class="status-bar">
        <tr>
            <td class="status-cell status-antre">
                ⚪ <strong>ANTRE / STANDBY</strong><br>
                <span style="font-size: 7px; opacity: 0.9;">Sepatu siap di meja kerja</span>
            </td>
            <td class="status-cell status-mulai">
                🔵 <strong>PROSES AKTIF (BIRU)</strong><br>
                <span style="font-size: 7px; opacity: 0.9;">Stopwatch waktu berjalan</span>
            </td>
            <td class="status-cell status-jeda">
                🟠 <strong>DIJEDA / PAUSE (ORANYE)</strong><br>
                <span style="font-size: 7px; opacity: 0.9;">Istirahat / tunggu lem kering</span>
            </td>
            <td class="status-cell status-selesai">
                🟢 <strong>SELESAI (HIJAU)</strong><br>
                <span style="font-size: 7px; opacity: 0.9;">Durasi terekam ke database</span>
            </td>
        </tr>
    </table>

    {{-- 4 MAIN STEPS --}}
    <table class="step-table">
        {{-- LANGKAH 1 --}}
        <tr class="step-row">
            <td class="step-num-cell">
                <div class="step-badge step-1-color">1</div>
            </td>
            <td class="step-content-cell">
                <div class="step-title">
                    LOGIN DI HP MASING-MASING
                    <span class="tagline">— Cukup 1x Login Saja, Sesi Otomatis Tersimpan</span>
                </div>
                <p class="step-desc">
                    Buka Google Chrome atau Safari di HP Anda, buka alamat: 
                    <span class="step-highlight">info.shoeworkshop.id/m/production</span>. Masuk menggunakan akun teknisi Anda.
                </p>
                <div class="step-tip">
                    💡 <strong>Tips:</strong> Setelah berhasil login pertama kali, sistem akan mengingat Anda terus. Tidak perlu mengetik kata sandi setiap kali ingin bekerja.
                </div>
            </td>
        </tr>

        {{-- LANGKAH 2 --}}
        <tr class="step-row">
            <td class="step-num-cell">
                <div class="step-badge step-2-color">2</div>
            </td>
            <td class="step-content-cell">
                <div class="step-title">
                    SCAN KOTAK QR CODE SPK
                    <span class="tagline">— Arahkan Kamera HP ke Kertas SPK Sepatu</span>
                </div>
                <p class="step-desc">
                    Tekan tombol <span class="step-highlight">📷 Buka Kamera Scanner</span> pada aplikasi web, lalu arahkan lensa HP ke kotak khusus bertuliskan <strong>"SCAN KONTROL TEKNISI"</strong> di lembar fisik SPK.
                </p>
                <div class="step-tip">
                    ⚡ <strong>Respon Cepat:</strong> Data sepatu, instruksi pengerjaan, dan kartu stasiun (Soling, Upper, Jahit, Cleaning) akan langsung terbuka otomatis di layar HP Anda.
                </div>
            </td>
        </tr>

        {{-- LANGKAH 3 --}}
        <tr class="step-row">
            <td class="step-num-cell">
                <div class="step-badge step-3-color">3</div>
            </td>
            <td class="step-content-cell">
                <div class="step-title">
                    KLIK "MULAI" & MANFAATKAN FITUR "JEDA"
                    <span class="tagline">— Waktu Riil Dihitung Otomatis</span>
                </div>
                <p class="step-desc">
                    Begitu tangan Anda mulai merawat atau memperbaiki sepatu, tekan tombol hijau <strong>"Mulai Pengerjaan"</strong>. Kartu akan berubah warna menjadi <strong>BIRU</strong> dan stopwatch aktif berjalan.
                </p>
                <div class="reasons-box">
                    ☕ <strong>Jika pekerjaan terhenti sementara, tekan tombol "Jeda" dan pilih alasan:</strong><br>
                    (1) Istirahat / Makan / Sholat &bull; (2) Menunggu Bahan / Sparepart &bull; (3) Menunggu Lem Kering / Proses Kimia &bull; (4) Kerjakan SPK Prioritas Lain &bull; (5) Kendala Mesin / Alat
                </div>
            </td>
        </tr>

        {{-- LANGKAH 4 --}}
        <tr class="step-row">
            <td class="step-num-cell">
                <div class="step-badge step-4-color">4</div>
            </td>
            <td class="step-content-cell">
                <div class="step-title">
                    KLIK "SELESAI" KETIKA TAHAP RAMPUNG
                    <span class="tagline">— Data Langsung Masuk ke Mandor & Pengawas</span>
                </div>
                <p class="step-desc">
                    Setelah pengerjaan di bagian Anda beres, tekan tombol <strong>"Selesai Pengerjaan"</strong>. Kartu berubah warna menjadi <strong>HIJAU</strong> dan durasi kerja riil tersimpan otomatis ke database.
                </p>
                <div class="step-tip">
                    ✨ <strong>Transparan:</strong> Mandor bengkel langsung melihat antrean sepatu bergeser ke tahap berikutnya tanpa Anda harus melapor manual ke meja kantor.
                </div>
            </td>
        </tr>
    </table>

    {{-- ATURAN PENTING & SOLUSI CEPAT --}}
    <div class="rules-card">
        <div class="rules-title">📌 ATURAN PENTING & SOLUSI CEPAT MEJA KERJA (TROUBLESHOOTING)</div>
        <table class="rules-table">
            <tr>
                <td class="rules-icon">🔍</td>
                <td style="width: 48%;">
                    <strong>QR Rusak, Kotor, atau Terlipat?</strong><br>
                    Jika barcode buram terkena air/lem, ketik nomor SPK langsung di kolom <em>"Cari SPK Manual"</em> pada layar atas. Detail pengerjaan akan tetap terbuka tanpa hambatan.
                </td>
                <td class="rules-icon">👥</td>
                <td style="width: 48%;">
                    <strong>Tukar Tugas / Dibantu Rekan Teknisi?</strong><br>
                    Jika SPK dialihkan ke rekan lain, pilih nama teknisi baru pada menu pilihan (dropdown) stasiun sebelum menekan Mulai agar pencatatan insentif tidak salah orang.
                </td>
            </tr>
            <tr>
                <td class="rules-icon">⏱️</td>
                <td>
                    <strong>Disiplin Timer Pengerjaan:</strong><br>
                    Jangan biarkan stopwatch berjalan saat Anda meninggalkan bengkel atau istirahat panjang. Selalu tekan tombol <strong>JEDA</strong> agar durasi rata-rata pengerjaan Anda tetap akurat dan adil.
                </td>
                <td class="rules-icon">📶</td>
                <td>
                    <strong>Koneksi Internet / Wi-Fi Bengkel:</strong><br>
                    Pastikan HP terhubung ke Wi-Fi Workshop. Jika ada kendala jaringan, segera hubungi Divisi IT / Pengawas agar koneksi stasiun Anda dibantu.
                </td>
            </tr>
        </table>
    </div>

    {{-- FOOTER --}}
    <table class="footer-table">
        <tr>
            <td style="width: 60%;">
                Diterbitkan oleh: <strong>Divisi Data Analyst & IT &bull; Shoe Workshop</strong> | SOP-IT-04 Rev. 1.0
            </td>
            <td style="width: 40%; text-align: right;">
                Lembar Panduan Fisik Meja Kerja &bull; Wajib Dilaminating &bull; Berlaku Nov 2026
            </td>
        </tr>
    </table>

</body>
</html>

