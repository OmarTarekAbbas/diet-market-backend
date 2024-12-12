<?php

namespace App\Modules\NutritionSpecialistMangers\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NutritionSpecialistManger
{
    /**
     * {@inheritDoc}
     */
    public function handle(Request $request, Closure $next)
    {
        $user = user();
        if ($user && $user->accountType() == 'nutritionSpecialistMangers') {
            return $next($request);
        }

        return response([
            'error' => 'Unauthorized User',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
