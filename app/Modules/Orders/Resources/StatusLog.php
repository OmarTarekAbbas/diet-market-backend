<?php

namespace App\Modules\Orders\Resources;

use App\Modules\Stores\Resources\Store;
use App\Modules\Orders\Traits\StatusColor;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class StatusLog extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['creator', 'status', 'notes', 'cancelingReason','orderItem'];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = ['store'];

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
        // 'store' => Store::class
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

    /**
     * {@inheritdoc}
     */
    protected function extend($request)
    {
        $this->set('statusColor', StatusColor::statusColor($this->resource->status));
        $this->set('statusIcon', StatusColor::statusIcon('order'));
        $this->set('statusText', trans("orders.status.{$this->resource->status}"));
    }
}
