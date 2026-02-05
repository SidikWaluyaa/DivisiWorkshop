---
description: Standard Operating Procedure for CS Data Revision & Editing (Big 4 Standard)
---

# Workflow: CS Data Revision System

This workflow defines the secure and auditable process for editing Customer Service data (Leads, SPK, and Customer Profiles) to ensure data integrity and accountability.

## 1. Initial Assessment

When a CS agent identifies a mistake or requires an update to a record:

- **Condition 1:** If the record is in "Initial" status (e.g., `GREETING`), proceed to **Step 2 (Fast-Track Edit)**.
- **Condition 2:** If the record is in "Locked" status (e.g., `DEAL`, `PRODUCTION`), proceed to **Step 3 (Governed Revision)**.

## 2. Fast-Track Edit (Low Risk)

// turbo

1. Open the Lead/Customer detail page.
2. Click the specific field or the "Edit" button.
3. Modify the data (e.g., fix a typo in the name or phone number).
4. System automatically validates the format (e.g., regex for phone numbers).
5. On Save:
   - System updates the `CsLead` record.
   - System records a "Silent Audit" (stored but not necessarily highlighted in the main feed).

## 3. Governed Revision (High Risk)

1. User clicks "Request Edit" on a locked record.
2. System prompts for a **Revision Reason** (Mandatory).
3. System checks for `admin` or `lead_cs` role.
   - If not authorized: Display "Requires Approval" message.
   - If authorized: Open edit mode with highlighted "Critical Fields".
4. User modifies high-impact data (e.g., Service Type, Source, or Priority).
5. **System Execution:**
   - Starts a DB Transaction.
   - Updates `CsLead`.
   - Propagates changes to related `WorkOrder` or `CsSpk` (Ensures consistency).
   - Generates a **Change Snapshot** (comparing `getOriginal()` vs `getAttributes()`).
   - Commits Transaction.
6. **Notification:**
   - Notify the assigned Workshop Team if the change affects production details.
   - Notify Finance if the change affects billing-related fields.

## 4. Audit & Verification

1. Every edit is visible in the "Activity Timeline" of the record.
2. The UI displays: `[User Name] modified [Field Name] from "[Old Value]" to "[New Value]" [Time Ago]`.
3. Admin can periodically review the **Global Audit Log** to identify patterns (e.g., frequent source changes might indicate training needs).

---
> [!IMPORTANT]
> Always use `DB::transaction` when propagating edits to ensure that if the update fails on the WorkOrder, the Lead update is also rolled back.
