<?php

namespace App\Modules\SubscriptionMeals\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class SubscriptionMealsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        
        'repository' => 'subscriptionMeals',
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
