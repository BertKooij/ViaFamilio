<?php

namespace Domain\Trees\Events;

use Illuminate\Foundation\Events\Dispatchable;

class RemovingTreeMember
{
    use Dispatchable;

    /**
     * The tree instance.
     *
     * @var mixed
     */
    public $tree;

    /**
     * The tree member being removed.
     *
     * @var mixed
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $tree
     * @param  mixed  $user
     * @return void
     */
    public function __construct($tree, $user)
    {
        $this->tree = $tree;
        $this->user = $user;
    }
}
