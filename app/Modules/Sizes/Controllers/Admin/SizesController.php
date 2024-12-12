<?php

namespace App\Modules\Sizes\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class SizesController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'sizes',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                'name' => 'required',
                'price' => 'required',
            ],
            'store' => [],
            'update' => [],
        ],
    ];
}
