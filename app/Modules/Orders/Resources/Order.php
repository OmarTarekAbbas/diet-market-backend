<?php

namespace App\Modules\Orders\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Modules\Clubs\Resources\Club;
use App\Modules\Coupons\Resources\Coupon;
use App\Modules\Orders\Traits\StatusColor;
use App\Modules\Banks\Resources\BankTransfer;
use App\Modules\Customers\Resources\Customer;
use App\Modules\AddressBook\Resources\AddressBook;
use App\Modules\General\Services\PaymentMethodList;
use App\Modules\Orders\Traits\StatusColorSpecialist;
use App\Modules\BranchesClubs\Resources\BranchesClub;
use App\Modules\Orders\Repositories\OrdersRepository;
use App\Modules\StoreManagers\Resources\StoreManager;
use App\Modules\Products\Resources\ProductPackageSize;
use App\Modules\ShippingMethods\Resources\ShippingMethod;
use App\Modules\RestaurantManager\Resources\RestaurantManager;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;
use App\Modules\NutritionSpecialistMangers\Resources\NutritionSpecialistManger;

class Order extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = [
        'id', 'checkOutId', 'paymentMethod', 'currency', 'status', 'subTotal', 'shippingFees', 'totalQuantity', 'finalPrice', 'notes', 'customer', 'paymentInfo', 'cancelingReason', 'returnedAmount',
        'nextStatus', 'wallet', 'taxes', 'originalPrice', 'requestReturning', 'partialReturn', 'rewardPoints', 'totalQuantity', 'subscription', 'shippingMethod',
        'rating', 'firstWeek', 'productsType', 'secondWeek', 'thirdWeek', 'fourthWeek', 'returningReason', 'expectedDeliveryIn', 'fromTime', 'toTime', 'deliveryType', 'restaurantsReviews', 'clubsReviews', 'nutritionSpecialist', 'nutritionSpecialistsReviews', 'listReturnedOrderItems', 'cashOnDeliveryPrice', 'deliveryName', 'idpackagesClubs', 'insideWhereType', 'subWidthProduct',
    ];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = ['isAmountFull', 'isCheckAmountFull', 'isGetLastOrderIsNotReview'];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = [
        'shippingAddress', 'coupon', 'couponDiscount', 'checkOutId', 'paymentInfo', 'rating', 'bankTransfer', 'usedRewardPoints', 'rewordDiscount', 'isActiveRewardPoints',
        'firstWeek', 'secondWeek', 'productsType', 'thirdWeek', 'currency', 'fourthWeek', 'returningReason', 'deliveryDate', 'expectedDeliveryIn', 'restaurantManager', 'seller', 'fromTime', 'toTime', 'club', 'deliveryType', 'restaurantsReviews', 'clubsReviews', 'mainBranchClub', 'nutritionSpecialist', 'nutritionSpecialistsReviews', 'listReturnedOrderItems', 'cashOnDeliveryPrice', 'requestReturning', 'isAvailableItemFood', 'deliveryName', 'idpackagesClubs', 'isGetLastOrderIsNotReview', 'insideWhereType', 'subWidthProduct',
    ];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = [
        'createdAt' => 'd-m-Y h:i:s a',
        'deliveryDate' => 'd-m-Y',
        'firstWeek.deliveryDate' => 'd-m-Y',
        'secondWeek.deliveryDate' => 'd-m-Y',
        'thirdWeek.deliveryDate' => 'd-m-Y',
        'fourthWeek.deliveryDate' => 'd-m-Y',
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
        'coupon' => Coupon::class,
        'shippingAddress' => AddressBook::class,
        'bankTransfer' => BankTransfer::class,
        'shippingMethod' => ShippingMethod::class,
        'restaurantManager' => RestaurantManager::class,
        'club' => Club::class,
        'mainBranchClub' => BranchesClub::class,
        'nutritionSpecialist' => NutritionSpecialistManger::class,
        // 'nutritionSpecialistManager' => NutritionSpecialistManger::class,
    ];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [
        'items' => OrderItem::class,
        'statusLog' => StatusLog::class,
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
     * {@inheritdoc}
     */
    protected function extend($request)
    {
        // dd($this->resource);
        // dd(StatusColor::statusColor($this->resource->status));
        if ($this->productsType == 'nutritionSpecialist') {
            $this->set('statusColor', StatusColorSpecialist::statusColor($this->resource->status));
        } else {
            $this->set('statusColor', StatusColor::statusColor($this->resource->status));
        }
        $this->set('statusIcon', StatusColor::statusIcon('order'));
        if ($this->productsType == 'food') {
            $this->set('statusText', trans("orders.statusMeals.{$this->resource->status}"));
        } elseif ($this->productsType == 'clubs') {
            $this->set('statusText', trans("orders.statusClubs.{$this->resource->status}"));
        } elseif ($this->productsType == 'nutritionSpecialist') {
            $this->set('statusText', trans("orders.statusNutritionSpecialist.{$this->resource->status}"));
        } else {
            $this->set('statusText', trans("orders.status.{$this->resource->status}"));
        }
        $this->set('finalPriceTextOrder', trans("orders.price", ['value' => $this->finalPrice]));
        // $this->set('finalPrice',$this->finalPrice);
        // 'priceText' => trans('orders.price', ['value' => $this->finalPrice]),

        $this->setCanReturnOrder();

        $this->setPaymentMethodText();

        $this->setTotals();

        $this->listReturnedItems($request);

        if ($this->productsType == 'clubs') {
            if (Carbon::now()->format('Y-m-d') > $this->items[0]['subscribeEndAt']) {
                $this->set('subscribeEndclub', true); // end subscribe
            } else {
                $this->set('subscribeEndclub', false); // lsa subscribe
            }
            $subscribeClubs = repo('clubs')->subscribeClubs($this->club['id']) ?? false;
            $clubBookings = repo('clubBookings')->clubBookings($this->club['id']) ?? false;
            $this->set('subscribeClubs', $subscribeClubs);
            $this->set('clubBookings', $clubBookings);
        }

        if ($this->productsType == 'nutritionSpecialist') {
            // dd(
            //     Carbon::now()->format('Y-m-d') <= $this->date && Carbon::now()->format('H:i') > $this->endTime,
            //     Carbon::now()->format('Y-m-d') , $this->date ,Carbon::now()->format('H:i') , $this->endTime

            // );
            if ($this->status == 'canceled' || $this->status == 'completed') {
                $this->set('cancellationReservation', true);
            } else {
                // dd(Carbon::now()->format('Y-m-d') , $this->date ,Carbon::now()->format('H:i') ,$this->endTime);

                $this->set('cancellationReservation', Carbon::now()->format('Y-m-d') == $this->date && Carbon::now()->format('H:i') > $this->endTime);
                //true  (ميقدرش يلغي الحجز)
                //false ( يقدر يلغي الحجز)
            }
            $this->set('dayText', Carbon::parse($this->date)->translatedFormat('l'));
        }
        // dd($this->items);
        if ($request->type == 'food') {
            $this->set('isAvailableItemFood', repo('productMeals')->isAvailableItemFood($this->items));
        }
    }

    /**
     * Method setPaymentMethodText
     *
     * @return void
     */
    private function setPaymentMethodText()
    {
        if (config('app.type') === 'site') {
            $paymentMethodText = PaymentMethodList::list()->where('code', $this->paymentMethod)->first();
            // dd($this->productsType);
            $this->set('paymentMethodInfo', $paymentMethodText);
        }
    }

    /**
     * get totals
     */
    private function setTotals()
    {
        $totals = [];

        // dd($this->productsType);

        if ($this->productsType == 'clubs') {
            $langText = trans('orders.subTotalTextLangClubs');
            $finalLangText = trans('orders.finalPriceTextLang');
            $shippingFeesLangText = trans('orders.shippingFeesTextLang');
        } elseif ($this->productsType == 'food') {
            $langText = trans('orders.subTotalTextLangMeals');
            $finalLangText = trans('orders.finalPriceTextLangMeals');
            $shippingFeesLangText = trans('orders.shippingFeesTextLangMeals');
        } elseif ($this->productsType == 'nutritionSpecialist') {
            $langText = trans('orders.subTotalTextLangNutritionSpecialist');
            $finalLangText = trans('orders.finalPriceTextLang');
            $shippingFeesLangText = trans('orders.shippingFeesTextLang');
        } else {
            $langText = trans('orders.subTotalTextLangProduct');
            $finalLangText = trans('orders.finalPriceTextLang');
            $shippingFeesLangText = trans('orders.shippingFeesTextLang');
        }
        // dd($this->productsType);
        if ($this->subTotal) {
            if ($this->productsType == 'clubs' || $this->productsType == 'nutritionSpecialist') {
                $totals[] = [
                    'text' => $langText,
                    'price' => $this->subTotal,
                    'priceText' => trans('orders.price', ['value' => $this->subTotal]),
                    'type' => 'subTotal',
                ];
            } else {
                $totals[] = [
                    'text' => $langText,
                    'price' => $this->originalPrice,
                    'priceText' => trans('orders.price', ['value' => $this->originalPrice]),
                    'type' => 'originalPrice',

                ];
            }
        }

        if ($this->shippingFees) {
            $totals[] = [
                'text' => $shippingFeesLangText,
                'price' => $this->shippingFees,
                'priceText' => trans('orders.price', ['value' => $this->shippingFees]),
                'type' => 'shippingFees',
            ];
        }

        if ($this->taxes) {
            $totals[] = [
                'text' => trans('orders.taxesTextLang'),
                'price' => $this->taxes,
                'priceText' => trans('orders.price', ['value' => $this->taxes]),
                'type' => 'taxes',
            ];
        }
        if ($this->wallet > 0) {
            $walletLangText = trans('orders.walletLangText');
        } else {
            $walletLangText = trans('orders.walletTextLang');
        }
        if ($this->wallet) {
            $totals[] = [
                'text' => $walletLangText,
                'price' => $this->wallet,
                'priceText' => trans('orders.price', ['value' => $this->wallet]),
                'type' => 'wallet',
            ];
        }
        // dd(($this->coupon['code']));
        if ($this->couponDiscount) {
            if ($this->productsType == 'food') {
                $totals[] = [
                    'text' => trans('orders.couponDiscountFoodTextLang'),
                    'price' => $this->couponDiscount,
                    'priceText' => trans('orders.price', ['value' => -$this->couponDiscount]) . ' (' . ($this->coupon['code']) . ') ',
                    'type' => 'couponDiscount',
                ];
            } else {
                $totals[] = [
                    'text' => trans('orders.couponDiscountTextLang'),
                    'price' => $this->couponDiscount,
                    'priceText' => trans('orders.price', ['value' => $this->couponDiscount]) . ' (' . ($this->coupon['code']) . ') ',
                    'type' => 'couponDiscount',
                ];
            }
        }

        if ($this->specialDiscount) {
            $totals[] = [
                'text' => trans('cart.specialDiscount'),
                'price' => $this->specialDiscount,
                'priceText' => trans('cart.price', ['value' => $this->specialDiscount]),
                'type' => 'specialDiscount',
            ];
        }

        if ($this->cashOnDeliveryPrice) {
            $totals[] = [
                'text' => trans('orders.cashOnDeliveryPrice'),
                'price' => (int) $this->cashOnDeliveryPrice,
                'priceText' => trans('orders.price', ['value' => (int) $this->cashOnDeliveryPrice]),
                'type' => 'cashOnDeliveryPrice',
            ];
        }
        // dd($this->finalPrice);
        // if ($this->finalPrice) {
        $totals[] = [
            'text' => $finalLangText,
            'price' => $this->finalPrice,
            'priceText' => trans('orders.price', ['value' => $this->finalPrice]),
            'type' => 'finalPriceText',
        ];
        // }

        $this->set('totals', $totals);
    }

    /**
     * Method setCanReturnOrder
     *
     * @return void
     */
    private function setCanReturnOrder()
    {
        $canReturnOrder = $this->resource->status === OrdersRepository::COMPLETED_STATUS || $this->resource->status === OrdersRepository::REQUEST_RETURNING_STATUS || $this->resource->status === OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS;
        if ($canReturnOrder) {
            $statusLog = $this->resource->statusLog;
            $lastStatus = end($statusLog);
            $returnOrderDays = repo('settings')->getSetting('ReturnedOrder', 'returnOrderDays'); // make by omar
            $timeAfterAdminFromOrderCreation = (new Carbon($lastStatus['createdAt']->toDateTime()))->addDay($returnOrderDays);
            // dd(date('Y-m-d H:i:s'), $timeAfterAdminFromOrderCreation->toDateTimeString());
            // $timeAfterAdminFromOrderCreation = '2021-10-13 18:33:56';
            if (date('Y-m-d H:i:s') > $timeAfterAdminFromOrderCreation->toDateTimeString()) {
                $canReturnOrder = false;
            }
        }

        $this->set('canReturnOrder', $canReturnOrder);
    }

    /**
     * Method setShippingDates
     *
     * @return void
     */
    private function setShippingDates()
    {
        $weeks = ['firstWeek', 'secondWeek', 'thirdWeek', 'fourthWeek'];

        $shippingDates = [];

        foreach ($weeks as $week) {
            $lang = 'en_US';

            if (app()->getLocale() == 'ar') {
                $lang = 'ar_SA';
            }

            if (!is_object($this->{$week}) && !isset($this->{$week}['deliveryDate'])) {
                continue;
            }

            if (is_object($this->{$week})) {
                $dateTime = Carbon::parse($this->{$week}->toDateTime())->locale($lang);
            } elseif (is_string($this->{$week}['deliveryDate'])) {
                $dateTime = Carbon::parse($this->{$week}['deliveryDate'])->locale($lang);
            } else {
                $dateTime = Carbon::parse($this->{$week}['deliveryDate']->toDateTime())->locale($lang);
            }

            $shippingDates[] = [
                'key' => $week,
                'text' => trans("weeks.{$week}"),
                'day' => $dateTime->dayName,
                'date' => $dateTime->format('d/m'),
                'time' => !is_object($this->{$week}) ? $this->{$week}['deliveryTime'] : $dateTime->format('h:s'),
            ];
        }

        $this->set('shippingDates', $shippingDates);

        if (user() && user()->accountType() == 'customer') {
            self::$disabledKeys = $weeks;
        }
    }

    /**
     * @param Request $request
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    private function listReturnedItems(Request $request)
    {
        // check if customer & request form listReturnedOrders
        if (user() && user()->accountType() === 'customer' && $request->route()->getActionMethod() === 'listReturnedOrders' && $this->status == OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS) {
            // requestReturning
            $items = $this->items;

            foreach ($items as $key => $item) {
                if (empty($item['requestReturning']) || $item['requestReturning'] == null) {
                    unset($items[$key]);
                }
            }

            $items = repo('products')->wrapMany($items);

            // correct
            $this->set('items', $items);

            // wrong
            // $this->items = $items;
        }
    }
}
