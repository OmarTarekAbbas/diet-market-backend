<?php

namespace App\Modules\ShippingMethods\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ShippingMethodsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'shippingMethods',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                // 'type' => 'required|in:normal,fast,subscription',
                // 'typeMethods' => 'required|in:international,local',
            ],
            'store' => [],
            'update' => [],
        ],
    ];
}
