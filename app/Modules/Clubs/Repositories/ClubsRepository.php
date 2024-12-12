<?php

namespace App\Modules\Clubs\Repositories;

use DateTime;
use DatePeriod;
use DateInterval;
use Carbon\Carbon;
use App\Modules\Cities\Models\City;
use App\Modules\General\Helpers\Visitor;
use App\Modules\General\Services\Slugging;
use App\Modules\Clubs\Models\Club as Model;
use App\Modules\Clubs\Filters\Club as Filter;
use App\Modules\Clubs\Resources\Club as Resource;
use App\Modules\BranchesClubs\Models\BranchesClub;
use App\Modules\PackagesClubs\Models\PackagesClub;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class ClubsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'clubs';

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
    const DATA = ['name', 'aboutClub', 'mainBranchClub', 'gender', 'metaTag', 'KeyWords'];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = ['logo', 'images', 'cover', 'commercialRegisterImage'];

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
    const INTEGER_DATA = ['commercialRegisterNumber', 'rating', 'totalRating'];

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
    const BOOLEAN_DATA = ['published', 'isBusy', 'bookAheadOfTime'];

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
    const DOCUMENT_DATA = [];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel::class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [
        'branches' => BranchesClub::class,
        'package' => PackagesClub::class,
    ];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = ['name', 'aboutClub', 'branches', 'logo', 'images', 'cover', 'rating', 'totalRating', 'published', 'package', 'commercialRegisterImage', 'commercialRegisterNumber', 'mainBranchClub', 'isBusy', 'city', 'bookAheadOfTime', 'profitRatio', 'metaTag', 'KeyWords'];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'inInt' => [
            'id',
        ],
        'like' => [
            'name' => 'name.text',
            // 'gender' => 'gender'
        ],
        'boolean' => [
            'published',
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
        // dd($request->all());
        $this->setSlug($model, $request);
    }

    /**
     * Method setSlug
     *
     * @param $model $model
     * @param $request $request
     *
     * @return void
     */
    public function setSlug($model, $request)
    {
        if (!$model['slug']) {
            $slug = [];

            foreach ($request->name as $name) {
                $slug[] = [
                    'text' => $model->getId() . '/' . Slugging::make($name['text'], $name['localeCode']),
                    'localeCode' => $name['localeCode'],
                ];
            }

            $model->slug = $slug;
        }
    }

    /**
     * Method onCreate
     *
     * @param $model $model
     * @param $request $request
     *
     * @return void
     */
    public function onCreate($model, $request)
    {
        // dd($request->package);
        $this->branchesClubsRepository->branchesClubs($model->id, $request);
        $this->packagesClubsRepository->packagesClubs($model->id, $request);
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
        $this->branchesClubsRepository->branchesClubsUpdate($model->id, $request);
        $this->packagesClubsRepository->packagesClubsUpdate($model->id, $request);
        $this->clubManagersRepository->updateClubManagers($model);
        $this->ordersRepository->updateClubForOrders($model);
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
        $this->branchesClubsRepository->branchesClubsDelete($id);
        $this->packagesClubsRepository->packagesClubsDelete($id);
        $this->clubManagersRepository->deleteClubManagers($model);
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($location = $this->option('location')) {
            $km = $this->settingsRepository->getSetting('club', 'searchArea') ?? (int) env('SEARCH_AREA');

            $this->query->whereLocationNear('mainBranchClub.location', [(float) $location['coordinates'][0] /* latitude */, (float) $location['coordinates'][1]/* longitude */], $km);
        }

        if ($gender = $this->option('gender')) {
            $this->query->where(function ($query) use ($gender) {
                return $query
                    ->where('gender', $gender)
                    ->orWhere('gender', '=', 'all');
            });
        }
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    public function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Method schedule
     *
     * @param $id $id
     *
     * @return void
     */
    public function schedule($club)
    {
        $user = user();
        if (!$user) {
            $user = $this->guestsRepository->getByModel('customerDeviceId', Visitor::getDeviceId());
        }
        // dd($user );
        // $userLocation = $user->location;
        $userLocation = [
            $user['location']['coordinates'][0],
            $user['location']['coordinates'][1],
        ];
        $branches = $this->branchesClubsRepository->getQuery()->where('club.id', $club['id'])->get();
        // dd($branches);
        $newBranches = [];

        foreach ($branches as $branch) {
            $workTimes = [];

            // $branchLocation = $branch['location'];
            $branchLocation = [
                $branch['location']['coordinates'][0],
                $branch['location']['coordinates'][1],
            ];


            // $distance = $this->haversineGreatCircleDistance($userLocation['coordinates'][0], $userLocation['coordinates'][1], (float)$branchLocation['coordinates'][0],  (float) $branchLocation['coordinates'][1]);

            //key Google Map
            $key = KEY_GOOGLE_MAB;
            $URL = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$userLocation[0]},{$userLocation[1]}&destinations={$branchLocation[0]},{$branchLocation[1]}&key={$key}";
            //Method getCurlExec
            $getkm = $this->getCurlExec($URL);
            $getkm2 = trim($getkm, ' km');
            $getkm2 = intval(preg_replace('/[^\d.]/', '', $getkm2));
            $km = $this->settingsRepository->getSetting('club', 'searchArea') ?? (int) env('SEARCH_AREA');


            // dd($branch['workTimes']);
            foreach ($branch['workTimes'] as $key => $workTime) {
                if ($workTime['available'] == "no") {
                    continue;
                }

                if ($workTime['close'] == '00:00') {
                    $workTime['close'] = '23:59';
                }

                $times = $times2 = [];

                // $dayDate = date("Y-m-d", strtotime("next " . $workTime['day']));
                $dayDate = date("Y-m-d", strtotime($workTime['day']));
                // dd( $dayDate);
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

                foreach ($period as $date) {
                    $times[] = $date->format("H:i");
                    $times2[] = trans('general.' . $date->format("h:i")) . ' ' . trans('general.' . $date->format("A"));
                }


                $workTimes[$key]['times'] = $times;
                $workTimes[$key]['timesTwelveHoursFormat'] = $times2;
            }

            array_multisort(
                array_map('date', array_column($workTimes, 'date')),
                SORT_ASC,
                $workTimes
            );

            $city['id'] = $branch['city']['id'];

            if (app()->getLocale() == 'en') {
                $city['name'] = $branch['city']['name'][1]['text'];
            } else {
                $city['name'] = $branch['city']['name'][0]['text'];
            }

            if ($getkm2 - 10 > $km) { // 30Km For Admin Setting
                continue;
            } else {
                $newBranches[] = [
                    'id' => $branch['id'],
                    'city' => $city,
                    'location' => $branch['location'],
                    'distance' => (float) $getkm - 10,
                    'workTimes' => $workTimes,
                ];
            }
        }

        array_multisort(
            array_column($newBranches, 'distance'),
            SORT_ASC,
            $newBranches
        );


        return $newBranches;
    }

    /**
     * Method getCurlExec
     *
     * @param $URL $URL
     *
     * @return void
     */
    public function getCurlExec($URL)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);
        $response = curl_exec($curl);
        $decodeResponseLocation = json_decode($response, true); // Set second argument as TRUE
        $rowResponseLocation = $decodeResponseLocation['rows'];
        foreach ($rowResponseLocation as $row) {
            $elementDistance = $row['elements'];
            foreach ($elementDistance as $distance) {
                $getdistance = $distance['distance']['text'] ?? 0;
            }
        }

        return $getdistance;
    }

    // public function schedule($club)
    // {
    //     $user = user();

    //     $userLocation = $user->location;
    //     $branches = $this->branchesClubsRepository->getQuery()->where('club.id', $club['id'])->get();
    //     // dd($branches);
    //     $newBranches = [];

    //     foreach ($branches as $branch) {

    //         $workTimes = [];

    //         $branchLocation = $branch['location'];


    //         $distance = $this->haversineGreatCircleDistance($userLocation['coordinates'][0], $userLocation['coordinates'][1], (float)$branchLocation['coordinates'][0],  (float) $branchLocation['coordinates'][1]);
    //         // dd($branch['workTimes']);
    //         foreach ($branch['workTimes'] as $key => $workTime) {

    //             if ($workTime['available'] == "no") continue;

    //             if ($workTime['close'] == '00:00') {
    //                 $workTime['close'] = '23:59';
    //             }

    //             $times = $times2 = [];

    //             // $dayDate = date("Y-m-d", strtotime("next " . $workTime['day']));
    //             $dayDate = date("Y-m-d", strtotime($workTime['day']));
    //             // dd( $dayDate);
    //             $workTimes[$key]['day'] = $workTime['day'];

    //             $workTimes[$key]['dayText'] = Carbon::parse($dayDate)->translatedFormat('l');

    //             $workTimes[$key]['date'] =  $dayDate;

    //             $workTimes[$key]['dateText'] =   Carbon::parse($dayDate)->translatedFormat('F d');
    //             $workTimes[$key]['month'] =   Carbon::parse($dayDate)->translatedFormat('F');
    //             $workTimes[$key]['dayNumber'] =   Carbon::parse($dayDate)->translatedFormat('d');

    //             $period = new DatePeriod(
    //                 new DateTime($workTime['open']),
    //                 new DateInterval('PT1H'),
    //                 new DateTime($workTime['close'])
    //             );

    //             foreach ($period as $date) {
    //                 $times[] = $date->format("H:i");
    //                 $times2[] = trans('general.' . $date->format("h:i")) . ' ' . trans('general.' . $date->format("A"));
    //             }


    //             $workTimes[$key]['times'] = $times;
    //             $workTimes[$key]['timesTwelveHoursFormat'] = $times2;
    //         }

    //         array_multisort(
    //             array_map('date', array_column($workTimes, 'date')),
    //             SORT_ASC,
    //             $workTimes
    //         );

    //         $city['id'] = $branch['city']['id'];

    //         if (app()->getLocale() == 'en') {
    //             $city['name'] = $branch['city']['name'][1]['text'];
    //         } else {
    //             $city['name'] = $branch['city']['name'][0]['text'];
    //         }


    //         $newBranches[] = [
    //             'id' => $branch['id'],
    //             'city' => $city,
    //             'location' => $branch['location'],
    //             'distance' => $distance,
    //             'workTimes' => $workTimes
    //         ];
    //     }

    //     array_multisort(
    //         array_column($newBranches, 'distance'),
    //         SORT_ASC,
    //         $newBranches
    //     );


    //     return $newBranches;
    // }

    /**
     * Method saveBranch
     *
     * @param BranchesClub $branchesClub
     *
     * @return void
     */
    public function saveBranch(BranchesClub $branchesClub)
    {
        $this->createAndUpdateClubs($branchesClub);
    }

    /**
     * Method saveBackages
     *
     * @param PackagesClub $packagesClub
     *
     * @return void
     */
    public function saveBackages(PackagesClub $packagesClub)
    {
        $clubs = Model::where('id', $packagesClub['club']['id'])->get();
        foreach ($clubs as $club) {
            $club->reassociate($packagesClub, 'packagesClubs')->save();
        }
    }

    /**
     * Method updateBackages
     *
     * @param PackagesClub $packagesClub
     *
     * @return void
     */
    public function updateBackages(PackagesClub $packagesClub)
    {
        $clubs = Model::where('id', $packagesClub['club']['id'])->get();
        foreach ($clubs as $club) {
            $club->reassociate($packagesClub, 'packagesClubs')->save();
        }
    }

    /**
     * Method deleteBackages
     *
     * @param PackagesClub $packagesClub
     *
     * @return void
     */
    public function deleteBackages(PackagesClub $packagesClub)
    {
        $clubs = Model::where('id', $packagesClub['club']['id'])->get();
        foreach ($clubs as $club) {
            $club->disassociate($packagesClub, 'packagesClubs')->save();
        }
    }

    /**
     * Method createAndUpdateClubs
     *
     * @param $branchesClub $branchesClub
     *
     * @return void
     */
    public function createAndUpdateClubs($branchesClub)
    {
        // dd($branchesClub);
        $clubs = Model::where('id', $branchesClub['club']['id'])->get();
        // dd($branchesClub->mainBranch);
        foreach ($clubs as $club) {
            // $club->reassociate($branchesClub, 'branches')->save();
            if ($branchesClub->mainBranch == true) {
                $club->mainBranchClub = $branchesClub->sharedInfo();
                $club->save();
            }
        }
    }

    /**
     * update Rate
     *
     * @param int $clubId
     * @param float $avg
     * @param int $total
     */
    public function updateRate(int $clubId, float $avg, int $total)
    {
        $club = $this->getModel($clubId);
        $club->update([
            'rating' => $avg,
            'totalRating' => $total,
        ]);
    }

    /**
     * Method subscribeClubs
     *
     * @return void
     */
    // public function subscribeClubs($id)
    // {
    //     $customer = user();
    //     if ($customer['subscribeClubs']) {

    //         $subscribeClubs = $customer['subscribeClubs'];
    //         $arraySubscribeClubs = [];
    //         foreach ($subscribeClubs as $key => $subscribeClub) {
    //             $arraySubscribeClubs[] = $subscribeClub['product']['club']['id'];
    //         }
    //         $subscribeClubsCheck = null;
    //         foreach ($arraySubscribeClubs as $key => $arraySubscribeClub) {

    //             if ($arraySubscribeClub == $id) {
    //                 $subscribeClubsCheck = true;
    //             }
    //         }

    //         return $subscribeClubsCheck;
    //     }
    // }

    /**
     * Method subscribeClubs
     *
     * @param $id $id
     *
     * @return void
     */
    public function subscribeClubs($id)
    {
        $customer = user();
        if ($customer && $customer['subscribeClubs']) {
            $subscribeClubs = $customer['subscribeClubs'];
            $subscribeClubsCheck = null;
            foreach ($subscribeClubs as $key => $subscribeClub) {
                if ($subscribeClub['product']['club']['id'] == $id) {
                    if (Carbon::now()->format('Y-m-d') < $subscribeClub['subscribeEndAt']) {
                        $subscribeClubsCheck = true;
                    }
                }
            }

            return $subscribeClubsCheck;
        }
    }

    /**
     * Method subscribeClubCustomer
     *
     * @return void
     */
    public function subscribeClubCustomer()
    {
        $customer = user();
        if ($customer && $customer['subscribeClubs']) {
            $subscribeClubs = $this->orderItemsRepository->wrapMany($customer['subscribeClubs']);
            $arraySubscribeClub = [];
            foreach ($subscribeClubs as $key => $subscribeClub) {
                if (Carbon::now()->format('Y-m-d') < $subscribeClub['subscribeEndAt']) {
                    // dd($subscribeClub['club']);
                    $subscribeClub['club'] = $this->clubsRepository->wrap($this->clubsRepository->getQuery()->where('id', $subscribeClub['club'])->first());
                    $arraySubscribeClub[] = $subscribeClub;
                }
            }

            return $arraySubscribeClub;
        }
    }

    /**
     * Method listClubs
     *
     * @return void
     */
    public function listClubs($request)
    {
        $queryClub = $this->clubsRepository->getQuery();

        if ($request->has('id')) {
            $queryClub->where('id', (int) $request->id);
        }

        if ($request->has('gender')) {
            // $queryClub->where('gender', $request->gender);
            $queryClub->where(function ($query) use ($request) {
                return $query
                    ->where('gender', $request->gender)
                    ->orWhere('gender', '=', 'all');
            });
        }

        if ($request->has('name')) {
            $queryClub->whereLike('name.0.text', $request->name);
        }



        $queryClub = $queryClub->orderBy('id', 'desc')->paginate(15);

        $listClubs = $this->clubsRepository->wrapMany($queryClub->items()); 

        $clubs = [];

        foreach ($listClubs as $key => $listClub) {
            $branches = $this->branchesClubsRepository->getBranchesClubs($listClub->id);
            $packages = $this->packagesClubsRepository->getPackagesClubs($listClub->id);
            $listClub['branches'] = $branches;
            $listClub['package'] = $packages;
            $clubs[] = $listClub;
        }

        return $listClubs;
    }

    /**
     * Method getPaginateInfoReturnedOrders
     *
     * @return void
     */
    public function getPaginateInfoClubs($request)
    {
        $queryClub = $this->clubsRepository->getQuery();

        if ($request->has('id')) {
            $queryClub->where('id', (int) $request->id);
        }

        if ($request->has('published')) {
            $queryClub->where('published', (bool) $request->published);
        }

        if ($request->has('name')) {
            $queryClub->whereLike('name.0.text', $request->name);
        }

        $data = $queryClub->orderBy('id', 'desc')->paginate(15);

        return $this->paginationInfo = [
            'currentResults' => $data->count(),
            'totalRecords' => $data->total(),
            'numberOfPages' => $data->lastPage(),
            'itemsPerPage' => $data->perPage(),
            'currentPage' => $data->currentPage(),
        ];
    }

    /**
     * Method showClub
     *
     * @param $id $id
     *
     * @return void
     */
    public function showClub(int $id)
    {
        $showClub = $this->clubsRepository->get($id);
        $branches = $this->branchesClubsRepository->getBranchesClubs($id);
        $packages = $this->packagesClubsRepository->getPackagesClubs($id);
        $showClub['branches'] = $branches;
        $showClub['package'] = $packages;

        return $showClub;
    }

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
        $type = 'club';
        $store->transaction = [
            'totalRequired' => $totalRequired = $this->transactionsRepository->getTotalRequired($storeId, $type),

            'totalOrder' => $this->transactionsRepository->getTotalOrder($storeId, $type),

            'profitRatio' => $this->transactionsRepository->getProfitRatio($storeId, $type),

        ];

        $store->save();
    }

    /**
     * Method deleteClubsOrder
     *
     * @param $id $id
     *
     * @return void
     */
    public function deleteClubsOrder($id)
    {
        return $this->ordersRepository->getQuery()->where('club.id', $id)->first();
    }
}
