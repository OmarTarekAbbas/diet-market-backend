<?php

namespace App\Modules\Wallet\Resources;

use App\Modules\Orders\Repositories\OrdersRepository;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class WalletDelivery extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'notes', 'title', 'creatorType', 'transactionType', 'reason', 'amount', 'orderId', 'commissionDiteMarket', 'totalAmountOrder', 'delivery'];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = ['notes','reason'];

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
            $this->set('balanceAfter', trans('cart.price', ['value' => $customer->walletBalance]));
            $this->set('commissionDiteMarketText', trans('cart.price', ['value' => $this->commissionDiteMarket]));
            if ($this->totalAmountOrder == 0) {
                $this->set('totalAmountOrderText', trans('cart.price', ['value' => $this->amount]));
            } else {
                $this->set('totalAmountOrderText', trans('cart.price', ['value' => $this->totalAmountOrder]));
            }
        }
        $this->paymentMethod();
        if ($this->transactionType == "deposit") {
            $this->set('color', 'cefef2');
            $this->set('colorIcon', '329f51');
            $this->set('image', asset('assets/icons/Wallat.png'));
        } elseif ($this->transactionType == "withdraw") {
            $this->set('color', 'fcdede');
            $this->set('colorIcon', 'ef3159');
            $this->set('image', asset('assets/icons/Wallat.png'));
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
        } elseif ($this->title == "سحب من المحفظة") {
            $paymentMethodText = [
                'code' => "سحب من المحفظة",
                'name' => "خصم رصيد بواسطة مدير النظام",
                'icon' => asset('assets/payment/walletNew.png'),
            ];
            $this->set('paymentMethodInfo', $paymentMethodText);
        } elseif ($this->title == "ايداع في المحفظة") {
            $paymentMethodText = [
                'code' => "ايداع في المحفظة",
                'name' => "اضافة رصيد بواسطة مدير النظام",
                'icon' => asset('assets/payment/walletNew.png'),
            ];
            $this->set('paymentMethodInfo', $paymentMethodText);
        }
    }
}
