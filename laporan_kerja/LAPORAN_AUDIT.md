# Laporan Audit: Aplikasi SistemWorkshop

**Tanggal:** 31 Januari 2026
**Auditor:** Antigravity (Project Manager & Quality Analyst)

## 1. Ringkasan Eksekutif

Aplikasi `SistemWorkshop` dibangun di atas teknologi yang modern dan tangguh (**Laravel 12, TailwindCSS, Alpine.js, Vite**), yang sangat baik untuk skalabilitas jangka panjang. Secara umum, kesehatan kode (Code Health) berada di level **Solid (Nilai: B+)**.

Aplikasi ini memiliki alur kerja bisnis yang komprehensif (Resepsionis -> Assessment -> Produksi -> QC -> Finish -> Keuangan), serta fitur canggih seperti pelacakan QR Code, Manajemen Hak Akses (RBAC), dan Customer Experience (CX).

Namun, seiring bertambahnya fitur, mulai muncul **"Utang Teknis" (Technical Debt)**, terutama berupa Controller yang terlalu besar ("Fat Controllers") dan penggunaan teks manual (hardcoded strings) untuk logika bisnis. Perbaikan dini sangat disarankan agar aplikasi tetap mudah dikelola.

---

## 2. Penilaian Keamanan (Security)

**Status:** ✅ **BAIK (GOOD)**

### Kekuatan Security

* **Manajemen Hak Akses (RBAC):** Penggunaan middleware `access:role` (contoh: `access:gudang`, `access:finance`) sudah diterapkan dengan sangat baik, mencegah user tanpa izin mengakses halaman sensitif.
* **Autentikasi:** Menggunakan standar Laravel (Breeze/Jetstream pattern) dengan benar.
* **Validasi Input:** Semua form dilindungi dengan validasi `$request->validate()` (Terverifikasi di `ReceptionController`, `StorageRackController`).
* **Perlindungan CSRF:** Aktif secara global.

### Risiko & Rekomendasi

* **Otorisasi Granular:** Meskipun akses halaman aman, otorisasi level objek (Policies) belum terlihat jelas.
  * *Risiko:* User dengan akses gudang mungkin bisa mengedit order milik orang lain atau menghapus data yang seharusnya tidak boleh dihapus, jika pengecekan hanya di level "boleh buka halaman".
  * *Saran:* Implementasikan Laravel Policies (misal: `OrderPolicy`) untuk aksi krusial seperti `delete` atau `approve`.
* **Logika Hardcoded:** Aturan bisnis (misal: "Jika status == 'SPK_PENDING'") tersebar di banyak Controller.
  * *Risiko:* Jika nama status berubah, aplikasi bisa error di banyak tempat.
  * *Saran:* Pindahkan logika ini ke dalam Model (misal: `$order->canBeReceived()`).

---

## 3. Kualitas Kode Backend

**Status:** ⚠️ **PERLU PERBAIKAN (NEEDS IMPROVEMENT)**

### Kekuatan Backend

* **Gaya Penulisan Kode:** Rapi, mudah dibaca, dan mengikuti standar PSR.
* **Database:** Penggunaan `DB::transaction` pada operasi kompleks (seperti `ReceptionController::store`) sangat bagus untuk mencegah data korup.

### Kelemahan Backend (Utang Teknis)

* **Fat Controllers (Controller Gemuk):**
  * `ReceptionController` memiliki lebih dari **1.150 baris**. Controller ini menangani import file, print PDF, logika bisnis, DAN notifikasi sekaligus.
  * *Dampak:* Sulit dites, sulit dibaca, dan rawan bug saat diedit.
  * *Saran:* Pisahkan logika ke dalam **Service Classes** (misal: `ReceptionService`, `OrderImportService`).
* **Hardcoded Strings:**
  * Nilai seperti `'shoes'`, `'accessories'`, `'before'`, `'Gudang Penerimaan'` ditulis manual di banyak tempat.
  * *Dampak:* Risiko typo tinggi (misal nulis `'shoe'` padahal harusnya `'shoes'`) yang menyebabkan bug.
  * *Saran:* Gunakan **PHP Enums** (misal: `StorageCategory::SHOES`, `Location::GUDANG_PENERIMAAN`).
* **Duplikasi Logika:**
  * Logika buat/update Customer (`Customer::updateOrCreate`) diulang-ulang di berbagai method.
  * *Saran:* Pindahkan ke `CustomerService` atau fungsi helper.

---

## 4. Frontend & UI/UX

**Status:** ✅ **BAIK (GOOD)**

### Kekuatan Frontend

* **Konsistensi Visual:** Penggunaan Tailwind CSS yang ekstensif membuat tampilan rapi dan seragam.
* **Responsivitas:** Sudah mendukung tampilan mobile (`hidden sm:inline-block`, `overflow`).
* **Interaktivitas:** Sidebar dan elemen UI lain menggunakan Alpine.js dengan benar.

### Kelemahan Frontend

* **Campuran Gaya Javascript:**
  * View `StorageRack` menggunakan **Vanilla JS** kuno (`document.getElementById`, `onclick`) bercampur dengan Alpine.js.
  * *Dampak:* Kode jadi tidak konsisten dan lebih sulit dikelola.
  * *Saran:* Ubah script manual menjadi komponen Alpine.js (`x-data`).
* **Ketergantungan CDN:**
  * Library penting (SweetAlert2, Chart.js) diload lewat link CDN online di `app.blade.php`.
  * *Risiko:* Kalau internet mati atau lambat, aplikasi error saat development. Versi juga bisa berubah sendiri.
  * *Saran:* Install lewat `npm` agar tersimpan lokal di project.

---

## 5. Rencana Perbaikan (Action Plan)

### Prioritas 1: Tinggi (Stabilitas & Kualitas)

* [ ] **Refactor `ReceptionController`**: Pecah logika `processReception` dan `import` ke Service terpisah.
* [ ] **Implementasi Enums**: Ganti tulisan manual status/kategori dengan PHP Enums.
* [ ] **Sentralisasi Logika Customer**: Buat Service khusus untuk menangani data Customer.

### Prioritas 2: Sedang (Kemudahan Maintenanc)

* [ ] **Rapikan Frontend**: Ubah script JS manual di View menjadi komponen Alpine.js.
* [ ] **Manajemen Dependency**: Pindahkan script CDN ke `package.json` (lokal).

### Prioritas 3: Rendah (Optimasi)

* [ ] **Policies Granular**: Tambahkan lapisan keamanan ekstra untuk aksi spesifik per data.
