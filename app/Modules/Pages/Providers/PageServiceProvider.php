<?php

namespace App\Modules\Pages\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class PageServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Pages/';
}
