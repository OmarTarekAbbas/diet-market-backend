<?php

namespace App\Modules\TypeOfFoodRestaurant\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class TypeOfFoodRestaurantController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'typeOfFoodRestaurants',
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
