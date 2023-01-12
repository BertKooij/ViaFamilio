<?php

namespace Domain\Trees\Actions;

use Domain\Trees\Models\Tree;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UpdateTreeName
{
    /**
     * Validate and update the given Tree's name.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, Tree $Tree, array $input): void
    {
        Gate::forUser($user)->authorize('update', $Tree);

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateTreeName');

        $Tree->forceFill([
            'name' => $input['name'],
        ])->save();
    }
}
