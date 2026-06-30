# Dokumentasi API - Sistem Workshop & Treatment

Dokumen ini berisi spesifikasi lengkap API (Application Programming Interface) yang digunakan dalam aplikasi Sistem Workshop. Seluruh endpoint menggunakan prefix `/api/v1/`.

---

## 🔐 Keamanan & Otentikasi

### 1. Endpoint Publik
Beberapa endpoint bersifat publik (misal: Tracking SPK dan Pengajuan Garansi oleh Customer) sehingga dapat diakses tanpa API Key, namun dilindungi oleh batasan laju permintaan (*Rate Limiting / Throttling*) dan kebijakan CORS.

### 2. Endpoint Terproteksi (API Key)
Endpoint internal/dashboard wajib menyertakan kunci otentikasi. Anda dapat menyediakannya melalui dua metode:
- **Header HTTP**: `X-API-KEY: <your_api_key>`
- **Query Parameter**: `?api_key=<your_api_key>` atau `?key=<your_api_key>`

*API Key yang valid didefinisikan pada file konfigurasi `.env` (`API_KEY`).*

---

## 1. Modul Publik (Public Module)

### GET `/api/v1/public/track`
Melacak status pengerjaan sepatu berdasarkan nomor SPK.
* **Otentikasi**: Tidak (Publik)
* **Rate Limit**: 60 request / menit
* **Parameter Query**:
  | Parameter | Tipe | Wajib | Keterangan |
  | :--- | :--- | :--- | :--- |
  | `spk_number` | String | Ya | Nomor SPK lengkap (contoh: `SPK-123456`) |
* **Response (Success - 200)**:
  ```json
  {
    "success": true,
    "data": {
      "id": 1,
      "spk_number": "SPK-123456",
      "customer_name": "John Doe",
      "shoe_brand": "Nike",
      "shoe_type": "Air Max",
      "shoe_color": "Hitam",
      "status": "PRODUCTION",
      "current_stage": {
        "label": "Produksi",
        "description": "Sepatu sedang dijahit/lem oleh teknisi"
      },
      "history": [
        {
          "step": "DITERIMA",
          "action": "ORDER_CREATED",
          "description": "SPK berhasil dibuat",
          "created_at": "2026-06-30T10:00:00Z"
        }
      ]
    }
  }
  ```

### GET `/api/v1/public/track-shoes`
Melacak daftar unit sepatu khusus dalam satu SPK jika terdapat multi-sepatu.
* **Otentikasi**: Tidak (Publik)
* **Rate Limit**: 60 request / menit
* **Parameter Query**:
  | Parameter | Tipe | Wajib | Keterangan |
  | :--- | :--- | :--- | :--- |
  | `spk_number` | String | Ya | Nomor SPK lengkap |

### POST `/api/v1/public/warranty-claims/check`
Memeriksa apakah nomor SPK dan nomor WhatsApp customer memenuhi syarat mengajukan klaim garansi.
* **Otentikasi**: Tidak (Publik)
* **Rate Limit**: 30 request / menit
* **Body Request (JSON)**:
  ```json
  {
    "spk_number": "SPK-123456",
    "customer_phone": "0895339939800"
  }
  ```
* **Response (Success - 200)**:
  ```json
  {
    "success": true,
    "message": "Layanan garansi tersedia dan aktif.",
    "data": {
      "work_order_id": 12,
      "customer_name": "John Doe",
      "shoe_brand": "Adidas",
      "shoe_type": "Ultraboost",
      "shoe_color": "Putih",
      "warranty_expires_at": "30 Jul 2026",
      "days_left": 30
    }
  }
  ```

### POST `/api/v1/public/warranty-claims/submit`
Mengajukan klaim garansi baru untuk SPK yang telah selesai.
* **Otentikasi**: Tidak (Publik)
* **Rate Limit**: 10 request / menit
* **Format Request**: `multipart/form-data`
* **Body Request**:
  | Field | Tipe | Wajib | Keterangan |
  | :--- | :--- | :--- | :--- |
  | `spk_number` | String | Ya | Nomor SPK pengerjaan awal |
  | `customer_phone` | String | Ya | Nomor WhatsApp terdaftar |
  | `problem_description` | String | Ya | Detail keluhan kerusakan (Min 10, Max 1000 karakter) |
  | `penggunaan` | String | Ya | Berapa kali sepatu dipakai (Min 5, Max 100 karakter) |
  | `problem_photos[]` | File (Gambar) | Ya | Gambar bukti kerusakan (Min 1, Max 3 file, Max 20MB per file) |
  | `google_review_photo` | File (Gambar) | Ya | Screenshot bukti review Google Maps (Max 20MB) |
* **Response (Success - 200)**:
  ```json
  {
    "success": true,
    "message": "Klaim garansi berhasil diajukan. CX kami akan segera menghubungi Anda."
  }
  ```

---

## 2. Modul Dasbor & Analitik (Dashboard & Summary)

Seluruh endpoint di bawah ini **Wajib** menggunakan API Key terproteksi.

### GET `/api/v1/dashboard-summary`
Mendapatkan statistik ringkasan dashboard utama.
* **Otentikasi**: Ya (API Key)

### GET `/api/v1/cx-summary`
Mendapatkan analitik performa pelayanan customer (*Customer Experience*).
* **Otentikasi**: Ya (API Key)

### GET `/api/v1/finance/dashboard`
Mendapatkan analitik laporan keuangan (pendapatan, piutang, nominal lunas, dll.).
* **Otentikasi**: Ya (API Key)

### GET `/api/v1/warehouse-summary`
Mendapatkan statistik ringkasan stok dan logistik gudang utama.
* **Otentikasi**: Ya (API Key)

### GET `/api/v1/warehouse-manifest-summary`
Mendapatkan laporan manifes barang masuk/keluar gudang.
* **Otentikasi**: Ya (API Key)

### GET `/api/v1/warehouse-sortir-summary`
Mendapatkan statistik penyaringan sepatu di bagian sortir gudang.
* **Otentikasi**: Ya (API Key)

### GET `/api/v1/warehouse-production-summary`
Mendapatkan analitik pengerjaan divisi produksi teknisi.
* **Otentikasi**: Ya (API Key)

### GET `/api/v1/warehouse-qc-summary`
Mendapatkan laporan kontrol kualitas (*Quality Control*) pengerjaan.
* **Otentikasi**: Ya (API Key)

---

## 3. Modul Sinkronisasi Data (Sync Suite)

Endpoint ini digunakan untuk integrasi sinkronisasi data antar-sistem/layanan eksternal.
* **Otentikasi**: Ya (API Key)

| Endpoint | HTTP Method | Keterangan |
| :--- | :--- | :--- |
| `/api/v1/finance-sync` | GET | Sinkronisasi data mutasi dan jurnal keuangan |
| `/api/v1/payment-sync` | GET | Sinkronisasi status verifikasi pembayaran bank |
| `/api/v1/warehouse-inventory-sync` | GET | Sinkronisasi inventaris stok bahan dan alat gudang |
| `/api/v1/warehouse-request-sync` | GET | Sinkronisasi pengajuan barang oleh teknisi |
| `/api/v1/warehouse-transaction-sync`| GET | Sinkronisasi catatan mutasi keluar/masuk bahan |
| `/api/v1/warehouse-sortir-sync` | GET | Sinkronisasi antrean sortir |
| `/api/v1/warehouse-forecast-sync` | GET | Sinkronisasi prediksi kebutuhan bahan baku |
| `/api/v1/warehouse-piutang-sync` | GET | Sinkronisasi data piutang customer aktif |
| `/api/v1/warehouse-piutang-before-sync`| GET| Sinkronisasi piutang sebelum masuk antrean workshop |
| `/api/v1/warehouse-shoerack-sync` | GET | Sinkronisasi lokasi peletakan rak sepatu |
| `/api/v1/service-tracking-sync` | GET | Sinkronisasi riwayat pelacakan servis |
| `/api/v1/workshop-sync` | GET | Sinkronisasi data pengerjaan internal workshop |

---

## 4. Integrasi Portal Customer (Customer Portal)

### GET `/api/v1/customer-portal/orders`
Mengambil seluruh riwayat SPK beserta foto pengerjaan milik customer berdasarkan nomor HP.
* **Otentikasi**: Ya (API Key)
* **Rate Limit**: 60 request / menit
* **Parameter Query**:
  | Parameter | Tipe | Wajib | Keterangan |
  | :--- | :--- | :--- | :--- |
  | `phone` | String | Ya | Nomor telepon customer (contoh: `0895339939800`) |
* **Response (Success - 200)**:
  ```json
  {
    "data": {
      "customer": {
        "name": "John Doe",
        "phone": "0895339939800",
        "email": "johndoe@example.com"
      },
      "work_orders": [
        {
          "spk_number": "SPK-123456",
          "shoe_brand": "Nike",
          "shoe_type": "Air Max",
          "status": "SELESAI",
          "services": [
            {
              "name": "Deep Clean",
              "price": 50000
            }
          ],
          "photos": [
            {
              "photo_url": "https://shoeworkshop.id/storage/photos/photo1.jpg"
            }
          ]
        }
      ]
    }
  }
  ```

---

## 5. Modul Customer Service (CS Module)

* **Otentikasi**: Ya (API Key)

### GET `/api/v1/cs-forecasting`
Mendapatkan analisis tren perkiraan jumlah pemesanan customer untuk alokasi CS.
* **Otentikasi**: Ya (API Key)

### GET `/api/v1/cs-kpi-leaderboard`
Mendapatkan peringkat performa (*leaderboard*) KPI konversi CS berdasarkan penutupan transaksi.
* **Otentikasi**: Ya (API Key)

---

## 6. Modul Layanan Pelanggan (CX Module)

* **Otentikasi**: Ya (API Key)

### GET `/api/v1/cx-after-confirmation`
Mendapatkan laporan SPK yang membutuhkan tindak lanjut setelah konfirmasi perbaikan.
* **Otentikasi**: Ya (API Key)

### GET `/api/v1/cx-overdue`
Mendapatkan daftar SPK yang pengerjaannya melebihi target tanggal estimasi selesai (*overdue*).
* **Otentikasi**: Ya (API Key)
