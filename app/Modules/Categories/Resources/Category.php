<?php

namespace App\Modules\Categories\Resources;

use App\Modules\Products\Resources\Product;
use App\Modules\Restaurants\Resources\Restaurant;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class Category extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id','metaTag', 'KeyWords'];

    /**
     * String Data
     *
     * @const array
     */
    const STRING_DATA = ['type', 'color'];

    /**
     * Boolean Data
     *
     * @const array
     */
    const BOOLEAN_DATA = ['published'];

    /**
     * Data that has multiple values based on locale codes
     * Mostly this is used with mongodb driver
     *
     * @const array
     */
    const LOCALIZED = ['name', 'slug', 'description'];

    /**
     * Object Data
     *
     * @const array
     */
    const OBJECT_DATA = [];

    /**
     * Float Data
     *
     * @const array
     */
    const FLOAT_DATA = [];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = [
        'name', 'slug', 'description', 'image', 'published', 'description',
        //  'restaurant',
        'type', 'color','products','metaTag', 'KeyWords'
    ];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = [];

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
    const RESOURCES = [
        // 'restaurant' => Restaurant::class
    ];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [
        'products' => Product::class,
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
        // repo('categories')->getCategoryPublished();
    }
}
