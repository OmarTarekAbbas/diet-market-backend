<?php

namespace App\Modules\NutritionSpecialistMangers\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Modules\Users\Traits\Auth\AccessToken;
use App\Notifications\VerifyProviderNotification;
use App\Modules\NutritionSpecialistMangers\Models\DeviceToken;
use App\Modules\NutritionSpecialist\Models\NutritionSpecialist;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;
use App\Modules\NutritionSpecialistMangers\Models\NutritionSpecialistManger;
use App\Modules\NutritionSpecialistMangers\Models\NutritionSpecialistManger as Model;
use App\Modules\NutritionSpecialistMangers\Filters\NutritionSpecialistManger as Filter;
use App\Modules\NutritionSpecialistMangers\Resources\NutritionSpecialistManger as Resource;

class NutritionSpecialistMangersRepository extends RepositoryManager implements RepositoryInterface
{
    use AccessToken;

    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'nutritionSpecialistMangers';

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
    const DATA = ['name', 'email', 'phoneNumber', 'password', 'description'];

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
    const INTEGER_DATA = ['rating', 'totalRating'];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = ['walletBalance'];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = ['isVerified', 'published'];

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
        'nutritionSpecialist' => NutritionSpecialist::class,

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
    const WHEN_AVAILABLE_DATA = ['name', 'email', 'password', 'phoneNumber', 'nutritionSpecialist', 'image', 'description', 'rating', 'totalRating', 'published'];

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
        if (!$model->id) {
            // generate 6 digits
            $model->verificationCode = mt_rand(1000, 9999);
            $model->isVerified = $request->isVerified ?: true;
            $model->published = $request->published ?: true;
            $model->walletBalance = 0;
        }

        // if ($request->unitTesting) {
        //     $this->createDataForUnitTest($model, $request);
        //     return;
        // }
    }

    /**
     * send verification code via mail
     * @param $StoreManager
     */
    public function sendVerificationMail($nutritionSpecialistMangers)
    {
        Notification::send($nutritionSpecialistMangers, new VerifyProviderNotification($nutritionSpecialistMangers));
    }

    /**
     * Check if customer can login
     *
     * @param Request $request
     * @return Resource|false
     */
    public function login(Request $request)
    {
        $filter = [];

        $value = $request->email;

        $filter[] = 'email';

        $filter[] = $value;

        $customer = NutritionSpecialistManger::where(...$filter)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return false;
        }

        $accessToken = $this->generateAccessToken($customer);

        $customer->accessToken = $accessToken;

        return $this->wrap($customer);
    }

    /**
     * Add new device to customer
     * Device options contains: type: ios|android, token: string
     *
     * @param nutritionSpecialistMangers $nutritionSpecialistMangers
     * @param array $deviceOptions
     * @return void
     */
    public function addNewDeviceToken(Model $nutritionSpecialistMangers, array $deviceOptions)
    {
        if ($this->getDeviceToken($nutritionSpecialistMangers, $deviceOptions)) {
            return;
        }

        $deviceToken = new DeviceToken([
            'serviceProviderId' => $nutritionSpecialistMangers->id,
            'type' => $deviceOptions['type'],
            'token' => $deviceOptions['token'],
        ]);

        $deviceToken->save();

        $nutritionSpecialistMangers->associate($deviceToken, 'devices')->save();
    }

    /**
     * Remove device from customer
     *
     * @param nutritionSpecialistMangers $nutritionSpecialistMangers
     * @param array $deviceOptions
     * @return void
     * @throws Exception
     */
    public function removeDeviceToken(Model $nutritionSpecialistMangers, array $deviceOptions)
    {
        $deviceToken = $this->getDeviceToken($nutritionSpecialistMangers, $deviceOptions);

        if (!$deviceToken) {
            return;
        }

        $nutritionSpecialistMangers->disassociate($deviceToken, 'devices')->save();

        $deviceToken->delete();
    }

    /**
     * Get device token for the given customer and device options
     *
     * @param nutritionSpecialistMangers $nutritionSpecialistMangers
     * @param array $deviceOptions
     * @return DeviceToken|null
     */
    public function getDeviceToken(NutritionSpecialistManger $nutritionSpecialistMangers, array $deviceOptions): ?DeviceToken
    {
        return DeviceToken::where('token', $deviceOptions['token'])->where('serviceProviderId', $nutritionSpecialistMangers->id)->where('type', $deviceOptions['type'])->first();
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
            $this->query->whereLocationNear('nutritionSpecialist.location', [(float) $location['coordinates'][0] /* latitude */, (float) $location['coordinates'][1]/* longitude */], $km);


            if ($nutritionSpecialistPublished = $this->option('nutritionSpecialistPublished')) {
                $this->query->where('nutritionSpecialist.published', $nutritionSpecialistPublished);
            }
        }
    }


    // /**
    //  * Method makeDataForUnit
    //  *
    //  * @param $model $model
    //  * @param $request $request
    //  * make Data For Unit
    //  * @return void
    //  */
    // public function makeDataForUnit($model, $request)
    // {
    //     $model->name = $request->name;
    //     $model->email = $request->email;
    // }

    // /**
    //  * Method createDataForUnitTest
    //  *
    //  * @param $model $model
    //  * @param $request $request
    //  * create Data For Unit Test
    //  * @return void
    //  */
    // public function createDataForUnitTest($model, $request)
    // {
    //     if ($request->method() == 'POST') {
    //         $this->makeDataForUnit($model, $request);
    //     }

    //     if ($request->method() == 'PUT') {
    //         $this->makeDataForUnit($model, $request);
    //     }
    // }

    /**
     * Update category info on category update
     *
     * @param Category $category
     * @return void
     */
    public function updateNutritionSpecialistMangers($nutritionSpecialist)
    {
        Model::where('nutritionSpecialist.id', $nutritionSpecialist->id)->update([
            'nutritionSpecialist' => $nutritionSpecialist->sharedInfo(),
        ]);
    }

    /**
     * Method deleteCategoryInfo
     *
     * @param $category $category
     *
     * @return void
     */
    public function deleteNutritionSpecialistMangers($nutritionSpecialist)
    {
        Model::where('nutritionSpecialist.id', $nutritionSpecialist->id)->delete();
    }

    /**
     * update Rate
     *
     * @param int $nutritionSpecialistId
     * @param float $avg
     * @param int $total
     */
    public function updateRate(int $nutritionSpecialistId, float $avg, int $total)
    {
        $nutritionSpecialist = $this->getModel($nutritionSpecialistId);
        $nutritionSpecialist->update([
            'rating' => $avg,
            'totalRating' => $total,
        ]);
    }

    /**
     * Method deleteStoreOrder
     *
     * @param $id $id
     *
     * @return void
     */
    public function updateNutritionSpecialistManger($id)
    {
        return  $this->ordersRepository->getQuery()->where('nutritionSpecialistManager.id', $id)->where('status', 'processing')->first();
    }

    /**
     * Update storeManger wallet balance
     *
     * @param int $storeManger
     * @return void
     */
    public function updateWalletBalanceForProvider(int $nutritionSpecialistManger)
    {
        $user = user() ?? $this->nutritionSpecialistMangersRepository->getModel($nutritionSpecialistManger);
        if ($user->accountType() === 'NutritionSpecialistManger' && $user->id === $nutritionSpecialistManger) {
            $nutritionSpecialistManger = $user;
        } else {
            $nutritionSpecialistManger = $this->getModel($nutritionSpecialistManger);
        }
        $nutritionSpecialistManger->walletBalanceDeposit = round($this->walletProviderRepository->getBalanceFor($nutritionSpecialistManger->id, 'deposit', 'NutritionSpecialistManger'), 2);

        $nutritionSpecialistManger->walletBalanceWithdraw = round($this->walletProviderRepository->getBalanceFor($nutritionSpecialistManger->id, 'withdraw', 'NutritionSpecialistManger'), 2);

        $nutritionSpecialistManger->walletBalance = round($nutritionSpecialistManger->walletBalanceDeposit - $nutritionSpecialistManger->walletBalanceWithdraw, 2);
        $nutritionSpecialistManger->save();
    }
}
