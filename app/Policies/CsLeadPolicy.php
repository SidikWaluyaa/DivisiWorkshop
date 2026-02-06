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
        return $user->hasAccess('cs');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CsLead $csLead): bool
    {
        if ($user->hasAccess('cs')) {
            return $user->isAdmin() || $user->isOwner() || $user->id === $csLead->cs_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAccess('cs');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CsLead $csLead): bool
    {
        if ($user->hasAccess('cs')) {
            return $user->isAdmin() || $user->isOwner() || $user->id === $csLead->cs_id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CsLead $csLead): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CsLead $csLead): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CsLead $csLead): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }
}
