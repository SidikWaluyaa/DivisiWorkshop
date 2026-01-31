<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; 
    }

    public function view(User $user, WorkOrder $workOrder)
    {
        return true;
    }

    public function create(User $user)
    {
        return in_array($user->role, ['admin', 'owner', 'receptionist', 'cs']);
    }

    public function update(User $user, WorkOrder $workOrder)
    {
        // General update
        return in_array($user->role, ['admin', 'owner', 'production_manager', 'technician', 'receptionist', 'cs']);
    }

    /**
     * Specific check for updating production stations
     */
    public function updateProduction(User $user, WorkOrder $workOrder)
    {
        if (in_array($user->role, ['admin', 'owner', 'production_manager'])) {
            return true;
        }

        if ($user->role === 'technician') {
            // Ensure order is actually in Production phase
            // Handle both Enum object and string value cases
            $status = $workOrder->status instanceof WorkOrderStatus ? $workOrder->status->value : $workOrder->status;
            
            return $status === WorkOrderStatus::PRODUCTION->value;
        }

        return false;
    }
    
    /**
     * Check if user can approve/QC final production
     */
    public function approveProduction(User $user, WorkOrder $workOrder)
    {
        // Only Admin/Owner/Manager can final approve
        return in_array($user->role, ['admin', 'owner', 'production_manager']);
    }

    /**
     * Check if user can reject production (Revision)
     */
    /**
     * Determine if the user can manage CX module (Process, Delete, etc.)
     */
    public function manageCx(User $user)
    {
        return $user->hasAccess('cx');
    }

    /**
     * Determine if the user can manage Finance (Payments, Delete, etc.)
     */
    public function manageFinance(User $user)
    {
        return $user->hasAccess('finance');
    }

    /**
     * Determine if the user can manage Inventory (Material Requests)
     */
    public function manageInventory(User $user)
    {
        return $user->hasAccess('gudang');
    }

    /**
     * Determine if the user can manage Storage (Racks)
     */
    public function manageStorage(User $user)
    {
        return $user->hasAccess('gudang');
    }

    public function rejectProduction(User $user, WorkOrder $workOrder)
    {
        // Only Admin/Owner/Manager can reject/revise
        return in_array($user->role, ['admin', 'owner', 'production_manager']);
    }

    /**
     * Check if user can perform operational finish actions (Pickup, Add Service, OTO)
     */
    public function updateFinish(User $user, WorkOrder $workOrder)
    {
        // Admin, Owner, Receptionist, CS
        return in_array($user->role, ['admin', 'owner', 'receptionist', 'cs']);
    }

    /**
     * Check if user can manage finish/history (Delete, Force Delete, Restore)
     */
    public function manageFinish(User $user)
    {
        // Only Admin/Owner (maybe Manager?) - Strict for data deletion
        return in_array($user->role, ['admin', 'owner']);
    }

    /**
     * Determine if the user can manage Reception (Create, Process, List)
     */
    public function manageReception(User $user)
    {
        return $user->hasAccess('gudang');
    }

    /**
     * Determine if the user can manage Sortir (Update materials, Finish, etc.)
     */
    public function updateSortir(User $user, WorkOrder $workOrder)
    {
        return $user->hasAccess('sortir');
    }

    /**
     * Determine if the user can perform destructive actions in Reception (Delete, Restore)
     */
    public function deleteReception(User $user)
    {
        // Restricted to Admin/Owner for safety
        return in_array($user->role, ['admin', 'owner']);
    }

    /**
     * Determine if the user can permanently purge Reception data
     */
    public function forceDeleteReception(User $user)
    {
        // Strictly Admin/Owner
        return in_array($user->role, ['admin', 'owner']);
    }
}
