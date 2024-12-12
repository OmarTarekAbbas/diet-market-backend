<?php

namespace App\Modules\Orders\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class OrderStatusDeliveryController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'orderStatusDelivery',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [],
            'update' => [],
        ],
    ];
}
