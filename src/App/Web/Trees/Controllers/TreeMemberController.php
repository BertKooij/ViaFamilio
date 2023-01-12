<?php

namespace App\Web\Trees\Controllers;

use Domain\Trees\Actions\InviteTreeMember;
use Domain\Trees\Actions\RemoveTreeMember;
use Domain\Trees\Actions\UpdateTreeMemberRole;
use Domain\Trees\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Jetstream;

class TreeMemberController extends Controller
{
    /**
     * Add a new tree member to a tree.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $treeId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $treeId)
    {
        $tree = Tree::findOrFail($treeId);

        app(InviteTreeMember::class)->invite(
            $request->user(),
            $tree,
            $request->email ?: '',
            $request->role
        );

        return back(303);
    }

    /**
     * Update the given tree member's role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $treeId
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $treeId, $userId)
    {
        app(UpdateTreeMemberRole::class)->update(
            $request->user(),
            Tree::findOrFail($treeId),
            $userId,
            $request->role
        );

        return back(303);
    }

    /**
     * Remove the given user from the given tree.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $treeId
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $treeId, $userId)
    {
        $tree = Tree::findOrFail($treeId);

        app(RemoveTreeMember::class)->remove(
            $request->user(),
            $tree,
            $user = Jetstream::findUserByIdOrFail($userId)
        );

        if ($request->user()->id === $user->id) {
            return redirect(config('fortify.home'));
        }

        return back(303);
    }
}
