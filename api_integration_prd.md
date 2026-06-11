# PRD & API Specification: Customer Portal Integration

This document outlines the Product Requirements and API Integration Specification for connecting the **Shoestudio Workshop System** with a sibling customer portal web application.

---

## 1. Product Requirements (PRD)

### Objectives
- Allow customers to log in using their phone number on the sibling website.
- Display a comprehensive timeline/history of customer shoe repairs (both completed and ongoing).
- Expose detailed shoe status, repair services applied, and uploaded photos (before/after/QC stages) directly to the customer.

### Security Model
- **Authentication**: Backend-to-backend communication. The sibling website verifies the customer's phone number (via OTP/SMS/Password) on its side, and then fetches the data securely from the main system backend using a secret client API key.
- **Abuse Prevention**: Throttling/Rate Limiting is enforced on the endpoint to prevent brute-force attacks on phone numbers.

---

## 2. API Specification

### Endpoint
`GET /api/v1/customer-portal/orders`

### Request Headers
| Header | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `X-API-KEY` | String | Yes | The secret API Key matching the `dashboard_api_key` configuration. |

> [!NOTE]
> The API Key can also be passed via the query parameter `api_key` or `key` if needed (e.g., `/api/v1/customer-portal/orders?phone=...&api_key=SECRET`), though using the header `X-API-KEY` is highly recommended for security.

### Query Parameters
| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `phone` | String | Yes | The customer's phone number. Can be in formats like `0812...`, `62812...`, or `+62 812-...`. The main system automatically normalizes this input. |

---

## 3. JSON Response Schema

```json
{
    "status": "success",
    "data": {
        "customer": {
            "id": 1,
            "name": "Sidik",
            "phone": "6281214696299",
            "email": "customer@example.com",
            "address": "Jl. Merdeka No. 123",
            "city": "Bandung",
            "province": "Jawa Barat",
            "district": null,
            "village": null,
            "postal_code": null
        },
        "work_orders": [
            {
                "id": 1,
                "spk_number": "S-2606-02-0001-SW",
                "shoe_brand": "Reebok",
                "shoe_type": "F",
                "shoe_color": "Hitam",
                "shoe_size": "40",
                "category": "Sepatu",
                "status": {
                    "code": "SELESAI",
                    "label": "Selesai"
                },
                "priority": "NORMAL",
                "notes": "10 HK - Bergaransi",
                "entry_date": "2026-06-02 14:08:00",
                "estimation_date": "2026-06-19 00:00:00",
                "finished_date": "2026-06-02 15:17:38",
                "taken_date": "2026-06-03 09:20:05",
                "payment": {
                    "status": "Belum Bayar",
                    "total_amount": 250000,
                    "paid_amount": 0,
                    "remaining_balance": 250000
                },
                "services": [
                    {
                        "id": 1,
                        "service_id": 3,
                        "service_name": "Ganti Outsole Reguler",
                        "category_name": "Reparasi Sol",
                        "cost": 250000,
                        "notes": null
                    }
                ],
                "photos": [
                    {
                        "id": 12,
                        "step": "RECEPTION",
                        "photo_url": "https://shoestudio.id/storage/photos/spk-1-reception.jpg",
                        "caption": "Penerimaan awal",
                        "is_spk_cover": true,
                        "is_public": true
                    }
                ]
            }
        ]
    },
    "message": "Customer portal orders retrieved successfully."
}
```

---

## 4. Sibling Web Integration Snippet (PHP)

Below is an integration template code that you can copy to the sibling web app:

```php
<?php

class WorkshopIntegrationService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('WORKSHOP_API_URL', 'https://your-workshop-domain.com/api/v1');
        $this->apiKey = env('WORKSHOP_API_KEY');
    }

    /**
     * Fetch orders list and repair status for a specific phone number.
     */
    public function getCustomerHistory(string $phone)
    {
        $url = $this->baseUrl . '/customer-portal/orders?' . http_build_query([
            'phone' => $phone
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-API-KEY: ' . $this->apiKey,
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            // Handle error or invalid token
            return null;
        }

        $decoded = json_decode($response, true);
        return $decoded['data'] ?? null;
    }
}
```
