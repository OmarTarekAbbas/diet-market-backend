<?php

namespace App\Modules\Orders\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class OrderServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Orders/';
}
