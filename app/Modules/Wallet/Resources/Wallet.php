<?php

namespace App\Modules\Wallet\Resources;

use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class Wallet extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'notes', 'title', 'creatorType', 'transactionType', 'reason', 'amount', 'orderId'];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = [];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = ['createdAt' => 'd-m-Y'];

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
        //        'createdBy' => User::class,
    ];

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

    protected function extend($request)
    {
        // dd( $this->balanceBefore);
        $customer = user();
        if ($this->amount) {
            $this->set('amountText', trans('cart.price', ['value' => $this->amount]));
            $this->set('balanceBefore', trans('cart.price', ['value' => $this->balanceBefore]));
            $this->set('balanceAfter', trans('cart.price', ['value' => $this->balanceAfter]));
        }
    }
}
