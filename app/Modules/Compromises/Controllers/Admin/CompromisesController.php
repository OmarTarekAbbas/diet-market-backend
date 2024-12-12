<?php

namespace App\Modules\Compromises\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class CompromisesController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        
        'repository' => 'compromises',
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
