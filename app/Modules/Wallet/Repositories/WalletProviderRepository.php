<?php

namespace App\Modules\Wallet\Repositories;

// use App\Jobs\NotificationsJob;
use App\Modules\Wallet\Models\WalletProvider as Model;
use App\Modules\Wallet\Filters\WalletProvider as Filter;
use App\Modules\Wallet\Resources\WalletProvider as Resource;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class WalletProviderRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'walletProvider';

    /**
     * {@inheritDoc}
     */
    const MODEL = Model::class;

    /**
     * {@inheritDoc}
     */
    const RESOURCE = Resource::class;

    /**
     * Set the columns of the data that will be auto filled in the model
     *
     * @const array
     */
    const DATA = ['notes', 'title', 'transactionType', 'reason', 'type'];

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
    const INTEGER_DATA = ['orderId'];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = ['amount', 'balanceBefore', 'balanceAfter', 'commissionDiteMarket', 'totalAmountOrder'];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = [];

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
    const MULTI_DOCUMENTS_DATA = [];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = [];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'int' => [
            'provider' => 'provider.id',
        ],
    ];

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
     * @param mixed $model
     * @param \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
        $user = user() ?? $this->typeProvider($request);
        // dd($user , $model);
        $provider = $this->typeProvider($request);
        $providerData = $this->providerData($request, $provider);
      
        $model->provider = $providerData;

        $model->creatorType = $user->accountType();

        $latestAmount = $this->getQuery()->latest('id')->first();

        $model->balanceAfter = $latestAmount->amount ?? 0;
        $model->balanceBefore = $provider->walletBalance;
    }

    /**
     * {@inheritdoc}
     */
    public function onSave($model, $request, $oldModel = null)
    {
        if ($request->type === 'StoreManager') {
            return $this->storeManagersRepository->updateWalletBalanceForProvider($model->provider['id']);
        } elseif ($request->type === 'RestaurantManager') {
            return $this->restaurantManagersRepository->updateWalletBalanceForProvider($model->provider['id']);
        } elseif ($request->type === 'ClubManager') {
            return $this->clubManagersRepository->updateWalletBalanceForProvider($model->provider['id']);
        } elseif ($request->type === 'NutritionSpecialistManger') {
            return $this->nutritionSpecialistMangersRepository->updateWalletBalanceForProvider($model->provider['id']);
        }


        // if ($model->orderId) {
        //     $this->ordersRepository->updateReturnedAmount($model);
        // }
    }

    /**
     * It returns a model based on the type of provider
     * 
     * @param request is the request object
     * 
     * @return The return value is the result of the function.
     */
    public function typeProvider($request)
    {
        if ($request->type === 'StoreManager') {
            return $this->storeManagersRepository->getModel($request->provider);
        } elseif ($request->type === 'RestaurantManager') {
            return $this->restaurantManagersRepository->getModel($request->provider);
        } elseif ($request->type === 'ClubManager') {
            return $this->clubManagersRepository->getModel($request->provider);
        } elseif ($request->type === 'NutritionSpecialistManger') {
            return $this->nutritionSpecialistMangersRepository->getModel($request->provider);
        }
    }

    /**
     * It returns an array of data based on the type of provider.
     * </code>
     * 
     * @param request is the request object
     * @param provider is the object that I'm passing to the function
     * 
     * @return An array of data.
     */
    public function providerData($request, $provider)
    {
        if ($request->type === 'StoreManager') {
            return  [
                'id' => $provider->id,
                'name' => $provider->firstName . $provider->lastName,
                'email' => $provider->email,
            ];
        } elseif ($request->type === 'RestaurantManager') {
            return  [
                'id' => $provider->id,
                'name' => $provider->name,
                'email' => $provider->email,
            ];
        } elseif ($request->type === 'ClubManager') {
            return  [
                'id' => $provider->id,
                'name' => $provider->name,
                'email' => $provider->email,
            ];
        } elseif ($request->type === 'NutritionSpecialistManger') {
            return  [
                'id' => $provider->id,
                'name' => $provider['name'][0]['text'],
                'email' => $provider->email,
            ];
        }
    }

    /**
     * Create a new withdraw for customer
     *
     * @param array $options
     * @return \HZ\Illuminate\Mongez\Contracts\Repositories\Illuminate\Database\Eloquent\Model
     */
    public function withdraw(array $options)
    {
        // $this->

        $options['transactionType'] = 'withdraw';

        return $this->create($options);
    }

    /**
     * Create a new deposit for customer
     *
     * @param array $options
     * @return \HZ\Illuminate\Mongez\Contracts\Repositories\Illuminate\Database\Eloquent\Model
     */
    public function deposit(array $options)
    {
        $options['transactionType'] = 'deposit';

        return $this->create($options);
    }

    /**
     * Get total balance for the given customer id with transaction type
     *
     * @param int $userId
     * @param string $transactionType
     * @param string $userType
     * @return float
     */
    public function getBalanceFor(int $userId, string $transactionType, string $userType): float
    {
        return $this->getQuery()->where('type', $userType)->where("provider.id", $userId)->where('transactionType', $transactionType)->sum('amount');
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($provider = $this->option('provider')) {
            $this->query->where('provider.id', (int) $provider)->where('type', $this->option('type'));
        }
    }

    /**
     * @param int $userId
     * @param float $amount
     * @param string $title
     * @param string $reason
     * @param $notes
     * @param string $action
     * @return mixed
     */
    public function createAction(int $userId, float $amount, string $title, string $reason, $notes, $type, string $action)
    {
        // $userObject = $this->deliveryMenRepository->get((int) $userId);

        // dispatch(new NotificationsJob([
        //     'title' => trans("notifications.wallet.{$action}Title"),
        //     'content' => trans("notifications.wallet.{$action}Content", [
        //         'amount' => $amount,
        //     ]),
        //     'type' => $action,
        //     'user' => $userObject,
        //     'pushNotification' => true,
        //     'extra' => [
        //         'type' => $action,
        //         'title' => trans("notifications.wallet.{$action}Title"),
        //         'reason' => $reason,
        //         'amount' => $amount,
        //         'notes' => $notes,
        //     ],
        // ]));
        // dd($title);
        return $this->{$action}([
            'provider' => $userId,
            'amount' => $amount,
            'title' => trans("notifications.wallet.{$action}Title"),
            'reason' => $reason,
            'notes' => $notes,
        ]);
    }
}
