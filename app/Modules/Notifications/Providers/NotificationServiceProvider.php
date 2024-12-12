<?php

namespace App\Modules\Notifications\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class NotificationServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin", "site"];

    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Notifications/';
}
