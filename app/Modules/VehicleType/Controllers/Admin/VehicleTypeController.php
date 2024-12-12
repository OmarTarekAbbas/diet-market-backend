<?php

namespace App\Modules\VehicleType\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class VehicleTypeController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        
        'repository' => 'vehicleTypes',
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
