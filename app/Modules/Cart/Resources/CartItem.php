<?php

namespace App\Modules\Cart\Resources;

use App\Modules\Products\Resources\Product;
use App\Modules\Products\Resources\ProductOption;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class CartItem extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'quantity', 'notes', 'seller', 'skuProduct','onlySku'];

    public const FLOAT_DATA = [
        'totalSubscription', 'price', 'totalPrice', 'beforeSalePrice', 'widthProduct',
    ];

    const INTEGER_DATA = [
        'rewardPoints', 'purchaseRewardPoints',
    ];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = ['subscription', 'notes', 'seller'];

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
        'product' => Product::class,
    ];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [
        'options' => ProductOption::class,
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
        // $this->set('price', $this->data['totalPrice']);
        // $this->set('finalPrice', $this->data['totalPrice']);

        $this->set('totalPriceText', trans('products.price', ['value' => $this->totalPrice]));
        $this->set('beforeSalePriceText', trans('products.price', ['value' => $this->beforeSalePrice]));
    }
}
