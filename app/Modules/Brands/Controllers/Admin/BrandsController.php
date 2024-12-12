<?php

namespace App\Modules\Brands\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class BrandsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'brands',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => ['logo' => 'max:'.kbit],
            'store' => [],
            'update' => [],
        ],
    ];
}
