<?php

namespace Domain\Trees\Actions;

use Domain\Trees\Events\AddingTree;
use Domain\Trees\Models\Tree;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\Jetstream;

class CreateTree implements CreatesTeams
{
    /**
     * Validate and create a new Tree for the given user.
     *
     * @param  array<string, string>  $input
     */
    public function create(User $user, array $input): Tree
    {
        Gate::forUser($user)->authorize('create', Tree::class);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createTree');

        AddingTree::dispatch($user);

        $user->switchTree($tree = $user->ownedTrees()->create([
            'name' => $input['name'],
            'personal_tree' => false,
        ]));

        return $tree;
    }
}
