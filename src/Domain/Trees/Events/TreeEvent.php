<?php

namespace Domain\Trees\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class TreeEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The tree instance.
     *
     * @var \App\Models\Tree
     */
    public $tree;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Tree  $tree
     * @return void
     */
    public function __construct($tree)
    {
        $this->tree = $tree;
    }
}
