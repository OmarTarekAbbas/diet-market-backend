<?php

namespace App\Modules\Notifications\Resources;

use Carbon\Carbon;
use App\Modules\Orders\Traits\StatusColor;
use App\Modules\Orders\Traits\StatusColorDelivery;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class Notification extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'title', 'content', 'type', 'extra',  'seen', 'user'];

    /**
     * Integer Data
     *
     * @const array
     */
    const INTEGER_DATA = [
        'notificationCount',
    ];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = ['notificationCount'];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = ['createdAt'];

    /**
     * Data that has multiple values based on locale codes
     * Mostly this is used with mongodb driver
     *
     * @const array
     */
    const LOCALIZED = ['title', 'content'];

    /**
     * List of assets that will have a full url if available
     */
    const ASSETS = ['image'];

    /**
     * Data that will be returned as a resources
     *
     * i.e [city => CityResource::class],
     * @const array
     */
    const RESOURCES = [];

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
        $this->set('humanTime', Carbon::parse($this->createdAt)->diffForHumans());

        if (request()->url() == url('api/deliveryMen/notifications')) {
            $status = StatusColorDelivery::statusColor($this->extra['status'] ?? $this->type);
            $this->set('extra.statusColor', $status);

            if ($this->image) {
                $this->set('extra.statusIcon', url($this->image));
            // $this->set('extra.iconColor', StatusColorDelivery::iconColor($this->extra['status'] ?? $this->type));
            } else {
                // dd($this->extra['status']);
                $this->set('extra.statusIcon', StatusColorDelivery::statusIcon($this->type));
                $this->set('extra.iconColor', StatusColorDelivery::iconColor($this->extra['status'] ?? $this->type));
            }
        } else {
            $status = StatusColor::statusColor($this->extra['status'] ?? $this->type);
            $statusIcon = StatusColor::statusIcon($this->type);
            $this->set('extra.statusIcon', StatusColor::statusIcon($this->type));
        }
    }
}
