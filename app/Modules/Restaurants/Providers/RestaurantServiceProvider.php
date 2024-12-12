<?php

namespace App\Modules\Restaurants\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class RestaurantServiceProvider extends ModuleServiceProvider
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
    protected $namespace = 'App/Modules/Restaurants/';

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        parent::boot();

        // if ($this->app->request->server('restaurant')) {
        //     $this->app->request['restuarant'] = $reqyest->server('restaurant');
        // }
    }
}
