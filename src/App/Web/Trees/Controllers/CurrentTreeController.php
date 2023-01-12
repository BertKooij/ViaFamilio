<?php

namespace App\Web\Trees\Controllers;

use Domain\Trees\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class CurrentTreeController extends Controller
{
    public function update(Request $request) : Response
    {
        $tree = Tree::findOrFail($request->tree_id);

        if (! $request->user()->switchTree($tree)) {
            abort(403);
        }

        return redirect(config('fortify.home'), 303);
    }
}
