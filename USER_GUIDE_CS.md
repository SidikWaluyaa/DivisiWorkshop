# Panduan Penggunaan Sistem CS Management

Dokumen ini menjelaskan alur kerja dan penggunaan fitur-fitur pada modul Customer Service (CS) Management.

## 1. Dashboard Overview

Halaman Dashboard CS menampilkan:

- **Metrics Card**: Ringkasan jumlah lead di setiap tahap (Greeting, Konsultasi, Closing), Hot Leads, dan perlu Follow Up.
- **Monitoring Pembayaran Workshop**: Tabel pesanan workshop yang menunggu konfirmasi pembayaran (Post-Assessment).
- **Kanban Board**: Tiga kolom utama untuk manajemen lead:
  - **Greeting**: Lead baru masuk.
  - **Konsultasi**: Lead sedang diskusi/quotation.
  - **Closing/SPK**: Lead siap deal atau sudah SPK.

## 2. Membuat Lead Baru (Greeting)

1. Klik tombol **"+ Lead Baru"** di pojok kanan atas atau **"Chat Masuk"**.
2. Isi form:
   - **No. Telepon**: Wajib diisi (format 08...).
   - **Nama Customer**: Opsional.
   - **Sumber**: WhatsApp, Instagram, dll.
   - **Prioritas**: HOT / WARM / COLD.
3. Klik **Simpan**. Lead akan muncul di kolom **GREETING**.

## 3. Tahap Konsultasi & Quotation

1. Klik kartu lead di kolom Greeting, atau klik tombol **"→ Konsultasi"** pada kartu.
2. Di halaman detail lead:
   - Catat preferensi customer (Merek sepatu, Warna, dll).
   - Klik **"Buat Quotation"** (Tab Quotation).
   - Tambahkan item layanan dan harga.
   - Klik **"Simpan & Generate Preview"**.
3. Kirim Quotation ke customer (via WhatsApp/Download PDF).
4. Jika customer setuju, klik **"Tandai Diterima"**. Lead siap dipindah ke Closing.

## 4. Tahap Closing & Generate SPK

1. Pindahkan lead ke kolom **CLOSING** (drag & drop atau tombol aksi).
2. Klik tombol **"Generate SPK"** pada kartu lead atau di halaman detail.
3. Isi form SPK:
   - Lengkapi data alamat customer.
   - Pilih jenis pengiriman (Offline/Online/Pickup).
   - Tentukan DP (min. 50% atau sesuai kebijakan).
4. Klik **"Generate SPK"**. Nomor SPK akan otomatis dibuat.

## 5. Konfirmasi Pembayaran DP (SPK)

1. Setelah SPK dibuat, status menjadi **Waiting DP**.
2. Jika customer sudah transfer DP:
   - Buka detail lead/SPK.
   - Klik tombol **"Konfirmasi DP Dibayar"**.
   - Isi metode pembayaran dan upload bukti transfer.
   - Klik **"Kirim Verifikasi"**.
3. Status berubah menjadi **Waiting Verification**. Finance akan memverifikasi pembayaran.
4. Setelah diverifikasi Finance (Status: **DP Paid**), tombol **"→ Workshop"** akan muncul.

## 6. Serah Terima ke Workshop

1. Pastikan SPK sudah lunas DP (Status: DP Paid).
2. Klik tombol **"→ Workshop"** di dashboard atau detail lead.
3. Sistem akan membuat **Work Order** dan melempar data ke bagian Gudang/Workshop.
4. Lead dianggap selesai (Converted).

## 7. Monitoring Pembayaran Workshop (Pelunasan)

1. Cek bagian **"Monitoring Pembayaran Workshop"** di atas dashboard.
2. Daftar ini berisi order yang sudah selesai di-assess oleh workshop dan butuh pelunasan/pembayaran tambahan.
3. Klik **"Konfirmasi Bayar"**, upload bukti transfer pelunasan.
4. Order akan diteruskan kembali ke Workshop (Preparation) setelah diverifikasi Finance.
