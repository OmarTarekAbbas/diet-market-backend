<?php

namespace App\Modules\Customers\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class CustomerServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Customers/';
}
