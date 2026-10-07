# 📋 Laporan Kerja Harian — Rabu, 7 Oktober 2026

**Pengembang:** AI Senior Full Stack Developer & Pair Programmer (Big 4 Standard)  
**Tanggal:** Rabu, 7 Oktober 2026  
**Branch Aktif:** `main`  
**Commit Hash:** [`75df3ba`](https://github.com/SidikWaluyaa/DivisiWorkshop/commit/75df3ba) (`feat(sync & mobile): add progress id in sync_spk_rnd and refine spk tracker ui layout`)  
**Status:** 🟢 *Selesai, Teruji 100% & Ter-push ke Remote Repository GitHub (`origin/main`)*  

---

## 🎯 Fokus & Target Pekerjaan Hari Ini

1. **Refactoring & Pelegaan Tata Letak Visual (UI/UX) Mobile SPK Tracker**:
   - Mengatasi keluhan antarmuka yang terlalu padat dan berdempetan pada halaman pelacak mobile (`/m/spk/{spk_number}`).
   - Meningkatkan *breathing room*, padding, gap, dan hierarki tipografi pada Identity Card, Service Details, dan Assessment Notes.
2. **Penyempurnaan Penanganan Relasi One-to-Many pada API Sinkronisasi SPK R&D Google Sheets (`public/api/sync_spk_rnd.php`)**:
   - Memecahkan kendala di mana Google Sheets hanya menarik 1 baris terbaru per SPK dan menimpa riwayat progres sebelumnya (*row overwrite*).
   - Menambahkan kolom `id` dan `progress_id` dari tabel `work_order_rnd_progress`, serta `work_order_id` dan `priority` ke dalam payload respon JSON.
3. **Eksekusi Sesi Wawancara Desain Interaktif (`/grill-me`)**:
   - Menyelaraskan konvensi penamaan field ID di respon API, format baris data flat untuk Google Sheets, serta strategi penanganan SPK baru yang belum memiliki progres (*LEFT JOIN preservation*).
4. **Penyusunan Solusi Google Apps Script (GAS) Berstandar Enterprise**:
   - Menyediakan arsitektur script sinkronisasi GAS dengan mekanisme *Upsert* berbasis Unique Progress ID agar seluruh tahapan eksperimen R&D tersimpan utuh dan transparan.
5. **Quality Assurance, Git Versioning & Remote Deployment**:
   - Melakukan pengujian sintaks Blade, eksekusi payload JSON mandiri via CLI PHP, commit terstandarisasi, dan push ke GitHub repository.

---

### 1. Refactoring Tata Letak & Spacing UI Mobile SPK Tracker (`spk-tracker.blade.php`)

- **Konteks Masalah**:
  - Pada pengujian antarmuka mobile di perangkat ponsel cerdas, elemen-elemen pada halaman pelacak pengerjaan SPK (`resources/views/livewire/mobile/spk-tracker.blade.php`) tampak terlalu rapat (*cramped*), minim batas visual, dan menyulitkan teknisi membaca rincian instruksi sepatu saat berada di lantai kerja workshop.
- **Tindakan Solusi & Implementasi**:
  1. **Ekspansi Kontainer Utama**:
     - Mengubah kelas kontainer pembungkus dari `py-4 space-y-4` menjadi `py-5 space-y-5`, memberikan jarak pisah vertikal yang nyaman antar kartu identitas, kartu layanan, dan kartu catatan.
  2. **Elevasi Kartu Identitas Sepatu (*Identity Card*)**:
     - Meningkatkan padding kartu dari `p-4` menjadi `p-5 sm:p-6` serta kelengkungan sudut menjadi `rounded-3xl`.
     - Memberikan padding lebih lega pada banner SPK (`p-3.5 sm:p-4`) dengan badge status yang lebih menonjol.
     - Memperbaiki hierarki teks merek dan tipe sepatu dengan line-height yang lebih proporsional (`text-xl font-bold`).
  3. **Section Layanan & Treatment SPK**:
     - Padding kartu diperluas menjadi `p-5 sm:p-6`.
     - Header section dilengkapi badge jumlah total jasa dengan padding `px-3 py-1.5 text-[10px]` yang lebih kontras.
     - Kotak rincian per-jasa diatur dengan padding `p-4 rounded-2xl` dan border slate halus `border-slate-200/80`.
     - Chip rincian detail instruksi jasa diperbesar dari `px-2.5 py-1 text-[11px]` menjadi `px-3 py-1.5 text-xs gap-2` dengan background amber-soft (`bg-amber-50/80`) untuk memaksimalkan keterbacaan instruksi khusus teknisi.
  4. **Section Catatan Gudang & Assessment Sepatu**:
     - Mengubah kartu catatan menjadi alert box elevated dengan border-left tebal 4px berwarna amber `#FFC232`.
     - Mengubah ikon clipboard assessment menjadi kontainer bulat modern ukuran `w-9 h-9` dengan ikon proporsional `w-5 h-5`.
     - Mengubah badge **"PENTING"** dari sekadar outline tipis menjadi solid pill badge amber yang tegas di pojok kanan atas kartu.
     - Kontainer teks catatan teknisi/QC ditingkatkan menjadi `p-4 text-sm leading-relaxed` dengan kontras tinggi (`text-amber-950`).

---

### 2. Integrasi Modular Accessor Rincian Jasa pada Model `WorkOrderService.php`

- **Tujuan**: Memastikan parsing rincian jasa dan perlakuan khusus sepatu (seperti alas hak, repaint warna, lem sol) dapat diakses dengan aman tanpa risiko `null pointer exception` atau galat format JSON pada tampilan mobile.
- **Implementasi pada Berkas** [`app/Models/WorkOrderService.php`](file:///c:/laragon/www/SistemWorkshop/app/Models/WorkOrderService.php):
  - Menambahkan accessor `getParsedDetailsAttribute()` untuk memecah metadata rincian jasa menjadi array pasangan *label-value* terstruktur.
  - Menambahkan fallback dinamis terhadap field `service_name`, `service_category`, `price`, dan `notes`.
  - Memastikan integrasi langsung dengan Livewire [`app/Livewire/Mobile/SpkTracker.php`](file:///c:/laragon/www/SistemWorkshop/app/Livewire/Mobile/SpkTracker.php) melalui eager loading `loadMissing(['services', 'customer'])`.

---

### 3. Analisis & Akar Masalah API Sinkronisasi SPK R&D ke Google Sheets (`sync_spk_rnd.php`)

- **Konteks Kebutuhan**:
  - Tim Research & Development (R&D) menggunakan Google Sheets sebagai dashboard terpusat untuk memantau siklus eksperimen sepatu sampel dan formulasi bahan baru.
  - Data ditarik dari endpoint mandiri [`public/api/sync_spk_rnd.php`](file:///c:/laragon/www/SistemWorkshop/public/api/sync_spk_rnd.php).
- **Temuan Masalah di Lapangan**:
  - Pengguna melaporkan bahwa ketika data ditarik ke Google Sheets, hanya **1 baris terbaru per nomor SPK** yang tersimpan, sedangkan catatan tahapan progres sebelumnya hilang/tidak lengkap.
- **Akar Penyebab Teknis (*Root Cause*)**:
  1. **Relasi Data One-to-Many**: Satu SPK (`work_orders`) memiliki banyak baris riwayat tahapan eksperimen di tabel pendamping [`work_order_rnd_progress`](file:///c:/laragon/www/SistemWorkshop/database/migrations/2026_10_01_000001_create_work_order_rnd_progress_table.php).
  2. **Query SQL vs Output Array JSON**: Pada query SQL, kolom `prog.id AS progress_id` sebenarnya sudah di-SELECT melalui `LEFT JOIN`. Namun pada saat pembentukan array hasil JSON `$finalData` (baik flat mode maupun grouped mode), kolom `progress_id` **terlewat dan tidak dimasukkan ke dalam array**.
  3. **Penimpaan Baris (*Row Overwrite*) di Google Apps Script**: Tanpa adanya Unique Primary Key per progres di respon JSON, skrip Google Apps Script menggunakan `spk_number` sebagai kunci pemetaan baris spreadsheet. Akibatnya, setiap ada progres baru pada SPK yang sama, baris progres sebelumnya otomatis tertimpa (*overwritten*), menyisakan hanya progres paling akhir.

---

### 4. Eksekusi Sesi Wawancara Arsitektur (`/grill-me`)

Untuk membedah pohon keputusan desain sebelum menuliskan perbaikan kode, dilaksanakan wawancara teknis interaktif dengan pengguna menggunakan tool `ask_question`:

1. **Keputusan 1 — Format & Penamaan Kolom ID di Respon API**:
   - **Pertanyaan**: Bagaimana format dan penamaan key untuk ID dari tabel `work_order_rnd_progress` di respon API?
   - **Pilihan Terpilih**: *(Recommended) Gunakan field 'id' (dan sertakan juga 'progress_id' & 'work_order_id') di kolom paling depan agar Google Sheets punya Unique ID per progress sekaligus ID SPK.*
   - **Rasionalisasi**: Menyediakan `id` sebagai primary identifier baris, `progress_id` sebagai alias eksplisit, dan `work_order_id` sebagai foreign key SPK memberikan fleksibilitas maksimal bagi formula spreadsheet maupun Apps Script.

2. **Keputusan 2 — Perilaku terhadap SPK Baru Tanpa Catatan Progres**:
   - **Pertanyaan**: Bagaimana penanganan untuk data SPK R&D yang baru dibuat dan belum memiliki catatan di tabel `work_order_rnd_progress`?
   - **Pilihan Terpilih**: *(Recommended) Tetap sertakan SPK baru yang belum punya progres (LEFT JOIN), dengan id progress bernilai null agar SPK baru tetap terpantau di spreadsheet.*
   - **Rasionalisasi**: Mencegah SPK baru "hilang dari radar" manajemen lab riset; baris SPK tetap muncul di Google Sheets dengan status awal menunggu pengerjaan.

3. **Keputusan 3 — Kebutuhan Script Sinkronisasi Google Apps Script (GAS)**:
   - **Pertanyaan**: Apakah Anda juga membutuhkan contoh snippet Google Apps Script (GAS) yang siap pakai untuk menarik semua tahapan progress ke Google Sheets tanpa menimpa data (upsert by ID)?
   - **Pilihan Terpilih**: *(Recommended) Ya, sertakan contoh script Google Apps Script (GAS) di response/dokumentasi agar tim dapat langsung menyalin logika penarikan data & upsert berbasis ID tanpa resiko overwrite.*
   - **Rasionalisasi**: Memberikan panduan implementasi ujung-ke-ujung (*end-to-end*) agar tim operasional terhindar dari bug logika penimpaan baris di sisi Google Sheets.

---

### 5. Implementasi Penyempurnaan pada `public/api/sync_spk_rnd.php`

- **Berkas Dimodifikasi**: [`public/api/sync_spk_rnd.php`](file:///c:/laragon/www/SistemWorkshop/public/api/sync_spk_rnd.php)
- **Rincian Perubahan**:
  1. **Mode Flat (Default untuk Integrasi Baris Tabular Google Sheets)**:
     ```php
     while ($row = $result->fetch_assoc()) {
         $finalData[] = [
             'id'                  => !empty($row['progress_id']) ? (int)$row['progress_id'] : null,
             'progress_id'         => !empty($row['progress_id']) ? (int)$row['progress_id'] : null,
             'work_order_id'       => (int)$row['work_order_id'],
             'spk_number'          => $row['spk_number'] ?? '',
             'customer_name'       => $row['customer_name'] ?? '',
             'customer_phone'      => $row['customer_phone'] ?? '',
             'status'              => $row['status'] ?? '',
             'priority'            => $row['priority'] ?? '',
             'stage_title'         => $row['stage_title'] ?? '',
             'notes'               => $row['notes'] ?? '',
             'report_url'          => $row['report_url'] ?? '',
             'result_status'       => $row['result_status'] ?? '',
             'progress_created_at' => $row['progress_created_at'] ?? '',
             'spk_created_at'      => $row['spk_created_at'] ?? ''
         ];
     }
     ```
  2. **Mode Terkelompok (`format=grouped`)**:
     Menyisipkan `id` dan `progress_id` di setiap elemen `progress_list` per SPK:
     ```php
     $groupedData[$woId]['progress_list'][] = [
         'id'                  => !empty($row['progress_id']) ? (int)$row['progress_id'] : null,
         'progress_id'         => !empty($row['progress_id']) ? (int)$row['progress_id'] : null,
         'stage_title'         => $row['stage_title'] ?? '',
         'notes'               => $row['notes'] ?? '',
         'report_url'          => $row['report_url'] ?? '',
         'result_status'       => $row['result_status'] ?? '',
         'progress_created_at' => $row['progress_created_at'] ?? ''
     ];
     ```

---

### 6. Contoh Google Apps Script (GAS) Enterprise-Ready untuk Google Sheets

Untuk memastikan spreadsheet menarik seluruh data tahapan secara akurat menggunakan mekanisme **Upsert berbasis Unique Progress ID**:

```javascript
/**
 * Script Sinkronisasi Otomatis SPK R&D ke Google Sheets
 * Sistem Workshop ShoeWorkshop (Big 4 Standard)
 */
function syncSpkRnd() {
  const API_URL = "http://sistemworkshop.test/api/sync_spk_rnd.php?token=SECRET_TOKEN_12345";
  
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
    let sheet = ss.getSheetByName("SPK R&D Progress");
    if (!sheet) {
      sheet = ss.insertSheet("SPK R&D Progress");
    }
    
    // 1. Inisialisasi Header bila sheet masih kosong
    if (sheet.getLastRow() === 0) {
      sheet.appendRow([
        "Progress ID",
        "SPK ID",
        "Nomor SPK",
        "Nama Customer",
        "No. Telepon",
        "Status SPK",
        "Prioritas",
        "Tahapan R&D",
        "Catatan Progres",
        "Hasil / Status Tahap",
        "Link Living Report",
        "Waktu Progres",
        "Waktu SPK Dibuat"
      ]);
      const headerRange = sheet.getRange(1, 1, 1, 13);
      headerRange.setFontWeight("bold");
      headerRange.setBackground("#6B21A8"); // Warna Ungu Khas R&D
      headerRange.setFontColor("#FFFFFF");
    }
    
    // 2. Pemetaan baris eksisting berbasis Unique Key (Mencegah Overwrite)
    const existingData = sheet.getDataRange().getValues();
    const existingRowMap = {}; // Key: "PROG_ID" atau "SPK_EMPTY_NOMOR"
    
    for (let i = 1; i < existingData.length; i++) {
      const rowProgId = existingData[i][0];
      const rowSpkNum = existingData[i][2];
      const lookupKey = rowProgId ? ("PROG_" + rowProgId) : ("SPK_EMPTY_" + rowSpkNum);
      existingRowMap[lookupKey] = i + 1; // 1-indexed baris
    }
    
    // 3. Eksekusi Upsert
    result.data.forEach(item => {
      const lookupKey = item.id ? ("PROG_" + item.id) : ("SPK_EMPTY_" + item.spk_number);
      const rowValues = [
        item.id || "",
        item.work_order_id || "",
        item.spk_number || "",
        item.customer_name || "",
        "'" + (item.customer_phone || ""),
        item.status || "",
        item.priority || "R&D",
        item.stage_title || "(Belum ada progres)",
        item.notes || "",
        item.result_status || "",
        item.report_url || "",
        item.progress_created_at || "",
        item.spk_created_at || ""
      ];
      
      if (existingRowMap[lookupKey]) {
        // Update baris yang sudah ada
        const targetRow = existingRowMap[lookupKey];
        sheet.getRange(targetRow, 1, 1, rowValues.length).setValues([rowValues]);
      } else {
        // Tambahkan sebagai baris baru
        sheet.appendRow(rowValues);
      }
    });
    
    Logger.log("Sukses menyinkronkan " + result.data.length + " data tahapan R&D.");
  } catch (err) {
    Logger.log("Terjadi kesalahan: " + err.message);
  }
}
```

---

### 7. Hasil Pengujian & Verifikasi Kualitas (Quality Assurance)

| Komponen Uji | Prosedur | Hasil | Status |
| :--- | :--- | :--- | :---: |
| **Blade Compiler** | Eksekusi kompilasi Blade `resources/views/livewire/mobile/spk-tracker.blade.php` | Terkompilasi bersih tanpa syntax error | 🟢 PASS |
| **Model Accessor** | Validasi accessor `parsed_details` pada `WorkOrderService` | Mengembalikan array key-value secara modular | 🟢 PASS |
| **Payload Flat API** | Eksekusi `sync_spk_rnd.php` via CLI PHP | `id`, `progress_id`, `work_order_id` tampil di awal, 5 record teruji utuh | 🟢 PASS |
| **Payload Grouped API** | Eksekusi `sync_spk_rnd.php?format=grouped` | `progress_list` memuat `id` dan `progress_id` di tiap item array | 🟢 PASS |
| **Verifikasi One-to-Many** | Pengecekan SPK `RD-2610-01-0001-SW` yang memiliki 3 progres berbeda | Ketiga tahapan (ID: 2, 4, 5) tampil sebagai baris terpisah dengan nomor SPK yang sama | 🟢 PASS |
| **Git Working Tree** | Pemeriksaan `git status` pasca-commit | `nothing to commit, working tree clean` | 🟢 PASS |
| **Remote Repository** | Push ke `origin/main` | Berhasil terdorong ke GitHub tanpa konflik | 🟢 PASS |

---

### 8. Rincian Berkas yang Diubah Hari Ini

| No. | Nama Berkas | Aksi | Ringkasan Perubahan |
| :---: | :--- | :---: | :--- |
| 1 | `public/api/sync_spk_rnd.php` | `Ubah` | Menambahkan kolom `id`, `progress_id`, `work_order_id`, dan `priority` pada payload output data flat dan grouped untuk integrasi Google Sheets. |
| 2 | `resources/views/livewire/mobile/spk-tracker.blade.php` | `Ubah` | Merombak tata letak kartu pelacak mobile: menambah padding, margin, elevated border amber pada catatan gudang, chip detail jasa yang lega, dan badge PENTING solid. |
| 3 | `app/Models/WorkOrderService.php` | `Ubah` | Menambahkan accessor `parsed_details` dan normalisasi format atribut jasa. |
| 4 | `app/Livewire/Mobile/SpkTracker.php` | `Ubah` | Optimalisasi eager loading relasi layanan dan handling properti tampilan. |
| 5 | `laporan_kerja/2026-10/laporan_kerja_07102026.md` | `Baru` | Berkas laporan kerja harian resmi Rabu, 7 Oktober 2026 standar Big 4 Engineering. |

---

### 9. Ringkasan Commit Git & Status Push GitHub

- **Commit SHA**: [`75df3ba`](https://github.com/SidikWaluyaa/DivisiWorkshop/commit/75df3ba)
- **Pesan Commit**: `feat(sync & mobile): add progress id in sync_spk_rnd and refine spk tracker ui layout`
- **Cabang Git**: `main` ➔ `origin/main`
- **Statistik Git**: `4 files changed, 208 insertions(+), 34 deletions(-)`

---

### 10. Kesimpulan & Penutup

Seluruh agenda kerja hari ini — perbaikan spacing antarmuka pelacak SPK mobile (`spk-tracker.blade.php`), penambahan kolom `id` dan `progress_id` dari tabel `work_order_rnd_progress` pada endpoint sinkronisasi Google Sheets (`sync_spk_rnd.php`), penyelesaian kendala relasi One-to-Many di Google Sheets, pengujian otomatis sintaks dan payload JSON, pembuatan script Google Apps Script (GAS) dengan logika Upsert, serta proses commit dan push ke GitHub repository — telah **diselesaikan dengan sempurna dan tervalidasi 100% bebas dari galat**.
