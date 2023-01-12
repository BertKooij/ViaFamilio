<?php

namespace Domain\Trees\Actions;

use Domain\Trees\Events\TreeMemberUpdated;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;

class UpdateTreeMemberRole
{
    /**
     * Update the role for the given tree member.
     *
     * @param  mixed  $user
     * @param  mixed  $tree
     * @param  int  $treeMemberId
     * @param  string  $role
     * @return void
     */
    public function update($user, $tree, $treeMemberId, string $role)
    {
        Gate::forUser($user)->authorize('updateTreeMember', $tree);

        Validator::make([
            'role' => $role,
        ], [
            'role' => ['required', 'string', new Role],
        ])->validate();

        $tree->users()->updateExistingPivot($treeMemberId, [
            'role' => $role,
        ]);

        TreeMemberUpdated::dispatch($tree->fresh(), Jetstream::findUserByIdOrFail($treeMemberId));
    }
}
