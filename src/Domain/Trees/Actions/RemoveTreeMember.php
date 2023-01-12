<?php

namespace Domain\Trees\Actions;

use Domain\Trees\Events\TreeMemberRemoved;
use Domain\Trees\Models\Tree;
use Domain\Users\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class RemoveTreeMember
{
    /**
     * Remove the Tree member from the given Tree.
     */
    public function remove(User $user, Tree $tree, User $treeMember): void
    {
        $this->authorize($user, $tree, $treeMember);

        $this->ensureUserDoesNotOwnTree($treeMember, $tree);

        $tree->removeUser($treeMember);

        TreeMemberRemoved::dispatch($tree, $treeMember);
    }

    /**
     * Authorize that the user can remove the Tree member.
     */
    protected function authorize(User $user, Tree $tree, User $treeMember): void
    {
        if (! Gate::forUser($user)->check('removeTreeMember', $tree) &&
            $user->id !== $treeMember->id) {
            throw new AuthorizationException;
        }
    }

    /**
     * Ensure that the currently authenticated user does not own the Tree.
     */
    protected function ensureUserDoesNotOwnTree(User $treeMember, Tree $tree): void
    {
        if ($treeMember->id === $tree->owner->id) {
            throw ValidationException::withMessages([
                'tree' => [__('You may not leave a Tree that you created.')],
            ])->errorBag('removeTreeMember');
        }
    }
}
