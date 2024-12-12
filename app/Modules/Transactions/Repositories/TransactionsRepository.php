<?php

namespace App\Modules\Transactions\Repositories;

use Carbon\Carbon;
use App\Modules\Cities\Models\City;
use App\Modules\Orders\Models\Order;
use App\Modules\Customers\Models\Customer;
use App\Modules\Orders\Repositories\OrdersRepository;
use App\Modules\Transactions\Models\Transaction as Model;
use App\Modules\Transactions\Filters\Transaction as Filter;

use App\Modules\Transactions\Resources\Transaction as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class TransactionsRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'transactions';

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
    const DATA = ['orderId', 'seller', 'paymentMethod', 'appCommission', 'totalOrder', 'transactionAmount', 'totalRequired', 'type', 'profitRatio', 'totalRequiredSeller'];

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
    const BOOLEAN_DATA = ['suspended'];

    /**
     * Set columns list of date values.
     *
     * @cont array
     */
    const DATE_DATA = [
        'createdAt',
    ];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [
        // 'seller' => Customer::class,
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
    const WHEN_AVAILABLE_DATA = [];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'int' => [
            'id',
            'seller' => 'seller.id',
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
    const PAGINATE = null;

    /**
     * Number of items per page in pagination
     * If set to null, then it will taken from pagination configurations
     *
     * @const int|null
     */
    const ITEMS_PER_PAGE = null;

    /**
     * Deduction of commission from the store based on the payment method.
     */
    const ONLINE_PAYMENT = [
        OrdersRepository::VISA_PAYMENT_METHOD,
        OrdersRepository::MADA_PAYMENT_METHOD,
        OrdersRepository::MASTER_PAYMENT_METHOD,
        OrdersRepository::APPLE_PAY_PAYMENT_METHOD,
    ];

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
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        if ($seller = $this->option('store')) {
            $this->query->where('seller.store.id', (int) $seller);
        }

        if ($seller = $this->option('restaurant')) {
            $this->query->where('seller.restaurant.id', (int) $seller);
        }
        if ($seller = $this->option('club')) {
            $this->query->where('seller.club.id', (int) $seller);
        }
        if ($seller = $this->option('nutritionSpecialist')) {
            $this->query->where('seller.nutritionSpecialist.id', (int) $seller);
        }

        if ($orderId = $this->option('order')) {
            $this->query->where('orderId', (int) $orderId);
        }

        if ($city = $this->option('city')) {
            $this->query->where('city.id', (int) $city);
        }
        if ($type = $this->option('type')) {
            $this->query->where('type', $type);
        }
        if (!empty($options['from'])) {
            $this->query->where('createdAt', '>=', static::mongoTime(Carbon::parse($options['from'])));
        }

        if (!empty($options['to'])) {
            $this->query->where('createdAt', '<', static::mongoTime(Carbon::parse($options['to'])));
        }
        if ($this->option('wallet')) {
            $this->query->where('createdAt', '<', Carbon::now()->subDays(15));
        }
    }

    /**
     * get app commission by seller
     *
     * @return float
     */
    // public function getAppCommission($seller)
    // {
    //     $query = $this->commissionsRepository->getQuery();

    //     $commission = $query->where('seller.id', $seller['id'])->first();

    //     return $commission->value ?? 0;
    // }

    /**
     * add new transaction form order
     *
     * @param Order $order
     */
    public function add(Order $order)
    {
        if ($order->seller) {
            $orderSellers = $order->seller;
            $type = 'products';
            foreach ($orderSellers as $orderSeller) {
                $seller = $this->storeManagersRepository->sharedInfo($orderSeller['id']);
                $cityId = $orderSeller['city']['id'];

                if ($seller['store']['profitRatio'] == -1) {
                    $profitRatio = $this->settingsRepository->getSetting('store', 'profitRatio');
                } else {
                    $profitRatio = $seller['store']['profitRatio'];
                }
                //start
                $city = $this->citiesRepository->sharedInfo($cityId);

                $items = collect($order->items)->where('seller.id', (int)$seller['id']);
                $subTotal = $items->sum('totalPrice');
                $totalRequired = 0.0;
                $profitRatio = (($subTotal * $profitRatio) / 100);
                $totalRequired = $subTotal - $profitRatio;

                $this->create([
                    'seller' => $seller, // مدير
                    'city' => ($city) ? $city['id']  : null, // المدينه
                    'type' => $type, // نوع
                    'orderId' => (int) $order['id'], //1-	رقم الطلب
                    'totalOrder' => $subTotal, // 6-	الإجمالي
                    'transactionAmount' => $totalRequired, // قيمه عموله السيلير
                    'profitRatio' => $profitRatio, //2-	 نسبه عموله دايت
                    'suspended' => false,
                    'totalRequired' => $totalRequired,  //3-	 قيمة العمولة السيلير
                    'paymentMethod' => $order['paymentMethod'], // 4-	طريقة الدفع
                    // 'deliveryType' => $order['deliveryType'],
                ]);
                // end


            }
        } elseif ($order->restaurantManager) {
            $seller = $this->restaurantManagersRepository->sharedInfo($order->restaurantManager['id']);
            $cityId = $seller['restaurant']['city']['id'];
            $type = 'food';
            if ($seller['restaurant']['profitRatio'] == -1) {
                $profitRatio = $this->settingsRepository->getSetting('restaurant', 'profitRatio');
            } else {
                $profitRatio = $seller['restaurant']['profitRatio'] ?? 0;
            }
        } elseif ($order->clubManager) {
            $seller = $this->clubManagersRepository->sharedInfo($order->clubManager['id']);
            $cityId = $seller['club']['mainBranchClub']['city']['id'];
            $type = 'club';
            if ($seller['club']['profitRatio'] == -1) {
                $profitRatio = $this->settingsRepository->getSetting('club', 'profitRatio');
            } else {
                $profitRatio = $seller['club']['profitRatio'];
            }
        } elseif ($order->nutritionSpecialistManager) {
            $seller = $this->nutritionSpecialistMangersRepository->sharedInfo($order->nutritionSpecialist['id']);
            $cityId = $seller['nutritionSpecialist']['city']['id'];
            $type = 'nutritionSpecialist';

            if ($seller['nutritionSpecialist']['profitRatio'] == -1) {
                $profitRatio = $this->settingsRepository->getSetting('nutritionSpecialist', 'profitRatio');
            } else {
                $profitRatio = $seller['nutritionSpecialist']['profitRatio'];
            }
        }

        if (!$order->seller) {
            $city = $this->citiesRepository->sharedInfo($cityId);
            $subTotal = $order['subTotal'];
            $totalRequired = 0.0;
            $profitRatio = (($subTotal * $profitRatio) / 100);
            $totalRequired = $subTotal - $profitRatio;
            $this->create([
                'seller' => $seller, // مدير
                'city' => ($city) ? $city['id']  : null, // المدينه
                'type' => $type, // نوع
                'orderId' => (int) $order['id'], //1-	رقم الطلب
                'totalOrder' => $subTotal, // 6-	الإجمالي
                'transactionAmount' => $totalRequired, // قيمه عموله السيلير
                'profitRatio' => $profitRatio, //2-	 نسبه عموله دايت
                'suspended' => false,
                'totalRequired' => $totalRequired,  //3-	 قيمة العمولة السيلير
                'paymentMethod' => $order['paymentMethod'], // 4-	طريقة الدفع
                // 'deliveryType' => $order['deliveryType'],
            ]);
        }
    }

    /**
     * @param $model
     * @param $request
     * @param null $oldModel
     */
    public function onSave($model, $request, $oldModel = null)
    {
        if ($model->type == "products") {
            $this->storesRepository->updateTransaction($model->seller['store']['id']);
        } elseif ($model->type == "food") {
            $this->restaurantsRepository->updateTransaction($model->seller['restaurant']['id']);
        } elseif ($model->type == "club") {
            $this->clubsRepository->updateTransaction($model->seller['club']['id']);
        } elseif ($model->type == "nutritionSpecialist") {
            $this->nutritionSpecialistsRepository->updateTransaction($model->seller['nutritionSpecialist']['id']);
        }
    }

    /**
     * get sum transaction Amount
     *
     * @param int $orderId
     * @return float
     */
    public function updateTransactionSuspended(int $orderId): void
    {
        $model = $this->getByModel('orderId', (int) $orderId);

        $model->suspended = true;

        $model->save();
    }

    /**
     * get sum transaction Amount
     *
     * @param int $sellerId
     * @return float
     */
    public function getTotalTransactionAmount(int $sellerId, $type): float
    {
        if ($type == 'products') {
            return round((float) $this->getQuery()->where('seller.store.id', $sellerId)->sum('transactionAmount'));
        } elseif ($type == "food") {
            return round((float) $this->getQuery()->where('seller.restaurant.id', $sellerId)->sum('transactionAmount'));
        } elseif ($type == "club") {
            return round((float) $this->getQuery()->where('seller.club.id', $sellerId)->sum('transactionAmount'));
        } elseif ($type == "nutritionSpecialist") {
            return round((float) $this->getQuery()->where('seller.nutritionSpecialist.id', $sellerId)->sum('transactionAmount'));
        }
    }

    /**
     * get sum appCommission
     *
     * @param int $sellerId
     * @return float
     */
    public function getTotalAppCommission(int $sellerId, $type): float
    {
        if ($type == 'products') {
            return round((float) $this->getQuery()->where('seller.store.id', $sellerId)->sum('appCommission'));
        } elseif ($type == "food") {
            return round((float) $this->getQuery()->where('seller.restaurant.id', $sellerId)->sum('appCommission'));
        } elseif ($type == "club") {
            return round((float) $this->getQuery()->where('seller.club.id', $sellerId)->sum('appCommission'));
        } elseif ($type == "nutritionSpecialist") {
            return round((float) $this->getQuery()->where('seller.nutritionSpecialist.id', $sellerId)->sum('appCommission'));
        }
    }

    /**
     * get wallet total
     *
     * @param int $sellerId
     * @return float
     */
    public function getTotalWallet(int $sellerId): float
    {
        return round((float) $this->getQuery()->where('seller.store.id', $sellerId)->where('createdAt', '<', Carbon::now()->subDays(15))->where('suspended', false)->sum('transactionAmount'));
    }

    /**
     * get sum totalRequired
     *
     * @param int $sellerId
     * @return float
     */
    public function getTotalRequired(int $sellerId, $type): float
    {
        if ($type == 'products') {
            return round((float) $this->getQuery()->where('seller.store.id', $sellerId)->sum('totalRequired'));
        } elseif ($type == "food") {
            return round((float) $this->getQuery()->where('seller.restaurant.id', $sellerId)->sum('totalRequired'));
        } elseif ($type == "club") {
            return round((float) $this->getQuery()->where('seller.club.id', $sellerId)->sum('totalRequired'));
        } elseif ($type == "nutritionSpecialist") {
            return round((float) $this->getQuery()->where('seller.nutritionSpecialist.id', $sellerId)->sum('totalRequired'));
        }
    }

    /**
     * get sum totalOrder
     *
     * @param int $sellerId
     * @return float
     */
    public function getTotalOrder(int $sellerId, $type): float
    {
        if ($type == 'products') {
            return round((float) $this->getQuery()->where('seller.store.id', $sellerId)->sum('totalOrder'));
        } elseif ($type == "food") {
            return round((float) $this->getQuery()->where('seller.restaurant.id', $sellerId)->sum('totalOrder'));
        } elseif ($type == "club") {
            return round((float) $this->getQuery()->where('seller.club.id', $sellerId)->sum('totalOrder'));
        } elseif ($type == "nutritionSpecialist") {
            return round((float) $this->getQuery()->where('seller.nutritionSpecialist.id', $sellerId)->sum('totalOrder'));
        }
    }

    /**
     * Method getProfitRatio
     *
     * @param int $sellerId
     * @param $type $type
     *
     * @return float
     */
    public function getProfitRatio(int $sellerId, $type): float
    {
        if ($type == 'products') {
            return round((float) $this->getQuery()->where('seller.store.id', $sellerId)->sum('profitRatio'));
        } elseif ($type == "food") {
            return round((float) $this->getQuery()->where('seller.restaurant.id', $sellerId)->sum('profitRatio'));
        } elseif ($type == "club") {
            return round((float) $this->getQuery()->where('seller.club.id', $sellerId)->sum('profitRatio'));
        } elseif ($type == "nutritionSpecialist") {
            return round((float) $this->getQuery()->where('seller.nutritionSpecialist.id', $sellerId)->sum('profitRatio'));
        }
    }

    /**
     * Method listStore
     *
     * @param $id $id
     *
     * @return void
     */
    public function listStore(int $id)
    {
        $store = $this->storesRepository->get($id);
        if ($store) {
            $totalRequiredSeller = ($store['transaction']) ? $store['transaction']['totalRequired'] : 0;

            return $totalRequiredSeller;
        } else {
            $totalRequiredSeller = 0.0;

            return $totalRequiredSeller;
        }
    }

    /**
     * Method listRestaurant
     *
     * @param $id $id
     *
     * @return void
     */
    public function listRestaurant(int $id)
    {
        $restaurant = $this->restaurantsRepository->get($id);
        if ($restaurant) {
            $totalRequiredSeller = ($restaurant['transaction']) ? $restaurant['transaction']['totalRequired'] : 0;

            return $totalRequiredSeller;
        } else {
            $totalRequiredSeller = 0.0;

            return $totalRequiredSeller;
        }
    }

    /**
     * Method listClub
     *
     * @param int $id
     *
     * @return void
     */
    public function listClub(int $id)
    {
        $club = $this->clubsRepository->get($id);
        if ($club) {
            $totalRequiredSeller = ($club['transaction']) ? $club['transaction']['totalRequired'] : 0;

            return $totalRequiredSeller;
        } else {
            $totalRequiredSeller = 0.0;

            return $totalRequiredSeller;
        }
    }

    /**
     * Method listNutritionSpecialist
     *
     * @param int $id
     *
     * @return void
     */
    public function listNutritionSpecialist(int $id)
    {
        $nutritionSpecialist = $this->nutritionSpecialistsRepository->get($id);
        if ($nutritionSpecialist) {
            $totalRequiredSeller = ($nutritionSpecialist['transaction']) ? $nutritionSpecialist['transaction']['totalRequired'] : 0;

            return $totalRequiredSeller;
        } else {
            $totalRequiredSeller = 0.0;

            return $totalRequiredSeller;
        }
    }

    /**
     * Method amountDiteMarket
     *
     * @return void
     */
    public function amountDiteMarket()
    {
        return $this->getQuery()->sum('profitRatio');
    }

    /**
     * Method amountServiceProviders
     *
     * @param $type $type
     * @param $id $id
     *
     * @return void
     */
    public function amountServiceProviders($type, $sellerId)
    {
        if ($type == 'products') {
            return round((float) $this->getQuery()->where('seller.store.id', $sellerId)->sum('totalRequired'));
        } elseif ($type == "food") {
            return round((float) $this->getQuery()->where('seller.restaurant.id', $sellerId)->sum('totalRequired'));
        } elseif ($type == "club") {
            return round((float) $this->getQuery()->where('seller.club.id', $sellerId)->sum('totalRequired'));
        } elseif ($type == "nutritionSpecialist") {
            return round((float) $this->getQuery()->where('seller.nutritionSpecialist.id', $sellerId)->sum('totalRequired'));
        }
    }
}
