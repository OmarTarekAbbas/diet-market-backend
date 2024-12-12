<?php

namespace App\Modules\StoreManagers\Resources;

use App\Modules\Cities\Resources\City;
use App\Modules\Stores\Resources\Store;
use App\Modules\Cart\Resources\CartItem;
use App\Modules\Countries\Resources\Country;
use App\Modules\ShippingMethods\Resources\ShippingMethod;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class StoreManager extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'email', 'phoneNumber', 'accessToken', 'verificationCode', 'totalNotifications', 'store', 'items', 'shippingMethod'];

    /**
     * String Data
     *
     * @const array
     */
    const STRING_DATA = ['firstName', 'lastName'];

    /**
     * Boolean Data
     *
     * @const array
     */
    const BOOLEAN_DATA = ['published', 'isVerified'];

    /**
     * Integer Data
     *
     * @const array
     */
    const INTEGER_DATA = ['totalRating'];

    /**
     * Float Data
     *
     * @const array
     */
    const FLOAT_DATA = ['rating', 'walletBalance', 'walletBalanceDeposit', 'walletBalanceWithdraw'];

    /**
     * Object Data
     *
     * @const array
     */
    const OBJECT_DATA = [];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = ['email', 'phoneNumber', 'accessToken', 'verificationCode', 'totalNotifications', 'items', 'firstName', 'lastName', 'published', 'isVerified', 'totalRating', 'shippingMethod'];

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
        'store' => Store::class,
        'country' => Country::class,
        'city' => City::class,
        'shippingMethod' => ShippingMethod::class,
    ];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [
        'items' => CartItem::class,
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
}
