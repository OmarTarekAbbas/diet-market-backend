<?php

namespace App\Modules\PackagesClubs\Repositories;

use App\Modules\Clubs\Models\Club;
use App\Modules\PackagesClubs\Models\PackagesClub as Model;
use App\Modules\PackagesClubs\Filters\PackagesClub as Filter;
use App\Modules\PackagesClubs\Resources\PackagesClub as Resource;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class PackagesClubsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'packagesClubs';

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
    const INTEGER_DATA = ['rewardPoints', 'purchaseRewardPoints', 'monthsNumber'];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = ['finalPrice'];

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
        'club' => Club::class,
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
    const WHEN_AVAILABLE_DATA = ['name', 'rewardPoints', 'purchaseRewardPoints', 'finalPrice', 'published', 'club', 'monthsNumber'];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'like' => [
            'name' => 'name.text',
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
    //     $this->clubsRepository->saveBackages($model);
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
    //     $this->clubsRepository->updateBackages($model);
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
    //     $this->clubsRepository->deleteBackages($model);
    // }

    /**
     * Method packagesClubs
     *
     * @param $id $id
     * @param $request $request
     *
     * @return void
     */
    public function packagesClubs($id, $request)
    {
        $packages = $request->package;
        foreach ($packages as $key => $package) {
            repo('packagesClubs')->create([
                'name' => $package['name'],
                'published' => (int) $package['published'],
                'finalPrice' => (float) $package['finalPrice'],
                // 'rewardPoints' => (int)$package['rewardPoints'],
                // 'purchaseRewardPoints' => (int)$package['purchaseRewardPoints'],
                'monthsNumber' => (int) $package['monthsNumber'],
                'club' => $id,
            ]);
        }
    }

    public function packagesClubsUpdate($id, $request)
    {
        $packages = $request->package;
        if ($packages) {
            foreach ($packages as $key => $package) {
                $published = ((int) $package['published'] == 1) ? true : false;
                if (isset($package['id'])) {
                    $this->packagesClubsRepository->getQuery()->where('id', (int) $package['id'])->where('club.id', $id)->update([
                        'name' => $package['name'],
                        'published' => $published,
                        'finalPrice' => (float) $package['finalPrice'],
                        // 'rewardPoints' => (int)$package['rewardPoints'],
                        // 'purchaseRewardPoints' => (int)$package['purchaseRewardPoints'],
                        'monthsNumber' => (int) $package['monthsNumber'],
                    ]);
                } else {
                    repo('packagesClubs')->create([
                        'name' => $package['name'],
                        'published' => (int) $package['published'],
                        'finalPrice' => (float) $package['finalPrice'],
                        // 'rewardPoints' => (int)$package['rewardPoints'],
                        // 'purchaseRewardPoints' => (int)$package['purchaseRewardPoints'],
                        'monthsNumber' => (int) $package['monthsNumber'],
                        'club' => $id,
                    ]);
                }
            }
        }
    }

    /**
     * Method packagesClubsDelete
     *
     * @param $id $id
     *
     * @return void
     */
    public function packagesClubsDelete($id)
    {
        $packages = $id;
        $packages = Model::where('club.id', $id)->get();
        foreach ($packages as $key => $package) {
            $package->delete();
        }
    }

    /**
     * Method getPackagesClubs
     *
     * @param int $id
     *
     * @return void
     */
    public function getPackagesClubs(int $id)
    {
        if (config('app.type') === 'site') {
            return $this->packagesClubsRepository->wrapMany($this->packagesClubsRepository->getQuery()->where('club.id', $id)->where('published', true)->get());
        } else {
            return $this->packagesClubsRepository->wrapMany($this->packagesClubsRepository->getQuery()->where('club.id', $id)->get());
        }
    }
}
