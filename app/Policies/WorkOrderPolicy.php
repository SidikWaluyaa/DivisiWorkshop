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
        return $user->isAdmin() || $user->isOwner() || $user->isGudang() || $user->isCS();
    }

    public function update(User $user, WorkOrder $workOrder)
    {
        return $user->isAdmin() || $user->isOwner() || $user->isWorkshop() || $user->isGudang() || $user->isCS();
    }

    /**
     * Specific check for updating production stations
     */
    public function updateProduction(User $user, WorkOrder $workOrder)
    {
        if ($user->isAdmin() || $user->isOwner()) {
            return true;
        }

        if ($user->isWorkshop()) {
            // Ensure order is actually in Production phase
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
        // Only Admin/Owner or Authorized Workshop staff can final approve
        return $user->isAdmin() || $user->isOwner() || $user->hasAccess('workshop.approve');
    }

    /**
     * Determine if the user can manage CX module
     */
    public function manageCx(User $user)
    {
        return $user->can('access-cx');
    }

    /**
     * Determine if the user can manage Finance
     */
    public function manageFinance(User $user)
    {
        return $user->can('access-finance');
    }

    /**
     * Determine if the user can manage Inventory
     */
    public function manageInventory(User $user)
    {
        return $user->can('access-gudang');
    }

    /**
     * Determine if the user can manage Storage
     */
    public function manageStorage(User $user)
    {
        return $user->can('access-gudang');
    }

    public function rejectProduction(User $user, WorkOrder $workOrder)
    {
        return $user->isAdmin() || $user->isOwner() || $user->hasAccess('workshop.reject');
    }

    /**
     * Check if user can perform operational finish actions (Pickup, Add Service, OTO)
     */
    public function updateFinish(User $user, WorkOrder $workOrder)
    {
        return $user->isAdmin() || $user->isOwner() || $user->isGudang() || $user->isCS();
    }

    /**
     * Check if user can manage finish/history (Delete, Force Delete, Restore)
     */
    public function manageFinish(User $user)
    {
        return $user->isAdmin() || $user->isOwner();
    }

    /**
     * Determine if the user can manage Reception
     */
    public function manageReception(User $user)
    {
        return $user->can('access-gudang');
    }

    /**
     * Determine if the user can manage Sortir
     */
    public function updateSortir(User $user, WorkOrder $workOrder)
    {
        return $user->hasAccess('sortir');
    }

    /**
     * Determine if the user can perform destructive actions in Reception
     */
    public function deleteReception(User $user)
    {
        return $user->isAdmin() || $user->isOwner();
    }

    /**
     * Determine if the user can permanently purge Reception data
     */
    public function forceDeleteReception(User $user)
    {
        return $user->isAdmin() || $user->isOwner();
    }
}
