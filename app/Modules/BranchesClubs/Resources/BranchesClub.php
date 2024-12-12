<?php

namespace App\Modules\BranchesClubs\Resources;

use Carbon\Carbon;
use App\Modules\Clubs\Resources\Club;
use App\Modules\Cities\Resources\City;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class BranchesClub extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'location', 'workTimes'];

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
    const BOOLEAN_DATA = ['published', 'mainBranch'];

    /**
     * Integer Data
     *
     * @const array
     */
    const INTEGER_DATA = [];

    /**
     * Float Data
     *
     * @const array
     */
    const FLOAT_DATA = [];

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
    const WHEN_AVAILABLE = ['location', 'published', 'mainBranch', 'workTimes','city'];

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
        // 'club' => Club::class,
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

        $workingDays = $this['workTimes'] ?? [];

        $listworkingDays = [];
        foreach ($workingDays as $key => $workingDayTran) {
            // if ($workingDayTran['available'] == "no") continue;
            $workingDayTranList = [
                'open' => $workingDayTran['open'],
                'close' => $workingDayTran['close'],
                // 'day' => $workingDayTran['day'],
                'day' => trans('days.'.$workingDayTran['day']),
                'available' => $workingDayTran['available'],
            ];
            $listworkingDays[] = $workingDayTranList;
        }
        $this->set('workTimes', $listworkingDays);
    }
}
