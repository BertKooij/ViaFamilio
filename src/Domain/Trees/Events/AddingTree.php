<?php

namespace Domain\Trees\Events;

use Illuminate\Foundation\Events\Dispatchable;

class AddingTree
{
    use Dispatchable;

    /**
     * The tree owner.
     *
     * @var mixed
     */
    public $owner;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $owner
     * @return void
     */
    public function __construct($owner)
    {
        $this->owner = $owner;
    }
}
