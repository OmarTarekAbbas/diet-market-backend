<?php

namespace App\Modules\Rewards\Resources;

use App\Modules\Rewards\Traits\Type;
use App\Modules\Users\Resources\User;
use App\Modules\Rewards\Traits\Status;
use App\Modules\Customers\Resources\Customer;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class Reward extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'creatorType', 'transactionType', 'orderId', 'note', 'status'];

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
    const DATES = ['createdAt', 'expireDate'];

    const INTEGER_DATA = ['points', 'remainingPoints'];

    /**
     * Data that has multiple values based on locale codes
     * Mostly this is used with mongodb driver
     *
     * @const array
     */
    const LOCALIZED = [
        'title',
    ];

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
        'customer' => Customer::class,
        'createdBy' => User::class,
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
        // dd(StatusColor::statusColor($this->resource->status));

        $this->set('transactionTypeText', trans("rewards.transaction.{$this->resource->transactionType}"));
        $this->set('transactionTypeIcon', Type::TransactionIcon($this->resource->transactionType));
        $this->set('TransactionColor', Type::TransactionColor($this->resource->status));
        $this->set('statusText', trans("rewards.status.{$this->resource->status}"));
        $this->set('statusColor', Status::StatusColor($this->resource->status));
        $this->set('usedPoints', ($this->resource->points - $this->resource->remainingPoints));

        if ($this->data['expireDate']) {
            $this->set('DateText', ($this->resource->expireDate) ? trans("rewards.endIn") . ' ' . $this->resource->expireDate->translatedFormat('l d F Y') : '');
        } else {
            $this->set('DateText', '-');
        }
    }
}
