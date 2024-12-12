<?php

namespace App\Modules\Cities\Repositories;

use App\Modules\Countries\Models\Country;
use App\Modules\Cities\Models\City as Model;
use App\Modules\Cities\Filters\City as Filter;
use App\Modules\Cities\Resources\City as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class CitiesRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'cities';

    /**
     * {@inheritDoc}
     */
    const MODEL = Model::class;

    /**
     * {@inheritDoc}
     */
    const RESOURCE = Resource::class;

    /**
     * Set the columns of the data that will be auto filled in the model
     *
     * @const array
     */
    const DATA = ['name'];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = [];

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
    const FLOAT_DATA = ['shippingFees'];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = [
        'published',
    ];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [
        'country' => Country::class,
    ];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel::class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = [];

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
        'int' => ['id'],
    ];

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
        if ($request->has('location')) {
            $location = $request->location;
            $model->location = [
                'type' => 'Point',
                'coordinates' => [(float) $location['lat'], (float) $location['lng']],
            ];
        }
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($id = $this->option('id')) {
            $this->query->whereIn('id', array_map('intval', (array) $id));
        }

        if ($published = $this->option('published')) {
            $this->query->where('published', true);
        }
        if ($country = $this->option('country')) {
            $this->query->where('country.id', (int) $country);
        }
        if ($countryPublished = $this->option('countryPublished')) {
            $this->query->where('country.published', $countryPublished);
        }
    }

    /**
     * Method updateCities
     *
     * @param $Cities $Cities
     *
     * @return void
     */
    public function updateCities($country)
    {
        Model::where('country.id', $country->id)->update([
            'country' => $country->sharedInfo(),
        ]);
    }

    /**
     * Method deleteCities
     *
     * @param $Cities $Cities
     *
     * @return void
     */
    public function deleteCities($country)
    {
        Model::where('country.id', $country->id)->delete();
    }

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
        $this->branchesClubsRepository->updateCities($model);
    }

    /**
     * Method onDelete
     *
     * @param $model $model
     * @param $id $id
     *
     * @return void
     */
    public function onDelete($model, $id)
    {
        $this->branchesClubsRepository->deleteCities($model);
    }
}
