<?php

namespace App\Modules\PackagesClubs\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class PackagesClubsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        
        'repository' => 'packagesClubs',
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
