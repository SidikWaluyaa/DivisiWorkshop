# PROPOSAL STRATEGIS: PROGRAM MEMBERSHIP, LOYALTY & REFERRAL SHOE WORKSHOP
**Mengoptimalkan Retensi Pelanggan, Mengurangi Ketergantungan Iklan, dan Mendorong Repeat Order Berkelanjutan**

---

> **Dokumen Eksekutif:** Hasil Penyelarasan Rapat Tim, Analisis Keuntungan, Proyeksi Finansial, Audit HPP & Mitigasi Lapangan  
> **Disusun Oleh:** Tim Business & Data Analytics Shoe Workshop  
> **Periode Basis Data:** 1 Juni s/d 30 September 2026 (2.955 Pelanggan Aktif, Rp 1,83 Miliar Omset)  
> **Status:** Dokumen Final Siap Eksekusi (Ready for Executive Decision)

---

## 1. Executive Summary

### 1.1 Latar Belakang & Urgensi Bisnis
Sepanjang periode 4 bulan terakhir (**1 Juni s/d 30 September 2026**), Shoe Workshop mencatatkan performa transaksi yang solid dengan melayani **2.955 pelanggan aktif** dan menghasilkan total perputaran omset sebesar **Rp 1.828.292.822 (~Rp 1,83 Miliar)** dari **3.170 transaksi invoice** dan **3.983 pasang sepatu**.

Namun, evaluasi mendalam terhadap database membuktikan adanya celah pertumbuhan terbesar (*the single biggest growth bottleneck*): **ketergantungan ekstrem pada pelanggan baru sekali beli (*one-time buyers*) serta ketergantungan pada iklan berbayar (Meta Ads)**.

Dalam rentang 4 bulan ini:
* Sebanyak **93,84% pelanggan (2.773 orang) hanya pernah bertransaksi satu kali** dan belum pernah kembali (*one-time drop*).
* Angka retensi *Repeat Order* baru berada di level **6,16% (182 orang)**.
* Sebanyak **81,74% invoice (2.591 transaksi) hanya membawa 1 pasang sepatu**.
* Di tengah naiknya biaya iklan digital (Meta Ads CAC), membiarkan ribuan pelanggan berlalu tanpa program pengikat loyalitas adalah pemborosan aset data dan potensi omset yang sangat masif.

### 1.2 Kerangka Dual-Engine: Penyelarasan Hasil Rapat Tim
Berdasarkan hasil diskusi dan kesepakatan tim project, program ini dirancang dengan struktur terpadu tiga pilar yang saling mengunci:

<div class="flow-container">
    <div class="flow-box active">
        <div class="flow-title">1. TIER REGULER</div>
        <div class="flow-subtitle">Transaksi Ke-1 (Semua Pelanggan)</div>
        <div class="flow-content">
            • <strong>Tanpa benefit diskon apa-apa</strong><br>
            • Belum ada program stempel loyalty<br>
            • Pintu masuk aktivasi program Referral
        </div>
    </div>
    <div class="flow-box highlight">
        <div class="flow-title">2. TIER GOLD</div>
        <div class="flow-subtitle">Akumulasi Belanja ≥ Rp 1.500.000</div>
        <div class="flow-content">
            • <strong>Diskon 15% khusus Jasa Lem Jahit</strong><br>
            • <strong>Program Stempel Aktif:</strong> 1 Stamp / Transaksi ≥ 250k (Non-Kelipatan)<br>
            • 10 Stamp = Voucher Diskon 25% Lem Jahit
        </div>
    </div>
    <div class="flow-box vip">
        <div class="flow-title">3. TIER PLATINUM VIP</div>
        <div class="flow-subtitle">Akumulasi Belanja ≥ Rp 3.500.000</div>
        <div class="flow-content">
            • <strong>Diskon 30% Jasa Lem Jahit</strong> (Batas Maksimal)<br>
            • <strong>Voucher Diskon Deep Clean Rp 60.000</strong><br>
            • Hak akumulasi stempel lanjut dari Gold
        </div>
    </div>
</div>

* **Pilar 1 - Membership (Status Tahunan Berjenjang):**
  * **Reguler Member:** Otomatis aktif saat pertama kali bertransaksi, **tanpa benefit diskon apa-apa** (hanya pencatatan di database pelanggan).
  * **Gold Member (Akumulasi Belanja ≥ Rp 1.500.000):** Mendapatkan **Voucher Diskon 15% khusus Jasa Lem Jahit** (berlaku 1 tahun).
  * **Platinum VIP (Akumulasi Belanja ≥ Rp 3.500.000):** Mendapatkan **Voucher Diskon 30% khusus Jasa Lem Jahit** (*cap* batas maksimal diskon bengkel) + **Voucher Diskon Deep Clean seharga Rp 60.000**.
  *(Catatan: Seluruh fasilitas operasional non-diskon ditiadakan agar fokus penuh pada eksekusi voucher diskon).*
* **Pilar 2 - Loyalty Program (Stempel Transaksi Berulang di Tier Gold):**
  * Program loyalty stempel **mulai berjalan di Tier Gold**.
  * **1 Stempel** didapatkan jika pelanggan melakukan reparasi dengan nominal **≥ Rp 250.000**.
  * **Tidak Berlaku Kelipatan:** 1 transaksi (invoice) bernilai ≥ Rp 250.000 = **1 Stempel** (transaksi < Rp 250.000 = 0 stempel).
  * Setelah mengumpulkan **10 Stempel**, pelanggan mendapatkan **Voucher Diskon 25% khusus Jasa Lem Jahit** (masa berlaku voucher 6 bulan).
  * Jika pelanggan mencapai total transaksi Rp 3,5 Juta dan naik ke Platinum di tengah pengumpulan, akumulasi stempel tetap dapat dilanjutkan.
* **Pilar 3 - Referral Program (Mendorong Pelanggan Naik ke Tier Gold):**
  * Tujuan utama: Mendorong pelanggan (termasuk Reguler) untuk repeat order dan mengakselerasi kenaikan status menjadi Tier Gold.
  * Member yang membawa teman baru bertransaksi: **Pengajak mendapatkan Voucher Deep Clean Rp 60.000** dan **Teman Baru juga mendapatkan Voucher Deep Clean Rp 60.000**.
  * **Syarat Penggunaan:** Baru dapat digunakan pada transaksi berikutnya, untuk 1 pasang sepatu, **wajib disertai jasa perbaikan lainnya di atas Rp 100.000**, dengan **masa berlaku voucher 1 minggu (7 hari)**.
  * **Proteksi Margin:** Karena profit pada voucher Deep Clean Rp 60.000 tergolong **tipis**, kewajiban menyertakan jasa reparasi lain di atas Rp 100.000 berfungsi sebagai penopang margin laba kotor transaksi secara keseluruhan.

---

## 2. Diagnosis Data Perilaku Pelanggan (Juni – September 2026)

Audit data operasional 1 Juni s/d 30 September 2026 membuktikan landasan empiris perancangan program:

### 2.1 Tabulasi Metrik Dasar Pelanggan
| Kategori Metrik | Nilai Aktual di Database | Catatan Analisis |
| :--- | :--- | :--- |
| **Total Pelanggan Aktif** | **2.955 Pelanggan** | Pelanggan unik yang bertransaksi 1 Juni – 30 Sept 2026 |
| **Total Invoice Transaksi** | **3.170 Invoice** | Rata-rata 792,5 invoice per bulan |
| **Total Pasang Sepatu Direparasi** | **3.983 Pasang Sepatu** | Rata-rata intake: ~995 pasang/bulan |
| **Total Akumulasi Omset** | **Rp 1.828.292.822** | Rata-rata omset bulanan: Rp 457.073.205 |
| **Pelanggan 1x Order Saja** | **2.773 orang (93,84%)** | Target pasar raksasa untuk re-aktivasi |
| **Pelanggan Repeat (≥ 2x)** | **182 orang (6,16%)** | Menyumbang Rp 247,4 Juta omset (13,5%) |
| **Pelanggan Super Repeat (≥ 3x)** | **27 orang (0,91%)** | Inti pelanggan paling loyal |
| **Order 1 Pasang Saja** | **2.591 invoice (81,74%)** | Mayoritas customer baru mencoba 1 pasang sepatu |
| **Multi-Pair Drop (≥ 2 Pasang)** | **577 invoice (18,20%)** | Peluang besar mendorong *basket size* |

![Anatomi Pelanggan Shoe Workshop](assets_membership/chart1_current_customer_behavior.png)

### 2.2 Distribusi Nilai Belanja (Basket Size per Invoice)
| Kelompok Belanja | Jumlah Invoice | Proporsi Transaksi | Total Omset Disumbang | Rata-rata per Invoice |
| :--- | :--- | :--- | :--- | :--- |
| **< Rp 250.000** | 1.318 inv | 41,58% | Rp 262.670.127 | Rp 199.294 |
| **Rp 250.000 – Rp 500.000** | 1.297 inv | 40,91% | Rp 454.600.650 | Rp 350.501 |
| **Rp 500.000 – Rp 1.000.000** | 452 inv | 14,26% | Rp 305.151.831 | Rp 675.114 |
| **Rp 1.000.000 – Rp 2.000.000** | 91 inv | 2,87% | Rp 115.614.000 | Rp 1.270.483 |
| **≥ Rp 2.000.000** | 12 inv | 0,38% | Rp 29.750.500 | Rp 2.479.208 |

> **Temuan Kritis Terkait Aturan Stempel:**  
> • Sebanyak **1.852 invoice (58,42%)** bernilai **≥ Rp 250.000**, yang berarti mayoritas transaksi reparasi reguler bengkel memenuhi syarat untuk mendapatkan stempel.  
> • Sebanyak **1.318 invoice (41,58%)** bernilai **< Rp 250.000** (rata-rata Rp 199.294). Batasan minimal Rp 250.000 ini menjadi alat dorong (*push factor*) bagi CS untuk menawarkan layanan tambahan (*up-selling*) sebesar rata-rata Rp 50.706 agar invoice mencapai Rp 250k.

![Distribusi Belanja](assets_membership/chart2_spending_distribution.png)

### 2.3 Analisis Siklus Waktu Repeat (*Repurchase Cycle*)
Dari 215 kejadian repeat order yang terekam sepanjang 1 Juni – 30 September 2026:
* **69,3% (149 kejadian)** melakukan repeat dalam kurun waktu **≤ 30 hari** setelah invoice pertama selesai (pelanggan mengirimkan sepatu berikutnya setelah puas dengan hasil kerja pertama).
* **20,0% (43 kejadian)** melakukan repeat di bulan ke-2 (**31–60 hari**).
* **9,3% (20 kejadian)** melakukan repeat di bulan ke-3 (**61–90 hari**).
* **1,4% (3 kejadian)** melakukan repeat di atas 90 hari.

> **Wawasan Kunci Waktu:**  
> Masa emas (*golden window*) untuk memicu repeat order adalah **dalam 30 hari pertama**. Penetapan masa berlaku Voucher Referral Deep Clean 60k selama **1 minggu (7 hari)** berfungsi menciptakan urgensi tinggi agar pelanggan segera mengambil keputusan tanpa menunda.

---

## 3. Desain Pilar 1: Program Membership (Status Tahunan Berjenjang)

### 3.1 Struktur Matriks 3 Tier Membership
Status membership berlaku selama 1 tahun berdasarkan akumulasi belanja pribadi:

| Level Membership | Ambang Batas Akumulasi Belanja | Hak Benefit Diskon Finansial | Program Loyalty (Stempel) |
| :--- | :--- | :--- | :--- |
| **REGULER MEMBER** | **Otomatis Aktif** sejak transaksi ke-1 (Semua pelanggan baru). | **Tidak ada benefit diskon apa-apa** (hanya pencatatan di database pelanggan). | Belum aktif (baru mulai di Tier Gold). |
| **GOLD MEMBER** | Total Transaksi Akumulasi **≥ Rp 1.500.000** | **Voucher Diskon 15% khusus Jasa Lem Jahit** (Masa berlaku 1 tahun). | **Program Loyalty Aktif:** 1 stempel per transaksi ≥ Rp 250k (tanpa kelipatan). |
| **PLATINUM VIP** | Total Transaksi Akumulasi **≥ Rp 3.500.000** *(Top Spender / Kolektor)* | • **Voucher Diskon 30% khusus Jasa Lem Jahit** *(Cap maksimal diskon bengkel)*<br>• **Voucher Diskon Deep Clean seharga Rp 60.000** | Berhak melanjutkan sisa akumulasi stempel dari Gold. |

### 3.2 Sebaran Pelanggan Berdasarkan Ambang Batas Final di Database
Berdasarkan data transaksi aktual 2.955 pelanggan aktif di database:
* **Reguler Member (< Rp 1.500.000):** **2.718 pelanggan (91,98%)** | Total Omset: **Rp 1,08 Miliar (59,1%)**.
* **Gold Member (Rp 1.500.000 – Rp 3.500.000):** **190 pelanggan (6,43%)** | Total Omset: **Rp 407,6 Juta (22,3%)**.
* **Platinum VIP (≥ Rp 3.500.000):** **47 pelanggan (1,59%)** | Total Omset: **Rp 340,5 Juta (18,6%)**.
* **Total Member Terkualifikasi Awal (Gold + Platinum):** **237 pelanggan (8,02%)** yang telah menyumbang **Rp 748,1 Juta (40,9% dari total perputaran omset bengkel!)**.

---

## 4. Desain Pilar 2: Loyalty Program (Sistem Stempel di Tier Gold)

Program Loyalty dirancang berdampingan dengan Membership dan **berjalan di Tier Gold**:

<div class="flow-container">
    <div class="flow-box active">
        <div class="flow-title">LANGKAH 1</div>
        <div class="flow-subtitle">Transaksi Kualifikasi</div>
        <div class="flow-content">
            Pelanggan Tier Gold melakukan transaksi reparasi <strong>≥ Rp 250.000</strong>.
        </div>
    </div>
    <div class="flow-box highlight">
        <div class="flow-title">LANGKAH 2</div>
        <div class="flow-subtitle">Perolehan Stempel</div>
        <div class="flow-content">
            Mendapatkan <strong>1 Stempel Loyalty</strong> (Aturan Tanpa Kelipatan: 1 Invoice = 1 Stamp).
        </div>
    </div>
    <div class="flow-box vip">
        <div class="flow-title">LANGKAH 3</div>
        <div class="flow-subtitle">Pencapaian Milestone</div>
        <div class="flow-content">
            Kumpulkan <strong>10 Stempel</strong> ➔ Mendapatkan <strong>Voucher Diskon 25% Jasa Lem Jahit</strong> (Valid 6 Bulan).
        </div>
    </div>
</div>

### 4.1 Aturan Pengumpulan Stempel
1. **Syarat Kualifikasi Nilai Belanja:** Transaksi invoice harus **minimal mencapai Rp 250.000 baru mendapatkan 1 Stempel**.
2. **Transaksi di Bawah Rp 250.000:** Mendapatkan **0 Stempel**. Hal ini menjadi pendorong bagi CS untuk menawarkan layanan tambahan agar nilai invoice menembus Rp 250k.
3. **Ketentuan Tanpa Kelipatan (Non-Multiple Rule):**
   * Transaksi Rp 250.000 = **1 Stempel**.
   * Transaksi Rp 600.000 = **1 Stempel**.
   * Transaksi Rp 1.200.000 = **1 Stempel**.  
   *(1 invoice transaksi di atas Rp 250k dihitung 1 stempel tanpa memperhitungkan kelipatan nominal).*

### 4.2 Target Penukaran Reward (10 Stempel)
* **Milestone Reward:** Setelah berhasil mengumpulkan **10 Stempel**, pelanggan berhak mendapatkan **Voucher Diskon 25% khusus Jasa Lem Jahit**.
* **Masa Berlaku Reward:** Voucher Diskon 25% Lem Jahit ini berlaku selama **6 Bulan** sejak diterbitkan.
* **Transisi Naik ke Platinum:** Jika pelanggan mencapai total transaksi Rp 3,5 Juta sebelum menyelesaikan 10 stempel, stempel yang sudah terkumpul tidak hangus dan tetap dapat dilanjutkan.

---

## 5. Desain Pilar 3: Referral Program (Mendorong Kenaikan ke Tier Gold)

Program Referral dirancang sebagai mesin akuisisi organik untuk mendorong pelanggan (termasuk Reguler) agar aktif bertransaksi hingga mencapai ambang batas Tier Gold (Rp 1,5 Juta).

<div class="flow-container">
    <div class="flow-box active">
        <div class="flow-title">1. REKOMENDASI</div>
        <div class="flow-subtitle">Pelanggan Mengajak Teman</div>
        <div class="flow-content">
            Pelanggan membagikan link/kode referral ke teman baru untuk reparasi di Shoe Workshop.
        </div>
    </div>
    <div class="flow-box highlight">
        <div class="flow-title">2. SELESAI TRANSAKSI</div>
        <div class="flow-subtitle">Order Pertama Selesai</div>
        <div class="flow-content">
            Teman baru menyelesaikan & melunasi invoice reparasi pertamanya.
        </div>
    </div>
    <div class="flow-box vip">
        <div class="flow-title">3. REWARD WIN-WIN</div>
        <div class="flow-subtitle">Voucher Deep Clean Rp 60k</div>
        <div class="flow-content">
            Pengajak & Teman Baru masing-masing mendapatkan <strong>Voucher Deep Clean Rp 60k</strong> untuk transaksi berikutnya.
        </div>
    </div>
</div>

### 5.1 Mekanisme Hadiah Win-Win
1. **Pengajak (Referrer):** Mendapatkan **Voucher Deep Clean seharga Rp 60.000**.
2. **Teman Baru (Referee):** Mendapatkan **Voucher Deep Clean seharga Rp 60.000**.

### 5.2 Syarat & Ketentuan Penggunaan Voucher
* **Hanya Berlaku pada Transaksi Selanjutnya (*Next Order*):** Tidak dapat langsung memotong transaksi pertama saat itu juga.
* **Berlaku untuk 1 Pasang Sepatu.**
* **Wajib Ada Jasa Perbaikan Lainnya di Atas Rp 100.000:** Karena harga promo Deep Clean Rp 60.000 memiliki keuntungan yang **masih ada tetapi tergolong tipis**, bengkel tidak boleh melayani cuci Rp 60k tanpa servis reparasi. Syarat ini menjamin setiap penukaran voucher menghasilkan omset jasa reparasi minimal Rp 100.000 yang bermargin tebal bagi bengkel.
* **Masa Berlaku Voucher 1 Minggu (7 Hari):** Menciptakan urgensi tinggi bagi pelanggan dan temannya untuk segera mengirimkan sepatu berikutnya ke bengkel.
* **Akumulasi Belanja Pribadi:** Nilai transaksi teman baru murni menjadi akumulasi belanja teman tersebut. Pengajak terdorong naik ke Gold melalui transaksi berikutnya saat menukarkan voucher reward-nya.

---

## 6. Analisis Keuntungan Bisnis (Dual-Perspective Value Matrix)

Suatu program retensi baru bisa berkelanjutan jika memberikan **keuntungan riil yang seimbang (Win-Win)** bagi perusahaan dan pelanggan:

| Aspek Program | Keuntungan Konkret bagi Shoe Workshop | Keuntungan Nyata bagi Pelanggan |
| :--- | :--- | :--- |
| **Tier Reguler (0% Diskon)** | • Margin transaksi awal terlindungi 100%<br>• Data kontak WhatsApp masuk ke CRM database tanpa biaya iklan | • Nomor terdaftar resmi untuk perlindungan garansi 30 hari<br>• Menabung saldo akumulasi belanja menuju status Gold |
| **Tier Gold (Diskon 15%)** | • Mengunci pelanggan agar tidak pindah ke kompetitor<br>• Omset Rp 1,5 Juta per customer sudah aman di tangan bengkel | • Penghematan nyata 15% untuk lem jahit selama 1 tahun penuh<br>• Akses eksklusif ke Program Stempel Loyalitas |
| **Loyalty Stempel (Gold)** | • Mendorong frekuensi kedatangan (repeat order) minimal 10 kali transaksi di atas Rp 250k | • Kesempatan mendapatkan reward diskon ekstra 25% lem jahit (masa aktif panjang 6 bulan) |
| **Tier Platinum (Diskon 30% + DC 60k)** | • Mengunci 47 pelanggan paling loyal (penyumbang 18,6% omset bengkel) seumur hidup | • Diskon tertinggi maksimal (30%), cuci sepatu murah Rp 60k, dan perlakuan khusus pelanggan VIP |
| **Referral Deep Clean Rp 60k** | • Akuisisi pelanggan baru gratis biaya iklan Meta Ads<br>• Garansi omset perbaikan baru di atas Rp 100.000 per penukaran | • Teman baru menikmati cuci spesial Rp 60k<br>• Pengajak menikmati cuci spesial Rp 60k untuk sepatu berikutnya |

### 6.1 Catatan Penting Proteksi Margin Deep Clean Rp 60.000
Berdasarkan evaluasi operasional, jasa Deep Clean di harga Rp 60.000 masih memberikan cuan bagi bengkel, meskipun marginnya tergolong **tipis** karena adanya alokasi upah tenaga kerja cuci, sabun/cleaner khusus, serta kemasan. Oleh karena itu, skema program mengunci voucher ini **hanya boleh digunakan jika dibarengi jasa reparasi lain > Rp 100.000**. Margin tebal dari reparasi sol/lem/jahit secara otomatis menutupi ketipisan margin cuci tersebut.

---

## 7. Model Proyeksi Finansial & Rumus Matematis Pertumbuhan

Model proyeksi ini dibangun secara empiris berdasarkan data operasional bulanan Shoe Workshop periode 1 Juni s/d 30 September 2026.

### 7.1 Baseline Data Operasional Bulanan Aktual
* **Omset Rata-rata Bulanan:** Rp 1.828.292.822 / 4 = **Rp 457.073.205 / bulan**.
* **Jumlah Transaksi Bulanan:** 3.170 invoice / 4 = **792,5 invoice / bulan**.
* **Pelanggan Aktif Bulanan:** 2.955 orang / 4 = **738,7 pelanggan / bulan**.
* **Repeat Rate Aktual:** 182 / 2.955 = **6,16%** (rata-rata 45,5 transaksi repeat per bulan).
* **Invoice di Bawah Rp 250.000:** 1.318 invoice / 4 = **329,5 invoice / bulan** (rata-rata Rp 199.294 per invoice).
* **Rata-rata Belanja Repeat Order:** **Rp 450.000 per transaksi**.

---

### 7.2 Rumus-Rumus Matematis Pengungkit Omset

<div class="formula-card">
    <div class="formula-header"><span class="formula-badge">Rumus 1</span> Peningkatan Omset dari Retensi Pelanggan (Repeat Order Lift)</div>
    <div class="formula-math">Δ Omset Repeat = Δ Pelanggan Repeat × AOV Repeat</div>
    <div class="formula-desc">
        Di mana: <strong>Δ Pelanggan Repeat = Pelanggan Aktif Bulanan × (Target Repeat Rate Baru - Repeat Rate Lama)</strong><br>
        <em>Parameter: Pelanggan Aktif Bulanan = 738,7 orang | Repeat Rate Lama = 6,16% | AOV Repeat = Rp 450.000</em>
    </div>
</div>

<div class="formula-card">
    <div class="formula-header"><span class="formula-badge">Rumus 2</span> Kenaikan Nilai Keranjang dari Up-Selling Ambang Batas Rp 250.000</div>
    <div class="formula-math">Δ Omset Up-Selling = (Invoice &lt; 250k × % Konversi Up-Sell) × (Rp 250.000 - Rata-rata Invoice &lt; 250k)</div>
    <div class="formula-desc">
        Di mana: <strong>Selisih Kenaikan Nilai = Rp 250.000 - Rp 199.294 = +Rp 50.706 per invoice yang berhasil di-upsell</strong><br>
        <em>Parameter: Total Invoice &lt; 250k bulanan = 329,5 transaksi</em>
    </div>
</div>

<div class="formula-card">
    <div class="formula-header"><span class="formula-badge">Rumus 3</span> Pendapatan Tambahan dari Akuisisi Organik (Referral Lift)</div>
    <div class="formula-math">Δ Omset Referral = Jumlah Referral Sukses × (AOV Teman Baru + AOV Penukaran Pengajak)</div>
    <div class="formula-desc">
        Di mana: <strong>Jumlah Referral Sukses = Pelanggan Aktif Bulanan × % Partisipasi Referral</strong><br>
        <em>Parameter: AOV Teman Order Pertama = Rp 350.000 | AOV Penukaran Pengajak = Rp 160.000 (Servis Min 100k + Cuci 60k)</em>
    </div>
</div>

<div class="formula-card">
    <div class="formula-header"><span class="formula-badge">Rumus 4</span> Estimasi Biaya Diskon & Reward Diberikan (Cost of Program)</div>
    <div class="formula-math">Biaya Diskon = (Omset Lem Jahit Gold × 15%) + (Omset Lem Jahit Plat × 30%)</div>
    <div class="formula-desc">
        Di mana porsi pengerjaan Lem Jahit rata-rata adalah <strong>60% dari total omset reparasi member elite</strong>.
    </div>
</div>

---

### 7.3 Simulasi 3 Skenario Proyeksi Finansial Bulanan

| Parameter & Metrik Kunci | Baseline Saat Ini | Skenario 1: Konservatif *(Bulan 1–2)* | Skenario 2: Moderat *(Bulan 3–4)* | Skenario 3: Agresif *(Bulan 5–6)* |
| :--- | :---: | :---: | :---: | :---: |
| **Repeat Order Rate (Tingkat Retensi)** | **6,16%** (45 orang) | **9,0%** (+21 orang) | **12,0%** (+43 orang) | **16,0%** (+73 orang) |
| **Keberhasilan Up-Sell < 250k** | 0% | 15% (49 invoice) | 25% (82 invoice) | 35% (115 invoice) |
| **Partisipasi Referral Bulanan**| 0% | 2% (15 pasang teman) | 4% (30 pasang teman) | 6% (44 pasang teman) |
| **+ Omset dari Repeat Order** | Rp 0 | +Rp 9.441.225 | +Rp 19.414.350 | +Rp 32.711.850 |
| **+ Omset dari Up-Selling 250k** | Rp 0 | +Rp 2.509.898 | +Rp 4.183.162 | +Rp 5.856.427 |
| **+ Omset dari Referral Baru** | Rp 0 | +Rp 7.535.250 | +Rp 15.070.500 | +Rp 22.605.750 |
| **TOTAL GROSS TAMBAHAN OMSET**| **Rp 0** | **+Rp 19.486.373** | **+Rp 38.668.012** | **+Rp 61.174.027** |
| **Estimasi Diskon Riil Diberikan**| Rp 0 | -Rp 12.246.750* | -Rp 18.370.125* | -Rp 24.493.500 |
| **PROYEKSI TOTAL OMSET BULANAN**| **Rp 457.073.205** | **Rp 476.559.578** | **Rp 495.741.217** | **Rp 518.247.232** |
| **Pertumbuhan Omset (%)** | **Baseline** | **+4,3%** | **+8,5%** | **+13,4%** |
| **NET TAMBAHAN UANG KAS MASUK** | **Baseline** | **+Rp 7.239.623 / bln** | **+Rp 20.297.887 / bln**| **+Rp 36.680.527 / bln**|

*\*Pada fase awal (Konservatif & Moderat), belum semua 237 member Gold/Platinum langsung repeat di bulan yang sama, sehingga beban diskon berjalan bertahap seiring repeat order.*

### 7.4 Efisiensi Biaya Iklan (*Meta Ads Substitution Value*)
* Di Skenario Moderat, bengkel mendapatkan **30 pelanggan baru per bulan via referral**.
* Dengan asumsi biaya iklan Meta Ads saat ini adalah Rp 40.000 per akuisisi pelanggan baru, program referral menghasilkan penghematan biaya pemasaran sebesar:  
  **30 pelanggan × Rp 40.000 = Rp 1.200.000 / bulan (Rp 14,4 Juta / tahun)**.
* Konversi transaksi dari referral mencapai **80% - 90%** (jauh lebih tinggi dari konversi chat iklan dingin yang hanya 25% - 35%).

---

### 7.5 Kuesioner Audit HPP untuk Manajemen, Workshop Leader & Keuangan
Agar proyeksi keuntungan kas bersih dapat dihitung dengan presisi mutlak, berikut daftar pertanyaan yang disiapkan untuk dikonfirmasi bersama tim operasional dan finance:

<div class="spec-box">
    <div class="spec-header">DAFTAR PERTANYAAN AUDIT HPP WORKSHOP</div>
    <div class="spec-body">
        <div class="spec-group">
            <div class="spec-group-title">1. HPP JASA DEEP CLEAN (HARGA PROMO RP 60.000)</div>
            • Berapa biaya bahan kimia pembersih (cleaner, conditioner, sabun sol, parfum) yang terpakai per pasang sepatu?<br>
            • Berapa alokasi upah tenaga kerja cuci per pasang sepatu?<br>
            • Berapa biaya kemasan (plastik/ziplock) dan kelengkapan per pasang?<br>
            • Berapa persisnya estimasi sisa margin kotor saat dijual di Rp 60.000 (apakah tersisa Rp 10.000, Rp 15.000, atau Rp 20.000 per pasang)?
        </div>
        <div class="spec-group">
            <div class="spec-group-title">2. HPP JASA LEM JAHIT (REGLUE & STITCHING)</div>
            • Berapa biaya bahan langsung (lem kuning, lem graft/primer, benang sol, ampelas) per pasang sepatu?<br>
            • Berapa upah teknisi per pasang untuk pengerjaan lem jahit standar?<br>
            • Dengan diskon 15% (Gold) dan 30% (Platinum), berapa sisa laba kotor jasa lem jahit untuk menutupi biaya operasional umum bengkel?
        </div>
        <div class="spec-group">
            <div class="spec-group-title">3. BATASAN KAPASITAS WORKSHOP</div>
            • Berapa batas maksimal pengerjaan deep clean di workshop per hari?
        </div>
    </div>
</div>

---

## 8. Manajemen Risiko & Mitigasi Kasus Lapangan (Edge Cases)

Berdasarkan audit riwayat chat di Sleekflow WhatsApp, berikut adalah potensi friksi di lapangan dan SOP mitigasinya:

### 8.1 Klaster Kasus Membership & Stempel Loyalty

<div class="case-card">
    <div class="case-header"><span class="badge-red">Case 1</span> Kekecewaan Member Reguler ("Member Kok Gak Dapet Diskon?")</div>
    <strong>Risiko:</strong> Pelanggan merasa menjadi member tidak ada gunanya jika tanpa diskon langsung.<br>
    <strong>SOP Mitigasi:</strong> Ubah istilah penyampaian CS dari sekadar <em>"Member Reguler"</em> menjadi <strong>"Akun Terdaftar & Tergaransi Resmi"</strong>.<br>
    <div class="script-box">
        <strong>Skrip CS:</strong> "Nomor Kakak sudah resmi terdaftar di sistem Shoe Workshop untuk klaim garansi 30 hari, pencatatan otomatis akumulasi belanja menuju Gold Member, dan pembagian kupon Referral Deep Clean Rp 60k!"
    </div>
</div>

<div class="case-card">
    <div class="case-header"><span class="badge-red">Case 2</span> Komplain Belanja Rp 800k Bawa 3 Sepatu Hanya Dapat 1 Stempel (Aturan Non-Kelipatan)</div>
    <strong>Risiko:</strong> Pelanggan merasa dirugikan karena membawa banyak sepatu tetapi stempelnya sama dengan yang bawa 1 sepatu.<br>
    <strong>SOP Mitigasi:</strong> CS mengedukasi bahwa stempel adalah reward frekuensi kunjungan, sedangkan nominal besar mempercepat naik ke Platinum VIP.<br>
    <div class="script-box">
        <strong>Skrip CS:</strong> "Betul Kak, stempel dihitung per kedatangan transaksi, namun nominal belanja Kakak yang Rp 800k ini langsung dicatat ke saldo akumulasi Kakak menuju <strong>Platinum VIP (target 3,5jt)</strong>. Begitu tembus Platinum, Kakak otomatis menikmati diskon permanen 30% untuk seluruh sepatu tanpa perlu kumpul stempel lagi!"
    </div>
</div>

<div class="case-card">
    <div class="case-header"><span class="badge-amber">Case 3</span> Transaksi Nanggung di Bawah Rp 250k (Misal Rp 220k)</div>
    <strong>Peluang Up-Selling:</strong> CS tidak boleh kaku menolak, melainkan menjadikannya penawaran layanan tambahan.<br>
    <div class="script-box">
        <strong>Skrip CS:</strong> "Biar dapat 1 stempel loyalitas Kak, cukup tambah treatment Pembersihan Tali & Insole hanya +Rp 35.000, total tagihan pas Rp 255.000 dan langsung mendapatkan 1 stempel!"
    </div>
</div>

### 8.2 Klaster Kasus Referral & Batas Waktu 1 Minggu

<div class="case-card">
    <div class="case-header"><span class="badge-red">Case 4</span> Voucher Referral Hangus Karena Melewati 7 Hari (The 7-Day Dilemma)</div>
    <strong>Risiko:</strong> Data menunjukkan repeat order sepatu rata-rata baru terjadi di hari ke-15 hingga 30. Pelanggan mungkin baru sempat memfoto sepatu keduanya di hari ke-10 dan mendapati voucher hangus.<br>
    <strong>SOP Mitigasi (Grace Period Policy):</strong> Berlakukan aturan <strong>Toleransi Chat Konsultasi</strong>: batas 7 hari adalah batas pelanggan <strong>memulai chat konsultasi dan mengirim foto sepatu</strong> ke CS. Jika chat sudah dimulai sebelum hari ke-7, masa voucher dikunci dan tetap berlaku meskipun fisik sepatu baru dikirim di hari ke-10.
</div>

<div class="case-card">
    <div class="case-header"><span class="badge-amber">Case 5</span> Pelanggan Minta Cuci Sepatu Doang Tanpa Servis Lain > 100k</div>
    <strong>Risiko:</strong> Pelanggan merasa terjebak syarat dan ketentuan tersembunyi.<br>
    <strong>SOP Mitigasi:</strong> Kupon digital WhatsApp wajib menampilkan teks tebal: <strong>"VOUCHER SPESIAL DEEP CLEAN Rp 60.000 (Disertai Treatment Reparasi Sol/Lem/Jahit)"</strong>. CS menjelaskan dengan ramah bahwa deep clean spesial ini adalah program pendamping reparasi sepatu karena margin cuci promo yang tipis.
</div>

<div class="case-card">
    <div class="case-header"><span class="badge-blue">Case 6</span> Teman Baru Minta Diskon Langsung di Transaksi Pertama</div>
    <strong>SOP Mitigasi:</strong> CS menjelaskan alur: <em>"Untuk order pertama ini nomor Kakak didaftarkan dan mendapatkan garansi resmi, dan voucher Deep Clean Rp 60k langsung aktif di sistem untuk sepatu Kakak berikutnya setelah order ini selesai."</em>
</div>

### 8.3 Klaster Teknis & Integrasi Sistem Sleekflow WhatsApp

<div class="case-card">
    <div class="case-header"><span class="badge-blue">Case 7</span> Pelanggan Berganti Nomor WhatsApp (Multi-Device Mismatch)</div>
    <strong>SOP Mitigasi:</strong> CS dibekali fitur pencarian <code>Customer_ID_Database</code> di Sleekflow untuk menautkan (*merge*) kontak ganda ke satu profil database.
</div>

<div class="case-card">
    <div class="case-header"><span class="badge-blue">Case 8</span> Pelanggan Berulang Kali Tanya Saldo Stempel</div>
    <strong>SOP Mitigasi:</strong> <strong>Otomatisasi Sleekflow API Webhook</strong>: Setiap kali status invoice di database kasir berubah menjadi <code>LUNAS</code>, bot Sleekflow otomatis mengirimkan pesan konfirmasi saldo stempel ke WhatsApp pelanggan tanpa perlu diketik manual oleh CS.
</div>

<div class="case-card">
    <div class="case-header"><span class="badge-amber">Case 9</span> Batasan Diskon Lem Jahit vs Penggantian Sol Baru (Material)</div>
    <strong>SOP Mitigasi:</strong> Invoice kasir wajib memisahkan antara baris <strong>Jasa Pengeleman/Penjahitan</strong> dan baris <strong>Biaya Bahan/Sparepart Sol Baru</strong>. Diskon 15%/30% dipotongkan khusus pada baris jasa lem jahit.
</div>

---

## 9. Integrasi Sistem & SOP Operasional CS

Program ini dirancang **100% otomatis berbasis Nomor WhatsApp Pelanggan** di Sleekflow dan database internal tanpa perlu kartu fisik:

<div class="flow-container">
    <div class="flow-box active">
        <div class="flow-title">1. INBOUND CHAT</div>
        <div class="flow-subtitle">Identifikasi Nomor Otomatis</div>
        <div class="flow-content">
            Sistem Sleekflow mendeteksi nomor WA dan menampilkan profil tier member di sidebar CS.
        </div>
    </div>
    <div class="flow-box highlight">
        <div class="flow-title">2. KONSULTASI & ORDER</div>
        <div class="flow-subtitle">Penerapan Hak Diskon</div>
        <div class="flow-content">
            CS menerapkan hak diskon Lem Jahit (15% Gold / 30% Plat) & penawaran Deep Clean Rp 60k.
        </div>
    </div>
    <div class="flow-box vip">
        <div class="flow-title">3. AUTO-UPDATE STEMPEL</div>
        <div class="flow-subtitle">Notifikasi Instan via Webhook</div>
        <div class="flow-content">
            Saat invoice lunas, webhook otomatis mengirim notifikasi penambahan saldo stempel ke chat WA pelanggan.
        </div>
    </div>
</div>

### 9.1 Script Komunikasi Customer Service (CS) Standar

<div class="case-card">
    <div class="case-header">1. Sapaan untuk Pelanggan Reguler (Mendorong Referral & Gold)</div>
    <div class="script-box">
        "Halo Kak [Nama Customer]! Terima kasih sudah reparasi di Shoe Workshop. Nomor Kakak sudah terdaftar resmi untuk perlindungan garansi 30 hari. Kumpulkan total transaksi hingga Rp 1,5 Juta untuk membuka status Gold Member dan menikmati Diskon 15% Lem Jahit serta Program Stempel Berhadiah! Kakak juga bisa ajak teman reparasi di sini untuk langsung dapat Voucher Deep Clean Rp 60.000!"
    </div>
</div>

<div class="case-card">
    <div class="case-header">2. Sapaan untuk Gold Member</div>
    <div class="script-box">
        "Halo Kak [Nama Customer]! Senang melayani Kakak kembali sebagai Gold Member kami. Untuk reparasi kali ini, Kakak mendapatkan potongan Diskon 15% khusus Jasa Lem Jahit. Karena transaksi Kakak mencapai [Nominal], sistem otomatis menambahkan +1 Stempel Loyalitas (Total Stempel: [X]/10 menuju Voucher Diskon 25% Lem Jahit)!"
    </div>
</div>

<div class="case-card">
    <div class="case-header">3. Sapaan untuk Platinum VIP</div>
    <div class="script-box">
        "Halo Kak [Nama Customer]! Terima kasih atas kesetiaan Kakak sebagai Platinum VIP Shoe Workshop. Kakak berhak atas Diskon Maksimal 30% khusus Jasa Lem Jahit serta layanan Deep Clean spesial seharga Rp 60.000 untuk sepatu kesayangan Kakak hari ini."
    </div>
</div>

<div class="case-card">
    <div class="case-header">4. Pemberitahuan Klaim Referral Sukses</div>
    <div class="script-box">
        "Selamat Kak! Teman yang Kakak rekomendasikan telah menyelesaikan reparasi pertamanya. Sebagai apresiasi, Kakak dan teman Kakak masing-masing mendapatkan Voucher Deep Clean Rp 60.000 untuk 1 pasang sepatu pada transaksi berikutnya (disertai jasa reparasi lain min. Rp 100.000). Voucher ini aktif selama 1 minggu ke depan ya Kak!"
    </div>
</div>

---

## 10. Action Plan & Roadmap Pengembangan Teknis Platform

Untuk mewujudkan ekosistem Membership, Loyalty Stempel, dan Referral yang terintegrasi penuh secara digital, Action Plan dibagi ke dalam 5 pilar kerja teknis dan operasional:

### 10.1 Penyelarasan Platform Web (shoeworkshop.id & info.shoeworkshop)

1. **Pemindahan Fitur Tracking Customer ke Web shoeworkshop.id:**
   * Seluruh pengecekan status reparasi pelanggan dipusatkan ke portal resmi: `shoeworkshop.id/tracking`.
   * Pelanggan cukup memasukkan **Nomor WhatsApp** atau **Nomor Invoice/SPK** untuk melihat live progress pengerjaan: `Diterima di Gudang` ➔ `Antrean Workshop` ➔ `Pengerjaan Lem/Jahit` ➔ `Quality Control` ➔ `Pengiriman Ekspedisi (Resi Terhubung)`.
   * Memangkas beban CS yang selama ini kerap dihujani chat manual *"Min, sepatu saya sudah sampai mana?"*.
2. **Penyembunyian Halaman Donasi di shoeworkshop.id:**
   * Menonaktifkan/menyembunyikan tautan dan halaman Donasi (`/donasi`) dari header, navbar, dan footer website `shoeworkshop.id`.
   * Fokus visual dan alur navigasi diarahkan 100% pada konversi jasa servis utama, tracking reparasi, dan portal membership.
3. **Penerapan Tagging Membership di Internal Web info.shoeworkshop:**
   * Di sistem manajemen internal `info.shoeworkshop`, tim developer menambahkan atribut data pada modul pelanggan:
     * `Tag Membership`: `REGULER`, `GOLD`, `PLATINUM`.
     * `Saldo Stempel`: Penghitung otomatis 0 s/d 10 stempel.
     * `Referral Tracking`: Field kode referral pengajak dan status penukaran voucher.
   * Tag ini otomatis tampil di dashboard operasional gudang intake, sistem CS, dan kasir kas masuk.

---

### 10.2 Spesifikasi Login, Onboarding & Member Portal di shoeworkshop.id

Portal pelanggan di `shoeworkshop.id` dirancang dengan alur *frictionless* (tanpa registrasi rumit dari nol):

1. **Pre-Generated Member Account (Login Instan):**
   * Setiap pelanggan yang nomornya sudah tercatat di database Shoe Workshop otomatis dibuatkan akun membership.
   * **Username Login:** Nomor WhatsApp aktif pelanggan (misal: `08123456789`).
   * **Password Default:** Password standar yang mudah diingat, yaitu kombinasi prefix bengkel dan 4 digit terakhir nomor HP (contoh: `SW` + `6789` = `SW6789`) atau `Shoe1234`.
2. **Onboarding & Kelengkapan Data Diri:**
   * Saat pelanggan pertama kali berhasil login, sistem memunculkan modal pop-up wajib (*onboarding wizard*):
     * *Langkah 1:* Ubah password default menjadi password pribadi baru.
     * *Langkah 2:* Melengkapi data diri: Nama Lengkap, Tanggal Lahir (untuk aktivasi voucher diskon ulang tahun otomatis), Email, Alamat Lengkap Pengiriman, serta Ukuran Sepatu Favorit.
3. **Fitur Dashboard Member & Tracking Loyalty:**
   * **Kartu Membership Digital:** Menampilkan lencana status tier (Reguler, Gold, atau Platinum) beserta tanggal masa aktifnya.
   * **Progress Bar Belanja Tahunan:** Menampilkan akumulasi belanja riil menuju tier berikutnya (contoh: *"Belanja Anda Rp 1.850.000 / Rp 3.500.000 menuju Platinum VIP"*).
   * **Kartu Stempel Digital (Khusus Gold/Platinum):** Visual 10 kotak stempel interaktif yang terisi otomatis setiap transaksi minimal Rp 250.000.
   * **Dompet Voucher Member:** Tempat menyimpan kupon aktif (Diskon Lem Jahit 15%/30%, Voucher Deep Clean Rp 60.000, Kupon Referral) lengkap dengan tombol *"Klaim via WhatsApp CS"*.
   * **Riwayat Garansi & Servis:** Daftar riwayat pengerjaan sepatu terdahulu beserta sisa masa garansi aktif (30–60 hari).

---

### 10.3 Mesin Automation Reminder & Campaign WhatsApp (H+7, H+30, H+90)

Untuk mengamankan proyeksi repeat order menuju 12% - 16%, sistem otomatisasi Sleekflow/API dijadwalkan mengirimkan pesan WhatsApp terpersonalisasi pada 3 momen emas:

<div class="wa-card">
    <div class="wa-header">
        <span>TRIGGER H+7 (7 Hari Pasca Sepatu Diterima)</span>
        <span>Quality Check & Aktivasi Referral 1 Minggu</span>
    </div>
    <div class="wa-body">
        <div class="wa-bubble">
            "Halo Kak [Nama Customer]! Salam hangat dari tim Shoe Workshop.<br><br>
            Sepatu [Tipe Sepatu/Merk] yang kemarin kami reparasi sudah dipakai beraktivitas belum nih? Semoga hasilnya nyaman dan kuat ya Kak! Jika ada hal yang kurang pas, jangan ragu kabari kami karena Kakak memiliki perlindungan garansi resmi.<br><br>
            Kabar gembira! Nomor Kakak terpilih mendapatkan <strong>2 Voucher Spesial Deep Clean Rp 60.000</strong>: satu untuk Kakak dan satu lagi bisa dibagikan ke teman/keluarga terdekat.<br><br>
            Voucher ini aktif selama 7 hari ke depan ya Kak! Cukup balas chat ini dengan ketik <strong>KLAIM VOUCHER</strong> untuk mengamankan promonya sebelum kedaluwarsa. Terima kasih banyak Kak!"
        </div>
    </div>
</div>

<div class="wa-card">
    <div class="wa-header">
        <span>TRIGGER H+30 (1 Bulan Pasca Reparasi)</span>
        <span>Golden Window Repeat Order (Kupon & Sepatu Kedua)</span>
    </div>
    <div class="wa-body">
        <div class="wa-bubble">
            "Halo Kak [Nama Customer]! Sudah 1 bulan nih sejak sepatu kesayangan Kakak selesai direparasi di Shoe Workshop.<br><br>
            Gimana kabarnya Kak, sepatunya masih tetap awet dan nyaman kan?<br><br>
            Oh iya Kak, di rumah masih ada koleksi sepatu kerja, sneakers, atau boots yang solnya sudah mulai mangap atau kotor?<br><br>
            Saat ini total belanja Kakak sudah tercatat <strong>[Rp X.XXX.XXX]</strong> di sistem kami. Cukup tambah servis sedikit lagi untuk membuka status <strong>Gold Member</strong> dan menikmati Diskon 15% Lem Jahit permanen serta pengumpulan stempel berhadiah!<br><br>
            Bisa langsung kirim foto sepatu Kakak yang lain ke chat ini ya, tim CS kami siap bantu estimasikan biaya servis terbaiknya. Ditunggu ya Kak!"
        </div>
    </div>
</div>

<div class="wa-card">
    <div class="wa-header">
        <span>TRIGGER H+90 (3 Bulan Pasca Reparasi)</span>
        <span>Re-engagement & Preventive Maintenance</span>
    </div>
    <div class="wa-body">
        <div class="wa-bubble">
            "Halo Kak [Nama Customer]! Gak terasa sudah 3 bulan berlalu sejak kunjungan terakhir Kakak di Shoe Workshop.<br><br>
            Sepatu yang dipakai rutin setiap hari biasanya mulai butuh perawatan nih Kak, mulai dari pembersihan kotoran membandel hingga pengecekan kekuatan lem sol agar tidak jebol tiba-tiba saat dipakai dinas/bepergian.<br><br>
            Bulan ini kami sedang menyiapkan slot perawatan khusus bagi pelanggan setia kami. Kakak bisa langsung cek status akun dan kupon Kakak di <strong>shoeworkshop.id/member</strong> atau langsung balas chat ini untuk konsultasi kerusakan sepatu gratis.<br><br>
            Yuk rawat sepatu kesayangan Kakak sebelum rusaknya makin parah. Kami tunggu ya Kak!"
        </div>
    </div>
</div>

---

### 10.4 Agenda Diskusi Teknis & Developer Handover Checklist (Tim IT shoeworkshop.id)

Berikut adalah daftar kebutuhan fitur teknis (*backlog requirements*) yang siap diserahkan untuk dibahas dalam rapat koordinasi bersama tim developer:

<div class="spec-box">
    <div class="spec-header">DEVELOPER HANDOVER & TECHNICAL SPEC CHECKLIST</div>
    <div class="spec-body">
        <div class="spec-group">
            <div class="spec-group-title">1. MODUL AUTENTIKASI & DATABASE:</div>
            • Skema tabel relasi: <code>customers</code>, <code>memberships</code>, <code>stamps</code>, <code>vouchers</code>.<br>
            • Import & mapping 2.955 data customer eksisting dari MySQL internal.<br>
            • Mekanisme auto-create akun (Username: No HP, Default Pass: SW + 4 digit terakhir).<br>
            • Logic Onboarding Flow: Paksa update data diri & ganti password saat pertama kali login.
        </div>
        <div class="spec-group">
            <div class="spec-group-title">2. MODUL DASHBOARD MEMBER (shoeworkshop.id/member):</div>
            • Komponen visual Badge Tier: Reguler / Gold / Platinum.<br>
            • Komponen Progress Bar akumulasi belanja menuju tier berikutnya.<br>
            • Komponen Kartu Stempel Digital (10 slot stempel visual).<br>
            • Dompet Voucher digital dengan tombol "Tukarkan / Chat CS".
        </div>
        <div class="spec-group">
            <div class="spec-group-title">3. MODUL TRACKING SPK (shoeworkshop.id/tracking):</div>
            • Halaman publik pencarian order berbasis No WA / No SPK.<br>
            • Tampilan timeline 5 status pengerjaan workshop secara real-time.
        </div>
        <div class="spec-group">
            <div class="spec-group-title">4. INTEGRASI SLEEKFLOW API & WEBHOOK:</div>
            • Webhook event <code>invoice_paid</code>: Menambah stempel & update spend di web.<br>
            • Cron job trigger harian: Pemicu blast reminder otomatis H+7, H+30, H+90.
        </div>
        <div class="spec-group">
            <div class="spec-group-title">5. PEMBERSIHAN WEBSITE:</div>
            • Unpublish / hide rute dan tombol donasi di seluruh elemen web.
        </div>
    </div>
</div>

---

### 10.5 Matriks Roadmap & Timeline Eksekusi Platform

| Fase Eksekusi | Sasaran Kerja Utama | Output yang Dihasilkan | Estimasi Durasi |
| :--- | :--- | :--- | :---: |
| **Fase 1: Web & Database Setup** | • Sembunyikan halaman donasi di shoeworkshop.id<br>• Setup modul live tracking SPK di web<br>• Terapkan tagging membership di info.shoeworkshop<br>• Pengisian kuesioner audit HPP riil | Website bersih, modul tracking aktif, database siap tagging | **Minggu 1** *(3–5 Hari)* |
| **Fase 2: Member Portal & Auth** | • Generate akun massal no HP + password default<br>• Onboarding ganti password & profil data diri<br>• Dashboard member & kartu stempel digital | Portal member aktif di shoeworkshop.id/member | **Minggu 2** *(4–6 Hari)* |
| **Fase 3: Integrasi & Automation** | • Integrasi Sleekflow API Webhook saat invoice lunas<br>• Setup cron job reminder otomatis H+7, H+30, H+90<br>• Pelatihan tim CS untuk SOP skrip kasus lapangan | Sistem CRM & chat berjalan 100% otomatis | **Minggu 3** *(3–5 Hari)* |
| **Fase 4: Launching & Monitoring** | • Broadcast sapaan apresiasi ke 237 Member Elite awal<br>• Peluncuran publik program Referral kupon 1 minggu<br>• Evaluasi mingguan kenaikan repeat rate & kas masuk | Repeat order menyala tanpa biaya iklan berbayar | **Minggu 4** *(Berkelanjutan)* |

---

## 11. Rekomendasi Keputusan untuk Manajemen

Berdasarkan seluruh hasil kajian data empiris, proyeksi finansial, dan kesepakatan rapat tim, kami merekomendasikan manajemen untuk segera menetapkan:

1. **Pemberlakuan Struktur 3 Tier Membership:**
   * **Reguler (< Rp 1,5 Jt):** Tanpa benefit diskon awal.
   * **Gold (≥ Rp 1,5 Jt):** Diskon 15% Jasa Lem Jahit + Akses Program Stempel.
   * **Platinum (≥ Rp 3,5 Jt):** Diskon 30% Jasa Lem Jahit (Cap maksimal) + Voucher Deep Clean Rp 60.000.
2. **Pemberlakuan Program Stempel di Tier Gold:**
   * 1 Stempel per invoice transaksi ≥ Rp 250.000 (tanpa kelipatan).
   * 10 Stempel = Voucher Diskon 25% Jasa Lem Jahit (berlaku 6 bulan).
3. **Pemberlakuan Program Referral dengan Kebijakan Grace Period:**
   * Voucher Deep Clean Rp 60.000 untuk pengajak dan teman baru (berlaku 1 minggu untuk batas mulai konsultasi, wajib disertai servis lain > Rp 100.000 untuk memproteksi margin cuci yang tipis).
4. **Persetujuan Pengembangan Platform Web shoeworkshop.id & info.shoeworkshop:**
   * Memindahkan tracking customer ke `shoeworkshop.id/tracking`, menyembunyikan halaman donasi, dan menerapkan tagging membership di `info.shoeworkshop`.
   * Membuat portal login member berbasis Nomor HP + password default dan fitur dashboard tracking loyalty stempel digital.
5. **Aktivasi Mesin Otomatisasi Reminder WhatsApp (H+7, H+30, H+90):**
   * Menerapkan skrip *copywriting* resmi untuk mengamankan *golden window repeat order* dan pengaktifan kupon referral.
6. **Eksekusi Pengisian Kuesioner Audit HPP Riil:**
   * Melibatkan tim Workshop Leader dan Finance untuk mengisi parameter biaya HPP bahan cuci, tenaga kerja, dan lem jahit guna mengunci angka laba bersih secara mutlak.
7. **Aktivasi Segera Database 237 Member Terpilih:**
   * Segera sapa 190 Gold Member dan 47 Platinum VIP via WhatsApp untuk menyalakan repeat order seketika tanpa biaya iklan.

---

*Dokumen proposal komprehensif ini siap diajukan untuk penetapan operasional resmi Shoe Workshop.*
