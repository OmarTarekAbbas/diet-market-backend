<?php

namespace App\Modules\Products\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ProductMealsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'productMeals',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                // 'discount.type' => 'in:amount,percentage',
                'name' => 'required',
                'price' => 'required',
                'discount.value' => 'required_if:discount.type,percentage,amount',
                'discount.startDate' => 'required_if:discount.type,percentage,amount|date',
                'discount.endDate' => 'required_if:discount.type,percentage,amount|date',
                'priceInSubscription' => 'required_if:inSubscription,true',
                // 'minQuantity' => 'required|numeric|min:1',
                // 'unit' => 'required',
                'category' => 'required',
                'restaurant' => 'required',
            ],
            'store' => [
                'images' => 'required|array',
                'images.*' => 'image',
            ],
            'update' => [
                'images' => 'nullable|array',
                'images.*' => 'image',
            ],
        ],
    ];
}
