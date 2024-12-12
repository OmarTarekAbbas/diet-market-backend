<?php

namespace App\Modules\StoreManagers\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IsStoreManager
{
    /**
     * {@inheritDoc}
     */
    public function handle(Request $request, Closure $next)
    {
        $user = user();
        if ($user && $user->accountType() == 'StoreManager') {
            return $next($request);
        }

        return response([
            'error' => 'Unauthorized User',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
