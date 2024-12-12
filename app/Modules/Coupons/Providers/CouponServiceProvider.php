<?php

namespace App\Modules\Coupons\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class CouponServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Coupons/';
}
