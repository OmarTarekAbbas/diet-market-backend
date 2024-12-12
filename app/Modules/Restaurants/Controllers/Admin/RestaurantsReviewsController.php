<?php

namespace App\Modules\Restaurants\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class RestaurantsReviewsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'restaurantsReviews',
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
