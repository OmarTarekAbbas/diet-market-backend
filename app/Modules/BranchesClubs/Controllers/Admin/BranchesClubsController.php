<?php

namespace App\Modules\BranchesClubs\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class BranchesClubsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'branchesClubs',
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
