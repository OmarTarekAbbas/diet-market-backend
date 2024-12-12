<?php

namespace App\Modules\Categories\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class CategoryServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Categories/';
}
