<?php

namespace App\Modules\Products\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ProductPackageSizeController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'productPackageSizes',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                'name' => 'required',
                'heightBox' => 'required',
                'lengthBox' => 'required',
                'weightBox' => 'required',
            ],
            'store' => [],
            'update' => [],
        ],
    ];
}
