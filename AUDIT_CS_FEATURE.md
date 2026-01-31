# Audit Fitur CS (Customer Service & Lead Management)

**Tanggal:** 31 Januari 2026
**Auditor:** Antigravity

## 1. Ringkasan Eksekutif

Fitur `CS` pada aplikasi ini berfungsi sebagai "Mini CRM" yang cukup canggih, mencakup manajemen Lead (Pipeline Greeting -> Konsultasi -> Closing), pembuatan Quotation, SPK, hingga Handover ke Workshop. Secara visual dan fungsional, fitur ini berjalan baik. Namun, ditemukan **Celah Keamanan Kritis (Critical Security Vulnerability)** terkait otorisasi data (IDOR) dan masalah struktur kode Backend yang perlu segera ditangani.

---

## 2. Audit Keamanan (Security Audit)

**Status:** 🚨 **KRITIS (CRITICAL)**

### Temuan Kritis (High Risk)

1. **Insecure Direct Object Reference (IDOR) pada `CsLeadController`:**
    * **Masalah:** Pada method seperti `updateStatus`, `show`, `moveToKonsultasi`, `generateSpk`, controller hanya melakukan `CsLead::findOrFail($id)`.
    * **Dampak:** User dengan role `cs` bisa melihat atau mengubah Lead milik Sales/CS lain hanya dengan menebak ID-nya, meskipun di Dashboard daftar lead sudah difilter. Tidak ada pengecekan kepemilikan (`cs_id`) di method-method aksi tersebut.
    * **Rekomendasi:** Tambahkan pengecekan kepemilikan atau gunakan Global Scope/Policy.

    ```php
    // Contoh perbaikan:
    if ($user->role !== 'admin' && $lead->cs_id !== $user->id) { abort(403); }
    ```

2. **Validasi File Upload:**
    * **Status:** ✅ Aman.
    * Terdapat validasi yang baik `mimes:jpeg,png,jpg` dan `max:5120` pada upload bukti bayar dan foto referensi.

---

## 3. Audit Backend (Code Quality)

**Status:** ⚠️ **PERLU IMPROVEMENT**

### Masalah Utama (Technical Debt)

1. **Fat Controller (`CsLeadController`):**
    * Controller ini sangat besar (>1000 baris) dan menangani terlalu banyak tanggung jawab (Violates Single Responsibility Principle).
    * **Contoh:** Method `generateSpk()` (Baris 450-618) menangani:
        * Normalisasi Customer.
        * Kalkulasi harga item & service.
        * Logic Custom Service.
        * Generate Nomor SPK.
        * Database Transaction.
        * Logging Activity.
    * **Dampak:** Kode sulit ditest dan rawan bug jika ada perubahan logic harga/diskon.

2. **Duplikasi Logika Bisnis:**
    * Logika pembuatan `WorkOrder` di `handToWorkshop` (Baris 660-800) kemungkinan besar duplikat atau mirip dengan logic di `ReceptionController`. Jika satu berubah, yang lain bisa tertinggal.
    * Pembuatan/Update Customer (`Customer::updateOrCreate`) tersebar di mana-mana.

3. **Hardcoded Values:**
    * String kategori seperti `'Layanan Custom'`, `'Sepatu'`, `'Tas'` ditulis manual (hardcoded). Sebaiknya gunakan Enum.

### Rekomendasi Backend

1. **Extract Service:** Pindahkan logic berat ke `app/Services/Cs/LeadService.php` dan `app/Services/Cs/SpkService.php`.
2. **Security Policy:** Buat `CsLeadPolicy` untuk menangani otorisasi view/update.

---

## 4. Audit Frontend (UI/UX)

**Status:** ✅ **BAIK (GOOD) dengan Catatan**

### Kelebihan

* **Desain:** Tampilan Kanban Board (`dashboard.blade.php`) modern, bersih, dan informatif.
* **Fitur:** Drag & Drop berjalan mulus menggunakan `Sortable.js`.
* **Responsif:** Layout Grid menyesuaikan layar (Mobile/Desktop) dengan baik (`grid-cols-1 lg:grid-cols-3`).

### Kekurangan

* **Inkonsistensi Javascript:**
  * Aplikasi utama menggunakan **Alpine.js**, tapi view CS Dashboard banyak menggunakan **Vanilla JS** (manual `document.getElementById`, `fetch`, `onclick`).
  * Penggunaan Modal dilakukan lewat manipulasi class manual (`classList.remove('hidden')`), padahal bisa lebih bersih pakai `x-data="{ open: false }"` ala Alpine.
  * Manual `fetch` API tanpa error handling global yang konsisten (hanya alert sederhana).

### Rekomendasi Frontend

* **Refactor ke Alpine.js:** Ubah manajemen Modal dan State lead ke Alpine.js agar kode lebih konsisten dengan modul lain.

---

## 5. Kesimpulan & Langkah Selanjutnya

Fitur CS secara bisnis sudah "siap pakai", namun memiliki celah keamanan yang wajib ditutup sebelum production deployment.

**Action Plan (Tanpa Mengubah Flow):**

1. **Fix Security (Prioritas Utama):** Tambahkan pengecekan `if ($lead->cs_id !== Auth::id())` di semua method krusial pada `CsLeadController`.
2. **Refactor Backend:** Pindahkan logic `generateSpk` dan `handToWorkshop` ke Service Class untuk mengurangi beban Controller.
3. **Refactor Frontend (Opsional):** Modernisasi JS di Dashboard CS agar seragam dengan Alpine.js.
