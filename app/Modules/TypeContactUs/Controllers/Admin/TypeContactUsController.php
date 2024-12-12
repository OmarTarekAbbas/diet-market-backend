<?php

namespace App\Modules\TypeContactUs\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class TypeContactUsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        
        'repository' => 'typeContactuses',
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
