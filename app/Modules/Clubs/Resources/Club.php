<?php

namespace App\Modules\Clubs\Resources;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Modules\Cities\Resources\City;
use App\Modules\BranchesClubs\Resources\BranchesClub;
use App\Modules\PackagesClubs\Resources\PackagesClub;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class Club extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'mainBranchClub', 'metaTag', 'KeyWords'];

    /**
     * String Data
     *
     * @const array
     */
    const STRING_DATA = ['gender'];

    /**
     * Boolean Data
     *
     * @const array
     */
    const BOOLEAN_DATA = ['published', 'isBusy', 'bookAheadOfTime'];

    /**
     * Integer Data
     *
     * @const array
     */
    const INTEGER_DATA = ['commercialRegisterNumber', 'rating', 'totalRating'];

    /**
     * Float Data
     *
     * @const array
     */
    const FLOAT_DATA = ['profitRatio', 'TotalOrders', 'profitRatioClub', 'profitRatioDiteMarket'];

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
    const WHEN_AVAILABLE = ['name', 'logo', 'aboutClub',  'images', 'published', 'commercialRegisterNumber', 'commercialRegisterImage', 'mainBranchClub', 'isBusy', 'cover', 'city', 'bookAheadOfTime', 'TotalOrders', 'profitRatioClub', 'profitRatioDiteMarket', 'slug','metaTag', 'KeyWords'];

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
    const LOCALIZED = ['name', 'aboutClub', 'slug'];

    /**
     * List of assets that will have a full url if available
     */
    const ASSETS = ['logo', 'images', 'cover', 'commercialRegisterImage'];

    /**
     * Data that will be returned as a resources
     *
     * i.e [city => CityResource::class],
     * @const array
     */
    const RESOURCES = [
        'city' => City::class,
        'mainBranchClub' => BranchesClub::class,
    ];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [
        'branches' => BranchesClub::class,
        'package' => PackagesClub::class,

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
        $this->isOpen();
    }

    /**
     * Method isOpen
     *
     * @return void
     */
    public function isOpen()
    {
        $currentDay = Carbon::now()->locale('en')->format('l');
        $workingDays = $this->mainBranchClub['workTimes'] ?? [];
        // dd($workingDays);

        $time = Carbon::now();
        foreach ($workingDays as $workingDay) {
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
        if ($this->transaction) {
            $this->set('TotalOrders', $this->transaction['totalOrder'] ?? 0);
            $this->set('profitRatioClub', $this->transaction['totalRequired'] ?? 0);
            $this->set('profitRatioDiteMarket', $this->transaction['profitRatio'] ?? 0);
            $this->set('paySeller', $this->transaction['totalRequired'] ?? 0);
            $this->set('payDiet', $this->transaction['profitRatio']) ?? 0;
        }
    }
}
