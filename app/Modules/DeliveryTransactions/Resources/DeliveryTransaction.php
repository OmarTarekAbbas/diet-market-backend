<?php

namespace App\Modules\DeliveryTransactions\Resources;

use App\Modules\Orders\Resources\OrderDelivery;
use App\Modules\DeliveryMen\Resources\DeliveryMan;
use App\Modules\Orders\Repositories\OrdersRepository;
use App\Modules\Orders\Repositories\OrderDeliveryRepository;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class DeliveryTransaction extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id','deliveryMan'];

    /**
     * String Data
     *
     * @const array
     */
    const STRING_DATA = ['deliveryStatus', 'paymentMethod'];

    /**
     * Boolean Data
     *
     * @const array
     */
    const BOOLEAN_DATA = ['published'];

    /**
     * Integer Data
     *
     * @const array
     */
    const INTEGER_DATA = ['orderDelivery'];

    /**
     * Float Data
     *
     * @const array
     */
    const FLOAT_DATA = [
        'amount', 'deliveryCommission','commissionDiteMarket','totalAmountOrder',
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
    const WHEN_AVAILABLE = [];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = [
        'createdAt',
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
        // 'deliveryMan' => DeliveryMan::class,
        // 'orderDelivery' => OrderDelivery::class,
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
        $this->paymentMethod();
        $this->setStatus();
    }

    /**
     * Method setStatus
     *
     * @return void
     */
    private function setStatus()
    {
        if ($this->deliveryStatus == OrderDeliveryRepository::DELIVERY_PENDING_STATUS) {
            $this->set('deliveryStatusText', 'في انتظار تأكيد المندوب');
        } elseif ($this->deliveryStatus == OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS) {
            $this->set('deliveryStatusText', 'تم الموافقة من قبل المندوب');
        } elseif ($this->deliveryStatus == OrderDeliveryRepository::DELIVERY_REJECTED_STATUS) {
            $this->set('deliveryStatusText', 'تم الرفض من قبل المندوب');
        } elseif ($this->deliveryStatus == OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS) {
            $this->set('deliveryStatusText', 'تأكيد استلام الطلب من المطعم');
        } elseif ($this->deliveryStatus == OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS) {
            $this->set('deliveryStatusText', 'تم تسليم الطلب الي العميل');
        } elseif ($this->deliveryStatus == OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS) {
            $this->set('deliveryStatusText', 'لم يتم استلام الطلب');
        } elseif ($this->deliveryStatus == OrderDeliveryRepository::DELIVERY_IS_NOT_SET_STATUS) {
            $this->set('deliveryStatusText', 'لم يتم تعيين مندوب');
        } elseif ($this->deliveryStatus == OrderDeliveryRepository::DELIVERY_MEAL_HAS_NOT_BEEN_RESTAURANT_STATUS) {
            $this->set('deliveryStatusText', 'لم يتم اتخاذ حاله تم تحضير الوجبه');
        } elseif ($this->deliveryStatus == OrderDeliveryRepository::ORDER_CANCELBYCUSTOMER_STATUS) {
            $this->set('deliveryStatusText', 'الاوردر ملغي من قبل العميل');
        } elseif ($this->deliveryStatus == OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS) {
            $this->set('deliveryStatusText', 'الاوردر ملغي من قبل الادمن');
        } elseif ($this->deliveryStatus == OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS) {
            $this->set('deliveryStatusText', 'تم اسناد الطلب الي المندوب ومر اكثر من 30 ثانية دون قبول او رفض الطلب .');
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
