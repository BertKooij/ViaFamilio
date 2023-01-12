<?php

namespace App\Web\Trees\Controllers;

use Domain\Trees\Actions\CreateTree;
use Domain\Trees\Actions\DeleteTree;
use Domain\Trees\Actions\UpdateTreeName;
use Domain\Trees\Actions\ValidateTreeDeletion;
use Domain\Trees\Models\Tree;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\RedirectsActions;

class TreeController extends Controller
{
    use RedirectsActions;

    public function show(Request $request, $treeId) : Response
    {
        $tree = Tree::findOrFail($treeId);

        Gate::authorize('view', $tree);

        return Jetstream::inertia()->render($request, 'Trees/Show', [
            'tree' => $tree->load('owner', 'users', 'treeInvitations'),
            'availableRoles' => array_values(Jetstream::$roles),
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions' => Jetstream::$defaultPermissions,
            'permissions' => [
                'canAddTreeMembers' => Gate::check('addTreeMember', $tree),
                'canDeleteTree' => Gate::check('delete', $tree),
                'canRemoveTreeMembers' => Gate::check('removeTreeMember', $tree),
                'canUpdateTree' => Gate::check('update', $tree),
            ],
        ]);
    }

    public function create(Request $request) : Response
    {
        Gate::authorize('create', Tree::class);

        return Jetstream::inertia()->render($request, 'Trees/Create');
    }

    public function store(Request $request) : \Illuminate\Http\Response
    {
        $creator = app(CreateTree::class);

        $creator->create($request->user(), $request->all());

        return $this->redirectPath($creator);
    }

    public function update(Request $request, $treeId) : RedirectResponse
    {
        $tree = Tree::findOrFail($treeId);

        app(UpdateTreeName::class)->update($request->user(), $tree, $request->all());

        return back(303);
    }

    public function destroy(Request $request, $treeId) : RedirectResponse
    {
        $tree = Tree::findOrFail($treeId);

        app(ValidateTreeDeletion::class)->validate($request->user(), $tree);

        $deleter = app(DeleteTree::class);

        $deleter->delete($tree);

        return $this->redirectPath($deleter);
    }
}
