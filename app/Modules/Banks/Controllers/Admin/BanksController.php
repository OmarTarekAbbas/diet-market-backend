<?php

namespace App\Modules\Banks\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class BanksController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        
        'repository' => 'banks',
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
