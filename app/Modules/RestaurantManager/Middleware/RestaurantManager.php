<?php

namespace App\Modules\RestaurantManager\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RestaurantManager
{
    /**
     * {@inheritDoc}
     */
    public function handle(Request $request, Closure $next)
    {
        $user = user();
        if ($user && $user->accountType() == 'restaurantManager') {
            return $next($request);
        }

        return response([
            'error' => 'Unauthorized User',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
