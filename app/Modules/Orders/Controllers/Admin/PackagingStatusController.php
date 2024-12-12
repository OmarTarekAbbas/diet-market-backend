<?php

namespace App\Modules\Orders\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class PackagingStatusController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'packagingStatus',
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
