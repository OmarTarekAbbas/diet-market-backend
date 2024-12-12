<?php

namespace App\Modules\DeliveryTransactions\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class DeliveryTransactionsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        
        'repository' => 'deliveryTransactions',
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
