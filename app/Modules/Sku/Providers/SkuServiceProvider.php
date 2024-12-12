<?php

namespace App\Modules\Sku\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class SkuServiceProvider extends ModuleServiceProvider
{
    /**
     * List of routes files
     *
     * @const array
     */
    const ROUTES_TYPES = ["admin","site"];

    /**
     * Module build type
     *
     * @const strong
     */
    const BUILD_MODE = 'api';

    /**
     * Views Name
     *
     * @const strong
     */
    const VIEWS_NAME = '';
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Sku/';
}
