<?php

namespace App\Modules\Orders\Resources;

use App\Modules\Stores\Resources\Store;
use App\Modules\Orders\Traits\StatusColor;
use App\Modules\Orders\Repositories\OrderDeliveryRepository;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class OrderStatusDeliveryLog extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['creator', 'status', 'orderId', 'orderDeliveryId','creatorBy','message','deliveryMen'];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = ['message','deliveryMen'];

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
        // $this->set('statusColor', StatusColor::statusColor($this->resource->status));
        // $this->set('statusIcon', StatusColor::statusIcon('order'));
        $this->setStatus();
    }

    /**
     * Method setStatus
     *
     * @return void
     */
    private function setStatus()
    {
        if ($this->status == OrderDeliveryRepository::DELIVERY_PENDING_STATUS) {
            $this->set('statusText', 'في انتظار تأكيد المندوب');
        } elseif ($this->status == OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS) {
            $this->set('statusText', 'تم الموافقة من قبل المندوب');
        } elseif ($this->status == OrderDeliveryRepository::DELIVERY_REJECTED_STATUS) {
            $this->set('statusText', 'تم الرفض من قبل المندوب');
        } elseif ($this->status == OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS) {
            $this->set('statusText', 'تأكيد استلام الطلب من المطعم');
        } elseif ($this->status == OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS) {
            $this->set('statusText', 'تم تسليم الطلب الي العميل');
        } elseif ($this->status == OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS) {
            $this->set('statusText', 'لم يتم استلام الطلب');
        } elseif ($this->status == OrderDeliveryRepository::DELIVERY_IS_NOT_SET_STATUS) {
            $this->set('statusText', 'لم يتم تعيين مندوب');
        } elseif ($this->status == OrderDeliveryRepository::DELIVERY_MEAL_HAS_NOT_BEEN_RESTAURANT_STATUS) {
            $this->set('statusText', 'لم يتم اتخاذ حاله تم تحضير الوجبه');
        } elseif ($this->status == OrderDeliveryRepository::ORDER_CANCELBYCUSTOMER_STATUS) {
            $this->set('statusText', 'الاوردر ملغي من قبل العميل');
        } elseif ($this->status == OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS) {
            $this->set('statusText', 'الاوردر ملغي من قبل الادمن');
        } elseif ($this->status == OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS) {
            $this->set('statusText', 'تم اسناد الطلب الي المندوب ومر اكثر من 30 ثانية دون قبول او رفض الطلب .');
        }
    }
}
