<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap');

  @media print {
    @page {
      size: A4;
      margin: 15mm 15mm 20mm 15mm;
      @bottom-right {
        content: "Halaman " counter(page);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 8pt;
        color: #64748b;
      }
      @bottom-left {
        content: "SOP & Panduan API Google Sheets (Taken Orders) • Shoe Workshop";
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 8pt;
        color: #64748b;
      }
    }
    body {
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
  }

  body {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    color: #0f172a;
    background: #ffffff;
    font-size: 10pt;
    line-height: 1.6;
    margin: 0;
    padding: 0;
  }

  h1 {
    font-size: 18pt;
    color: #1e1b4b;
    border-bottom: 3px solid #22AF85;
    padding-bottom: 8px;
    margin-top: 0;
    margin-bottom: 12px;
    page-break-after: avoid;
  }

  h2 {
    font-size: 13pt;
    color: #0f172a;
    background: #f8fafc;
    border-left: 4.5px solid #22AF85;
    padding: 6px 12px;
    margin-top: 22px;
    margin-bottom: 12px;
    border-radius: 0 6px 6px 0;
    page-break-after: avoid;
  }

  h3 {
    font-size: 11pt;
    color: #0d9488;
    margin-top: 16px;
    margin-bottom: 8px;
    page-break-after: avoid;
  }

  h4 {
    font-size: 10pt;
    color: #475569;
    border-left: 3px solid #cbd5e1;
    padding-left: 8px;
    margin-top: 12px;
    margin-bottom: 6px;
    page-break-after: avoid;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    margin-bottom: 16px;
    font-size: 9pt;
    page-break-inside: avoid;
  }

  tr {
    page-break-inside: avoid;
    page-break-after: auto;
  }

  thead {
    display: table-header-group;
  }

  th {
    background-color: #f1f5f9 !important;
    color: #0f172a !important;
    font-weight: 800;
    text-align: left;
    padding: 7px 10px;
    border: 1px solid #cbd5e1;
  }

  td {
    padding: 6px 10px;
    border: 1px solid #cbd5e1;
    vertical-align: top;
  }

  code {
    font-family: 'JetBrains Mono', monospace;
    font-size: 8.5pt;
    background-color: #f1f5f9;
    color: #0f766e;
    padding: 2px 6px;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
  }

  pre {
    background-color: #0f172a;
    color: #f8fafc;
    padding: 12px 14px;
    border-radius: 8px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 8.5pt;
    line-height: 1.45;
    overflow-x: auto;
    margin-bottom: 14px;
    page-break-inside: avoid;
  }

  pre code {
    background: transparent;
    border: none;
    color: inherit;
    padding: 0;
  }

  .page-break {
    page-break-before: always;
  }

  .badge {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 8pt;
    font-weight: 700;
  }

  .badge-emerald { background-color: #d1fae5 !important; color: #065f46 !important; }
  .badge-amber { background-color: #fef3c7 !important; color: #92400e !important; }
  .badge-indigo { background-color: #e0e7ff !important; color: #3730a3 !important; }
  .badge-rose { background-color: #ffe4e6 !important; color: #9f1239 !important; }

  .callout {
    border-radius: 8px;
    padding: 12px 16px;
    margin: 12px 0 16px 0;
    border-left: 4px solid;
    page-break-inside: avoid;
  }

  .callout-info {
    background-color: #f0fdfa;
    border-color: #22AF85;
    color: #134e4a;
  }

  .callout-warning {
    background-color: #fffbeb;
    border-color: #f59e0b;
    color: #92400e;
  }

  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 18px;
    page-break-inside: avoid;
  }

  .kpi-card {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 12px;
    background: #f8fafc !important;
    text-align: center;
  }

  .kpi-num {
    font-size: 15pt;
    font-weight: 900;
    color: #22AF85;
    margin-top: 4px;
  }

  .kpi-label {
    font-size: 8pt;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
  }

  .signature-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 30px;
    page-break-inside: avoid;
  }

  .signature-box {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 14px;
    text-align: center;
    background: #ffffff;
  }
</style>

# 📋 BUKU PANDUAN TEKNIS & SOP INTEGRASI API GOOGLE SHEETS
## Modul Sinkronisasi Data Sepatu Diambil (*Taken Orders*) 3 Bulan untuk CRM & Follow-up CS
**Dokumen:** SOP & Technical Integration Guide  
**Endpoint:** `/api/sync_taken_orders.php`  
**Sistem Terkait:** Sistem Workshop Sepatu (ShoeWorkshop) ➔ Google Sheets CS Hub  
**Versi:** 1.0 (Rilis Resmi: 30 September 2026)  

---

## 📊 1. RINGKASAN EKSEKUTIF & SASARAN BISNIS

Dokumen ini disusun sebagai panduan resmi bagi tim **Customer Service (CS)**, **Developer**, dan **Manajemen Workshop** dalam memanfaatkan API sinkronisasi data pengambilan sepatu (`taken_date`).

Tujuan utama dari modul ini adalah **otomasi alur kerja retensi pelanggan (*Customer Retention*)**:
1. Menarik data pelanggan yang sepatunya telah diambil tepat **3 bulan yang lalu**.
2. Memasukkan data tersebut ke lembar kerja **Google Sheets** secara otomatis setiap pagi.
3. Memberikan panduan bagi CS untuk melakukan *Follow-up After-Service* (cek kenyamanan sepatu, reminder perawatan berkala, atau penawaran treatment pembersihan).

<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-label">Interval Standar</div>
    <div class="kpi-num">3 Bulan</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Pencegahan Tanggal 31</div>
    <div class="kpi-num">100% Aktif</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Format Nomor WA</div>
    <div class="kpi-num">Otomatis 62</div>
  </div>
  <div class="kpi-card">
    <div class="kpi-label">Duplikasi Data</div>
    <div class="kpi-num">0% (Zero Duplicate)</div>
  </div>
</div>

---

<div class="page-break"></div>

## 🧠 2. ARSITEKTUR LOGIKA KALENDER & PENYELESAIAN TANGGAL 31

Dalam kalender Masehi, setiap bulan memiliki jumlah hari yang bervariasi (28/29 di Februari, 30 hari di 4 bulan, dan 31 hari di 7 bulan). Jika penarikan data 3 bulan ke belakang hanya mencocokkan tanggal yang sama persis (`day == day`), akan timbul **2 anomali fatal**:

### 2.1 Anomali yang Berhasil Dicegah

| Kasus Anomali | Contoh Skenario Tanpa Logika Cerdas | Dampak Negatif | Solusi Cerdas Sistem (`sync_taken_orders.php`) |
| :--- | :--- | :--- | :--- |
| **Kasus A: Data Terlewat (*Skipped*)** | Tanggal eksekusi **30 November**. 3 bulan lalu adalah **Agustus** (punya 31 hari). Sistem menarik 30 Agustus. Besoknya (1 Des), sistem menarik 1 September. | Customer yang mengambil sepatu tanggal **31 Agustus TIDAK AKAN PERNAH DITARIK** dan hilang selamanya dari radar CS. | **End-of-Month Catch-up**: Pada hari penutupan akhir bulan (30 Nov), sistem otomatis menyapu **tanggal 30 DAN tanggal 31 Agustus sekaligus**! Data 100% terselamatkan. |
| **Kasus B: Data Dobel (*Duplicate*)** | Tanggal eksekusi **31 Juli**. 3 bulan lalu adalah **April** (hanya 30 hari). Kemarin pada 30 Juli, data 30 April sudah ditarik. | Jika 31 Juli menarik 30 April lagi, customer yang sama akan **ditarik DUA KALI** dan menerima spam chat dari CS. | **Anti-Duplicate Guard**: Pada tanggal 31 di bulan yang targetnya hanya 30 hari, sistem mengembalikan **`0 data`**, karena tanggal 30-nya sudah sah ditarik kemarin. |

### 2.2 Diagram Matriks Perilaku Kalender
```
[Hari Kerja Biasa (Tgl 1 s/d 29/30)] ──► Tarik Tepat Tanggal Sama di 3 Bulan Lalu
[Akhir Bulan Pendek (30 Nov / 28 Feb)] ─► Tarik Tgl Tersebut + Sapu Sisa Hari (Tgl 31)
[Tanggal 31 (Target Hanya 30 Hari)] ────► Skip (0 Data) untuk Mencegah Dobel
```

---

<div class="page-break"></div>

## 🔌 3. SPESIFIKASI ENDPOINT & PARAMETER API

- **File Script:** `public/api/sync_taken_orders.php`
- **Method:** `GET`
- **Content-Type:** `application/json; charset=utf-8`
- **CORS:** `Access-Control-Allow-Origin: *` (Dapat diakses langsung oleh Google Sheets)

### 3.1 Daftar Parameter Query String

| Parameter | Tipe | Status | Nilai Default | Keterangan & Contoh |
| :--- | :---: | :---: | :---: | :--- |
| `token` | `string` | **Wajib** | - | Token keamanan sistem dari `.env` (`SYNC_API_TOKEN`). |
| `ref_date` | `date` | Opsional | Hari Ini (`Y-m-d`) | Tanggal acuan eksekusi (Format `YYYY-MM-DD`). Contoh: `ref_date=2026-09-30`. |
| `months` | `int` | Opsional | `3` | Interval bulan ke belakang. Contoh: `months=3` atau `months=6`. |
| `taken_date` | `date` | Opsional | - | Mode manual: Jika ingin menarik tanggal pengambilan tertentu secara spesifik. Contoh: `taken_date=2026-06-30`. |

### 3.2 Contoh Penggunaan URL:
1. **Mode Otomatis Harian (Cron / Apps Script Setiap Pagi):**
   ```http
   GET https://domain-workshop-anda.com/api/sync_taken_orders.php?token=SECRET_TOKEN_12345
   ```
2. **Mode Simulasi Tanggal Tertentu:**
   ```http
   GET https://domain-workshop-anda.com/api/sync_taken_orders.php?token=SECRET_TOKEN_12345&ref_date=2026-09-30
   ```
3. **Mode Filter Manual Taken Date:**
   ```http
   GET https://domain-workshop-anda.com/api/sync_taken_orders.php?token=SECRET_TOKEN_12345&taken_date=2026-06-30
   ```

---

<div class="page-break"></div>

## 📦 4. STRUKTUR PAYLOAD RESPONSE JSON

### 4.1 Contoh Response Sukses:
```json
{
    "status": "success",
    "endpoint": "sync_taken_orders.php",
    "meta": {
        "mode": "calculated_interval",
        "reference_date": "2026-09-30",
        "interval_months": 3,
        "target_dates": [
            "2026-06-30"
        ],
        "note": "Perhitungan kalender eksak dengan catch-up akhir bulan."
    },
    "count": 2,
    "data": [
        {
            "customer_name": "Budi Santoso",
            "customer_phone": "08123456789",
            "whatsapp_phone": "628123456789",
            "spk_number": "S-2606-15-0012; S-2606-20-0045",
            "total_spk": 2,
            "shoe_brand": "Nike; Adidas",
            "tanggal_selesai_paling_baru": "2026-06-30",
            "taken_date_full": "2026-06-30 16:45:00"
        }
    ]
}
```

### 4.2 Kamus Data Field:
- `customer_name`: Nama lengkap pelanggan.
- `customer_phone`: Nomor telepon asli dari formulir penerimaan SPK.
- `whatsapp_phone`: Nomor telepon yang sudah distandardisasi otomatis dengan awalan negara `62` tanpa tanda hubung/spasi (siap dipakai link `wa.me`).
- `spk_number`: Seluruh nomor SPK milik pelanggan yang diambil pada tanggal tersebut (dipisahkan tanda titik koma `; `).
- `total_spk`: Total pasang sepatu yang diselesaikan dan diambil.
- `shoe_brand`: Merek sepatu yang dikerjakan.
- `tanggal_selesai_paling_baru`: Tanggal pengambilan (`YYYY-MM-DD`).
- `taken_date_full`: Waktu timestamp lengkap saat pengambilan dicatat di sistem workshop.

---

<div class="page-break"></div>

## 📑 5. PANDUAN INTEGRASI LANGKAH DEMI LANGKAH KE GOOGLE SHEETS

### Langkah 1: Siapkan Spreadsheet Baru
1. Buka Google Sheets baru, beri nama: **`CRM Workshop - Follow Up 3 Bulan`**.
2. Buat nama Sheet: **`Data Follow Up`**.

### Langkah 2: Masukkan Skrip Google Apps Script
1. Klik menu **Extensions (Ekstensi)** ➔ **Apps Script**.
2. Hapus seluruh kode bawaan, lalu salin dan tempelkan kode berikut:

```javascript
/**
 * Script Penarikan Otomatis Data Pengambilan Sepatu 3 Bulan Lalu
 * Shoe Workshop Automation
 */
function syncTakenOrders() {
  const API_URL = "https://domain-workshop-anda.com/api/sync_taken_orders.php?token=SECRET_TOKEN_12345";
  
  try {
    const response = UrlFetchApp.fetch(API_URL, {
      method: "get",
      muteHttpExceptions: true
    });
    
    const result = JSON.parse(response.getContentText());
    
    if (result.status !== "success") {
      Logger.log("Gagal menarik data: " + result.message);
      return;
    }
    
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    let sheet = ss.getSheetByName("Data Follow Up");
    if (!sheet) {
      sheet = ss.insertSheet("Data Follow Up");
    }
    
    // Header jika sheet masih kosong
    if (sheet.getLastRow() === 0) {
      sheet.appendRow([
        "Waktu Tarik Data",
        "Nama Pelanggan",
        "Nomor Telepon",
        "WhatsApp Link",
        "Nomor SPK",
        "Jumlah Pasang",
        "Merek Sepatu",
        "Tanggal Diambil",
        "Status Follow Up (CS)",
        "Catatan CS"
      ]);
      
      // Format header tebal dan berwarna
      const headerRange = sheet.getRange(1, 1, 1, 10);
      headerRange.setFontWeight("bold");
      headerRange.setBackground("#22AF85");
      headerRange.setFontColor("#FFFFFF");
    }
    
    if (result.data && result.data.length > 0) {
      const timestamp = new Date().toLocaleString("id-ID", { timeZone: "Asia/Jakarta" });
      
      result.data.forEach(item => {
        const waLink = "https://wa.me/" + item.whatsapp_phone;
        sheet.appendRow([
          timestamp,
          item.customer_name,
          "'" + item.customer_phone,
          waLink,
          item.spk_number,
          item.total_spk,
          item.shoe_brand,
          item.tanggal_selesai_paling_baru,
          "Belum Dihubungi",
          ""
        ]);
      });
      
      Logger.log("Berhasil menambahkan " + result.data.length + " data pelanggan.");
    } else {
      Logger.log("Tidak ada data baru untuk hari ini.");
    }
    
  } catch (error) {
    Logger.log("Terjadi kesalahan: " + error.toString());
  }
}
```

### Langkah 3: Atur Trigger Otomatis Setiap Pagi
1. Pada halaman Apps Script, klik menu berikon jam di bilah kiri: **Triggers (Pemicu)**.
2. Klik tombol **+ Add Trigger (Tambahkan Pemicu)** di pojok kanan bawah.
3. Atur parameter:
   - *Choose which function to run:* **`syncTakenOrders`**
   - *Select event source:* **Time-driven (Berdasarkan waktu)**
   - *Select type of time based trigger:* **Day timer (Penghitung hari)**
   - *Select time of day:* **08:00 to 09:00 (Pukul 8 pagi WIB)**
4. Klik **Save (Simpan)** dan izinkan otorisasi akun Google Anda.

---

<div class="page-break"></div>

## 💬 6. TEMPLATE PESAN WHATSAPP FOLLOW-UP CS (3 BULAN PASCA SERVIS)

Tim Customer Service dapat menggunakan draf pesan ramah dan personal berikut untuk menyapa pelanggan:

```text
Halo Kak {{Nama Pelanggan}}, selamat pagi/siang! 😊

Semoga kabarnya selalu sehat ya kak. Kami dari Shoe Workshop ingin menanyakan kabar sepatu {{Merek Sepatu}} kesayangan Kakak yang selesai kami kerjakan sekitar 3 bulan yang lalu (No. SPK: {{Nomor SPK}}).

Gimana kak kondisinya setelah dipakai beraktivitas sehari-hari? Apakah lem, jahitan, maupun solnya masih terasa nyaman dan mantap dipakai?

Sekadar mengingatkan kak, untuk menjaga usia pakai sepatu tetap awet, kami menyarankan perawatan berkala seperti pembersihan (*Deep Clean*) atau pengecekan daya rekat sol setiap 3–4 bulan sekali.

Jika ada sepatu lain yang ingin dirawat atau dikonsultasikan, Kakak bisa langsung balas pesan ini yaa. Terima kasih banyak atas kepercayaannya pada Shoe Workshop! 🙏✨
```

---

## ✍️ 7. LEMBAR PENGESAHAN DOKUMEN

Dokumen panduan teknis integrasi API Google Sheets ini telah disahkan dan siap diimplementasikan untuk operasional harian:

<div class="signature-grid">
  <div class="signature-box">
    <strong>Disusun Oleh:</strong><br><br><br><br>
    <strong>AI Senior Full Stack Developer</strong><br>
    <span>Lead Software Engineer</span><br>
    <small>Tanggal: 30 September 2026</small>
  </div>
  <div class="signature-box">
    <strong>Diverifikasi Oleh:</strong><br><br><br><br>
    <strong>Supervisor CS & CRM</strong><br>
    <span>Koordinator Layanan Pelanggan</span><br>
    <small>Tanggal: 30 September 2026</small>
  </div>
  <div class="signature-box">
    <strong>Disetujui Oleh:</strong><br><br><br><br>
    <strong>Workshop Operations Manager</strong><br>
    <span>Head of Operations</span><br>
    <small>Tanggal: 30 September 2026</small>
  </div>
</div>
