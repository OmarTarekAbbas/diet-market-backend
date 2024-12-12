<?php

namespace App\Modules\HealthyData\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class HealthyDataController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        
        'repository' => 'healthyDatas',
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
