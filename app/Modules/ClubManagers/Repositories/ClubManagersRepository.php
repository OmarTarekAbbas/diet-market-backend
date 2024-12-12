<?php

namespace App\Modules\ClubManagers\Repositories;

use Illuminate\Http\Request;
use App\Modules\Clubs\Models\Club;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

use App\Modules\Users\Traits\Auth\AccessToken;

use App\Modules\ClubManagers\Models\ClubManager;

use App\Modules\ClubManagers\Models\DeviceToken;

use App\Notifications\VerifyProviderNotification;

use App\Modules\ClubManagers\Models\ClubManager as Model;

use App\Modules\ClubManagers\Filters\ClubManager as Filter;
use App\Modules\ClubManagers\Resources\ClubManager as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class ClubManagersRepository extends RepositoryManager implements RepositoryInterface
{
    use AccessToken;

    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'clubManagers';

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
    const BOOLEAN_DATA = ['published', 'isVerified'];

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
    const WHEN_AVAILABLE_DATA = ['name', 'email', 'password', 'club', 'published'];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'like' => [
            'name' => 'name',
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
    }

    /**
     * send verification code via mail
     * @param $StoreManager
     */
    public function sendVerificationMail($clubManager)
    {
        Notification::send($clubManager, new VerifyProviderNotification($clubManager));
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

        $customer = ClubManager::where(...$filter)->first();

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
     * @param clubManager $clubManager
     * @param array $deviceOptions
     * @return void
     */
    public function addNewDeviceToken(Model $clubManager, array $deviceOptions)
    {
        if ($this->getDeviceToken($clubManager, $deviceOptions)) {
            return;
        }

        $deviceToken = new DeviceToken([
            'serviceProviderId' => $clubManager->id,
            'type' => $deviceOptions['type'],
            'token' => $deviceOptions['token'],
        ]);

        $deviceToken->save();

        $clubManager->associate($deviceToken, 'devices')->save();
    }

    /**
     * Remove device from customer
     *
     * @param clubManager $clubManager
     * @param array $deviceOptions
     * @return void
     * @throws Exception
     */
    public function removeDeviceToken(Model $clubManager, array $deviceOptions)
    {
        $deviceToken = $this->getDeviceToken($clubManager, $deviceOptions);

        if (!$deviceToken) {
            return;
        }

        $clubManager->disassociate($deviceToken, 'devices')->save();

        $deviceToken->delete();
    }

    /**
     * Get device token for the given customer and device options
     *
     * @param clubManager $clubManager
     * @param array $deviceOptions
     * @return DeviceToken|null
     */
    public function getDeviceToken(ClubManager $clubManager, array $deviceOptions): ?DeviceToken
    {
        return DeviceToken::where('token', $deviceOptions['token'])->where('serviceProviderId', $clubManager->id)->where('type', $deviceOptions['type'])->first();
    }

    /**
     * Method updateClubManagers
     *
     * @param $restaurant $restaurant
     *
     * @return void
     */
    public function updateClubManagers($club)
    {
        Model::where('club.id', $club->id)->update([
            'club' => $club->sharedInfo(),
        ]);
    }

    /**
     * Method deleteCategoryInfo
     *
     * @param $category $category
     *
     * @return void
     */
    public function deleteClubManagers($club)
    {
        Model::where('club.id', $club->id)->delete();
    }

      /**
     * Update storeManger wallet balance
     *
     * @param int $storeManger
     * @return void
     */
    public function updateWalletBalanceForProvider(int $clubManager)
    {
        $user = user() ?? $this->clubManagersRepository->getModel($clubManager);
        if ($user->accountType() === 'ClubManager' && $user->id === $clubManager) {
            $clubManager = $user;
        } else {
            $clubManager = $this->getModel($clubManager);
        }
        $clubManager->walletBalanceDeposit = round($this->walletProviderRepository->getBalanceFor($clubManager->id, 'deposit' , 'ClubManager'), 2);

        $clubManager->walletBalanceWithdraw = round($this->walletProviderRepository->getBalanceFor($clubManager->id, 'withdraw' , 'ClubManager'), 2);

        $clubManager->walletBalance = round($clubManager->walletBalanceDeposit - $clubManager->walletBalanceWithdraw, 2);
        $clubManager->save();
    }
}
