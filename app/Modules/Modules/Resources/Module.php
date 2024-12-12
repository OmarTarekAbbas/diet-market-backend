<?php

namespace App\Modules\Modules\Resources;

use App\Modules\Products\Resources\Product;
use HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class Module extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['value'];

    /**
     * String Data
     *
     * @const array
     */
    const STRING_DATA = ['name', 'type', 'app', 'layout'];

    /**
     * Boolean Data
     *
     * @const array
     */
    const BOOLEAN_DATA = ['published'];

    /**
     * Integer Data
     *
     * @const array
     */
    const INTEGER_DATA = ['id', 'sortOrder'];

    /**
     * Data that has multiple values based on locale codes
     * Mostly this is used with mongodb driver
     *
     * @const array
     */
    const LOCALIZED = ['title'];

    /**
     * Object Data
     *
     * @const array
     */
    const OBJECT_DATA = ['extra'];

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
    const WHEN_AVAILABLE = ['name', 'sortOrder', 'published', 'value', 'extra', 'app', 'layout'];

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
    const ASSETS = [];

    /**
     * Data that will be returned as a resources
     *
     * i.e [city => CityResource::class],
     * @const array
     */
    const RESOURCES = [
        'data' => ModuleData::class,
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
     * @param \Request $request
     * @return array|void
     * @throws NotFoundRepositoryException
     */
    protected function extend($request)
    {
        // if visitor user or not admin get full data
        if (!user() || (user() && user()->accountType() === 'customer')) {
            $products = [];

            switch ($this->resource->type) {
                case 'newArrivals':
                    $products = $this->setNewArrivals();

                    break;
                case 'bestSeller':
                    $products = $this->setBestSeller();

                    break;
                case 'specials':
                    $products = $this->setSpecials();

                    break;
            }

            if (!empty($products)) {
                $this->set('data.products', $products);
            }
        }
    }

    /**
     * Set new arrivals Products
     *
     * @return void
     */
    private function setNewArrivals()
    {
        return repo('products')->getNewArrivals();
    }

    /**
     * Set Best Seller Products
     *
     * @return void
     */
    private function setBestSeller()
    {
        return repo('products')->getBestSeller();
    }

    /**
     * Set Product Specials
     *
     * @return void
     */
    private function setSpecials()
    {
        return repo('products')->getSpecials();
    }
}
