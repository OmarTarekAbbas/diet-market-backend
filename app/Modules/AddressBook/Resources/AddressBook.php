<?php

namespace App\Modules\AddressBook\Resources;

use App\Modules\Cities\Resources\City;
use App\Modules\Countries\Resources\Country;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class AddressBook extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = [
        'id', 'firstName', 'lastName', 'email', 'address', 'phoneNumber', 'buildingNumber', 'flatNumber', "verificationCode", 'floorNumber', 'type', 'specialMark', 'isPrimary', 'verified','district','location',
    ];

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
        'country' => Country::class,
        'city' => City::class,
        //        'district' => District::class,
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
}
