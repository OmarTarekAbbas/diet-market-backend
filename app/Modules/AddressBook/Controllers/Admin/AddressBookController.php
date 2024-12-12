<?php

namespace App\Modules\AddressBook\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class AddressBookController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'addressBooks',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => false, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [],
            'update' => [],
        ],
    ];
}
