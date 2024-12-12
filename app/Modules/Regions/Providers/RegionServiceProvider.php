<?php

namespace App\Modules\Regions\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class RegionServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Regions/';
}
