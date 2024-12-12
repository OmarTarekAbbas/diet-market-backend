<?php

namespace App\Modules\BranchesClubs\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Modules\Clubs\Models\Club;
use App\Modules\Cities\Models\City;
use App\Modules\General\Helpers\Visitor;
use App\Modules\BranchesClubs\Models\BranchesClub as Model;
use App\Modules\BranchesClubs\Filters\BranchesClub as Filter;
use App\Modules\BranchesClubs\Resources\BranchesClub as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class BranchesClubsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'branchesClubs';

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
    const DATA = ['workTimes'];

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
    const FLOAT_DATA = [];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = ['published', 'mainBranch'];

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
        'club' => Club::class,
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
    const WHEN_AVAILABLE_DATA = ['location', 'published', 'mainBranch', 'club', 'workTimes', 'city'];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [];

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
        if ($request->mainBranch == true) {
            $branchesClubs = Model::where('mainBranch', true)->where('club.id', $request->club)->first();
            if ($branchesClubs) {
                $branchesClubs->mainBranch = false;
                $branchesClubs->save();
            }
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
     * Method onSave
     *
     * @param $model $model
     * @param $request $request
     *
     * @return void
     */
    // public function onSave($model, $request)
    // {
    //     $this->clubsRepository->saveBranch($model);
    // }

    // /**
    //  * Method onUpdate
    //  *
    //  * @param $model $model
    //  * @param $request $request
    //  * @param $oldModel $oldModel
    //  *
    //  * @return void
    //  */
    // public function onUpdate($model, $request, $oldModel)
    // {
    //     $this->clubsRepository->updateBranch($model);
    // }

    // /**
    //  * {@inheritdoc}
    //  */
    // /**
    //  * Method onDelete
    //  *
    //  * @param $model $model
    //  * @param $id $id
    //  *
    //  * @return void
    //  */
    // public function onDelete($model, $id)
    // {
    //     $this->clubsRepository->deleteBranch($model);
    // }

    /**
     * Method checkAvailable
     *
     * @param $branchId $branchId
     * @param $date $date
     *
     * @return boolean
     */
    public function checkAvailable($branchId, $date, $time)
    {
        $branch = $this->getModel($branchId);

        if (!$branch) {
            return false;
        }

        $currentDay = Carbon::parse($date)->locale('en')->format('l');

        $workingDays = $branch->workTimes ?? [];

        $time = Carbon::parse($time);

        foreach ($workingDays as $workingDay) {
            if (Str::lower($currentDay) == Str::lower($workingDay['day'])) {
                if ($time->between($workingDay['open'], $workingDay['close']) && $workingDay['available'] == "yes") {
                    return true;

                    break;
                } else {
                    return false;

                    break;
                };
            }
        }

        return false;
    }

    /**
     * Method getBrancheMainBranch
     *
     * @param $id $id
     *
     * @return void
     */
    public function getBrancheMainBranch($id)
    {
        return $this->branchesClubsRepository->getQuery()->where('id', (int) $id)->where('mainBranch', true)->first();
    }

    /**
     * Method getBrancheMainBranch
     *
     * @param $id $id
     *
     * @return void
     */
    public function getBranchClub($id)
    {
        return $this->branchesClubsRepository->getQuery()->where('id', (int) $id)->first();
    }

    /**
     * Method branchesClubs
     *
     * @param $id $id
     * @param $request $request
     *
     * @return void
     */
    public function branchesClubs($id, $request)
    {
        $branches = $request->branches;
        foreach ($branches as $key => $branche) {
            repo('branchesClubs')->create([
                'workTimes' => $branche['workTimes'],
                'published' => $branche['published'],
                'mainBranch' => $branche['mainBranch'],
                'location' => $branche['location'],
                'city' => $branche['city'],
                'club' => $id,
            ]);
        }
    }

    /**
     * Method onSave
     *
     * @param $model $model
     * @param $request $request
     *
     * @return void
     */
    public function onSave($model, $request)
    {
        $this->clubsRepository->saveBranch($model);
    }

    // public function onUpdate($model, $request, $oldModel)
    // {
    //     $this->clubsRepository->saveBranch($model);
    // }

    /**
     * Method branchesClubsUpdate
     *
     * @param $id $id
     * @param $request $request
     *
     * @return void
     */
    public function branchesClubsUpdate($id, $request)
    {
        $branches = $request->branches;

        if ($branches) {
            foreach ($branches as $key => $branche) {
                $mainBranch = ((int) $branche['mainBranch'] == 1) ? true : false;
                $published = ((int) $branche['published'] == 1) ? true : false;

                if (isset($branche['id'])) {
                    $branchesClubs = $this->branchesClubsRepository->getQuery()->where('id', (int) $branche['id'])->where('club.id', $id)->first();
                    $brancheLocation = [
                        'type' => 'Point',
                        'coordinates' => [(float) $branche['location']['lat'], (float) $branche['location']['lng']],
                        'address' => $branche['location']['address'] ?? null,
                    ];
                    $city = $this->citiesRepository->get((int) $branche['city'] ?? $branchesClubs->city);
                    $branchesClubs->workTimes = $branche['workTimes'];
                    $branchesClubs->published = $published;
                    $branchesClubs->mainBranch = $mainBranch;
                    $branchesClubs->location = $brancheLocation;
                    $branchesClubs->city = $city->sharedInfo();
                    // $branchesClubs->save();
                    if ($branchesClubs->save()) {
                        $this->clubsRepository->saveBranch($branchesClubs);
                    }
                } else {
                    repo('branchesClubs')->create([
                        'workTimes' => $branche['workTimes'],
                        'published' => $branche['published'],
                        'mainBranch' => $branche['mainBranch'],
                        'location' => $branche['location'],
                        'city' => $branche['city'],
                        'club' => $id,
                    ]);
                }


                if ($mainBranch == true) {
                    $branchesClubs = $this->branchesClubsRepository->getQuery()->where('mainBranch', true)->where('club.id', $id)->where('id', '!=', (int) $branche['id'])->first();
                    if ($branchesClubs) {
                        $branchesClubs->mainBranch = false;
                        $branchesClubs->save();
                    }
                }
            }
        }
    }

    /**
     * Method branchesClubsDelete
     *
     * @param $id $id
     *
     * @return void
     */
    public function branchesClubsDelete($id)
    {
        $branches = $id;
        $branches = Model::where('club.id', $id)->get();
        foreach ($branches as $key => $branche) {
            $branche->delete();
        }
    }

    /**
     * Method getBranchesClubs
     *
     * @param int $id
     *
     * @return void
     */
    public function getBranchesClubs(int $id)
    {
        if (config('app.type') === 'site') {
            $km = $this->settingsRepository->getSetting('club', 'searchArea') ?? (int) env('SEARCH_AREA');
            $customer = user();
            if (!$customer) {
                $customer = $this->guestsRepository->getByModel('customerDeviceId', Visitor::getDeviceId());
            }

            return $this->branchesClubsRepository->getQuery()->where('club.id', $id)->where('published', true)->whereLocationNear('location', [(float) $customer->location['coordinates'][0] /* latitude */, (float) $customer->location['coordinates'][1]/* longitude */], $km)->get();
        } else {
            return $this->branchesClubsRepository->getQuery()->where('club.id', $id)->get();
        }
    }

    /**
     * Method updateCities
     *
     * @param $country $country
     *
     * @return void
     */
    public function updateCities($city)
    {
        Model::where('city.id', $city->id)->update([
            'city' => $city->sharedInfo(),
        ]);
    }

    /**
     * Method deleteCities
     *
     * @param $city $city
     *
     * @return void
     */
    public function deleteCities($city)
    {
        Model::where('city.id', $city->id)->delete();
    }
}
