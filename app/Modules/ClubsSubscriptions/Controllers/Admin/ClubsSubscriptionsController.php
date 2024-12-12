<?php

namespace App\Modules\ClubsSubscriptions\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ClubsSubscriptionsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'clubsSubscriptions',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [
                'club' => 'numeric|required',
                'subscription.package' => 'required|',
                'subscription.price' => 'numeric|required',
                'subscription.startDate' => 'required|date',
                'subscription.endDate' => 'required|date',
            ],
            'update' => [],
        ],
    ];
}
