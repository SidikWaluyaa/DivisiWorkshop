# B2B Integration Guide: Public Shoe Tracking API
This official integration guide details how external partner domains (specifically **`info.shoeworkshop.id`**) can interact with the SistemWorkshop public tracking endpoint to fetch real-time repair progress, item data, and step-by-step timestamps.

---

## 🔑 Endpoint Specifications

* **Request Method:** `GET`
* **Base URL:** `https://your-sistemworkshop-domain.com/api/v1/public/track`
* **Authentication:** None (Public CORS-whitelisted for `info.shoeworkshop.id` & `shoeworkshop.id` domains)
* **Rate Limit Protection:** Maximum `60` requests per minute per IP address.

### Query Parameters

| Parameter | Type | Required | Description | Example |
| :--- | :--- | :--- | :--- | :--- |
| `spk_number` | `string` | **Yes** | The unique SPK repair ticket number. | `S-2605-04-0001-SW` |

---

## 📦 Response Payloads (JSON)

### 1. Success Response (HTTP 200)
Returned when the requested SPK number is found in the database.

```json
{
  "success": true,
  "message": "Data pelacakan ditemukan.",
  "data": {
    "spk_number": "S-2605-04-0001-SW",
    "priority": "NORMAL",
    "customer_name": "Yusril Waluya",
    "shoe": {
      "brand": "Nike",
      "type": "Jordan",
      "color": "Hitam",
      "size": "40"
    },
    "current_status": {
      "code": "ASSESSMENT",
      "label": "Pengecekan",
      "description": "Order Anda sedang dalam proses pengecekan. Langkah berikutnya: Cuci.",
      "is_production_finished": false,
      "is_qc_finished": false
    },
    "visual_photos": {
      "before_photo_url": "https://your-domain.com/storage/photos/before_123.jpg",
      "after_photo_url": "https://your-domain.com/storage/photos/after_456.jpg",
      "hero_photo_url": "https://your-domain.com/storage/photos/hero_789.jpg"
    },
    "services": [
      {
        "service_name": "Ganti Upper Pola Sedang",
        "category": "Reparasi Upper",
        "cost": 225000
      }
    ],
    "timeline": {
      "DITERIMA": {
        "label": "Terima",
        "is_completed": true,
        "is_current": false,
        "waktu": "2026-05-04 09:36:30"
      },
      "ASSESSMENT": {
        "label": "Pengecekan",
        "is_completed": true,
        "is_current": true,
        "waktu": "2026-05-04 10:15:00"
      },
      "PREPARATION": {
        "label": "Cuci",
        "is_completed": false,
        "is_current": false,
        "waktu": null
      },
      "SORTIR": {
        "label": "Persiapan Bahan",
        "is_completed": false,
        "is_current": false,
        "waktu": null
      },
      "PRODUCTION": {
        "label": "Service",
        "is_completed": false,
        "is_current": false,
        "waktu": null
      },
      "QC": {
        "label": "QC Checking",
        "is_completed": false,
        "is_current": false,
        "waktu": null
      },
      "SELESAI": {
        "label": "Selesai Reparasi",
        "is_completed": false,
        "is_current": false,
        "waktu": null
      }
    }
  }
}
```

### 2. Error Response (HTTP 404)
Returned when the SPK number is incorrect or does not exist in the database.

```json
{
  "success": false,
  "message": "Data tidak ditemukan untuk SPK: 'S-9999-99-9999-XX'. Silakan periksa kembali."
}
```

---

## 📖 Fields Dictionary (Kamus Kolom)

* **`spk_number`**: The registered SPK code.
* **`customer_name`**: Full name of the customer (displays exactly as shown on the web).
* **`shoe`**: Details of the shoe including `brand`, `type`, `color`, and `size`.
* **`current_status`**: Describes the active step of the repair process:
  * `code`: The database internal status code.
  * `label`: User-friendly Indonesian status name.
  * `description`: Dynamically constructed instructions indicating what is being done and the next expected step.
* **`visual_photos`**: Direct public CDN/storage URLs for `before` (registration) and `after` (finish) photos if they exist.
* **`services`**: List of all repair services applied to the ticket.
* **`timeline`**: A chronological dictionary of the 7 stages of repair:
  * `label`: Public step name.
  * `is_completed`: Boolean flag showing if the stage has already passed.
  * `is_current`: Boolean flag showing if the stage is currently in progress.
  * `waktu`: Precise date and time timestamp (`YYYY-MM-DD HH:MM:SS`) when the step was completed. Returns `null` if the stage has not been reached yet.

---

## 💻 Integration JavaScript Quickstart (Client-Side)

Copy and paste this snippet directly in the frontend pages of **`info.shoeworkshop.id`**:

```javascript
/**
 * Triggers SPK tracking and handles the JSON response payload.
 * @param {string} spkNumber - The ticket SPK number.
 */
function getTrackingProgress(spkNumber) {
    // IMPORTANT: Replace with your actual live server domain
    const hostDomain = "https://your-sistemworkshop-domain.com";
    const requestUrl = `${hostDomain}/api/v1/public/track?spk_number=${encodeURIComponent(spkNumber)}`;

    fetch(requestUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json().then(data => ({
        status: response.status,
        body: data
    })))
    .then(res => {
        if (res.status === 200 && res.body.success) {
            const trackData = res.body.data;
            console.log("Tracking Found:", trackData);
            
            // Render basic info
            document.getElementById('displayCustomer').innerText = trackData.customer_name;
            document.getElementById('displayShoe').innerText = `${trackData.shoe.brand} - ${trackData.shoe.type}`;
            document.getElementById('displayStatus').innerText = trackData.current_status.label;
            
            // Loop and render the 7-step timeline
            Object.entries(trackData.timeline).forEach(([stageKey, stageDetails]) => {
                console.log(`Stage: ${stageDetails.label} | Done: ${stageDetails.is_completed} | Waktu: ${stageDetails.waktu || '-'}`);
            });
        } else {
            console.warn("SPK not found:", res.body.message);
        }
    })
    .catch(err => {
        console.error("API Call Failed:", err);
    });
}
```
