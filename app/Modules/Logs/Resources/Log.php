<?php

namespace App\Modules\Logs\Resources;

use App\Modules\Users\Resources\User;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class Log extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id'];

    /**
     * String Data
     *
     * @const array
     */
    const STRING_DATA = ['action'];

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
    const OBJECT_DATA = [
        'data',
        'oldData',
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
    const DATES = [
        'createdAt',
        'updatedAt',
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
        'user' => User::class,
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
