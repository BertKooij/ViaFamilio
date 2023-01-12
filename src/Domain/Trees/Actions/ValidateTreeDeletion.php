<?php

namespace Domain\Trees\Actions;

use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ValidateTreeDeletion
{
    /**
     * Validate that the tree can be deleted by the given user.
     *
     * @param  mixed  $user
     * @param  mixed  $tree
     * @return void
     */
    public function validate($user, $tree)
    {
        Gate::forUser($user)->authorize('delete', $tree);

        if ($tree->personal_tree) {
            throw ValidationException::withMessages([
                'tree' => __('You may not delete your personal tree.'),
            ])->errorBag('deleteTree');
        }
    }
}
