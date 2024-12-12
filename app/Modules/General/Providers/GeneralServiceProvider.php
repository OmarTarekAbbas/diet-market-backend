<?php

namespace App\Modules\General\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class GeneralServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/General/';
}
