# Database Migration Map

> **Last Updated**: 2026-01-20  
> **Total Migrations**: 34 files  
> **Status**: Production Ready ✅

---

## 📋 Migration Structure

### Core Tables (Foundation - 2024)
**Purpose**: Foundational database structure  
**Status**: Stable, do not modify

| # | Migration | Table | Description |
|---|-----------|-------|-------------|
| 1 | `2024_01_01_000001_create_users_table.php` | `users` | User authentication & roles |
| 2 | `2024_01_01_000002_create_jobs_and_cache_tables.php` | `jobs`, `cache` | Queue & cache system |
| 3 | `2024_01_01_000003_create_services_table.php` | `services` | Service catalog |
| 4 | `2024_01_01_000004_create_materials_table.php` | `materials` | Material inventory |
| 5 | `2024_01_01_000005_create_work_orders_table.php` | `work_orders` | Main work order table |
| 6 | `2024_01_01_000006_create_work_order_details_table.php` | `work_order_details` | Work order line items |
| 7 | `2024_01_01_000007_create_work_order_logs_table.php` | `work_order_logs` | Activity logs |
| 8 | `2024_01_01_000008_create_purchases_table.php` | `purchases` | Purchase orders |
| 9 | `2024_01_01_000009_create_work_order_photos_table.php` | `work_order_photos` | Photo documentation |

---

## 🔧 Extensions (2026)

### 1️⃣ Work Orders Module Extensions
**Purpose**: Enhance work order functionality for different workflows

| Migration | Adds | Module | Description |
|-----------|------|--------|-------------|
| `2026_01_16_093831_add_reception_fields_to_work_orders_table.php` | Reception fields | Reception | Entry date, priority, shoe info |
| `2026_01_16_164709_add_warehouse_reception_fields_to_work_orders.php` | Warehouse fields | Reception | Warehouse-specific tracking |
| `2026_01_16_144629_add_previous_status_to_work_orders_table.php` | Previous status | All | Status history tracking |
| `2026_01_17_100000_add_category_to_work_orders_table.php` | Category field | All | Order categorization |
| `2026_01_17_110326_add_cs_code_to_work_orders_table.php` | CS code | CX | Customer service tracking |
| `2026_01_19_085236_add_technician_notes_to_work_orders_table.php` | Technician notes | Production | Internal notes |
| `2026_01_19_150908_add_oto_columns_to_work_orders_table.php` | OTO fields | OTO | One-time offer integration |

**Dependencies**: All depend on `2024_01_01_000005_create_work_orders_table.php`

---

### 2️⃣ Finance & Payment System
**Purpose**: Track payments, invoices, and financial transactions

| Migration | Table/Adds | Description |
|-----------|------------|-------------|
| `2026_01_16_103606_add_finance_columns_to_work_orders_table.php` | Adds to `work_orders` | Total amount, payment status, invoice |
| `2026_01_16_103644_create_order_payments_table.php` | `order_payments` | Payment records |
| `2026_01_16_140526_add_proof_image_to_order_payments_table.php` | Adds to `order_payments` | Payment proof upload |
| `2026_01_17_110159_add_payment_tracking_columns_to_work_orders_table.php` | Adds to `work_orders` | DP tracking, payment dates |
| `2026_01_19_090636_add_discount_and_custom_service_columns.php` | Adds to `work_orders` | Discount, custom pricing |

**Dependencies**: 
- `2024_01_01_000005_create_work_orders_table.php`
- `2026_01_16_103644_create_order_payments_table.php` (for proof image)

---

### 3️⃣ Customer Management
**Purpose**: Customer data and lead management

| Migration | Table | Module | Description |
|-----------|-------|--------|-------------|
| `2026_01_16_173157_create_customers_table.php` | `customers` | CRM | Customer master data |
| `2026_01_16_173234_create_customer_photos_table.php` | `customer_photos` | CRM | Customer photo gallery |
| `2026_01_16_151503_create_cs_leads_table.php` | `cs_leads` | CS | Sales leads tracking |
| `2026_01_17_094700_add_address_details_to_cs_leads_table.php` | Adds to `cs_leads` | CS | Detailed address fields |

**Dependencies**: 
- `2026_01_16_173157_create_customers_table.php` (for photos)
- `2026_01_16_151503_create_cs_leads_table.php` (for address)

---

### 4️⃣ OTO (One-Time Offer) System
**Purpose**: Upsell and offer management system

| Migration | Table | Description |
|-----------|-------|-------------|
| `2026_01_19_150901_create_otos_table.php` | `otos` | OTO offers with pricing |
| `2026_01_19_150904_create_material_reservations_table.php` | `material_reservations` | Material stock reservation |
| `2026_01_19_152254_add_cx_fields_to_otos_table.php` | Adds to `otos` | CX tracking fields |
| `2026_01_19_152257_create_oto_contact_logs_table.php` | `oto_contact_logs` | Contact history |
| `2026_01_19_150908_add_oto_columns_to_work_orders_table.php` | Adds to `work_orders` | OTO reference |
| `2026_01_19_150912_add_reserved_stock_to_materials_table.php` | Adds to `materials` | Reserved stock tracking |

**Dependencies**: 
- `2024_01_01_000004_create_materials_table.php`
- `2024_01_01_000005_create_work_orders_table.php`
- `2026_01_19_150901_create_otos_table.php` (for CX fields and contact logs)

---

### 5️⃣ User & Access Management
**Purpose**: User permissions and access control

| Migration | Adds | Description |
|-----------|------|-------------|
| `2026_01_15_130721_add_access_rights_to_users_table.php` | Access rights JSON | Module-based permissions |
| `2026_01_16_151513_add_cs_code_to_users_table.php` | CS code | Customer service ID |

**Dependencies**: `2024_01_01_000001_create_users_table.php`

---

### 6️⃣ Complaints & CX Issues
**Purpose**: Customer feedback and issue tracking

| Migration | Table | Module | Description |
|-----------|-------|--------|-------------|
| `2026_01_14_083044_create_complaints_table.php` | `complaints` | CX | Customer complaints |
| `2026_01_16_144614_create_cx_issues_table.php` | `cx_issues` | CX | Internal CX issues |

**Dependencies**: `2024_01_01_000005_create_work_orders_table.php`

---

## 🔗 Migration Dependencies Graph

```
users (2024_01_01_000001)
├── add_access_rights (2026_01_15_130721)
└── add_cs_code (2026_01_16_151513)

materials (2024_01_01_000004)
└── add_reserved_stock (2026_01_19_150912)

work_orders (2024_01_01_000005)
├── work_order_details (2024_01_01_000006)
├── work_order_logs (2024_01_01_000007)
├── work_order_photos (2024_01_01_000009)
├── complaints (2026_01_14_083044)
├── add_reception_fields (2026_01_16_093831)
├── add_finance_columns (2026_01_16_103606)
├── add_previous_status (2026_01_16_144629)
├── add_warehouse_fields (2026_01_16_164709)
├── add_category (2026_01_17_100000)
├── add_payment_tracking (2026_01_17_110159)
├── add_cs_code (2026_01_17_110326)
├── add_technician_notes (2026_01_19_085236)
├── add_discount_columns (2026_01_19_090636)
└── add_oto_columns (2026_01_19_150908)

order_payments (2026_01_16_103644)
└── add_proof_image (2026_01_16_140526)

customers (2026_01_16_173157)
└── customer_photos (2026_01_16_173234)

cs_leads (2026_01_16_151503)
└── add_address_details (2026_01_17_094700)

otos (2026_01_19_150901)
├── add_cx_fields (2026_01_19_152254)
└── oto_contact_logs (2026_01_19_152257)

material_reservations (2026_01_19_150904)
cx_issues (2026_01_16_144614)
```

---

## 📝 Migration Execution Order

**IMPORTANT**: Migrations must be run in chronological order (timestamp-based).

### Initial Setup (Fresh Install)
```bash
php artisan migrate
```

### Rollback Strategy
```bash
# Rollback last batch
php artisan migrate:rollback

# Rollback specific steps
php artisan migrate:rollback --step=5

# Reset all migrations (DANGER - Development only)
php artisan migrate:reset
```

---

## ⚠️ Important Notes

### Production Safety
- ✅ All migrations are production-ready
- ✅ No breaking changes in extensions
- ✅ Foreign keys properly defined
- ✅ Indexes added for performance

### Maintenance Guidelines
1. **Never modify core tables (2024)** - Create new migrations instead
2. **Always add new columns as nullable** - For backward compatibility
3. **Test rollback** before deploying
4. **Backup database** before major migrations

### Common Issues
- **Foreign key errors**: Check if parent table exists
- **Column already exists**: Migration may have run partially
- **Syntax errors**: Check Laravel version compatibility

---

## 🔄 Migration Workflow

```mermaid
graph LR
    A[Create Migration] --> B[Write Schema]
    B --> C[Test Locally]
    C --> D[Review Code]
    D --> E[Commit to Git]
    E --> F[Deploy to Staging]
    F --> G[Test Staging]
    G --> H[Deploy to Production]
    H --> I[Monitor]
```

---

## 📊 Statistics

- **Total Tables**: 18 tables
- **Total Migrations**: 34 files
- **Core Tables**: 9 tables
- **Extension Migrations**: 25 files
- **Most Extended Table**: `work_orders` (8 extensions)

---

## 🚀 Quick Reference

### Check Migration Status
```bash
php artisan migrate:status
```

### Create New Migration
```bash
php artisan make:migration create_table_name_table
php artisan make:migration add_column_to_table_name_table
```

### Fresh Migration (Development Only)
```bash
php artisan migrate:fresh --seed
```

---

**For questions or issues, refer to Laravel Migration Documentation:**  
https://laravel.com/docs/migrations
