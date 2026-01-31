# Implementation Plan - CS Feature Perfection

**Goal:** Address critical security vulnerabilities (IDOR), refactor "Fat Controller" into Services, and modernize Frontend to Alpine.js as per `AUDIT_CS_FEATURE.md`.

## User Review Required

> [!IMPORTANT]
> This refactor involves moving significant logic from `CsLeadController` to new Service classes.

## Proposed Changes

### 1. Security & Policies (IDOR Fix)

Implement Laravel Policies to handle authorization logic centrally.

#### [NEW] [CsLeadPolicy.php](file:///c:/laragon/www/SistemWorkshop/app/Policies/CsLeadPolicy.php)

- Define `view`, `update`, `delete` methods.
- Logic: Allow if `admin/owner` OR `cs_id === user_id`.

#### [MODIFY] [AuthServiceProvider.php](file:///c:/laragon/www/SistemWorkshop/app/Providers/AuthServiceProvider.php)

- Register the Policy.

### 2. Backend Service Extraction

Refactor `CsLeadController` by extracting logic to Services.

#### [NEW] [CsLeadService.php](file:///c:/laragon/www/SistemWorkshop/app/Services/Cs/CsLeadService.php)

- Handle `duplicate check`, `store`, `updateStatus`, `generateSpk` (including Customer sync via `CustomerService`).

#### [NEW] [CsSpkService.php](file:///c:/laragon/www/SistemWorkshop/app/Services/Cs/CsSpkService.php)

- Handle `handToWorkshop` logic (converting SPK items to WorkOrders).

#### [MODIFY] [CsLeadController.php](file:///c:/laragon/www/SistemWorkshop/app/Http/Controllers/CsLeadController.php)

- Inject `CsLeadService` and `CsSpkService`.
- Replace business logic with Service calls.
- Add `$this->authorize()` checks.

### 3. Frontend Modernization

Replace Vanilla JS and manual DOM manipulation with Alpine.js.

#### [MODIFY] [dashboard.blade.php](file:///c:/laragon/www/SistemWorkshop/resources/views/cs/dashboard.blade.php)

- Wrap the main container in `x-data`.
- Convert "New Lead Modal" and "Payment Modal" to Alpine components.
- Use `x-on:click` instead of `onclick="..."`.
- Use `fetch` inside Alpine methods for cleaner state management.

## Verification Plan

### Automated Tests

- Run `php artisan test` (if available).
- Check `php artisan route:list`.

### Manual Verification

1. **Security**: Login as CS User A, try to access/update Lead of CS User B (should 403). Admin should be able to access all.
2. **Flow**: Create Lead -> Quotation -> SPK -> Handover -> WorkOrder. Ensure data flows correctly.
3. **Frontend**: Test Modals (Open/Close), Drag & Drop status update.
