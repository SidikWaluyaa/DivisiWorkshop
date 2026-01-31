# Implementation Plan - Workshop Feature Refinement

**Goal:** Fix critical bugs and improve code quality/security for Workshop (Dashboard, Production, Reception) without altering the existing user flow/UI.

## Proposed Changes

### 1. 🐛 Fix Critical Frontend Bug (Priority)
**Issue:** `bulkAction` in Production uses a static PHP variable for `activeTab`. Switching tabs (e.g., Sol -> Upper) doesn't update this variable, causing bulk actions to apply the wrong type (e.g., sending "prod_sol" items when "Upper" is active).

**Fix:** Refactor `bulkAction` to read the *live* `activeTab` value from Alpine.js state.

#### [MODIFY] [production/index.blade.php](file:///c:/laragon/www/SistemWorkshop/resources/views/production/index.blade.php)

- Update `bulkAction()` JS function to retrieve `activeTab` from DOM/Alpine scope.
- Remove the hardcoded PHP `let activeTab = ...`.

### 2. 🔒 Enhance Security (IDOR Prevention)
**Issue:** Controllers rely mainly on Route Middleware. Lack of specific ownership/status checks in methods.

**Fix:** Add `OrderPolicy` or `WorkshopPolicy` and enforce strict checks in Controllers.

#### [NEW] [WorkOrderPolicy.php](file:///c:/laragon/www/SistemWorkshop/app/Policies/WorkOrderPolicy.php)

- Define `view`, `update`, `moveStation`.
- Logic: Ensure user has correct role/permission for the specific station.

#### [MODIFY] [ProductionController.php](file:///c:/laragon/www/SistemWorkshop/app/Http/Controllers/ProductionController.php)

- Add `$this->authorize('update', $order)` or similar checks in `updateStation`, `approve`.

### 3. 🏗️ Refactor "Fat Controller" (Dashboard)
**Issue:** `WorkshopDashboardController` contains heavy logic (300+ lines of inline processing).

**Fix:** Extract logic to `WorkshopAnalyticsService`.

#### [NEW] [WorkshopAnalyticsService.php](file:///c:/laragon/www/SistemWorkshop/app/Services/Workshop/WorkshopAnalyticsService.php)

- Move `getRealtimeMetrics`, `getHistoricalMetrics`, `getTechnicianLoad` logic here.

#### [MODIFY] [WorkshopDashboardController.php](file:///c:/laragon/www/SistemWorkshop/app/Http/Controllers/WorkshopDashboardController.php)

- Inject Service.
- Clean up `index` method to just pass data to view.

## Verification

1. **Bulk Action Test**:
   - Go to Production -> Tab "Upper".
   - Select item. Click "Assign".
   - Verify Payload sends `type: "prod_upper"` (not "prod_sol").

2. **Security Test**:
   - Ensure regular user cannot hit `updateStation` for an order they shouldn't access (though UI flow remains same).

3. **Dashboard Test**:
   - Verify numbers on Dashboard match before/after refactor.
