<?php

namespace App\Modules\Meals\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class MealsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'meals',
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
                // 'categories' => 'required',
                'image' => 'required',
            ],
            'store' => [],
            'update' => [],
        ],
    ];
}
