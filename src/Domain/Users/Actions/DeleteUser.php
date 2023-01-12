<?php

namespace Domain\Users\Actions;

use Domain\Trees\Actions\DeleteTree;
use Domain\Trees\Models\Tree;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    protected DeleteTree $deletesTrees;

    public function __construct(DeleteTree $deletesTrees)
    {
        $this->deletesTrees = $deletesTrees;
    }

    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->deleteTrees($user);
            $user->deleteProfilePhoto();
            $user->tokens->each->delete();
            $user->delete();
        });
    }

    /**
     * Delete the Trees and Tree associations attached to the user.
     */
    protected function deleteTrees(User $user): void
    {
        $user->Trees()->detach();

        $user->ownedTrees->each(function (Tree $Tree) {
            $this->deletesTrees->delete($Tree);
        });
    }
}
