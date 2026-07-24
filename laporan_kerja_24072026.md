# 📋 Laporan Hasil Kerja & Fitur Baru Sistem Workshop
**Hari & Tanggal:** Jumat, 24 Juli 2026

Laporan ini memuat daftar pekerjaan yang telah diselesaikan hari ini.

---

## 1. 🛠️ Daftar Pekerjaan Selesai

### 🖨️ Fitur Cetak Label Alamat Polos (Plain A5 Address Label)
* **Mengapa Fitur Ini Dibuat:** Tim pengiriman membutuhkan opsi cetak label alamat yang bersih dan sederhana (hanya berisi Nama, Alamat, dan Nomor Telepon pelanggan) tanpa ornamen warna-warni, logo, peta, atau border tebal untuk menghemat tinta dan mempercepat pembacaan alamat kurir pihak ketiga.
* **Perubahan & Manfaatnya:**
  * **Layout Polos (Tanpa UI):** Halaman cetak didesain polos (hitam putih) di atas kertas A5 landscape, tanpa ada dekorasi visual sama sekali.
  * **Optimasi Posisi & Ukuran Font:** Memposisikan detail alamat sedikit lebih ke bawah dan ke kanan agar pas dengan area kertas kosong. Ukuran font diperkecil kembali, serta nomor telepon diposisikan rapat langsung di bawah alamat di dalam satu blok baris.
  * **Tombol Akses Cepat:** Menambahkan tombol **"Print Alamat Polos"** berwarna abu-abu gelap tepat di sebelah tombol "Print Label" pada halaman detail order (`/admin/orders/{id}`).
  * **Cetak Otomatis:** Jendela print browser otomatis terbuka setelah halaman label dimuat.

