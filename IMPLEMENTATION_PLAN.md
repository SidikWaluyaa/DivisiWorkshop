# Implementation Plan - System Perfection (Phase 1)

**Goal:** Address high-priority technical debt identified in the Audit Report, focusing on Backend Architecture (Enums, Services) and Frontend Modernization.

## User Review Required

> [!IMPORTANT]
> This refactor involves significant changes to `ReceptionController` and `StorageRack` logic. While functional parity will be maintained, comprehensive testing is required after these changes.

## Proposed Changes

### 1. Backend Architecture (Enums & Typing)

Replace hardcoded strings with strongly-typed PHP Enums for better safety and proper IntelliSense.

#### [NEW] [StorageCategory.php](file:///c:/laragon/www/SistemWorkshop/app/Enums/StorageCategory.php)

- Enum for `shoes`, `accessories`, `before` (Transit).

#### [NEW] [RackStatus.php](file:///c:/laragon/www/SistemWorkshop/app/Enums/RackStatus.php)

- Enum for `active`, `maintenance`, `full`.

#### [MODIFY] [StorageRack.php](file:///c:/laragon/www/SistemWorkshop/app/Models/StorageRack.php)

- Update Model to Cast these attributes to Enums.

### 2. Service Layer Extraction (Fixing "Fat Controller")

Move complex business logic out of Controllers.

#### [NEW] [CustomerService.php](file:///c:/laragon/www/SistemWorkshop/app/Services/CustomerService.php)

- Handle `updateOrCreate` logic for Customer data sync (currently duplicated in 3+ controllers).

#### [NEW] [ReceptionService.php](file:///c:/laragon/www/SistemWorkshop/app/Services/ReceptionService.php)

- Handle `store` (Manual Order), `processReception` (QC & Updates), and `receive` (Transit logic).

#### [MODIFY] [ReceptionController.php](file:///c:/laragon/www/SistemWorkshop/app/Http/Controllers/ReceptionController.php)

- Drastically reduce size. Controller will only validate input and call Service methods.

### 3. Frontend Modernization

Fix the mixed Javascript paradigms.

#### [MODIFY] [index.blade.php](file:///c:/laragon/www/SistemWorkshop/resources/views/storage/racks/index.blade.php)

- Replace inline vanilla JS (`document.getElementById`) with Alpine.js (`x-data`).

### 4. Dependency Management

Secure dependencies locally.

#### [MODIFY] [package.json](file:///c:/laragon/www/SistemWorkshop/package.json)

- Add `sweetalert2` and `chart.js`.

#### [MODIFY] [app.js](file:///c:/laragon/www/SistemWorkshop/resources/js/app.js)

- Import libraries globally.

#### [MODIFY] [app.blade.php](file:///c:/laragon/www/SistemWorkshop/resources/views/layouts/app.blade.php)

- Remove CDN links.

## Verification Plan

### Automated Tests

- Run `php artisan test` to ensure no regressions.
- Linting check via `php artisan route:list` to ensure no broken routes.

### Manual Verification

1. **Storage Racks**: Verify CRUD operations for Racks using the new Alpine.js UI.
2. **Reception Flow**: Create a manual order, Process QC (Pass/Reject), and ensure functionality remains identical.
3. **Dependencies**: Disconnect internet and verify SweetAlert/Charts still load.
