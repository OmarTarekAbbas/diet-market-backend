<?php

namespace App\Modules\ShippingCosts\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ShippingCostsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        
        'repository' => 'shippingCosts',
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
