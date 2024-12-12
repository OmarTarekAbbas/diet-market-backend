<?php

namespace App\Modules\Cities\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class CityServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin", "site"];

    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Cities/';

    public function boot()
    {
        parent::boot();
        if (request()->header('CITY') && !request()->has('city')) {
            request()->request->add(['city' => (int) request()->header('CITY')]);
        }
    }
}
