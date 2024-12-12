<?php

namespace App\Modules\Rewards\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Jobs\NotificationsJob;
use App\Modules\Customers\Models\Customer;
use App\Modules\Rewards\Models\Reward as Model;

use App\Modules\Rewards\Filters\Reward as Filter;
use App\Modules\Rewards\Resources\Reward as Resource;

use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class RewardsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'rewards';

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
    const DATA = ['title', 'transactionType', 'note', 'status', 'coupon'];

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
    const INTEGER_DATA = ['orderId', 'points', 'remainingPoints'];

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
    const BOOLEAN_DATA = [];

    /**
     * Set columns list of date values.
     *
     * @cont array
     */
    const DATE_DATA = ['expireDate'];

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
        'inInt' => [
            'id',
        ],
        'int' => [
            'customer' => 'customer.id',
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
     * Rewards statuses list
     *
     * @const string
     */
    const ACTIVE_STATUS = 'active';

    const USED_STATUS = 'used';

    const EXPIRED_STATUS = 'expired';

    /**
     * Rewards transactions type list
     *
     * @const string
     */
    const DEPOSIT_TYPE = 'deposit';

    const WITHDRAW_TYPE = 'withdraw';

    const EXCHANGE_TYPE = 'exchange';

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
        $user = user();

        if ($user->accountType() === 'customer') {
            $customer = $user;
        } else {
            $customer = $this->customersRepository->getModel((int) $request->customer);
        }

        if ($request->transactionType == static::DEPOSIT_TYPE && !$model->id) {
            $rewardCouponDuration = $this->settingsRepository->getSetting('reward', 'rewardCouponDuration') ?? 5;

            $model->canExpire = true;

            if ($rewardCouponDuration == 0) {
                $model->canExpire = false;
            }

            $expireDate = Carbon::now()->addDays($rewardCouponDuration)->toDateTimeString();

            $model->expireDate = $expireDate;

            $model->remainingPoints = $request->points;

            // dd($expireDate);
        }

        if (!$model->id) {
            $model->status = static::ACTIVE_STATUS;
        }

        $model->customer = $customer->only(['id', 'firstName', 'lastName','email']);

        $model->creatorType = $user->accountType();
    }

    /**
     * Method getUserPoints
     *
     * @return Object
     */
    public function getUserPoints()
    {
        $user = user();

        $rewardPointPrice = $this->settingsRepository->getSetting('reward', 'rewardPointPrice') ?? 1;

        $maxExchangePoints = $this->settingsRepository->getSetting('reward', 'maxExchangePoints') ?? 1000;

        $pointsPrice = $user->rewardPoint * $rewardPointPrice;

        $availablePoints = $user->rewardPoint;

        if ($user->rewardPoint > $maxExchangePoints) {
            $availablePoints = $maxExchangePoints;
        }

        $availablePointsPrice = $availablePoints * $rewardPointPrice;

        $pointsPrice = trans('products.price', ['value' => $pointsPrice]);

        $availablePointsPrice = trans('products.price', ['value' => $availablePointsPrice]);

        $data['userPoints'] = $user->rewardPoint;
        $data['userPointsPrice'] = $pointsPrice;
        $data['availablePoints'] = $availablePoints;
        $data['availablePointsPrice'] = $availablePointsPrice;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function onSave($model, $request, $oldModel = null)
    {
        $this->customersRepository->updateRewardBalance($model->customer['id']);
    }

    /**
     * Create a new withdraw for customer
     *
     * @param array $options
     * @return Model
     */
    public function withdraw(array $options)
    {
        $options['transactionType'] = static::WITHDRAW_TYPE;

        return $this->create($options);
    }

    /**
     * Create a new exchange for customer
     *
     * @param array $options
     * @return Model
     */
    public function exchange(array $options)
    {
        $userId = $options['customer'];

        $points = $options['points'];

        $rewards = $this->getActiveRewards($userId);

        foreach ($rewards as $reward) {
            if ($reward->remainingPoints < $points) {
                $points -= $reward->remainingPoints;
                $reward->remainingPoints = 0;
                $reward->status = static::USED_STATUS;
                $reward->save();
            } elseif ($reward->remainingPoints > $points) {
                $reward->remainingPoints -= $points;
                $reward->save();

                break;
            } else {
                $reward->remainingPoints = 0;
                $reward->status = static::USED_STATUS;
                $reward->save();

                break;
            }
        }

        $options['transactionType'] = static::EXCHANGE_TYPE;
        $options['status'] = static::ACTIVE_STATUS;

        return $this->create($options);
    }

    /**
     * Create a new deposit for customer
     *
     * @param array $options
     * @return Model
     */
    public function deposit(array $options)
    {
        $options['transactionType'] = static::DEPOSIT_TYPE;

        return $this->create($options);
    }

    /**
     * Create a new coupon for customer exchange
     *
     * @param array $options
     * @return Model
     */
    public function genrateRewardCoupon(int $points, int $userId)
    {
        $rewardCouponDuration = $this->settingsRepository->getSetting('reward', 'rewardCouponDuration') ?? 5;

        $rewardPointPrice = $this->settingsRepository->getSetting('reward', 'rewardPointPrice') ?? 1;

        $pointPrice = $points * $rewardPointPrice;

        $coupon = $this->couponsRepository->create([
            'code' => Str::random(8),
            'customer' => $userId,
            'startsAt' => Carbon::now(),
            'endsAt' => Carbon::now()->addDays(30),
            'rewardPoints' => $points,
            'value' => $pointPrice,
            'maxUsage' => 1,
            'minOrderValue' => 0,
            'type' => 'fixed',
            'published' => true,
        ]);


        $title = [];

        $title[0]['text'] = "‌you have {$coupon->code} for  {$points}‌ Points- {$pointPrice} ر.س";
        $title[0]['localeCode'] = "en";
        $title[1]['text'] = "‌لديك‌ ‌كوبون‌ {$coupon->code} مقابل  {$points}‌  نقاط - {$pointPrice} ر.س";
        $title[1]['localeCode'] = "ar";


        $this->exchange([
            'coupon' => $coupon->code,
            'points' => $points,
            'expireDate' => $coupon->endsAt,
            'customer' => $userId,
            'orderId' => 0,
            'title' => $title,
        ]);

        return $coupon;
    }

    /**
     * Get  rewards count by type
     *
     * @param int $customerId
     * @param string $transactionType
     * @return int
     */
    public function getRewardsCountByType(int $customerId, string $transactionType): int
    {
        return $this->getQuery()->where('customer.id', $customerId)->where('transactionType', $transactionType)->count();
    }

    /**
     * Get total balance for the given customer id with transaction type
     *
     * @param int $customerId
     * @param string $transactionType
     * @return float
     */
    public function getBalanceFor(int $customerId, string $transactionType): float
    {
        if ($transactionType === 'withdraw') {
            // return $this->getQuery()->where('customer.id', $customerId)->where('transactionType', $transactionType)->sum('points');
            return $this->getQuery()->where(function ($query) use ($transactionType) {
                $query->where('transactionType', $transactionType)->orWhere('transactionType', static::EXCHANGE_TYPE);
            })->where('customer.id', $customerId)->sum('points');
        } else {
            return $this->getQuery()->where('customer.id', $customerId)->where('transactionType', $transactionType)->sum('points');
        }
    }

    /**
     * Get total remainingPoints balance for the given customer id with transaction type = deposit
     *
     * @param int $customerId
     * @return float
     */
    public function getRemainBalanceFor(int $customerId): float
    {
        return $this->getQuery()->where('customer.id', $customerId)->where('transactionType', static::DEPOSIT_TYPE)->where(function ($query) { /* That's the closure */
            $query->where('expireDate', '>=', Carbon::now())
                ->orWhere('canExpire', false);
        })->sum('remainingPoints');
    }

    public function getLastTransaction(int $customerId)
    {
        return $this->getQuery()->where('customer.id', $customerId)->latest('id')->first();
    }

    /**
     * Get active rewards for the given customer id with transaction type = deposit
     *
     * @param int $customerId
     * @return
     */
    public function getActiveRewards(int $customerId)
    {
        return $this->getQuery()->where('customer.id', $customerId)->where('transactionType', static::DEPOSIT_TYPE)->where(function ($query) { /* That's the closure */
            $query->where('expireDate', '>=', Carbon::now())
                ->orWhere('canExpire', false);
        })->where('remainingPoints', '>', 0)->get();
    }

    /**
     * Check if customer have enough balance
     *
     * @param Customer $customer
     * @param float $checkedAmount
     * @return bool
     */
    public function hasEnoughBalance(Customer $customer, float $checkedAmount): bool
    {
        return $customer->rewardPoint >= $checkedAmount;
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($customer = $this->option('customer')) {
            $this->query->where('customer.id', (int) $customer);
        }
    }

    /**
     * update Rewards Status
     *
     * @param int $userId
     * @return Model
     */
    public function updateRewardStatus(int $userId)
    {
        $this->getQuery()->where('customer.id', $userId)->where('expireDate', '<', Carbon::now())->where('canExpire', true)->update([
            'status' => static::EXPIRED_STATUS,
        ]);
    }

    /**
     * update Exchange Rewards Status
     *
     * @param int $userId
     * @return Model
     */
    public function checkCoupon($coupon)
    {
        $this->getQuery()->where('coupon', $coupon)->update([
            'status' => static::USED_STATUS,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function onList($rewards)
    {
        $user = user();

        $this->updateRewardStatus($user->id);

        $this->customersRepository->updateRewardBalance($user->id);

        return $rewards;
    }

    /**
     * @param int $userId
     * @param int $point
     * @param string $title
     * @param string $reason
     * @param $notes
     * @param string $action
     * @return mixed
     */
    public function createAction(int $userId, int $point, string $title, string $reason, $notes, string $action)
    {
        $userObject = $this->customersRepository->get((int) $userId);
        dispatch(new NotificationsJob([
            'title' => trans("notifications.rewards.{$action}Title"),
            'content' => trans("notifications.rewards.{$action}Content", [
                'amount' => $point,
            ]),
            'type' => $action,
            'user' => $userObject,
            // 'userType' => 'customer',
            'pushNotification' => true,
            'extra' => [
                'type' => $action,
                'title' => $title,
                'reason' => $reason,
                'points' => $point,
                'notes' => $notes,
            ],
        ]));

        return $this->{$action}([
            'customer' => $userId,
            'points' => $point,
            'title' => $title,
            'note' => $notes,
        ]);
    }
}
