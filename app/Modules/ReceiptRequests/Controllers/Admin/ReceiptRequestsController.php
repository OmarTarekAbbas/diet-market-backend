<?php

namespace App\Modules\ReceiptRequests\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ReceiptRequestsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'receiptRequests',
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
