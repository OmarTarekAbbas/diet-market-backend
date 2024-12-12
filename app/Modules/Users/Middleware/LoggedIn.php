<?php

namespace App\Modules\Users\Middleware;

use Closure;
use Auth as BaseAuth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoggedIn
{
    /**
     * {@inheritDoc}
     */
    public function handle(Request $request, Closure $next)
    {
        if (user()) {
            return $next($request);
        }

        $accessToken = $request->authorizationValue();

        $repositoryName = config('app.users-repo', 'users');

        $user = repo($repositoryName)->getByAccessToken($accessToken ?: '');

        if ($user) {
            BaseAuth::login($user);

            return $next($request);
        } else {
            return response([
                'error' => 'Unauthorized Token',
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
