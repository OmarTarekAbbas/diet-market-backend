<?php

namespace App\Modules\ShippingMethods\Resources;

use App\Modules\Sku\Resources\Sku;
use Carbon\Carbon;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class ShippingMethod extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'name', 'expectedDeliveryIn', 'deliveryOptionId', 'type', 'cities'];

    const INTEGER_DATA = [
        'countOrders',
    ];

    const BOOLEAN_DATA = [
        'published',
    ];

    const FLOAT_DATA = [
        'totalPrice', 'shippingFees',
    ];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = ['cities', 'expectedDeliveryIn', 'published', 'totalPrice', 'shippingFees', 'countOrders', 'code', 'deliveryOptionId', 'skus'];

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
    const LOCALIZED = ['name'];

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
    const COLLECTABLE = [
        'skus' => Sku::class,
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
     */
    protected function extend($request)
    {
        $shippingAddressForCart = repo('cart')->getshippingAddressForCart();
        // dd($shippingAddressForCart->shippingAddress['city']['id']);
        if ($shippingAddressForCart->shippingAddress && $shippingAddressForCart->shippingAddress['city']) {
            foreach (collect($shippingAddressForCart->seller) as $seller) {
                $city = collect($this->cities ?? [])->where('city.id', $shippingAddressForCart->shippingAddress['city']['id'])
                    ->where('sellerCity.id', (int)$seller['city']['id'])
                    ->first();
            }

            if ($city) {
                $this->set('shippingFees', $city['shippingFees']);
                $this->set('expectedDeliveryIn', $city['expectedDeliveryIn']);
                $this->set('shippingFeesText', trans('cart.price', ['value' => $city['shippingFees']]));
                $expectedDeliveryInExplode = explode("-", $city['expectedDeliveryIn']);
                $expectedDeliveryInExplodeFirst = $expectedDeliveryInExplode[0];
                $expectedDeliveryInExplodeTwo = $expectedDeliveryInExplode[1];
                $expectedDeliveryInAverage = ($expectedDeliveryInExplodeFirst + $expectedDeliveryInExplodeTwo) / 2;
                $expectedDeliveryInTime = Carbon::now()->addDay(round($expectedDeliveryInAverage))->format('Y-m-d');
                $this->set('expectedDeliveryInAverage', round($expectedDeliveryInAverage));
                $this->set('expectedDeliveryInTime', $expectedDeliveryInTime);
            }else{
                $this->set('expectedDeliveryInTime', 2);
            }
            
            // if ($city && $city['expectedDeliveryIn']) {
            //     $expectedDeliveryInExplode = explode("-", $city['expectedDeliveryIn']);
            //     $expectedDeliveryInExplodeFirst = $expectedDeliveryInExplode[0];
            //     $expectedDeliveryInExplodeTwo = $expectedDeliveryInExplode[1];
            //     $expectedDeliveryInAverage = ($expectedDeliveryInExplodeFirst + $expectedDeliveryInExplodeTwo) / 2;
            //     $expectedDeliveryInTime = Carbon::now()->addDay(round($expectedDeliveryInAverage))->format('Y-m-d');
            //     $this->set('expectedDeliveryInAverage', round($expectedDeliveryInAverage));
            // } else {
            //     $expectedDeliveryInTime = Carbon::now()->addDay(2)->format('Y-m-d');
            // }
            // $this->set('expectedDeliveryInTime', $expectedDeliveryInTime);
        }

        if (user() && user()->accountType() == 'customer') {
            static::disable('cities');
        }
    }
}
