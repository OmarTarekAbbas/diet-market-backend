<?php

namespace App\Modules\StoreManagers\Repositories;

use App\Modules\Services\Gateways\OTO;
use Exception;
use Illuminate\Http\Request;
use App\Modules\Cities\Models\City;
use App\Modules\Stores\Models\Store;
use Illuminate\Support\Facades\Hash;
use App\Modules\Countries\Models\Country;
use Illuminate\Support\Facades\Notification;
use App\Modules\Users\Traits\Auth\AccessToken;
use App\Modules\StoreManagers\Models\DeviceToken;
use App\Notifications\VerifyProviderNotification;
use App\Modules\StoreManagers\Models\StoreManager;
use App\Modules\StoreManagers\Models\StoreManager as Model;
use App\Modules\StoreManagers\Filters\StoreManager as Filter;
use App\Modules\StoreManagers\Resources\StoreManager as Resource;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;
use Illuminate\Support\Facades\App;

class StoreManagersRepository extends RepositoryManager implements RepositoryInterface
{
    use AccessToken;

    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'storeManagers';

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
    const DATA = ['firstName', 'lastName', 'email', 'phoneNumber', 'password', 'address'];

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
    const INTEGER_DATA = ['totalRating'];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = ['rating', 'walletBalance'];

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
        'store' => Store::class,
        'country' => Country::class,
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
    const WHEN_AVAILABLE_DATA = [
        'firstName', 'lastName', 'email', 'phoneNumber', 'password', 'address', 'isVerified', 'store',
        'country', 'city', 'totalRating', 'rating',
    ];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'boolean' => ['published'],
        'int' => [
            'id',
            'group' => 'group.id',
        ],
        'like' => [
            'phoneNumber',
            // 'name' => ['firstName', 'lastName'],
            'firstName',
            'lastName',
            'email',
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
        if (!$model->id) {
            // generate 6 digits
            $model->verificationCode = mt_rand(1000, 9999);
            // $model->totalRating = 1;
            $model->isVerified = $request->isVerified ?: true;
            $model->published = $request->published ?: true;
            $model->walletBalance = 0;
            $model->name = $request->firstName . $request->lastName;
        }

        // if (!empty($request->store)) {
        //     $store = $request->store;

        //     if (!empty($store['id'])) {
        //         // we're updating
        //         $storeModel = $this->storesRepository->update($store['id'], $store);
        //     } else {
        //         //we are creating
        //         $storeModel = $this->storesRepository->create($store);
        //     }

        //     $model->store = $storeModel->sharedInfo();
        // }
    }

    /**
     * {@inheritDoc}
     */
    public function onCreate($model, $request)
    {
        $oto = App::make(OTO::class);
        $oto->addWarehouse($model);
    }

    /**
     * {@inheritDoc}
     */
    public function onSave($model, $request, $oldModel = null)
    {
        // if ($oldModel) {
        //     $this->productsRepository->updateStoreManager($model);
        // }
    }

    /**
     * {@inheritDoc}
     */
    public function onDelete($id, $model)
    {
        $this->productsRepository->deleteManagerProducts($id);
    }

    /**
     * send verification code via mail
     * @param $StoreManager
     */
    public function sendVerificationMail($StoreManager)
    {
        Notification::send($StoreManager, new VerifyProviderNotification($StoreManager));
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


        $customer = StoreManager::where(...$filter)->first();

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
     * @param StoreManager $StoreManager
     * @param array $deviceOptions
     * @return void
     */
    public function addNewDeviceToken(Model $StoreManager, array $deviceOptions)
    {
        if ($this->getDeviceToken($StoreManager, $deviceOptions)) {
            return;
        }

        $deviceToken = new DeviceToken([
            'serviceProviderId' => $StoreManager->id,
            'type' => $deviceOptions['type'],
            'token' => $deviceOptions['token'],
        ]);

        $deviceToken->save();

        $StoreManager->associate($deviceToken, 'devices')->save();
    }

    /**
     * Remove device from customer
     *
     * @param StoreManager $StoreManager
     * @param array $deviceOptions
     * @return void
     * @throws Exception
     */
    public function removeDeviceToken(Model $StoreManager, array $deviceOptions)
    {
        $deviceToken = $this->getDeviceToken($StoreManager, $deviceOptions);

        if (!$deviceToken) {
            return;
        }

        $StoreManager->disassociate($deviceToken, 'devices')->save();

        $deviceToken->delete();
    }

    /**
     * Get device token for the given customer and device options
     *
     * @param StoreManager $StoreManager
     * @param array $deviceOptions
     * @return DeviceToken|null
     */
    public function getDeviceToken(StoreManager $StoreManager, array $deviceOptions): ?DeviceToken
    {
        return DeviceToken::where('token', $deviceOptions['token'])->where('serviceProviderId', $StoreManager->id)->where('type', $deviceOptions['type'])->first();
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($name = $this->option('name')) {
            $columns = array_map(function ($column) {
                return "this.{$column}";
            }, ['firstName', 'lastName']);

            $columns = implode(' + " " + ', $columns);

            $this->query->whereRaw([
                '$where' => "({$columns}).match(/{$name}/)",
            ]);
        }

        // if (user() && user()->accountType() == 'user') {
        //     $this->query->where('published', true);
        // }
    }

    /**
     * update Rate
     *
     * @param int $storeId
     * @param float $avg
     * @param int $total
     */
    public function updateRate(int $storeId, float $avg, int $total)
    {
        $store = $this->getModel($storeId);
        $store->update([
            'rating' => $avg,
            'totalRating' => $total,
        ]);
    }

    /**
     * Method getstoreMangers
     *
     * @param $id $id
     *
     * @return void
     */
    public function getstoreMangers($id)
    {
        return $this->storeManagersRepository->getQuery()->where('store.id', (int) $id)->get();
    }

    /**
     * Method updateStoreManagers
     *
     * @param $store $store
     *
     * @return void
     */
    public function updateStoreManagers($store)
    {
        $storeManger = $this->storeManagersRepository->getQuery()->where('store.id', $store->id)->first();
        if ($storeManger) {
            $storeManger->store = $store->sharedInfo();
            if ($storeManger->save()) {
                $this->productsRepository->updateStoreManager($storeManger);
            }
        }
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
        $this->productsRepository->updateStoreManager($model);
        $oto = App::make(OTO::class);
        $oto->updateWarehouse($model);
    }

    /**
     * Method deleteCategoryInfo
     *
     * @param $category $category
     *
     * @return void
     */
    public function deleteStoreManagers($store)
    {
        $storeManger = Model::where('store.id', $store->id)->delete();
        $this->productsRepository->deleteManagerProducts($storeManger);
    }

    /**
     * Method listStoreManager
     *
     * @return void
     */
    public function listStoreManager()
    {
        $listStoreManager = $this->storeManagersRepository->wrapMany($this->storeManagersRepository->getQuery()->get());

        return $listStoreManager;
    }

    /**
     * Method checkStore
     *
     * @param $id $id
     *
     * @return void
     */
    public function checkStore($id)
    {
        return $this->getQuery()->where('store.id', $id)->first();
    }

    /**
     * Method checkStoreUpdate
     *
     * @param $id $id
     *
     * @return void
     */
    public function checkStoreUpdate($id, $storeMangerId)
    {
        return $this->getQuery()->where('store.id', $id)->where('id', '!=', $storeMangerId)->first();
    }

    /**
     * Update storeManger wallet balance
     *
     * @param int $storeManger
     * @return void
     */
    public function updateWalletBalanceForProvider(int $storeManger)
    {
        $user = user() ?? $this->storeManagersRepository->getModel($storeManger);
        if ($user->accountType() === 'StoreManager' && $user->id === $storeManger) {
            $storeManger = $user;
        } else {
            $storeManger = $this->getModel($storeManger);
        }
        $storeManger->walletBalanceDeposit = round($this->walletProviderRepository->getBalanceFor($storeManger->id, 'deposit', 'StoreManager'), 2);

        $storeManger->walletBalanceWithdraw = round($this->walletProviderRepository->getBalanceFor($storeManger->id, 'withdraw', 'StoreManager'), 2);

        $storeManger->walletBalance = round($storeManger->walletBalanceDeposit - $storeManger->walletBalanceWithdraw, 2);


        $storeManger->save();
    }
}
