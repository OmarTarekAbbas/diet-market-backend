<?php

namespace App\Modules\AddressBook\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class AddressBookServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin", "site"];

    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/AddressBook/';
}
