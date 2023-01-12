<?php

namespace Domain\Trees\Events;

use Illuminate\Foundation\Events\Dispatchable;

class InvitingTreeMember
{
    use Dispatchable;

    /**
     * The tree instance.
     *
     * @var mixed
     */
    public $tree;

    /**
     * The email address of the invitee.
     *
     * @var mixed
     */
    public $email;

    /**
     * The role of the invitee.
     *
     * @var mixed
     */
    public $role;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $tree
     * @param  mixed  $email
     * @param  mixed  $role
     * @return void
     */
    public function __construct($tree, $email, $role)
    {
        $this->tree = $tree;
        $this->email = $email;
        $this->role = $role;
    }
}
