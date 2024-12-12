<?php

namespace App\Modules\ClubBookings\Resources;

use Carbon\Carbon;
use App\Modules\Clubs\Resources\Club;
use App\Modules\Customers\Resources\Customer;
use App\Modules\ClubBookings\Traits\StatusColor;
use App\Modules\BranchesClubs\Resources\BranchesClub;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class ClubBooking extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['name', 'status', 'time', 'phone','nextStatus','notesAccepted','notesRejected'];

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
    const BOOLEAN_DATA = [];

    /**
     * Integer Data
     *
     * @const array
     */
    const INTEGER_DATA = ['id'];

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
    const WHEN_AVAILABLE = ['notesAccepted','notesRejected'];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = [
        'date' => 'Y-m-d',
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
        'customer' => Customer::class,
        'clubBranch' => BranchesClub::class,
        'club' => Club::class,
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
        $statusColor = StatusColor::statusColor($this->status);
        $statusName = StatusColor::statusName($this->status);

        $date = $this->date->format('Y-m-d');
        $time = $this->time;

        $dateTime = Carbon::parse($date.' '.$time)->translatedFormat('l d M, g A');

        $this->set('dateTime', $dateTime);

        $this->set('statusColor', $statusColor);
        $this->set('statusName', $statusName);
    }
}
