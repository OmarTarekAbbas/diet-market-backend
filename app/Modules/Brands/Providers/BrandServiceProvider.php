<?php

namespace App\Modules\Brands\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class BrandServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Brands/';
}
