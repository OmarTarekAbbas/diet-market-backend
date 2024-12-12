<?php

namespace App\Modules\Items\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ItemsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'items',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                'name' => 'required',
                'protein' => 'required',
                'carbohydrates' => 'required',
                'fat' => 'required',
                'categories' => 'required',
                'sizes' => 'required',
                'image' => 'required',
            ],
            'store' => [],
            'update' => [],
        ],
    ];
}
