<?php

namespace App\Modules\General\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MaintenanceMode
{
    /**
     * {@inheritDoc}
     */
    public function handle(Request $request, Closure $next)
    {
        // $maintenanceMode = repo('settings')->getMaintenanceMode();
        // $uri = $request->uri();
        // if ($maintenanceMode) {
        //     if (!Str::startsWith($uri, '/api/admin') && !Str::startsWith($uri, '/api/deliveryMen')) {
        //         return response(['errors' => [['key' => 'error', 'value' => trans('errors.maintenanceMode')]]], Response::HTTP_SERVICE_UNAVAILABLE);
        //     }
        // }
        // return $next($request);


        if (false !== strpos(request()->url(), 'deliveryMen')) {
            $maintenanceMode = repo('settings')->getMaintenanceDeliveryMenMode();
            $uri = $request->uri();
            if ($maintenanceMode) {
                if (!Str::startsWith($uri, '/api/admin')) {
                    return response(['errors' => [['key' => 'error', 'value' => trans('errors.maintenanceMode')]]], Response::HTTP_SERVICE_UNAVAILABLE);
                }
            }

            return $next($request);
        } else {
            $maintenanceMode = repo('settings')->getMaintenanceMode();
            $uri = $request->uri();
            if ($maintenanceMode) {
                if (!Str::startsWith($uri, '/api/admin') && !Str::startsWith($uri, '/api/deliveryMen')) {
                    return response(['errors' => [['key' => 'error', 'value' => trans('errors.maintenanceMode')]]], Response::HTTP_SERVICE_UNAVAILABLE);
                }
            }

            return $next($request);
        }
    }
}
