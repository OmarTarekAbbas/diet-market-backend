<?php

namespace App\Modules\Stores\Repositories;

use App\Modules\Stores\Models\Store as Model;
use App\Modules\Stores\Filters\Store as Filter;
use App\Modules\Stores\Resources\Store as Resource;
use App\Modules\ShippingMethods\Models\ShippingMethod;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class StoresRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'stores';

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
    const DATA = ['name', 'description', 'commercialRecordId', 'metaTag', 'KeyWords'];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = ['logo', 'commercialRecordImage'];

    /**
     * Auto fill the following columns as arrays directly from the request
     * It will encoded and stored as `JSON` format,
     * it will be also auto decoded on any database retrieval either from `list` or `get` methods
     *
     * @const array
     */
    const ARRAYBLE_DATA = ['selfShipping'];

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
    const FLOAT_DATA = ['profitRatio'];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = ['published'];

    /**
     * Geo Location data
     *
     * @const array
     */
    const LOCATION_DATA = ['location'];

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
        // 'shippingMethods' => ShippingMethod::class
    ];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = [
        'name', 'description', 'commercialRecordId', 'logo', 'commercialRecordImage', 'published', 'location', 'profitRatio', 'metaTag', 'KeyWords'
    ];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'boolean' => ['published'],

        'inInt' => [
            'id',
        ],
        'like' => [
            'name' => 'name.text',
        ],
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
    const PAGINATE = true;

    /**
     * Number of items per page in pagination
     * If set to null, then it will taken from pagination configurations
     *
     * @const int|null
     */
    const ITEMS_PER_PAGE = 15;

    /**
     * Set any extra data or columns that need more customizations
     * Please note this method is triggered on create or update call
     *
     * @param   mixed $model
     * @param   \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
        // if ($request->selfShipping) {
        //     $cities = [];

        //     foreach ($request->selfShipping as $city) {
        //         $cities[] = [
        //             'city' => $this->citiesRepository->sharedInfo((int) $city['city']),
        //             'shippingFees' => (float) $city['shippingFees'],
        //             'expectedDeliveryIn' => $city['expectedDeliveryIn']
        //         ];
        //     }

        //     $model->selfShipping = $cities;
        // }
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($location = $this->option('location')) {
            $km = $this->settingsRepository->getSetting('store', 'searchArea') ?? 50;
            $this->query->whereLocationNear('location', [(float) $location['lat'] /* latitude */, (float) $location['lng']/* longitude */], $km);
        }
    }

    /**
     * calculate Total Points
     *
     * @param int $storeId
     */
    public function calculateTotalPoints(int $storeId)
    {
        $store = $this->getModel($storeId);
        $store->update([
            'totalPoints' => $store->totalPoints += $store->points,
        ]);
    }

    /**
     * update Rate
     *
     * @param int $storeId
     * @param float $avg
     */
    public function updateRate(int $storeId, float $avg)
    {
        $store = $this->getModel($storeId);
        $store->update([
            'rating' => $avg,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function onSave($model, $request, $oldModel = null)
    {
        // if ($oldModel) {
        //     $this->cartRepository->updateStore($model);
        //     $this->productsRepository->updateStore($model);
        // }
    }

    /**
     * @param int $storeId
     */
    public function updateTransaction(int $storeId)
    {
        $store = $this->getModel($storeId);

        // dd($store);
        $type = 'products';
        $store->transaction = [
            'totalRequired' => $totalRequired = $this->transactionsRepository->getTotalRequired($storeId, $type),

            'totalOrder' => $this->transactionsRepository->getTotalOrder($storeId, $type),

            'profitRatio' => $this->transactionsRepository->getProfitRatio($storeId, $type),
        ];

        $store->save();
    }

    /**
     * update rating record
     * @return void
     */
    public function updateRating($id, $rating): void
    {
        $provider = $this->getModel($id);
        if ($provider) {
            $provider->rating = $rating;
            $provider->save();
        }
    }

    /**
     * Method deleteStoreOrder
     *
     * @param $id $id
     *
     * @return void
     */
    public function deleteStoreOrder($id)
    {
        return $this->ordersRepository->getQuery()->where('seller.store.id', $id)->first();
    }


    /**
     * It adds a warehouse to the OTO system.
     * 
     * @param model The model that is being created.
     * @param request The request object.
     * @param OTO oto is the object of the OTO class
     */
    // public function onCreate($model, $request)
    // {
    //     $oto = App::make(OTO::class);
    //     $responseGetGovernorate = $oto->addWarehouse($model);
    // }

    /**
     * Method onUpdate
     *
     * @param $model $model
     * @param $request $request
     * @param $oldModel $oldModel
     *
     * @return void
     */
    public function onUpdate($model, $request, $oldModel)
    {
        $this->storeManagersRepository->updateStoreManagers($model);
    }

    /**
     * {@inheritdoc}
     */
    public function onDelete($model, $id)
    {
        $this->storeManagersRepository->deleteStoreManagers($model);
    }
}
