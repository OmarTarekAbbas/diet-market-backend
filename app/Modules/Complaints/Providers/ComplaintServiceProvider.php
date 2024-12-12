<?php

namespace App\Modules\Complaints\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class ComplaintServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin", "site"];

    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Complaints/';
}
