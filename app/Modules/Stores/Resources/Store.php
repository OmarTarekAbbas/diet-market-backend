<?php

namespace App\Modules\Stores\Resources;

use App\Modules\StoreManagers\Resources\StoreManager;
use App\Modules\ShippingMethods\Resources\ShippingMethod;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class Store extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'selfShipping', 'metaTag', 'KeyWords'];

    /**
     * String Data
     *
     * @const array
     */
    const STRING_DATA = ['commercialRecordId'];

    /**
     * Boolean Data
     *
     * @const array
     */
    const BOOLEAN_DATA = ['published'];

    /**
     * Integer Data
     *
     * @const array
     */
    const INTEGER_DATA = [];

    /**
     * Float Data
     *
     * @const array
     */
    const FLOAT_DATA = ['profitRatio', 'TotalOrders', 'profitRatioStore', 'profitRatioDiteMarket'];

    /**
     * Object Data
     *
     * @const array
     */
    const OBJECT_DATA = ['location'];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = [
        'selfShipping', 'published', 'TotalOrders', 'profitRatioStore', 'profitRatioDiteMarket', 'metaTag', 'KeyWords'
    ];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = [];

    /**
     * Data that has multiple values based on locale codes
     * Mostly this is used with mongodb driver
     *
     * @const array
     */
    const LOCALIZED = ['name', 'description'];

    /**
     * List of assets that will have a full url if available
     */
    const ASSETS = ['logo', 'commercialRecordImage'];

    /**
     * Data that will be returned as a resources
     *
     * i.e [city => CityResource::class],
     * @const array
     */
    const RESOURCES = [
        // 'storeMangers' => StoreManager::class,
    ];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [
        // 'shippingMethods' => ShippingMethod::class
    ];

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
     * {@inheritdoc}
     */
    protected function extend($request)
    {
        // dd(url('api/orders/returned/'.$request->route('id')));
        if (request()->url() == url('api/orders/returned') || request()->url() == url('api/orders/returned/' . $request->route('id'))) {
            $this->set('storeMangers', null);
        } else {
            // $storeMangers = repo('storeManagers')->getstoreMangers($this->id);
            // $this->set('storeMangers', $storeMangers);
        }
        if ($this->transaction) {
            $this->set('TotalOrders', $this->transaction['totalOrder'] ?? 0);
            $this->set('profitRatioStore', $this->transaction['totalRequired'] ?? 0);
            $this->set('profitRatioDiteMarket', $this->transaction['profitRatio'] ?? 0);
            $this->set('paySeller', $this->transaction['totalRequired'] ?? 0);
            $this->set('payDiet', $this->transaction['profitRatio'] ?? 0);
        }
    }
}
