<?php

namespace App\Modules\NutritionSpecialist\Repositories;

use DateTime;
use DatePeriod;
use DateInterval;
use Carbon\Carbon;

use App\Modules\Cities\Models\City;
use App\Modules\Orders\Repositories\OrdersRepository;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;
use App\Modules\NutritionSpecialist\Models\NutritionSpecialist as Model;
use App\Modules\NutritionSpecialist\Filters\NutritionSpecialist as Filter;
use App\Modules\NutritionSpecialist\Resources\NutritionSpecialist as Resource;

class NutritionSpecialistsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'nutritionSpecialists';

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
    const DATA = ['name', 'workTimes', 'metaTag', 'KeyWords'];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = ['commercialRegisterImage'];

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
    const INTEGER_DATA = ['commercialRegisterNumber', 'rewardPoints', 'purchaseRewardPoints'];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = ['rating', 'totalRating', 'finalPrice', 'profitRatio'];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = ['published', 'isBusy'];

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
    const DOCUMENT_DATA = [
        'city' => City::class,
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
    const WHEN_AVAILABLE_DATA = ['name', 'workTimes', 'commercialRegisterImage', 'commercialRegisterNumber', 'rating', 'totalRating', 'published', 'isBusy', 'location', 'finalPrice', 'rewardPoints', 'purchaseRewardPoints', 'profitRatio', 'city', 'metaTag', 'KeyWords'];

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
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($location = $this->option('location')) {
            $km = $this->settingsRepository->getSetting('nutritionSpecialist', 'searchArea') ?: 1000;
            // dd($km);
            $this->query->whereLocationNear('location', [(float) $location['coordinates'][0] /* latitude */, (float) $location['coordinates'][1]/* longitude */], $km);
        }
    }

    /**
     * Method schedule
     *
     * @param $nutritionSpecialist $nutritionSpecialist
     *
     * @return void
     */
    public function schedule($nutritionSpecialist)
    {
        $user = user();
        foreach ($nutritionSpecialist['nutritionSpecialist']['workTimes'] as $key => $workTime) {
            if ($workTime['available'] == "no") {
                continue;
            }
            if ($workTime['close'] == '00:00') {
                $workTime['close'] = '23:59';
            }
            // $times = $times2 = [];

            $dayDate = date("Y-m-d", strtotime($workTime['day']));


            $workTimes[$key]['day'] = $workTime['day'];

            $workTimes[$key]['dayText'] = Carbon::parse($dayDate)->translatedFormat('l');

            $workTimes[$key]['date'] = $dayDate;

            $workTimes[$key]['dateText'] = Carbon::parse($dayDate)->translatedFormat('F d');
            $workTimes[$key]['month'] = Carbon::parse($dayDate)->translatedFormat('F');
            $workTimes[$key]['dayNumber'] = Carbon::parse($dayDate)->translatedFormat('d');

            $period = new DatePeriod(
                new DateTime($workTime['open']),
                new DateInterval('PT1H'),
                new DateTime($workTime['close'])
            );

            $PENDING_STATUS = OrdersRepository::PENDING_STATUS;
            $PROCESSING_STATUS = OrdersRepository::PROCESSING_STATUS;
            $COMPLETED_STATUS = OrdersRepository::COMPLETED_STATUS;

            $orders = $this->ordersRepository->getQuery()->where('nutritionSpecialist.id', (int) $nutritionSpecialist['id'])->where('date', $dayDate)->where(function ($query) use ($PENDING_STATUS, $PROCESSING_STATUS, $COMPLETED_STATUS) {
                $query->where('status', '=', $PENDING_STATUS)
                    ->orWhere('status', '=', $PROCESSING_STATUS)
                    ->orWhere('status', '=', $COMPLETED_STATUS);
            })->pluck('startTime')->toArray();

            $times = [];
            foreach ($period as $date) {
                // dd(Carbon::now()->format('Y-m-d'), Carbon::now()->format('H:i'), $date->format("A"), $dayDate);
                if ($dayDate == Carbon::now()->format('Y-m-d')) {
                    if (Carbon::now()->format('H:i') >= $date->format("H:i")) {
                        unset($times);
                    }
                }

                $times[] = [
                    'times' => $date->format("H:i"),
                    'timesTwelveHoursFormat' => trans('general.' . $date->format("h:i")) . ' ' . trans('general.' . $date->format("A")),
                    'isAvailable' => (!in_array($date->format("H:i"), $orders)) ? true : false,
                    // 'isTimeOut' => (Carbon::now()->format('H:i') >= $date->format("H:i")) ? true : false,
                ];
            }

            $workTimes[$key]['times'] = $times;
        }

        array_multisort(
            array_map('date', array_column($workTimes, 'date')),
            SORT_ASC,
            $workTimes
        );

        return $workTimes;
    }


    // /**
    //  * update Rate
    //  *
    //  * @param int $nutritionSpecialistId
    //  * @param float $avg
    //  * @param int $total
    //  */
    // public function updateRate(int $nutritionSpecialistId, float $avg, int $total)
    // {
    //     $nutritionSpecialist = $this->getModel($nutritionSpecialistId);
    //     $nutritionSpecialist->update([
    //         'rating' => $avg,
    //         'totalRating' => $total
    //     ]);
    // }

    /**
     * Method updateTransaction
     *
     * @param int $storeId
     *
     * @return void
     */
    public function updateTransaction(int $storeId)
    {
        $store = $this->getModel($storeId);
        // dd($store);
        $type = 'nutritionSpecialist';
        $store->transaction = [
            'totalRequired' => $totalRequired = $this->transactionsRepository->getTotalRequired($storeId, $type),

            'totalOrder' => $this->transactionsRepository->getTotalOrder($storeId, $type),

            'profitRatio' => $this->transactionsRepository->getProfitRatio($storeId, $type),

        ];

        $store->save();
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
        $this->nutritionSpecialistMangersRepository->updateNutritionSpecialistMangers($model);
    }

    /**
     * {@inheritdoc}
     */
    public function onDelete($model, $id)
    {
        $this->nutritionSpecialistMangersRepository->deleteNutritionSpecialistMangers($model);
    }

    /**
     * Method listNutritionSpecialistMangers
     *
     * @param $options $options
     *
     * @return void
     */
    public function listNutritionSpecialistMangers($options)
    {
        $listNutritionSpecialists = [];
        $nutritionSpecialists = $this->nutritionSpecialistsRepository->list($options);

        foreach ($nutritionSpecialists as $key => $nutritionSpecialist) {
            $nutritionSpecialistMangers = $this->nutritionSpecialistMangersRepository->getQuery()->where('nutritionSpecialist.id', (int) $nutritionSpecialist->id)->first(['id', 'email', 'name']);
            // dd($nutritionSpecialistMangers);
            $nutritionSpecialist['nutritionSpecialistMangers'] = $nutritionSpecialistMangers;
            $listNutritionSpecialists[] = $nutritionSpecialist;
        }

        return $listNutritionSpecialists;
    }

    /**
     * Method deleteClubsOrder
     *
     * @param $id $id
     *
     * @return void
     */
    public function deleteClinicOrder($id)
    {
        return $this->ordersRepository->getQuery()->where('nutritionSpecialist.nutritionSpecialist.id', $id)->where('status', 'processing')->first();
    }
}
