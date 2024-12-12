<?php

namespace App\Modules\Campaigns\Providers;

use HZ\Illuminate\Mongez\Managers\Providers\ModuleServiceProvider;

class CampaignServiceProvider extends ModuleServiceProvider
{
    /**
     * {@inheritDoc}
     */
    const ROUTES_TYPES = ["admin","site"];
    
    /**
     * {@inheritDoc}
     */
    protected $namespace = 'App/Modules/Campaigns/';
}
