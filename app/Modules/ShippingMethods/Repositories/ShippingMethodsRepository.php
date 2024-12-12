<?php

namespace App\Modules\ShippingMethods\Repositories;

use App\Modules\ShippingMethods\Models\ShippingMethod as Model;
use App\Modules\ShippingMethods\Filters\ShippingMethod as Filter;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;

use App\Modules\ShippingMethods\Resources\ShippingMethod as Resource;
use App\Modules\Sku\Models\Sku;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class ShippingMethodsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'shippingMethods';

    /**
     * Model class name
     *
     * @const string
     */
    const MODEL = Model::class;

    /**
     * Resource class name
     *
     * @const string
     */
    const RESOURCE = Resource::class;

    /**
     * Set the columns of the data that will be auto filled in the model
     *
     * @const array
     */
    const DATA = [
        'name', 'type', 'deliveryOptionId'
    ];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = ['image'];

    /**
     * Auto fill the following columns as arrays directly from the request
     * It will encoded and stored as `JSON` format,
     * it will be also auto decoded on any database retrieval either from `list` or `get` methods
     *
     * @const array
     */
    const ARRAYBLE_DATA = [];

    /**
     * Set columns list of integers values.
     *
     * @cont array
     */
    const INTEGER_DATA = [];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = [];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = [
        'published',
    ];

    /**
     * Set columns list of date values.
     *
     * @cont array
     */
    const DATE_DATA = [];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel::class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [
        'skus' => Sku::class,
    ];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = ['cities', 'expectedDeliveryIn', 'published', 'totalPrice', 'shippingFees', 'name', 'type', 'deliveryOptionId', 'skus'];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'boolean' => ['published'],
        'like' => [
            'name' => 'name.text',
        ],
        '=' => ['type'],
        'int' => ['id'],
    ];

    /**
     * Set of the parents repositories of current repo
     *
     * @const array
     */
    const CHILD_OF = [];

    /**
     * Set of the children repositories of current repo
     *
     * @const array
     */
    const PARENT_OF = [];

    /**
     * Set all filter class you will use in this module
     *
     * @const array
     */
    const FILTERS = [
        Filter::class,
    ];

    /**
     * Determine wether to use pagination in the `list` method
     * if set null, it will depend on pagination configurations
     *
     * @const bool
     */
    const PAGINATE = false;

    /**
     * Number of items per page in pagination
     * If set to null, then it will taken from pagination configurations
     *
     * @const int|null
     */
    const ITEMS_PER_PAGE = null;

    /**
     * Set any extra data or columns that need more customizations
     * Please note this method is triggered on create or update call
     *
     * @param mixed $model
     * @param \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
        $cities = [];
        foreach ($request->cities as $city) {
            $cities[] = [
                'sellerCity' => $this->citiesRepository->sharedInfo((int) $city['sellerCities']),
                'city' => $this->citiesRepository->sharedInfo((int) $city['city']),
                'shippingFees' => (float) $city['shippingFees'],
                'expectedDeliveryIn' => $city['expectedDeliveryIn'],
            ];
        }
        $model->cities = $cities;
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($id = $this->option('id')) {
            $this->query->where('id', (int) $id);
        }

        // if ($this->option('city')) {
        //     $city = $this->option('city');
        //     $sellerCities = $this->option('sellerCity');

        //     foreach ($sellerCities as $sellerCity) {
        //         $this->query->where('cities.city.id', (int) $city)->where('cities.sellerCity.id', (int) $sellerCity);
        //     }
        // }

        if ($skus = $this->option('skus')) {
            foreach ($skus as $sku) {
                $this->query->where('skus.name', $sku);
            }
        }
    }

    /**
     * Method getByCity
     *
     * @param int $shippingMethodId
     * @param int $cityId
     *
     * @return void
     */
    public function getByCity(int $shippingMethodId, int $cityId)
    {
        $shippingMethod = $this->getQuery()->where('id', $shippingMethodId)->where('cities.city.id', $cityId)->first();

        $city = collect($shippingMethod['cities'] ?? [])->where('city.id', $cityId)->first();

        if ($city) {
            $shippingMethod->shippingFees = $city['shippingFees'];
            $shippingMethod->expectedDeliveryIn = $city['expectedDeliveryIn'];
        }
        unset($shippingMethod['cities']);

        return $this->wrap($shippingMethod);
    }

    /**
     * Method getBySeller
     *
     * @param int $shippingMethodId
     *
     * @return void
     */
    public function getBySeller(int $shippingMethodId)
    {
        $shippingMethod = $this->getQuery()->where('id', $shippingMethodId)->first();
        unset($shippingMethod['cities']);

        return $this->wrap($shippingMethod);
    }
}
