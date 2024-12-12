<?php

namespace App\Modules\Modules\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider as BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
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
    protected $namespace = 'App/Modules/Modules/';
}
