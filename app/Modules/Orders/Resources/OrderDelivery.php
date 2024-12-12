<?php

namespace App\Modules\Orders\Resources;

use App\Modules\AddressBook\Resources\AddressBook;
use App\Modules\Orders\Traits\StatusColorDelivery;
use App\Modules\Orders\Repositories\OrdersRepository;
use App\Modules\Orders\Repositories\OrderDeliveryRepository;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class OrderDelivery extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'deliveryMenId', 'customerId', 'distanceToTheRestaurant', 'distanceToTheCustomer', 'totalDistance', 'location', 'addressOrderCustomer', 'status', 'order', 'restaurant', 'deliveryReasonsRejected', 'customer', 'paymentMethodInfo', 'deliveryReasonsNotCompletedOrder', 'minuteToTheRestaurant', 'minuteToTheCustomer', 'deliveryMen', 'nextStatus', 'totalMinute', 'timer'];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = ['reason', 'deliveryReasonsRejected', 'distanceToTheRestaurant', 'distanceToTheCustomer', 'totalDistance', 'location', 'addressOrderCustomer', 'status', 'order', 'restaurant', 'deliveryReasonsNotCompletedOrder', 'minuteToTheRestaurant', 'minuteToTheCustomer', 'deliveryMen', 'totalMinute', 'totalDistanceInt'];

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
     * Float Data
     *
     * @const array
     */
    const FLOAT_DATA = ['totalDistanceInt'];

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
        // 'deliveryReasonsRejected' => DeliveryReasonsRejected::class
        'addressOrderCustomer' => AddressBook::class,

    ];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [
        'statusLog' => OrderStatusDeliveryLog::class,
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
        // $this->set('deliveryReasonsRejected', $this->deliveryReasonsRejected);
        $this->set('finalPriceText', trans('orders.price', ['value' => $this->finalPrice ?? 0]));
        $this->set('deliveryCostText', trans('orders.price', ['value' => $this->deliveryCost ?? 0]));
        $this->set('deliveryCommissionText', trans('orders.price', ['value' => $this->deliveryCommission ?? 0]));
        $this->paymentMethod();
        $this->setStatus();
        $status = StatusColorDelivery::statusColor($this->status);
        $this->set('statusColor', $status);
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

    /**
     * Method paymentMethod
     *
     * @return void
     */
    private function paymentMethod()
    {
        if ($this->paymentMethod == OrdersRepository::CASH_ON_DELIVERY) {
            $paymentMethodText = [
                'code' => OrdersRepository::CASH_ON_DELIVERY,
                'name' => 'الدفع عند الاستلام',
                'icon' => asset('assets/payment/cash.png'),
            ];
            $this->set('paymentMethodInfo', $paymentMethodText);
        } elseif ($this->paymentMethod == OrdersRepository::MADA_PAYMENT_METHOD) {
            $paymentMethodText = [
                'code' => OrdersRepository::MADA_PAYMENT_METHOD,
                'name' => trans('orders.mada'),
                'icon' => asset('assets/payment/madaNew.png'),
            ];
            $this->set('paymentMethodInfo', $paymentMethodText);
        } elseif ($this->paymentMethod == OrdersRepository::VISA_PAYMENT_METHOD) {
            $paymentMethodText = [
                'code' => OrdersRepository::VISA_PAYMENT_METHOD,
                'name' => trans('orders.visa'),
                'icon' => asset('assets/payment/visa-masterCardNew.png'),
            ];
            $this->set('paymentMethodInfo', $paymentMethodText);
        } elseif ($this->paymentMethod == OrdersRepository::APPLE_PAY_PAYMENT_METHOD) {
            $paymentMethodText = [
                'code' => OrdersRepository::APPLE_PAY_PAYMENT_METHOD,
                'name' => trans('orders.applePay'),
                'icon' => asset('assets/payment/applePayNew.png'),
            ];
            $this->set('paymentMethodInfo', $paymentMethodText);
        } elseif ($this->paymentMethod == OrdersRepository::WALLET_PAYMENT_METHOD) {
            $paymentMethodText = [
                'code' => OrdersRepository::WALLET_PAYMENT_METHOD,
                'name' => trans('orders.wallet'),
                'icon' => asset('assets/payment/walletNew.png'),
            ];
            $this->set('paymentMethodInfo', $paymentMethodText);
        }
    }
}
