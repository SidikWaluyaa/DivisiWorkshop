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

### 🔍 Fitur Pencarian & Rekomendasi Jasa Otomatis pada Modal "Tambah Jasa" CX (`/cx`)
* **Mengapa Fitur Ini Dibuat:** Tim CS (Customer Service) membutuhkan proses penginputan layanan tambahan yang cepat saat ada kesepakatan baru dengan pelanggan. Sebelumnya, CS harus mencari nama layanan secara manual dari daftar pilihan (dropdown) yang sangat panjang dan rawan salah input harga.
* **Perubahan & Manfaatnya:**
  * **Pencarian Cepat Layanan (Autocomplete Search):** Menu pilihan dropdown kini dilengkapi kotak pencarian interaktif (dikendalikan dengan Alpine.js). CS cukup mengetik kata kunci (misal: *"Clean"*) untuk menyaring daftar jasa resmi secara seketika.
  * **Tombol Saran Jasa (Badge Quick-Click):** Jika sebelumnya teknisi di stasiun bengkel sudah merekomendasikan jasa tertentu secara tertulis, rekomendasi tersebut otomatis muncul berupa tombol badge saran di bagian atas modal.
  * **Pengisian Otomatis Sekali Klik (Auto-Fill):** CS cukup mengklik badge saran tersebut sekali, maka kolom Kategori, Nama Jasa, Harga resmi, dan Hari Kerja tambahan akan **langsung terisi otomatis secara ajaib**, mengurangi typo dan mempercepat proses.

### 🔔 Fitur Tombol Lonceng Panggil Pengambilan Sepatu Real-Time (Akun Sandi)
* **Mengapa Fitur Ini Dibuat:** Mempermudah tim administrasi/CS di halaman detail SPK untuk memanggil petugas gudang/workshop (dalam hal ini akun `sandi@workshop.com`) guna mengambil sepatu dari rak barang jadi secara real-time.
* **Perubahan & Manfaatnya:**
  * **Tombol Lonceng Panggil (`/admin/orders/{id}`):** Menambahkan tombol **"Panggil Pengambilan"** berikon lonceng beranimasi goyang (`animate-bounce`) warna indigo di halaman detail SPK. Ketika diklik, sinyal panggilan langsung masuk antrean database (`pickup_calls`).
  * **Listener Real-Time Multi-Akun:** Menambahkan detektor otomatis yang berjalan di latar belakang setiap 3 detik khusus untuk akun terdaftar (whitelist) di sistem (saat ini aktif untuk **`sandi@workshop.com`** dan **`admin@workshop.com`**).
  * **Bunyi Lonceng & Popup Detil Lengkap (2 Kolom):** Ketika ada panggilan pending, browser akun terdaftar akan memutarkan berkas audio kustom **`ambil.aac`** (dengan *looping* terus-menerus sebelum ditutup) dan menampilkan popup **SweetAlert2 (lebar 820px split 2 kolom premium)**. Kolom kiri menampilkan foto sepatu cover, kolom kanan menampilkan rincian terstruktur meliputi Nomor SPK, Nomor & Status Pembayaran Invoice, Nama & Telp Pelanggan, Spesifikasi Sepatu (Brand/Tipe/Warna/Size), **Status Pengerjaan**, dan Catatan SPK.
  * **Pemisahan & Penyembunyian Info Rak:** Lokasi rak dibedakan menjadi **Rak Inbound**, **Rak Aksesoris**, dan **Rak Finish** secara terpisah dengan desain border warna khusus. Jika order tidak ditempatkan di rak mana pun, kotak informasi rak otomatis disembunyikan agar popup tetap ringkas.
  * **Tombol Akses Stasiun Langsung (Direct Link):** Popup kini dilengkapi dengan tombol **"Buka [Nama Stasiun] ➔"** (misal: Buka Stasiun QC / Stasiun Sortir) yang akan membuka tab baru langsung menuju stasiun SPK terkait. Khusus stasiun **Finish**, Sandi diarahkan ke halaman pencarian index (`/finish?search=NOMOR_SPK`) agar data SPK terfilter otomatis seperti hasil pencarian visual. Untuk divisi yang tidak memiliki detail individu (seperti *QC* dan *Preparation*), diarahkan ke halaman index aman untuk mencegah error *BadMethodCallException*.
  * **Bypass Blokir Autoplay & Polling 3 Detik:** Menambahkan pelacak gesture (`mousemove`/`touchstart`/`click`) untuk meng-unlock pemutaran suara di browser Sandi apabila diblokir di awal oleh browser. Jeda polling dipercepat menjadi setiap **3 detik** agar notifikasi terdengar seketika.
  * **Tutup Bebas & Tanpa Pengaruh Data:** Menutup popup (melalui tombol Tutup Peringatan, Esc, tombol X, atau klik luar) otomatis menandai notifikasi dibaca di latar belakang agar berhenti berbunyi, tanpa mempengaruhi status transaksi/SPK.



