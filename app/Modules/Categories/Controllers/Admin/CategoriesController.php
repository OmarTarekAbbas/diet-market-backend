<?php

namespace App\Modules\Categories\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class CategoriesController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'categories',
        'listOptions' => [
            'select' => [],
            'filterBy' => ['name'],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                'name' => 'required',
                'description' => 'required',
                'type' => 'required|in:food,products',
                // 'restaurant' => 'required_if:type,food',
                'image' => 'max:'.kbit,
            ],
            'store' => [],
            'update' => [],
        ],
    ];
}
