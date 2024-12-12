<?php

namespace App\Modules\Cart\Resources;

use App\Modules\Coupons\Resources\Coupon;
use App\Modules\Restaurants\Resources\Restaurant;
use App\Modules\AddressBook\Resources\AddressBook;
use App\Modules\StoreManagers\Resources\StoreManager;
use App\Modules\ShippingMethods\Resources\ShippingMethod;
use App\Modules\RestaurantManager\Resources\RestaurantManager;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class Cart extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = [
        'group','deviceId','customer','type',
        'totalPrice', 'taxes', 'finalPrice', 'originalPrice', 'shippingAddress', 'couponDiscount', 'coupon', 'wallet', 'specialDiscount', 'rewordDiscount',
        'isActiveRewardPoints', 'shippingFees', 'shippingMethod', 'usedRewardPoints', 'subscription', 'freeShipping', 'freeExpressShipping',
    ];

    const FLOAT_DATA = [
        'specialDiscount', 'freeExpressShippingDiscount', 'freeShippingDiscount', 'taxesValue', 'totalSubscription', 'totalItems', //'amountsDue'
    ];

    const INTEGER_DATA = [
        'rewardPoints', 'totalQuantity', 'purchaseRewardPoints', 'restaurantManager', 'restaurant',
    ];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = [
        'group',
        'shippingAddress', 'couponDiscount', 'specialDiscount', 'coupon', 'wallet', 'rewordDiscount', 'isActiveRewardPoints', 'shippingFees', 'shippingMethod', 'usedRewardPoints', 'subscription',
        'specialDiscount', 'freeExpressShippingDiscount', 'freeShippingDiscount', 'freeShipping', 'freeExpressShipping', 'restaurantManager', 'restaurant', 'seller',
    ];

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
        'coupon' => Coupon::class,
        'shippingAddress' => AddressBook::class,
        'shippingMethod' => ShippingMethod::class,
        'restaurant' => Restaurant::class,
        'restaurantManager' => RestaurantManager::class,
        // 'seller' => StoreManager::class,
    ];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [
        'items' => CartItem::class,
        'seller' => StoreManager::class,
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
     * @param \Request $request
     * @return array|void
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    protected function extend($request)
    {
        $allowByReward = false;
        $user = user();
        // $minRewardPoints = repo('settings')->getMinRewardPoints();

        if (
            $user
            && !empty($user->rewardPoint)
            && ($this->purchaseRewardPoints > 0)
            && ($user->rewardPoint >= $this->purchaseRewardPoints)
        ) {
            // && ($minRewardPoints > 0 && $minRewardPoints <= $this->purchaseRewardPoints))
            $allowByReward = true;
        }

        $this->set('canUseRewardPoints', $allowByReward);

        $this->setTotals();
    }

    /**
     * get totals
     */
    private function setTotals()
    {
        $totals = [];

        if ($this->type == 'clubs') {
            $langText = trans('orders.subTotalTextLangClubs');
            $finalLangText = trans('orders.finalPriceTextLang');
        } elseif ($this->type == 'food') {
            $langText = trans('orders.subTotalTextLangMeals');
            $finalLangText = trans('orders.finalPriceTextLangMeals');
        } else {
            $langText = trans('orders.subTotalTextLangProduct');
            $finalLangText = trans('orders.finalPriceTextLang');
        }

        if ($this->totalPrice) {
            $totals[] = [
                'text' => $langText,
                'price' => $this->originalPrice,
                'priceText' => trans('cart.price', ['value' => $this->originalPrice]),
                'type' => 'originalPrice',
            ];
        }

        if ($this->taxes) {
            $totals[] = [
                'text' => trans('cart.taxes'),
                'price' => $this->taxes,
                'priceText' => trans('cart.price', ['value' => $this->taxes]),
                'type' => 'taxes',
            ];
        }

        if ($this->specialDiscount) {
            $totals[] = [
                'text' => trans('cart.specialDiscount'),
                'price' => $this->specialDiscount,
                'priceText' => trans('cart.price', ['value' => $this->specialDiscount]),
                'type' => 'specialDiscount',
            ];
        }
        // dd($user->walletBalance < 0);

        $user = user();
        if ($user && $user->walletBalance < 0) {
            $totals[] = [
                'text' => trans('orders.walletTextLang'),
                'price' => $user->walletBalance,
                'priceText' => trans('orders.price', ['value' => $user->walletBalance]),
                'type' => 'walletMinus',
            ];
        }

        if ($this->wallet) {
            $totals[] = [
                'text' => trans('orders.walletTextLang'),
                'price' => $this->wallet,
                'priceText' => trans('orders.price', ['value' => $this->wallet]),
                'type' => 'wallet',
            ];
        }

        if ($this->couponDiscount) {
            if ($this->type == 'food') {
                $totals[] = [
                    'text' => trans('orders.couponDiscountFoodTextLang'),
                    'price' => $this->couponDiscount,
                    'priceText' => trans('orders.price', ['value' => -$this->couponDiscount]),
                    'type' => 'couponDiscount',
                ];
            } else {
                $totals[] = [
                    'text' => trans('orders.couponDiscountTextLang'),
                    'price' => $this->couponDiscount,
                    'priceText' => trans('orders.price', ['value' => $this->couponDiscount]),
                    'type' => 'couponDiscount',
                ];
            }
        }

        if ($this->rewordDiscount) {
            $totals[] = [
                'text' => trans('cart.rewordDiscount'),
                'price' => $this->rewordDiscount,
                'priceText' => trans('orders.price', ['value' => $this->rewordDiscount]),
                'type' => 'rewordDiscount',
            ];
        }

        if ($this->type == 'food') {
            // dd(request()->url() , url('/api/orders'));
            if (request()->deliveryType == 'inHome' && request()->url() == url('/api/cart')) {
                if ($this->restaurant['delivery'] == false) {
                    $deliveryValue = repo('settings')->getSetting('deliveryMen', 'deliveryMenCost');

                    $finalPrice = ($deliveryValue + $this->finalPrice);
                } else {
                    $deliveryValue = $this->restaurant['deliveryValue'];

                    $finalPrice = ($this->restaurant['deliveryValue'] + $this->finalPrice);
                }

                $totals[] = [
                    'text' => trans('orders.shippingFeesTextLang'),
                    'price' => $deliveryValue,
                    'priceText' => trans('orders.price', ['value' => $deliveryValue]),
                    'type' => 'shippingFees',
                ];
                $totals[] = [
                    'text' => $finalLangText,
                    'price' => $finalPrice,
                    'priceText' => trans('orders.price', ['value' => $finalPrice]),
                    'type' => 'finalPriceText',
                ];
            } else {
                $totals[] = [
                    'text' => $finalLangText,
                    'price' => $this->finalPrice,
                    'priceText' => trans('orders.price', ['value' => $this->finalPrice]),
                    'type' => 'finalPriceText',
                ];
            }
        } else {
            if ($this->shippingFees) {
                $totals[] = [
                    'text' => trans('orders.shippingFeesTextLang'),
                    'price' => $this->shippingFees,
                    'priceText' => trans('orders.price', ['value' => $this->shippingFees]),
                    'type' => 'shippingFees',
                ];
            }
            $totals[] = [
                'text' => $finalLangText,
                'price' => $this->finalPrice,
                'priceText' => trans('orders.price', ['value' => $this->finalPrice]),
                'type' => 'finalPriceText',
            ];
        }



        $this->set('totals', $totals);
    }
}
