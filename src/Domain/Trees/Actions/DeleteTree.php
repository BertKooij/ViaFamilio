<?php

namespace Domain\Trees\Actions;

use Domain\Trees\Models\Tree;
use Laravel\Jetstream\Contracts\DeletesTeams;

class DeleteTree implements DeletesTeams
{
    /**
     * Delete the given Tree.
     */
    public function delete(Tree $Tree): void
    {
        $Tree->purge();
    }
}
