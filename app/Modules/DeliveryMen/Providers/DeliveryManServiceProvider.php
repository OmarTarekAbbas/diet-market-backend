<?php

namespace App\Modules\DeliveryMen\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class DeliveryManServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/DeliveryMen/';
}
