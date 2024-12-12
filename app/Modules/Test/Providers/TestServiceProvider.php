<?php

namespace App\Modules\Test\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class TestServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["site"];

    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Test/';
}
