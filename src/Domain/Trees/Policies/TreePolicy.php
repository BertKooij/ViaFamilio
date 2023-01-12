<?php

namespace Domain\Trees\Policies;

use Domain\Trees\Models\Tree;
use Domain\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TreePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tree $tree): bool
    {
        return $user->belongsToTree($tree);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tree $tree): bool
    {
        return $user->ownsTree($tree);
    }

    /**
     * Determine whether the user can add tree members.
     */
    public function addTreeMember(User $user, Tree $tree): bool
    {
        return $user->ownsTree($tree);
    }

    /**
     * Determine whether the user can update tree member permissions.
     */
    public function updateTreeMember(User $user, Tree $tree): bool
    {
        return $user->ownsTree($tree);
    }

    /**
     * Determine whether the user can remove tree members.
     */
    public function removeTreeMember(User $user, Tree $tree): bool
    {
        return $user->ownsTree($tree);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tree $tree): bool
    {
        return $user->ownsTree($tree);
    }
}
