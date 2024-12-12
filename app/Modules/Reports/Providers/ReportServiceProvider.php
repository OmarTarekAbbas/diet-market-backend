<?php

namespace App\Modules\Reports\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class ReportServiceProvider extends ModuleServiceProvider
{
    /**
     * List of routes files
     *
     * @const array
     */
    const ROUTES_TYPES = ["admin"];

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
    protected $namespace = 'App/Modules/Reports/';
}
