<?php

namespace App\Modules\Banners\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class BannersController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'banners',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                'type' => 'required|in:product,specials,category,image,link',
            ],
            'store' => [],
            'update' => [],
        ],
    ];
}
