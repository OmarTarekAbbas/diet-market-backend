<?php

namespace App\Modules\NutritionSpecialist\Resources;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Modules\Cities\Resources\City;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class NutritionSpecialist extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'location', 'workTimes', 'nutritionSpecialistMangers', 'metaTag', 'KeyWords'];

    /**
     * String Data
     *
     * @const array
     */
    const STRING_DATA = [];

    /**
     * Boolean Data
     *
     * @const array
     */
    const BOOLEAN_DATA = ['published', 'isBusy'];

    /**
     * Integer Data
     *
     * @const array
     */
    const INTEGER_DATA = ['commercialRegisterNumber', 'rewardPoints', 'purchaseRewardPoints'];

    /**
     * Float Data
     *
     * @const array
     */
    const FLOAT_DATA = ['rating', 'totalRating', 'finalPrice', 'profitRatio', 'TotalOrders', 'profitRatioNutritionSpecialists', 'profitRatioDiteMarket'];

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
    const WHEN_AVAILABLE = ['name', 'workTimes', 'commercialRegisterImage', 'commercialRegisterNumber', 'rating', 'totalRating', 'published', 'isBusy', 'location', 'finalPrice', 'rewardPoints', 'purchaseRewardPoints', 'city', 'nutritionSpecialistMangers', 'TotalOrders', 'profitRatioNutritionSpecialists', 'profitRatioDiteMarket', 'metaTag', 'KeyWords'];

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
    const ASSETS = ['commercialRegisterImage'];

    /**
     * Data that will be returned as a resources
     *
     * i.e [city => CityResource::class],
     * @const array
     */
    const RESOURCES = [
        'city' => City::class,

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
        $this->set('finalPriceText', trans('products.price', ['value' => $this->finalPrice]));
        $this->isOpen();
        if ($this->transaction) {
            $this->set('TotalOrders', $this->transaction['totalOrder'] ?? 0);
            $this->set('profitRatioNutritionSpecialists', $this->transaction['totalRequired'] ?? 0);
            $this->set('profitRatioDiteMarket', $this->transaction['profitRatio'] ?? 0);
            $this->set('paySeller', $this->transaction['totalRequired'] ?? 0);
            $this->set('payDiet', $this->transaction['profitRatio'] ?? 0);
        }
    }

    /**
     * Method isOpen
     *
     * @return void
     */
    public function isOpen()
    {
        $currentDay = Carbon::now()->locale('en')->format('l');
        $workingDays = $this->workTimes ?? [];

        $time = Carbon::now();

        foreach ($workingDays as $workingDay) {
            if ($workingDay['close'] == '00:00') {
                $workingDay['close'] = '23:59';
            }
            if (Str::lower($currentDay) == Str::lower($workingDay['day'])) {
                $this->set('openToday', [
                    'open' => $workingDay['open'],
                    'close' => $workingDay['close'],
                    'day' => $workingDay['day'],
                    'available' => $workingDay['available'],
                ]);
                if ($time->between($workingDay['open'], $workingDay['close'])) {
                    $this->set('isClosed', false);
                } else {
                    $this->set('isClosed', true);
                };
            }
        }
        $listworkingDays = [];
        foreach ($workingDays as $key => $workingDayTran) {
            if ($workingDayTran['available'] == "no") {
                continue;
            }
            $workingDayTranList = [
                'open' => $workingDayTran['open'],
                'close' => $workingDayTran['close'],
                // 'day' => $workingDayTran['day'],
                'day' => trans('days.' . $workingDayTran['day']),
                'available' => $workingDayTran['available'],
            ];
            $listworkingDays[] = $workingDayTranList;
        }
        $this->set('workTimeTrans', $listworkingDays);
    }
}
