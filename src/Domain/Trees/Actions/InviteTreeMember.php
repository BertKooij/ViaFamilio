<?php

namespace Domain\Trees\Actions;

use Closure;
use Domain\Trees\Events\InvitingTreeMember;
use Domain\Trees\Mails\TreeInvitationMail;
use Domain\Trees\Models\Tree;
use Domain\Users\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Rules\Role;

class InviteTreeMember
{
    /**
     * Invite a new Tree member to the given Tree.
     */
    public function invite(User $user, Tree $tree, string $email, string $role = null): void
    {
        Gate::forUser($user)->authorize('addTreeMember', $tree);

        $this->validate($tree, $email, $role);

        InvitingTreeMember::dispatch($tree, $email, $role);

        $invitation = $tree->TreeInvitations()->create([
            'email' => $email,
            'role' => $role,
        ]);

        Mail::to($email)->send(new TreeInvitationMail($invitation));
    }

    /**
     * Validate the invite member operation.
     */
    protected function validate(Tree $tree, string $email, ?string $role): void
    {
        Validator::make([
            'email' => $email,
            'role' => $role,
        ], $this->rules($tree), [
            'email.unique' => __('This user has already been invited to the Tree.'),
        ])->after(
            $this->ensureUserIsNotAlreadyOnTree($tree, $email)
        )->validateWithBag('addTreeMember');
    }

    /**
     * Get the validation rules for inviting a Tree member.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function rules(Tree $tree): array
    {
        return array_filter([
            'email' => [
                'required', 'email',
                Rule::unique('tree_invitations')->where(function (Builder $query) use ($tree) {
                    $query->where('Tree_id', $tree->id);
                }),
            ],
            'role' => Jetstream::hasRoles()
                            ? ['required', 'string', new Role]
                            : null,
        ]);
    }

    /**
     * Ensure that the user is not already on the Tree.
     */
    protected function ensureUserIsNotAlreadyOnTree(Tree $tree, string $email): Closure
    {
        return function ($validator) use ($tree, $email) {
            $validator->errors()->addIf(
                $tree->hasUserWithEmail($email),
                'email',
                __('This user already belongs to the Tree.')
            );
        };
    }
}
