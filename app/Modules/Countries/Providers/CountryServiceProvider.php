<?php

namespace App\Modules\Countries\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class CountryServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Countries/';
}
