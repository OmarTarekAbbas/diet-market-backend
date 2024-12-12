<?php

namespace App\Modules\Sections\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class SectionsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'sections',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [
                'restaurant' => 'required',
                'restaurantManager' => 'required',
                'name' => 'required',
            ],
            'update' => [],
        ],
    ];
}
