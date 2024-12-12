<?php

namespace App\Modules\Options\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class OptionServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Options/';
}
