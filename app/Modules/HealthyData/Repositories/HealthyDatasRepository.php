<?php

namespace App\Modules\HealthyData\Repositories;

use App\Modules\General\Helpers\Visitor;
use App\Modules\DietTypes\Models\DietType;
use App\Modules\HealthyData\Models\HealthyDatum as Model;
use App\Modules\HealthyData\Filters\HealthyDatum as Filter;
use App\Modules\HealthyData\Resources\HealthyDatum as Resource;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class HealthyDatasRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'healthyDatas';

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
    const DATA = ['healthInfo', 'specialDiet', 'type', 'specialDietGrams', 'specialDietPercentage', 'customerDeviceId'];

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
    const INTEGER_DATA = ['customerId', 'dietTypes'];

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
    //'id', 'healthInfo','dietTypes','type','specialDiet','customerId'

    const DOCUMENT_DATA = [
        // 'dietTypes' => DietType::class

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
    const WHEN_AVAILABLE_DATA = ['healthInfo', 'specialDiet', 'type', 'specialDietGrams', 'specialDietPercentage', 'published', 'customerDeviceId'];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'boolean' => ['published'],
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
        $this->specialDiet($model, $request);
        // get user
        $customer = user();
        if ($customer) {
            $model->customerId = $customer->id;
        } else {
            // get Visitor id
            $model->customerDeviceId = Visitor::getDeviceId();
        }
        if ((int) $request->customerId) {
            $model->customerId = (int) $request->customerId;
        }
        // dd($model->customerId);
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
     * Method specialDiet
     *
     * @param $model $model
     * @param $request $request
     * MAKE special Diet
     * @return void
     */
    public function specialDiet($model, $request)
    {
        if ($request->dietTypes) {
            $model->specialDietGrams = null;
            $model->specialDietPercentage = null;
        } else {
            $model->dietTypes = 0;
            $this->specialDietPercentageAndGrams($model, $request);
        }
    }

    /**
     * Method calculatorCalories
     *
     * @param $fat [float]
     * @param $protein 
     * @param $carbohydrates
     *
     * @return float
     */
    public function calculatorCalories($fat, $protein, $carbohydrates): float
    {
        return ($fat * 9) + ($protein * 4) + ($carbohydrates * 4);
    }

    /**
     * Method specialDietPercentageAndGrams
     *
     * @param $model $model
     * @param $request $request
     *special Diet Percentage And Grams
     * @return void
     */
    public function specialDietPercentageAndGrams($model, $request)
    {
        if ($request->specialDiet) {
            if ($request->type == 'grams') {
                $calories = $this->calculatorCalories($request->specialDiet['fat'], $request->specialDiet['protein'], $request->specialDiet['carbohydrates']);
                $model->specialDietGrams = [
                    'fat' => round($request->specialDiet['fat']),
                    'protein' => round($request->specialDiet['protein']),
                    'carbohydrates' => round($request->specialDiet['carbohydrates']),
                    'calories' => round($calories),
                ];

                $model->specialDietPercentage = [
                    'fat' => round((($request->specialDiet['fat'] * 9) / $calories * 100)),
                    'protein' => round((($request->specialDiet['protein'] * 4) / $calories * 100)),
                    'carbohydrates' => round((($request->specialDiet['carbohydrates'] * 4) / $calories * 100)),
                    'calories' => round($calories),
                ];
            } else {
                $calories = $request->specialDiet['calories'];
                $model->specialDietPercentage = [
                    'fat' => round($request->specialDiet['fat']),
                    'protein' => round($request->specialDiet['protein']),
                    'carbohydrates' => round($request->specialDiet['carbohydrates']),
                    'calories' => round($calories),
                ];

                $model->specialDietGrams = [
                    'fat' => round((($calories * $request->specialDiet['fat'] / 100) / 9)),
                    'protein' => round((($calories * $request->specialDiet['protein'] / 100) / 4)),
                    'carbohydrates' => round((($calories * $request->specialDiet['carbohydrates'] / 100) / 4)),
                    'calories' => round($calories),
                ];
            }
        }
    }

    /**
     * Method countUseDite
     *
     * @param $id $id
     *
     * @return void
     */
    public function countUseDite(int $id)
    {
        return $this->getQuery()->where('dietTypes', $id)->count();
    }

    /**
     * Method removeDite
     *
     * @param int $id
     *
     * @return void
     */
    public function removeDite(int $id)
    {
        $removeDites = Model::where('dietTypes', $id)->get();
        foreach ($removeDites as $key => $removeDite) {
            $removeDite->delete();
        }
    }

    /**
     * Method removeCustomer
     *
     * @param int $id
     *
     * @return void
     */
    public function removeCustomer(int $id)
    {
        return Model::where('customerId', $id)->delete();
    }
}
