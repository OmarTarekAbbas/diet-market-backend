<?php

namespace App\Modules\Items\Repositories;

use App\Modules\Sizes\Models\Size;
use App\Modules\Categories\Models\Category;
use App\Modules\Items\Models\Item as Model;
use App\Modules\Items\Filters\Item as Filter;
use App\Modules\Restaurants\Models\Restaurant;
use App\Modules\Items\Resources\Item as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class ItemsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'items';

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
    const DATA = ['name'];

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
    const FLOAT_DATA = ['protein', 'carbohydrates', 'fat', 'calories'];

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
    const LOCATION_DATA = [];

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
    const DOCUMENT_DATA = [
        'categories' => Category::class,
        'restaurant' => Restaurant::class,

    ];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel::class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [
        'sizes' => Size::class,
    ];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = ['name', 'image', 'protein', 'carbohydrates', 'fat', 'calories', 'published', 'categories', 'sizes', 'restaurant'];

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
        'inInt' => ['id'],
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
    const PAGINATE = null;

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
     * @param   mixed $model
     * @param   \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
        if ($request->unitTesting) {
            $this->createDataForUnitTest($model, $request);

            return;
        }
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
    }

    /**
     * Method calculatorCalories
     *
     * @param $fat $fat
     * @param $protein $protein
     * @param $carbohydrates $carbohydrates
     *
     * @return float
     */
    public function calculatorCalories($fat, $protein, $carbohydrates): float
    {
        return ($fat * 9) + ($protein * 4) + ($carbohydrates * 4);
    }

    /**
     * Method makeDataForUnit
     *
     * @param $model $model
     * @param $request $request
     * make Data For Unit
     * @return void
     */
    public function makeDataForUnit($model, $request)
    {
        $model->name = $request->name;
        $model->protein = $request->protein;
        $model->carbohydrates = $request->carbohydrates;
        $model->fat = $request->fat;
        $model->calories = $this->calculatorCalories($model->fat, $model->protein, $model->carbohydrates);
    }

    /**
     * Method createDataForUnitTest
     *
     * @param $model $model
     * @param $request $request
     * create Data For Unit Test
     * @return void
     */
    public function createDataForUnitTest($model, $request)
    {
        if ($request->method() == 'POST') {
            $this->makeDataForUnit($model, $request);
        }

        if ($request->method() == 'PUT') {
            $this->makeDataForUnit($model, $request);
        }
    }
}
