<?php

namespace App\Modules\Logs\Providers;

use App\Modules\Logs\Services\LogsEvents;
use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class LogServiceProvider extends ModuleServiceProvider
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
    protected $namespace = 'App/Modules/Logs/';

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        (new LogsEvents)->subscribe();

        $this->map();
    }
}
