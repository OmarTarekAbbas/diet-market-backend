<?php

namespace App\Modules\Campaigns\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class CampaignDeliveriesController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'campaignDeliveries',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => ['image' => 'max:'.kbit],
            'store' => [],
            'update' => [],
        ],
    ];
}
