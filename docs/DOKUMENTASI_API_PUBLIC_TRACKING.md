# 📘 Dokumentasi Integrasi API Public Tracking (Web Eksternal)

**Versi API:** `v1`  
**Terakhir Diperbarui:** 10 November 2026  
**Target Penggunaan:** Integrasi halaman pelacakan SPK pada website eksternal (misal: `shoeworkshop.id`), widget status order, maupun bot layanan pelanggan.

---

## 📌 Daftar Isi
1. [Spesifikasi Endpoint Utama (`/api/v1/public/track`)](#1-spesifikasi-endpoint-utama-apiv1publictrack)
2. [Aturan Bisnis & Logika Status (Normal, OTO, Garansi, Revisi, Hold CX)](#2-aturan-bisnis--logika-status-normal-oto-garansi-revisi-hold-cx)
3. [Kamus Data (Data Dictionary) Response JSON](#3-kamus-data-data-dictionary-response-json)
4. [Contoh Response JSON Berdasarkan Skenario](#4-contoh-response-json-berdasarkan-skenario)
   - [4.1 Skenario 1: Pengerjaan Normal / Selesai (`SELESAI`)](#41-skenario-1-pengerjaan-normal--selesai-selesai)
   - [4.2 Skenario 2: Sedang Proses OTO (`OTO`)](#42-skenario-2-sedang-proses-oto-oto)
   - [4.3 Skenario 3: Selesai + Ada Penawaran OTO (`has_oto_offer = true`)](#43-skenario-3-selesai--ada-penawaran-oto-has_oto_offer--true)
   - [4.4 Skenario 4: Sedang Proses Klaim Garansi (`GARANSI`)](#44-skenario-4-sedang-proses-klaim-garansi-garansi)
   - [4.5 Skenario 5: Sedang Proses Revisi Kualitas (`REVISI`)](#45-skenario-5-sedang-proses-revisi-kualitas-revisi)
   - [4.6 Skenario 6: Tertunda / Butuh Konfirmasi CX (`is_on_hold = true`)](#46-skenario-6-tertunda--butuh-konfirmasi-cx-is_on_hold--true)
   - [4.7 Response Error (`404 Not Found` & `422 Unprocessable Entity`)](#47-response-error-404-not-found--422-unprocessable-entity)
5. [Endpoint Ringan Alternatif (`/api/v1/public/track-shoes`)](#5-endpoint-ringan-alternatif-apiv1publictrack-shoes)
6. [Panduan Implementasi UI & Contoh Kode (JavaScript / Fetch)](#6-panduan-implementasi-ui--contoh-kode-javascript--fetch)

---

## 1. Spesifikasi Endpoint Utama (`/api/v1/public/track`)

| Atribut | Nilai |
| :--- | :--- |
| **URL Endpoint** | `{BASE_URL}/api/v1/public/track` |
| **HTTP Method** | `GET` |
| **Autentikasi** | Publik (Tidak memerlukan API Key / Bearer Token) |
| **Rate Limit** | `60 requests per minute` per IP Address |
| **Header Disarankan** | `Accept: application/json` |

### Query Parameter

| Parameter | Tipe | Wajib | Contoh | Keterangan |
| :--- | :--- | :---: | :--- | :--- |
| `spk_number` | `string` | **Ya** | `S-2605-04-0001-SW` | Mendukung **Nomor SPK Reguler (`-SW` / `-CS`)** maupun **Nomor SPK Garansi (`-GR`)**. Jika input berupa URL hasil scan QR Code (misal `https://.../S-2605-04-0001-SW`), API otomatis mengekstrak segmen nomor SPK terakhir. |

**Contoh Pemanggilan Request:**
```http
GET https://workshop.shoeworkshop.id/api/v1/public/track?spk_number=S-2605-04-0001-SW HTTP/1.1
Accept: application/json
```

---

## 2. Aturan Bisnis & Logika Status (Normal, OTO, Garansi, Revisi, Hold CX)

Pada tampilan pelacakan, terdapat **7 tahapan (stepper)** standar di dalam objek `data.timeline`:
1. `DITERIMA` — **Terima**
2. `ASSESSMENT` — **Pengecekan**
3. `PREPARATION` — **Cuci**
4. `SORTIR` — **Persiapan Bahan**
5. `PRODUCTION` — **Service**
6. `QC` — **QC Checking**
7. `SELESAI` — **Selesai Reparasi**

### Tabel Pemetaan Kondisi Khusus (PENTING untuk Frontend)

Ketika sepatu sedang dalam proses **OTO**, **Garansi**, atau **Revisi**, sistem backend **secara otomatis menarik posisi stepper (`is_current: true`) kembali ke Tahap 5 (`PRODUCTION` / Service)** dan menonaktifkan status selesai (`timeline.SELESAI.is_completed = false`), sehingga pelanggan tidak salah mengira sepatu sudah selesai.

| Kondisi SPK | `current_status.code` | `current_status.label` | Step Aktif di `timeline` | Flag Boolean Aktif | Rekomendasi Banner di UI Web |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **1. Alur Standar** | `DITERIMA` s/d `SELESAI` | `Terima` s/d `Selesai Reparasi` | Sesuai tahapan aktif | Semua `false` | Tidak perlu banner khusus |
| **2. Sedang OTO (Disetujui Customer)** | `OTO` | `Service (OTO)` | Step 5: `PRODUCTION` (`is_current: true`) | `is_oto: true` | **Banner Oranye:** Tampilkan `oto_title` & `oto_message` |
| **3. Selesai + Ada Penawaran OTO** | `SELESAI` | `Selesai Reparasi` | Step 7: `SELESAI` (`is_current: true`) | `has_oto_offer: true` | **Banner Teal/Hijau:** Tampilkan `oto_offer_message` |
| **4. Sedang Klaim Garansi (`OPEN`)** | `GARANSI` | `Service (Garansi)` | Step 5: `PRODUCTION` (`is_current: true`) | `is_warranty: true` | **Banner Indigo/Biru:** Tampilkan `warranty_title`, `warranty_spk_number`, & `warranty_message` |
| **5. Sedang Revisi Kualitas** | `REVISI` | `Service (Revisi)` | Step 5: `PRODUCTION` (`is_current: true`) | `is_revising: true` | **Banner Kuning/Amber:** Tampilkan `revision_title` & `revision_message` |
| **6. Ditangguhkan / Kendala CX** | `CX_FOLLOWUP` / `HOLD_FOR_CX` | Sesuai tahap terakhir | Tahap sebelum hold (`is_current: true`) | `is_on_hold: true` | **Banner Merah/Rose:** Tampilkan `hold_title`, `hold_message`, & tombol link ke `report_issue_url` |

> **Catatan Pencarian SPK Garansi (`-GR`):**
> Selama klaim garansi masih berstatus `OPEN` (sedang dikerjakan), pelanggan dapat melacak menggunakan **Nomor SPK Asli (`S-2605-04-0001-SW`)** maupun **Nomor SPK Garansi (`S-2605-04-0001-GR`)**. Keduanya akan mengembalikan data SPK yang sama dengan status `current_status.code = "GARANSI"` dan `is_warranty = true`.

---

## 3. Kamus Data (Data Dictionary) Response JSON

| Field Path | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `success` | `boolean` | `true` jika SPK ditemukan, `false` jika gagal/tidak ditemukan. |
| `message` | `string` | Pesan ringkasan hasil request. |
| `data.spk_number` | `string` | Nomor SPK utama. |
| `data.priority` | `string\|null` | Prioritas pengerjaan (`Reguler`, `Prioritas`, `Urgent`, `Express`). |
| `data.customer_name` | `string` | Nama pelanggan pemilik sepatu. |
| `data.shoe` | `object` | Informasi fisik sepatu (`brand`, `type`, `color`, `size`). |
| `data.current_status.code` | `string` | Kode status efektif saat ini (`DITERIMA`, `ASSESSMENT`, `PREPARATION`, `SORTIR`, `PRODUCTION`, `QC`, `SELESAI`, **`OTO`**, **`GARANSI`**, **`REVISI`**, `CX_FOLLOWUP`, `HOLD_FOR_CX`). |
| `data.current_status.label` | `string` | Label status yang siap ditampilkan langsung ke pelanggan (contoh: `"Service (OTO)"`, `"Service (Garansi)"`, `"Service (Revisi)"`, `"Selesai Reparasi"`). |
| `data.current_status.description` | `string` | Kalimat penjelasan status aktif saat ini beserta langkah berikutnya. |
| `data.current_status.is_production_finished` | `boolean` | Bernilai `false` selama proses OTO / Garansi / Revisi masih berjalan. |
| `data.current_status.is_qc_finished` | `boolean` | Bernilai `false` selama proses OTO / Garansi / Revisi masih berjalan. |
| `data.is_on_hold` | `boolean` | `true` jika pengerjaan ditangguhkan karena menunggu konfirmasi CX dari pelanggan. |
| `data.hold_title` | `string\|null` | Judul alert kendala CX (terisi jika `is_on_hold = true`). |
| `data.hold_message` | `string\|null` | Pesan penjelasan kendala CX (terisi jika `is_on_hold = true`). |
| `data.report_issue_url` | `string\|null` | URL halaman laporan foto kendala CX untuk dibuka pelanggan (terisi jika `is_on_hold = true`). |
| `data.is_oto` | `boolean` | `true` jika sepatu sedang dalam pengerjaan layanan tambahan **OTO** (`ACCEPTED` / `IN_PROGRESS`). |
| `data.oto_title` | `string\|null` | Judul banner OTO aktif (terisi jika `is_oto = true`). |
| `data.oto_message` | `string\|null` | Deskripsi pengerjaan OTO beserta daftar layanan tambahan (terisi jika `is_oto = true`). |
| `data.has_oto_offer` | `boolean` | `true` jika layanan utama sudah selesai dan ada penawaran OTO yang menunggu konfirmasi pelanggan (`PENDING_CX` / `CONTACTED` / `PENDING_CUSTOMER`). |
| `data.oto_offer_message` | `string\|null` | Pesan rekomendasi penawaran OTO (terisi jika `has_oto_offer = true`). |
| `data.is_warranty` | `boolean` | `true` jika sepatu sedang dalam proses pengerjaan **Klaim Garansi** (`OPEN`). |
| `data.warranty_spk_number` | `string\|null` | Nomor SPK Garansi (`S-...-GR`) jika ada klaim garansi aktif. |
| `data.warranty_title` | `string\|null` | Judul banner klaim garansi (terisi jika `is_warranty = true`). |
| `data.warranty_message` | `string\|null` | Deskripsi pengerjaan garansi beserta keluhan yang ditangani (terisi jika `is_warranty = true`). |
| `data.is_revising` | `boolean` | `true` jika sepatu sedang dalam proses **Revisi Kualitas**. |
| `data.revision_title` | `string\|null` | Judul banner revisi (terisi jika `is_revising = true`). |
| `data.revision_message` | `string\|null` | Deskripsi proses revisi (terisi jika `is_revising = true`). |
| `data.visual_photos` | `object` | URL foto kondisi sepatu (`before_photo_url`, `after_photo_url`, `hero_photo_url`). |
| `data.services` | `array` | Daftar layanan pada SPK (`service_name`, `category`, `cost`). |
| `data.timeline` | `object` | Objek berisi 7 tahapan (`DITERIMA` s/d `SELESAI`). Masing-masing memiliki `label`, `is_completed` (`bool`), `is_current` (`bool`), dan `waktu` (`YYYY-MM-DD HH:mm:ss` atau `null`). |

---

## 4. Contoh Response JSON Berdasarkan Skenario

### 4.1 Skenario 1: Pengerjaan Normal / Selesai (`SELESAI`)

```json
{
  "success": true,
  "message": "Data pelacakan ditemukan.",
  "data": {
    "spk_number": "S-2605-04-0001-SW",
    "priority": "Reguler",
    "customer_name": "Budi Santoso",
    "shoe": {
      "brand": "Nike Air Force 1",
      "type": "Sneakers",
      "color": "White",
      "size": "42"
    },
    "current_status": {
      "code": "SELESAI",
      "label": "Selesai Reparasi",
      "description": "Order Anda sedang dalam proses selesai reparasi. Terima kasih telah mempercayakan sepatu Anda kepada kami!",
      "is_production_finished": true,
      "is_qc_finished": true
    },
    "is_on_hold": false,
    "hold_title": null,
    "hold_message": null,
    "report_issue_url": null,
    "is_oto": false,
    "oto_title": null,
    "oto_message": null,
    "has_oto_offer": false,
    "oto_offer_message": null,
    "is_warranty": false,
    "warranty_spk_number": null,
    "warranty_title": null,
    "warranty_message": null,
    "is_revising": false,
    "revision_title": null,
    "revision_message": null,
    "visual_photos": {
      "before_photo_url": "https://workshop.shoeworkshop.id/storage/photos/before_1.jpg",
      "after_photo_url": "https://workshop.shoeworkshop.id/storage/photos/after_1.jpg",
      "hero_photo_url": "https://workshop.shoeworkshop.id/storage/photos/after_1.jpg"
    },
    "services": [
      {
        "service_name": "Deep Clean",
        "category": "Treatment",
        "cost": 85000
      }
    ],
    "timeline": {
      "DITERIMA": {
        "label": "Terima",
        "is_completed": true,
        "is_current": false,
        "waktu": "2026-11-01 10:15:00"
      },
      "ASSESSMENT": {
        "label": "Pengecekan",
        "is_completed": true,
        "is_current": false,
        "waktu": "2026-11-01 14:20:00"
      },
      "PREPARATION": {
        "label": "Cuci",
        "is_completed": true,
        "is_current": false,
        "waktu": "2026-11-02 09:30:00"
      },
      "SORTIR": {
        "label": "Persiapan Bahan",
        "is_completed": true,
        "is_current": false,
        "waktu": "2026-11-02 15:10:00"
      },
      "PRODUCTION": {
        "label": "Service",
        "is_completed": true,
        "is_current": false,
        "waktu": "2026-11-04 11:00:00"
      },
      "QC": {
        "label": "QC Checking",
        "is_completed": true,
        "is_current": false,
        "waktu": "2026-11-05 13:45:00"
      },
      "SELESAI": {
        "label": "Selesai Reparasi",
        "is_completed": true,
        "is_current": true,
        "waktu": "2026-11-05 16:00:00"
      }
    }
  }
}
```

---

### 4.2 Skenario 2: Sedang Proses OTO (`OTO`)

Terjadi ketika pelanggan menyetujui penawaran layanan tambahan OTO (`ACCEPTED` / `IN_PROGRESS`). Posisi stepper otomatis kembali ke `PRODUCTION` (`is_current: true`) dan `SELESAI` menjadi `is_completed: false`.

```json
{
  "success": true,
  "message": "Data pelacakan ditemukan.",
  "data": {
    "spk_number": "S-2605-04-0001-SW",
    "priority": "Prioritas",
    "customer_name": "Budi Santoso",
    "current_status": {
      "code": "OTO",
      "label": "Service (OTO)",
      "description": "Order Anda sedang dalam proses pengerjaan layanan tambahan One Time Offer (OTO).",
      "is_production_finished": false,
      "is_qc_finished": false
    },
    "is_on_hold": false,
    "is_oto": true,
    "oto_title": "Pengerjaan Layanan Tambahan (OTO)",
    "oto_message": "Sepatu Anda saat ini sedang berada di Stasiun Khusus OTO untuk pengerjaan layanan tambahan (One Time Offer) yang telah Anda setujui. Layanan OTO: Repaint Midsole, Unyellowing.",
    "has_oto_offer": false,
    "oto_offer_message": null,
    "is_warranty": false,
    "is_revising": false,
    "timeline": {
      "DITERIMA": { "label": "Terima", "is_completed": true, "is_current": false, "waktu": "2026-11-01 10:15:00" },
      "ASSESSMENT": { "label": "Pengecekan", "is_completed": true, "is_current": false, "waktu": "2026-11-01 14:20:00" },
      "PREPARATION": { "label": "Cuci", "is_completed": true, "is_current": false, "waktu": "2026-11-02 09:30:00" },
      "SORTIR": { "label": "Persiapan Bahan", "is_completed": true, "is_current": false, "waktu": "2026-11-02 15:10:00" },
      "PRODUCTION": { "label": "Service", "is_completed": true, "is_current": true, "waktu": null },
      "QC": { "label": "QC Checking", "is_completed": false, "is_current": false, "waktu": null },
      "SELESAI": { "label": "Selesai Reparasi", "is_completed": false, "is_current": false, "waktu": null }
    }
  }
}
```

---

### 4.3 Skenario 3: Selesai + Ada Penawaran OTO (`has_oto_offer = true`)

Terjadi ketika layanan utama telah selesai (`SELESAI`), dan tim workshop memberikan rekomendasi layanan tambahan OTO yang masih menunggu konfirmasi pelanggan (`PENDING_CX` / `CONTACTED` / `PENDING_CUSTOMER`).

```json
{
  "current_status": {
    "code": "SELESAI",
    "label": "Selesai Reparasi",
    "description": "Order Anda sedang dalam proses selesai reparasi. Terima kasih telah mempercayakan sepatu Anda kepada kami!",
    "is_production_finished": true,
    "is_qc_finished": true
  },
  "is_oto": false,
  "oto_title": null,
  "oto_message": null,
  "has_oto_offer": true,
  "oto_offer_message": "Layanan utama sepatu Anda telah selesai. Tim kami merekomendasikan penawaran khusus layanan tambahan: Repaint Midsole, Unyellowing. Silakan hubungi admin CX kami untuk konfirmasi."
}
```

---

### 4.4 Skenario 4: Sedang Proses Klaim Garansi (`GARANSI`)

Terjadi ketika klaim garansi berstatus `OPEN` (baik dilacak dengan SPK asli `S-2605-04-0001-SW` maupun SPK garansi `S-2605-04-0001-GR`).

```json
{
  "current_status": {
    "code": "GARANSI",
    "label": "Service (Garansi)",
    "description": "Order Anda sedang dalam proses pengerjaan klaim garansi.",
    "is_production_finished": false,
    "is_qc_finished": false
  },
  "is_warranty": true,
  "warranty_spk_number": "S-2605-04-0001-GR",
  "warranty_title": "Pengerjaan Klaim Garansi",
  "warranty_message": "Sepatu Anda saat ini sedang dalam proses penanganan klaim garansi oleh tim teknisi kami untuk memastikan hasil perbaikan kembali optimal. Catatan Garansi: \"Lem bagian depan sedikit terbuka\"",
  "timeline": {
    "DITERIMA": { "label": "Terima", "is_completed": true, "is_current": false, "waktu": "2026-11-01 10:15:00" },
    "ASSESSMENT": { "label": "Pengecekan", "is_completed": true, "is_current": false, "waktu": "2026-11-01 14:20:00" },
    "PREPARATION": { "label": "Cuci", "is_completed": true, "is_current": false, "waktu": "2026-11-02 09:30:00" },
    "SORTIR": { "label": "Persiapan Bahan", "is_completed": true, "is_current": false, "waktu": "2026-11-02 15:10:00" },
    "PRODUCTION": { "label": "Service", "is_completed": true, "is_current": true, "waktu": null },
    "QC": { "label": "QC Checking", "is_completed": false, "is_current": false, "waktu": null },
    "SELESAI": { "label": "Selesai Reparasi", "is_completed": false, "is_current": false, "waktu": null }
  }
}
```

---

### 4.5 Skenario 5: Sedang Proses Revisi Kualitas (`REVISI`)

```json
{
  "current_status": {
    "code": "REVISI",
    "label": "Service (Revisi)",
    "description": "Order Anda sedang dalam penyesuaian pengerjaan ulang (revisi).",
    "is_production_finished": false,
    "is_qc_finished": false
  },
  "is_revising": true,
  "revision_title": "Pengerjaan Ulang & Penyesuaian Kualitas (Revisi)",
  "revision_message": "Sepatu Anda saat ini sedang dalam proses penyesuaian pengerjaan ulang oleh tim spesialis kami untuk memastikan hasil akhir memenuhi standar mutu terbaik."
}
```

---

### 4.6 Skenario 6: Tertunda / Butuh Konfirmasi CX (`is_on_hold = true`)

```json
{
  "current_status": {
    "code": "CX_FOLLOWUP",
    "label": "Service",
    "description": "Order Anda sedang dalam proses service. Langkah berikutnya: QC Checking.",
    "is_production_finished": false,
    "is_qc_finished": false
  },
  "is_on_hold": true,
  "hold_title": "Konfirmasi Kendala Diperlukan",
  "hold_message": "Tim workshop kami menemukan kendala teknis atau kondisi khusus pada sepatu Anda. Silakan lihat Laporan Kendala (CX Report) dan hubungi admin kami untuk konfirmasi tindakan.",
  "report_issue_url": "https://workshop.shoeworkshop.id/r/S-2605-04-0001-SW"
}
```

---

### 4.7 Response Error (`404 Not Found` & `422 Unprocessable Entity`)

**SPK Tidak Ditemukan (`404 Not Found`):**
```json
{
  "success": false,
  "message": "Data tidak ditemukan untuk SPK: 'S-9999-XX'. Silakan periksa kembali."
}
```

**Parameter `spk_number` Kosong (`422 Unprocessable Entity`):**
```json
{
  "message": "The spk number field is required.",
  "errors": {
    "spk_number": [
      "The spk number field is required."
    ]
  }
}
```

---

## 5. Endpoint Ringan Alternatif (`/api/v1/public/track-shoes`)

Jika website eksternal hanya membutuhkan status singkat dan daftar layanan (tanpa objek `timeline` 7 tahap atau foto), gunakan endpoint ringan berikut:

- **Endpoint:** `GET {BASE_URL}/api/v1/public/track-shoes?spk_number=S-2605-04-0001-SW` (atau `?id=165`)
- **Rate Limit:** 60 requests / minute per IP
- **Contoh Response:**
```json
{
  "success": true,
  "message": "Data pelacakan sepatu ditemukan.",
  "data": {
    "id": 165,
    "spk_number": "S-2605-04-0001-SW",
    "shoe_brand": "Nike Air Force 1",
    "shoe_type": "Sneakers",
    "shoe_size": "42",
    "shoe_color": "White",
    "status": "OTO",
    "status_label": "Service (OTO)",
    "is_on_hold": false,
    "hold_title": null,
    "hold_message": null,
    "report_issue_url": null,
    "is_oto": true,
    "oto_title": "Pengerjaan Layanan Tambahan (OTO)",
    "oto_message": "Sepatu Anda saat ini sedang berada di Stasiun Khusus OTO untuk pengerjaan layanan tambahan (One Time Offer) yang telah Anda setujui. Layanan OTO: Repaint Midsole.",
    "has_oto_offer": false,
    "oto_offer_message": null,
    "is_warranty": false,
    "warranty_spk_number": null,
    "warranty_title": null,
    "warranty_message": null,
    "is_revising": false,
    "revision_title": null,
    "revision_message": null,
    "services": [
      {
        "service_name": "Deep Clean",
        "category_name": "Treatment",
        "cost": 85000,
        "status": "COMPLETED"
      }
    ]
  }
}
```

---

## 6. Panduan Implementasi UI & Contoh Kode (JavaScript / Fetch)

Berikut contoh fungsi JavaScript (`async/await`) siap pakai untuk mengambil data pelacakan dan menentukan banner aktif di frontend:

```javascript
const BASE_URL = 'https://workshop.shoeworkshop.id';

async function fetchTrackingSpk(spkNumber) {
  const url = new URL(`${BASE_URL}/api/v1/public/track`);
  url.searchParams.set('spk_number', spkNumber.trim());

  const response = await fetch(url.toString(), {
    method: 'GET',
    headers: {
      Accept: 'application/json',
    },
  });

  const result = await response.json();

  if (!response.ok || !result.success) {
    throw new Error(result.message || 'SPK tidak ditemukan.');
  }

  return result.data;
}

/**
 * Helper untuk menentukan banner status khusus di atas stepper
 */
function resolveActiveBanner(trackingData) {
  if (trackingData.is_on_hold) {
    return {
      type: 'HOLD_CX',
      theme: 'danger', // Merah / Rose
      title: trackingData.hold_title,
      message: trackingData.hold_message,
      actionUrl: trackingData.report_issue_url,
      actionLabel: 'Lihat Laporan Kendala',
    };
  }

  if (trackingData.is_warranty) {
    return {
      type: 'WARRANTY',
      theme: 'indigo', // Biru / Indigo
      badge: trackingData.warranty_spk_number,
      title: trackingData.warranty_title,
      message: trackingData.warranty_message,
    };
  }

  if (trackingData.is_oto) {
    return {
      type: 'OTO_ACTIVE',
      theme: 'orange', // Oranye
      badge: 'Fast Track OTO',
      title: trackingData.oto_title,
      message: trackingData.oto_message,
    };
  }

  if (trackingData.is_revising) {
    return {
      type: 'REVISION',
      theme: 'warning', // Kuning / Amber
      title: trackingData.revision_title,
      message: trackingData.revision_message,
    };
  }

  if (trackingData.has_oto_offer) {
    return {
      type: 'OTO_OFFER',
      theme: 'teal', // Hijau / Teal
      title: 'Penawaran Layanan Tambahan (One Time Offer) Tersedia',
      message: trackingData.oto_offer_message,
    };
  }

  return null;
}
```

