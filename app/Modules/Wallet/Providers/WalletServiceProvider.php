<?php

namespace App\Modules\Wallet\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class WalletServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin", "site"];

    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Wallet/';
}
