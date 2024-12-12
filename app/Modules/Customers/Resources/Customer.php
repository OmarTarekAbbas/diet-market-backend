<?php

namespace App\Modules\Customers\Resources;

use App\Modules\Cart\Resources\Cart;
use App\Modules\Orders\Resources\Order;
use App\Modules\DietTypes\Resources\DietType;
use App\Modules\AddressBook\Resources\AddressBook;
use App\Modules\CustomerGroups\Resources\CustomerGroup;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class Customer extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['subscribedToNewsLetter',/*'devices'*/  'location','subscribeClubs' ,'deviceCart'];

    /**
     * String Data
     *
     * @const array
     */
    const STRING_DATA = ['firstName', 'lastName', 'email', 'phoneNumber', 'accessToken'];

    /**
     * Boolean Data
     *
     * @const array
     */
    const BOOLEAN_DATA = [
        'published', 'isVerified',
    ];

    /**
     * Integer Data
     *
     * @const array
     */
    const INTEGER_DATA = [
        'id', 'totalNotifications', 'totalOrders', 'rewardPoint', 'rewardPointWithdraw', 'rewardPointDeposit', 'totalItems', 'verificationCode', 'favoritesCount', 'totalRefusedReceive','newVerificationCode',
    ];

    /**
     * Float Data
     *
     * @const array
     */
    const FLOAT_DATA = [
        'walletBalance', 'totalOrdersPurchases',
    ];

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
    const WHEN_AVAILABLE = [
        'verificationCode', 'addresses', 'cart', "subscribedToNewsLetter", "cartSubscription", "group", "createdAt", 'totalItems', 'favoritesCount', 'devices','dietTypes',
    ];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = ['createdAt' => 'd-m-Y', 'birthDate'];

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
        'cart' => Cart::class,
        'group' => CustomerGroup::class,
        'cartSubscription' => Cart::class,
        'dietTypes' => DietType::class,
        'cartMeal' => Cart::class,
        // 'subscribeClubs' => Order::class,

    ];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [
        'addresses' => AddressBook::class,
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
     * @param \Request $request
     * @return array|void
     */
    protected function extend($request)
    {
    }
}
