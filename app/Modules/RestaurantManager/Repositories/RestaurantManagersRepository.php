<?php

namespace App\Modules\RestaurantManager\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Modules\Restaurants\Models\Restaurant;
use App\Modules\Users\Traits\Auth\AccessToken;
use App\Notifications\VerifyProviderNotification;
use App\Modules\RestaurantManager\Models\DeviceToken;
use App\Modules\RestaurantManager\Models\RestaurantManager;

use App\Modules\RestaurantManager\Models\RestaurantManager as Model;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;
use App\Modules\RestaurantManager\Filters\RestaurantManager as Filter;
use App\Modules\RestaurantManager\Resources\RestaurantManager as Resource;

class RestaurantManagersRepository extends RepositoryManager implements RepositoryInterface
{
    use AccessToken;

    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'restaurantManagers';

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
    const DATA = ['name', 'email', 'password', 'phoneNumber'];

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
    const FLOAT_DATA = ['walletBalance'];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = ['isVerified'];

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
        'restaurant' => Restaurant::class,

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
    const WHEN_AVAILABLE_DATA = ['name', 'email', 'password', 'restaurant'];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'like' => [
            'name' => 'name.text',
            'email' => 'email',
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
        if (!$model->id) {
            // generate 6 digits
            $model->verificationCode = mt_rand(1000, 9999);
            $model->isVerified = $request->isVerified ?: true;
            $model->published = $request->published ?: true;
            $model->walletBalance = 0;
        }

        if ($request->unitTesting) {
            $this->createDataForUnitTest($model, $request);

            return;
        }
    }

    /**
     * send verification code via mail
     * @param $StoreManager
     */
    public function sendVerificationMail($RestaurantManager)
    {
        Notification::send($RestaurantManager, new VerifyProviderNotification($RestaurantManager));
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

        $customer = RestaurantManager::where(...$filter)->first();

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
     * @param RestaurantManager $RestaurantManager
     * @param array $deviceOptions
     * @return void
     */
    public function addNewDeviceToken(Model $RestaurantManager, array $deviceOptions)
    {
        if ($this->getDeviceToken($RestaurantManager, $deviceOptions)) {
            return;
        }

        $deviceToken = new DeviceToken([
            'serviceProviderId' => $RestaurantManager->id,
            'type' => $deviceOptions['type'],
            'token' => $deviceOptions['token'],
        ]);

        $deviceToken->save();

        $RestaurantManager->associate($deviceToken, 'devices')->save();
    }

    /**
     * Remove device from customer
     *
     * @param RestaurantManager $RestaurantManager
     * @param array $deviceOptions
     * @return void
     * @throws Exception
     */
    public function removeDeviceToken(Model $RestaurantManager, array $deviceOptions)
    {
        $deviceToken = $this->getDeviceToken($RestaurantManager, $deviceOptions);

        if (!$deviceToken) {
            return;
        }

        $RestaurantManager->disassociate($deviceToken, 'devices')->save();

        $deviceToken->delete();
    }

    /**
     * Get device token for the given customer and device options
     *
     * @param RestaurantManager $RestaurantManager
     * @param array $deviceOptions
     * @return DeviceToken|null
     */
    public function getDeviceToken(RestaurantManager $RestaurantManager, array $deviceOptions): ?DeviceToken
    {
        return DeviceToken::where('token', $deviceOptions['token'])->where('serviceProviderId', $RestaurantManager->id)->where('type', $deviceOptions['type'])->first();
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
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
     * Method updateRestaurantManagers
     *
     * @param $restaurant $restaurant
     *
     * @return void
     */
    public function updateRestaurantManagers($restaurant)
    {
        Model::where('restaurant.id', $restaurant->id)->update([
            'restaurant' => $restaurant->sharedInfo(),
        ]);
    }

    /**
     * Method deleteCategoryInfo
     *
     * @param $category $category
     *
     * @return void
     */
    public function deleteRestaurantManagers($restaurant)
    {
        Model::where('restaurant.id', $restaurant->id)->delete();
    }

     /**
     * Update storeManger wallet balance
     *
     * @param int $storeManger
     * @return void
     */
    public function updateWalletBalanceForProvider(int $restaurantManager)
    {
        $user = user() ?? $this->restaurantManagersRepository->getModel($restaurantManager);
        if ($user->accountType() === 'RestaurantManager' && $user->id === $restaurantManager) {
            $restaurantManager = $user;
        } else {
            $restaurantManager = $this->getModel($restaurantManager);
        }
        $restaurantManager->walletBalanceDeposit = round($this->walletProviderRepository->getBalanceFor($restaurantManager->id, 'deposit' , 'RestaurantManager'), 2);

        $restaurantManager->walletBalanceWithdraw = round($this->walletProviderRepository->getBalanceFor($restaurantManager->id, 'withdraw' , 'RestaurantManager'), 2);

        $restaurantManager->walletBalance = round($restaurantManager->walletBalanceDeposit - $restaurantManager->walletBalanceWithdraw, 2);
        $restaurantManager->save();
    }
}
