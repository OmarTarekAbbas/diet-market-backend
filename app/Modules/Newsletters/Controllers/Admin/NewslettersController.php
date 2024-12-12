<?php

namespace App\Modules\Newsletters\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class NewslettersController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        
        'repository' => 'newsletters',
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
