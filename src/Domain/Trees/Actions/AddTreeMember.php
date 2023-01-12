<?php

namespace Domain\Trees\Actions;

use Closure;
use Domain\Trees\Events\AddingTreeMember;
use Domain\Trees\Events\TreeMemberAdded;
use Domain\Trees\Models\Tree;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;

class AddTreeMember
{
    /**
     * Add a new Tree member to the given Tree.
     */
    public function add(User $user, Tree $Tree, string $email, string $role = null): void
    {
        Gate::forUser($user)->authorize('addTreeMember', $Tree);

        $this->validate($Tree, $email, $role);

        $newTreeMember = Jetstream::findUserByEmailOrFail($email);

        AddingTreeMember::dispatch($Tree, $newTreeMember);

        $Tree->users()->attach(
            $newTreeMember, ['role' => $role]
        );

        TreeMemberAdded::dispatch($Tree, $newTreeMember);
    }

    /**
     * Validate the add member operation.
     */
    protected function validate(Tree $Tree, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules(), [
            'email.exists' => __('We were unable to find a registered user with this email address.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnTree($Tree, $email)
        )->validateWithBag('addTreeMember');
    }

    /**
     * Get the validation rules for adding a Tree member.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function rules(): array
    {
        return array_filter([
            'email' => ['required', 'email', 'exists:users'],
            'role' => Jetstream::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ]);
    }

    /**
     * Ensure that the user is not already on the Tree.
     */
    protected function ensureUserIsNotAlreadyOnTree(Tree $Tree, string $email): Closure
    {
        return function ($validator) use ($Tree, $email) {
            $validator->errors()->addIf(
                $Tree->hasUserWithEmail($email),
                'email',
                __('This user already belongs to the Tree.')
            );
        };
    }
}
