<?php

namespace App\Modules\Options\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class OptionsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'options',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'store' => [
                'name' => 'required',
            ],
            'update' => [],
        ],
    ];
}
