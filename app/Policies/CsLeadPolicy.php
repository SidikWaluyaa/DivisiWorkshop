<?php

namespace App\Policies;

use App\Models\CsLead;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CsLeadPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAccess('cs_leads');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CsLead $csLead): bool
    {
        // Admin & Owner can view everything (handled by hasAccess implicitly for those roles)
        if ($user->hasAccess('cs_leads')) {
            // If they have access, they can view their own, OR if they are admin/owner (hasAccess returns true)
            return in_array($user->role, ['admin', 'owner']) || $user->id === $csLead->cs_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAccess('cs_leads');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CsLead $csLead): bool
    {
        if ($user->hasAccess('cs_leads')) {
            return in_array($user->role, ['admin', 'owner']) || $user->id === $csLead->cs_id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CsLead $csLead): bool
    {
        return in_array($user->role, ['admin', 'owner']); // Only Admin/Owner can delete
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CsLead $csLead): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CsLead $csLead): bool
    {
        return in_array($user->role, ['admin', 'owner']);
    }
}
