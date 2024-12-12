<?php

namespace App\Modules\Cart\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class CartServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Cart/';
}
