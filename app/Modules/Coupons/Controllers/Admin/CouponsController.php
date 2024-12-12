<?php

namespace App\Modules\Coupons\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class CouponsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'coupons',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                'type' => 'required|in:percentage,fixed',
            ],
            'store' => [],
            'update' => [],
        ],
    ];
}
