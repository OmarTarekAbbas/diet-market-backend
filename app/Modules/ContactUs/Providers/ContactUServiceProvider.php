<?php

namespace App\Modules\ContactUs\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class ContactUServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/ContactUs/';
}
