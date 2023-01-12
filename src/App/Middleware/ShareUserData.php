<?php

namespace App\Middleware;

use Inertia\Inertia;

class ShareUserData
{

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param callable $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        Inertia::share(array_filter([
            'user' => function () use ($request) {
                if (!$user = $request->user()) {
                    return;
                }

                if ($user) {
                    $user->currentTree;
                }

                return array_merge($user->toArray(), array_filter([
                    'all_trees' => $user->allTrees()->values(),
                ]), [
                    'two_factor_enabled' => !is_null($user->two_factor_secret),
                ]);
            },
        ]));

        return $next($request);
    }
}
