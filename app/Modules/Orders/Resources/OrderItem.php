<?php

namespace App\Modules\Orders\Resources;

use Carbon\Carbon;
use App\Modules\Products\Resources\Product;
use App\Modules\Products\Resources\ProductOption;
use App\Modules\StoreManagers\Resources\StoreManager;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;
use App\Modules\Orders\Traits\StatusColor;
use App\Modules\Products\Resources\ProductPackageSize;

class OrderItem extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'totalPrice', 'requestReturning', 'quantity', 'price',  'rewardPoints', 'subscribeStartAt', 'subscribeEndAt', 'notes', 'startTime', 'endTime', 'orderStatus', 'isRated', 'nextStatus', 'status', 'club', 'customer', 'skuProduct', 'widthProduct', 'taxes', 'originalPrice', 'lengthBox', 'weightBox', 'heightBox','selectBox','printAWBURL'];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = ['subscribeStartAt', 'subscribeEndAt', 'notes', 'startTime', 'endTime', 'date', 'orderStatus', 'ReturningReason', 'PackagingStatus', 'isAvailableItem', 'status', 'club', 'customer', 'skuProduct', 'widthProduct', 'seller', 'lengthBox', 'weightBox', 'heightBox','selectBox','printAWBURL'];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = ['createdAt', 'subscribeStartAt', 'subscribeEndAt'];

    /**
     * Boolean Data
     *
     * @const array
     */
    const BOOLEAN_DATA = ['isCountSeen'];

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
        'ReturningReason' => ReturningReason::class,
        'PackagingStatus' => PackagingStatus::class,
        'seller' => StoreManager::class,
        // 'productPackageSize' => ProductPackageSize::class,

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

    protected function extend($request)
    {
        // dd($this->requestReturning);

        $this->set('totalPriceText', trans('products.price', ['value' => $this->totalPrice]));
        if ($request->type == 'products') {
            $this->set('isAvailableItem', repo('products')->isAvailableItem($this->product['id']));
            $this->set('statusColor', StatusColor::statusColor($this->resource->status));
            if ($this->isRated) {
                $this->set('isRated', true);
            } else {
                $this->set('isRated', false);
            }
        }
        if (user() && user()->accountType() === 'deliveryMen') {
            $this->set(
                'date',
                Carbon::parse($this->date)->translatedFormat('l d F Y')
            );
        } else {
            $this->set('date', [
                'text' => Carbon::parse($this->date)->translatedFormat('l d F Y'),
            ]);
        }
    }
}
