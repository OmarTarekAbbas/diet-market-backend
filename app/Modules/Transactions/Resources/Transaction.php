<?php

namespace App\Modules\Transactions\Resources;

use App\Modules\Cities\Resources\City;
use App\Modules\StoreManagers\Resources\StoreManager;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class Transaction extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'orderId', 'seller', 'paymentMethod', 'appCommission', 'totalOrder', 'transactionAmount', 'totalRequired','type'];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = [];

    /**
     * Float Data
     *
     * @const array
     */
    const FLOAT_DATA = [
        'appCommission', 'totalRequired', 'transactionAmount','totalRequiredSeller','profitRatio',
    ];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = [
        'createdAt' => 'd-m-Y h:i:s a',
    ];

    /**
     * Data that has multiple values based on locale codes
     * Mostly this is used with mongodb driver
     *
     * @const array
     */
    const LOCALIZED = [];

    /**
     * List of assets that will have a full url if available
     */
    const ASSETS = [];

    /**
     * Data that will be returned as a resources
     *
     * i.e [city => CityResource::class],
     * @const array
     */
    const RESOURCES = [
        // 'seller' => StoreManager::class,
        'city' => City::class,
    ];

    /**
     * Boolean Data
     *
     * @const array
     */
    const BOOLEAN_DATA = ['suspended'];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [];

    /**
     * List of keys that will be unset before sending
     *
     * @var array
     */
    protected static $disabledKeys = [];

    /**
     * List of keys that will be taken only
     *
     * @var array
     */
    protected static $allowedKeys = [];

    /**
     * Extend data
     */
    protected function extend($request)
    {
        // if ($this->type == 'products') {
        //     $this->set('totalRequiredSeller',  repo('transactions')->listStore($this->seller['store']['id']));
        // }elseif ($this->type == 'food') {
        //     $this->set('totalRequiredSeller',  repo('transactions')->listRestaurant($this->seller['restaurant']['id']));
        // }elseif ($this->type == 'club') {
        //     $this->set('totalRequiredSeller',  repo('transactions')->listClub($this->seller['club']['id']));
        // }elseif ($this->type == 'nutritionSpecialist') {
        //     $this->set('totalRequiredSeller',  repo('transactions')->listNutritionSpecialist($this->seller['nutritionSpecialist']['id']));
        // }
        $this->set('totalRequiredSeller', $this->profitRatio ?? 0);
    }
}
