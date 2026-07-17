# Laporan Audit Database & Analisis Sistem
**Sistem ERP & CRM Workshop Sepatu**
*Dipersiapkan oleh: Senior Database Administrator & System Analyst*

---

## 📊 Ringkasan Eksekutif (Executive Summary)

Laporan audit ini menyajikan analisis mendalam terhadap skema database MySQL (total **58 tabel**) yang menopang aplikasi Sistem Workshop Sepatu. Evaluasi difokuskan pada integritas data, normalisasi, performa query, serta skalabilitas sistem jangka panjang. Berikut adalah poin-poin penting bagi para stakeholder non-teknis:

1. **Dualisme Kebenaran Finansial (Kritis):** Terdapat tumpang tindih data transaksi keuangan yang parah antara tabel `work_orders` (SPK) dan tabel `invoices`. Hal ini berisiko memicu selisih nominal pada laporan keuangan jika sinkronisasi aplikasi mengalami kegagalan.
2. **Redundansi Profil Pelanggan:** Informasi profil pelanggan (Nama, WhatsApp, Email, Alamat) diduplikasi secara mentah di banyak tabel seperti `cs_leads`, `work_orders`, `shippings`, dan `warranty_claims` alih-alih merujuk ke ID unik tabel `customers`.
3. **Penyimpanan Finansial Bertipe String/Text:** Pada tabel program promosi tambahan (`otos`), nominal harga disimpan dalam tipe data `varchar` (teks). Ini menghalangi database melakukan kalkulasi matematika dasar secara efisien dan rawan terjadi *corrupted data*.
4. **Resiko Kehilangan Jejak Audit Pembayaran:** Adanya dua tabel pembayaran terpisah (`order_payments` dan `invoice_payments`) yang mencatat data secara redundan menyulitkan rekonsiliasi kas bank.
5. **Potensi Bottleneck Performa pada Timeline:** Tabel audit trail `work_order_logs` yang menampung log aktivitas teknisi akan terus bertambah besar secara eksponensial. Kolom kategori log (`action` & `step`) saat ini belum memiliki indeks, sehingga berisiko melambatkan pemuatan halaman detail SPK di masa depan.
6. **Data Terstruktur Terjebak dalam Kolom TEXT:** Informasi detail jasa dan bahan di tingkat CRM masih disimpan dalam format serialized string/JSON (`longtext`), menyulitkan pelacakan analitik performa jasa terlaris.
7. **Rekomendasi Umum:** Database perlu disesuaikan menuju tingkat normalisasi ketiga (3NF) guna menjamin konsistensi data, kemudahan maintenance, serta efisiensi penyimpanan server.

---

## 🔍 Detail Temuan Audit

### 1. Struktur Schema & Duplikasi

#### 🔴 Temuan 1.1: Redundansi Ekstrem Kolom Finansial (Dualisme Kebenaran)
*   **Lokasi:** Tabel `work_orders` dan `invoices`
*   **Kategori:** Duplikasi / Normalisasi
*   **Severity:** **Critical**
*   **Masalah:** Data keuangan dideklarasikan secara mandiri di kedua tabel. Tabel `work_orders` memiliki kolom `total_transaksi`, `total_paid`, `sisa_tagihan`, `status_pembayaran`, dan `discount`. Tabel `invoices` memiliki kolom `total_amount`, `paid_amount`, `discount`, `status` (Belum Bayar, DP/Cicil, Lunas).
*   **Dampak:** Apabila salah satu tabel ter-update sedangkan tabel lainnya gagal (misalnya karena gangguan koneksi di tengah pemrosesan aplikasi), laporan keuangan akan desinkronisasi dan tidak valid.
*   **Rekomendasi Perbaikan:** Pusat data finansial harus berada di tabel `invoices`. Kolom keuangan di `work_orders` dinonaktifkan/dihapus, lalu dialihkan menggunakan relasi dinamis ke `invoices.id`.
    ```sql
    -- Contoh skema pembersihan (Rekomendasi DDL)
    ALTER TABLE work_orders 
    DROP COLUMN total_transaksi,
    DROP COLUMN total_paid,
    DROP COLUMN sisa_tagihan,
    DROP COLUMN status_pembayaran,
    DROP COLUMN discount;
    ```

#### 🟡 Temuan 1.2: Duplikasi Profil Kontak Pelanggan
*   **Lokasi:** Tabel `customers`, `cs_leads`, `work_orders`, `shippings`, dan `warranty_claims`
*   **Kategori:** Duplikasi / Normalisasi
*   **Severity:** **High**
*   **Masalah:** Kolom `customer_name` dan `customer_phone` disalin sebagai string baru di hampir setiap tabel transaksi alih-alih merujuk ke tabel master `customers`.
*   **Dampak:** Anomali pembaruan data (Update Anomaly). Jika pelanggan mengubah nomor WhatsApp-nya, perubahan tersebut tidak otomatis tercermin di riwayat pengiriman, klaim garansi, maupun SPK aktif, sehingga membingungkan admin operasional.
*   **Rekomendasi Perbaikan:** Wajibkan penggunaan `customer_id` sebagai Foreign Key di semua tabel terkait, lalu hapus kolom string duplikat tersebut.
    ```sql
    -- Menambahkan FK di tabel work_orders (contoh rancangan DDL)
    ALTER TABLE work_orders ADD COLUMN customer_id BIGINT UNSIGNED NULL;
    ALTER TABLE work_orders ADD CONSTRAINT fk_work_orders_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL;
    ```

#### 🟡 Temuan 1.3: Redundansi Log Pembayaran Ganda
*   **Lokasi:** Tabel `order_payments` dan `invoice_payments`
*   **Kategori:** Duplikasi
*   **Severity:** **High**
*   **Masalah:** Sistem memiliki dua tabel pencatatan kas masuk terpisah. `order_payments` terikat langsung pada `work_order_id`, sedangkan `invoice_payments` terikat pada `invoice_id`.
*   **Dampak:** Rekonsiliasi kas bank menjadi sangat kompleks karena pelacakan kas terbagi dua arus. Data pembayaran rawan terhitung ganda (*double entry*).
*   **Rekomendasi Perbaikan:** Satukan pencatatan kas masuk ke dalam satu tabel pembayaran terpusat (misal `payments`) yang hanya merujuk ke `invoice_id` (karena satu invoice dapat mewakili satu atau banyak SPK sekaligus).

---

### 2. Desain Field / Kolom

#### 🟡 Temuan 2.1: Tipe Data Finansial Menggunakan VARCHAR pada Tabel OTO
*   **Lokasi:** Tabel `otos` (kolom `total_normal_price`, `total_oto_price`, dan `total_discount`)
*   **Kategori:** Field Design
*   **Severity:** **High**
*   **Masalah:** Kolom-kolom harga pada program penawaran khusus (OTO) disimpan menggunakan tipe data `varchar(191)` (string/teks).
*   **Dampak:** 
    1. Operasi agregasi database seperti `SUM()`, `AVG()`, dan pembandingan numerik (`>=`, `<=`) tidak dapat dilakukan secara native di tingkat database tanpa casting manual yang lambat.
    2. Data rawan disusupi teks non-angka yang dapat memicu crash aplikasi saat kalkulasi.
*   **Rekomendasi Perbaikan:** Ubah tipe data kolom tersebut menjadi `decimal(15,2)` atau `int` (jika sistem sepakat tidak menggunakan pecahan desimal rupiah).
    ```sql
    -- Contoh migrasi tipe data kolom harga
    ALTER TABLE otos 
    MODIFY COLUMN total_normal_price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    MODIFY COLUMN total_oto_price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    MODIFY COLUMN total_discount DECIMAL(15,2) NOT NULL DEFAULT 0.00;
    ```

#### 🔵 Temuan 2.2: Dualisme Penyimpanan Aksesoris
*   **Lokasi:** Tabel `work_orders` (kolom `accessories_data` vs `accessories_tali`, `accessories_insole`, `accessories_box`)
*   **Kategori:** Field Design / Duplikasi
*   **Severity:** **Medium**
*   **Masalah:** Informasi aksesoris bawaan sepatu disimpan dua kali secara bersamaan: di dalam teks JSON (`accessories_data` bertipe `longtext`) dan di dalam kolom kolom tabular (`accessories_tali`, `accessories_insole`, `accessories_box` bertipe `varchar(20)`).
*   **Dampak:** Boros penyimpanan dan rawan desinkronisasi antara data terstruktur JSON dan data kolom flat saat admin melakukan penyuntingan manual.
*   **Rekomendasi Perbaikan:** Hapus kolom JSON `accessories_data` dan optimalkan penggunaan kolom flat yang sudah di-index. Kolom flat jauh lebih cepat saat dibaca oleh query statistik logistik gudang.

---

### 3. Index & Query Performance

#### 🟡 Temuan 3.1: Ketiadaan Index pada Kolom Aktivitas Timeline
*   **Lokasi:** Tabel `work_order_logs` (kolom `action` dan `step`)
*   **Kategori:** Index & Query Performance
*   **Severity:** **High**
*   **Masalah:** Kolom `step` dan `action` sangat sering digunakan untuk melakukan klasifikasi phase timeline, filter aktivitas, dan grouping log SPK. Namun, tidak ada indeks yang menaunginya.
*   **Dampak:** Seiring berjalannya sistem, tabel log akan menampung ratusan ribu baris data. Setiap kali admin membuka halaman detail SPK (`/admin/orders/show`), database akan melakukan *full table scan* untuk menyusun timeline, memicu beban server CPU yang tinggi dan melambatkan pemuatan halaman.
*   **Rekomendasi Perbaikan:** Tambahkan Composite Index untuk mempercepat query timeline detail.
    ```sql
    -- Contoh penambahan indeks gabungan
    CREATE INDEX idx_work_order_logs_query ON work_order_logs (work_order_id, action, step);
    ```

#### 🔵 Temuan 3.2: Foreign Key Tanpa Indexing
*   **Lokasi:** Berbagai tabel transaksi (misal `customer_id` di `cs_spk`, `processed_by` di `warranty_claims`, `uploaded_by` di `customer_photos`)
*   **Kategori:** Index
*   **Severity:** **Medium**
*   **Masalah:** Beberapa kolom foreign key (yang digunakan untuk relasi `JOIN`) dideklarasikan tanpa indeks pembantu di tabel anak.
*   **Dampak:** Operasi query `JOIN` yang menghubungkan tabel-tabel ini akan mengalami degradasi performa yang signifikan saat volume data meningkat.
*   **Rekomendasi Perbaikan:** Pastikan setiap relasi kunci tamu (Foreign Key) dibarengi dengan pembuatan indeks.
    ```sql
    -- Contoh pembuatan indeks kunci tamu
    CREATE INDEX idx_warranty_claims_processed_by ON warranty_claims (processed_by);
    ```

---

### 4. Relasi & Constraint

#### 🟡 Temuan 4.1: Penggunaan ON DELETE CASCADE pada Tabel Finansial Tanpa Soft-Delete
*   **Lokasi:** Relasi tabel `invoice_payments` ke `invoices`
*   **Kategori:** Relasi / Safety
*   **Severity:** **High**
*   **Masalah:** Cascading rule diset otomatis menghapus pembayaran jika data invoice diinduknya dihapus secara tidak sengaja.
*   **Dampak:** Hilangnya riwayat transaksi kas masuk penting secara permanen jika user salah klik menghapus data invoice. Penghapusan data akuntansi tanpa *soft-delete* sangat berisiko terhadap kepatuhan audit keuangan.
*   **Rekomendasi Perbaikan:** Gunakan opsi `ON DELETE RESTRICT` pada tabel pembayaran agar sistem menolak penghapusan invoice selama data pembayaran di dalamnya masih tercatat.
    ```sql
    -- Contoh perubahan constraint relasi keuangan
    ALTER TABLE invoice_payments DROP FOREIGN KEY invoice_payments_invoice_id_foreign;
    ALTER TABLE invoice_payments ADD CONSTRAINT fk_payments_invoice_restrict FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE RESTRICT;
    ```

---

### 5. Skalabilitas & Maintenance

#### 🟡 Temuan 5.1: Serialized Data pada Level Prospek CRM
*   **Lokasi:** Tabel `cs_quotation_items` dan `cs_spk_items` (kolom `services` dan `requested_materials` bertipe `longtext`)
*   **Kategori:** Skalabilitas / Kolom JSON/TEXT
*   **Severity:** **High**
*   **Masalah:** Data layanan jasa dan kebutuhan bahan baku disimpan dalam bentuk teks serialized (JSON mentah) di level prospek CRM.
*   **Dampak:** Database tidak bisa melakukan analisis tren bisnis dengan efisien (misalnya mencari "10 Jenis Jasa Reparasi yang Paling Sering Ditanyakan Pelanggan di Tahap Quotation" atau "Berapa banyak bahan lem yang diproyeksikan bulan depan"). Query pencarian data di dalam JSON bertipe TEXT membutuhkan resource besar.
*   **Rekomendasi Perbaikan:** Normalisasikan data jasa dan bahan di tingkat CRM ke dalam tabel pivot (seperti halnya `work_order_services` di tingkat produksi), sehingga relasi langsung merujuk ke master `services` dan `materials`.
