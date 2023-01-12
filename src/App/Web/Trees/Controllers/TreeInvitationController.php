<?php

namespace App\Web\Trees\Controllers;

use Domain\Trees\Actions\AddTreeMember;
use Domain\Trees\Models\TreeInvitation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class TreeInvitationController extends Controller
{
    public function accept(Request $request, TreeInvitation $invitation) : Response
    {
        app(AddTreeMember::class)->add(
            $invitation->tree->owner,
            $invitation->tree,
            $invitation->email,
            $invitation->role
        );

        $invitation->delete();

        return redirect(config('fortify.home'))->banner(
            __('Great! You have accepted the invitation to join the :tree tree.', ['tree' => $invitation->tree->name]),
        );
    }

    public function destroy(Request $request, TreeInvitation $invitation) : Response
    {
        if (! Gate::forUser($request->user())->check('removeTreeMember', $invitation->tree)) {
            throw new AuthorizationException;
        }

        $invitation->delete();

        return back(303);
    }
}
