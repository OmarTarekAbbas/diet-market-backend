<?php

namespace App\Modules\Customers\Repositories;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Modules\General\Helpers\Visitor;
use App\Modules\Orders\Models\OrderItem;
use App\Modules\Cart\Helpers\VisitorCart;
use App\Modules\DietTypes\Models\DietType;
use App\Modules\Users\Traits\Auth\AccessToken;
use App\Modules\Newsletters\Services\Gateways\SMS;
use App\Modules\Customers\Models\Customer as Model;

use App\Modules\CustomerGroups\Models\CustomerGroup;
use App\Modules\Customers\Filters\Customer as Filter;
use App\Modules\Customers\Resources\Customer as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class CustomersRepository extends RepositoryManager implements RepositoryInterface
{
    use AccessToken;

    /**
     * {@inheritDoc}
     */
    const NAME = 'customers';

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
    const DATA = ['firstName', 'lastName', 'email', 'phoneNumber', 'password', 'deviceCart'];

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
    const INTEGER_DATA = [
        'rewardPoint',
    ];

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
    const BOOLEAN_DATA = ['published', 'subscribedToNewsLetter'];

    /**
     * Geo Location data
     *
     * @const array
     */
    const LOCATION_DATA = ['location'];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [
        'dietTypes' => DietType::class,
        'group' => CustomerGroup::class,
    ];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel::class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [
        // 'subscribeClubs' => OrderItem::class
    ];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = [
        'password', 'subscribedToNewsLetter', 'rewardPoint', 'firstName', 'lastName', 'email', 'phoneNumber', 'published',
        'accessTokens', 'accessToken', 'deviceCart',
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
     * @param mixed $model
     * @param \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {

        if (!$model->id) {
            // generate 6 digits
            $model->verificationCode = mt_rand(1000, 9999);
            $model->isVerified = (bool) $request->isVerified ?: false;
            $model->walletBalance = 0;
            $model->totalNotifications = 0;
            $model->totalOrders = 0;
            $model->rewardPoint = 0;
            $model->favoritesCount = 0;
            if ((int) $request->published == 1) {
                $published = true;
            } else {
                $published = false;
            }
            $model->deviceCart = request()->header('DEVICE-ID', null);
            if (config('app.type') === 'admin') {
                $model->isVerified = true;
            }
            $model->cart = [
                'items' => [],
                'totalPrice' => 0.0,
                'taxes' => 0.0,
                'finalPrice' => 0.0,
                'originalPrice' => 0.0,
                'rewardPoints' => 0,
                'totalQuantity' => 0,
                'useRewardPoints' => false,
                'isActiveRewardPoints' => false,
                'subscription' => false,
            ];

            $model->cartMeal = [
                'items' => [],
                'totalPrice' => 0.0,
                'taxes' => 0.0,
                'finalPrice' => 0.0,
                'originalPrice' => 0.0,
                'rewardPoints' => 0,
                'totalQuantity' => 0,
                'useRewardPoints' => false,
                'isActiveRewardPoints' => false,
                'subscription' => true,
            ];
        }

        if (!$model->cart) {
            $model->cart = [
                'items' => [],
                'totalPrice' => 0.0,
                'taxes' => 0.0,
                'finalPrice' => 0.0,
                'originalPrice' => 0.0,
                'rewardPoints' => 0,
                'totalQuantity' => 0,
                'useRewardPoints' => false,
                'isActiveRewardPoints' => false,
                'subscription' => false,
            ];
            $model->cartMeal = [
                'items' => [],
                'totalPrice' => 0.0,
                'taxes' => 0.0,
                'finalPrice' => 0.0,
                'originalPrice' => 0.0,
                'rewardPoints' => 0,
                'totalQuantity' => 0,
                'useRewardPoints' => false,
                'isActiveRewardPoints' => false,
                'subscription' => false,
            ];
        }

        if (!$model->cartSubscription) {
            $model->cartSubscription = [
                'items' => [],
                'totalPrice' => 0.0,
                'taxes' => 0.0,
                'finalPrice' => 0.0,
                'originalPrice' => 0.0,
                'rewardPoints' => 0,
                'totalQuantity' => 0,
                'useRewardPoints' => false,
                'isActiveRewardPoints' => false,
                'subscription' => true,
            ];
        }

        if (!$model->favoritesCount) {
            $model->favoritesCount = 0;
        }

        if ($request->email) {
            $model->email = strtolower($request->email);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function onCreate($user, $request)
    {
        $this->generateAccessToken($user, $request);

        // $this->getCartFromAuth($user, $request);

        $this->sendOTP($user->phoneNumber, $user->verificationCode);
        if (config('app.type') === 'admin') {
            $this->customerGroupsRepository->createCustomerGroups($request);
        }
    }

    /**
     * Get emails for the given filter
     *
     * @param array $filter
     * @return array
     */
    public function listEmails(array $filter = []): array
    {
        $model = $this->getQuery();

        if (!empty($filter['group'])) {
            $model->where('group.id', (int) $filter['group']);
        }

        return $model->pluck('email')->toArray();
    }

    /**
     * Update total orders for the given customer
     *
     * @param $customerId
     * @return void
     */
    public function updateTotalOrders(int $customerId)
    {
        // dd($customerId);
        $customer = $this->getModel($customerId);

        $customer->totalOrders = $this->ordersRepository->getTotal([
            'customer' => $customer->id,
        ]);

        $customer->totalOrdersPurchases = $this->ordersRepository->getTotalPrice([
            'customer' => $customer->id,
        ]);

        $customer->save();

        // $this->customerGroupsRepository->findGroupForCustomer($customer->refresh());
    }

    /**
     * update cart in user
     *
     * @param $user
     */
    public function updateCart($user)
    {
        $user->cart = $this->cartRepository->getCurrentCart($user->id)->sharedInfo();

        $user->save();
    }

    public function replaceVisitorCart(Request $request, $user)
    {
        $deviceId = Visitor::getDeviceId();
        $items = VisitorCart::getCartItemWithDelete($deviceId, $user);

        $customer = user();

        $customer->getCart()->addMultiple($items, $customer->id);


        //$this->updateCart($user);
    }

    /**
     * add cart form login or register
     * @param $user
     * @param $request
     */
    public function getCartFromAuth($user, $request)
    {
        $deviceId = Visitor::getDeviceId();

        try {
            if ($deviceId) {
                $items = VisitorCart::getCartItemWithDelete($deviceId);
                $user->getCart()->addMultiple($items, $user->id);
            }
        } catch (\Exception $exception) {
        }
    }

    /**
     * Check if customer can login
     *
     * @param Request $request
     * @return Resource|false|\Illuminate\Http\Resources\Json\JsonResource|\JsonResource
     * @throws \Exception
     */
    public function login(Request $request)
    {
        $filter = [];

        $value = strtolower($request->Phone);

        // if the phone number is numeric, then search in phone numbers
        // otherwise search in email
        if (is_numeric($value)) {
            $filter[] = 'phoneNumber';
        } else {
            $filter[] = 'email';
        }

        $filter[] = $value;

        $customer = Model::where('phoneNumber', $value)->first();
        if (!$customer) {
            return false;
        }

        // dd($customer->password);

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return false;
        }
        // dd($customer,!Hash::check($request->password, $customer->password));

        if (!$customer->published) {
            throw new \Exception(trans('auth.You cannot log in because there is a problem with your account'));
        }

        // if (!$customer || $request->verificationCode != $customer->verificationCode) return false;

        $accessToken = $this->generateAccessToken($customer);

        $customer->accessToken = $accessToken;
        // $customer->isVerified = true;
        // $customer->verificationCode = null;

        // $this->getCartFromAuth($customer, $request);



        if (!isset($customer->rewardPoint)) {
            $customer->rewardPoint = 0;
        }

        $deviceId = Visitor::getDeviceId();
        $items = VisitorCart::getCartItems($deviceId, $customer);
        /*
        $request['withoutQuantity'] = true;
        $customer->getCart()->addMultiple($items, $customer->id);
        $request['withoutQuantity'] = false;
        */
        $customer->save();
        if ($customer->isVerified == true) {
            return $this->wrap($customer->refresh());
        }
    }

    /**
     * Send new verification phone number
     *
     * @param Request $request
     * @return false|\Illuminate\Http\Resources\Json\JsonResource|\JsonResource
     */
    public function sendLoginOTP(Request $request)
    {
        $filter = [];

        $value = strtolower($request->Phone);

        // if the phone number is numeric, then search in phone numbers
        // otherwise search in email
        if (is_numeric($value) || is_numeric(str_replace('+', '', $value))) {
            $filter[] = 'phoneNumber';
        } else {
            $filter[] = 'email';
        }

        $filter[] = $value;

        $customer = Model::where(...$filter)->first();

        if (!$customer) {
            return false;
        }

        $customer->verificationCode = mt_rand(1000, 9999);
        $customer->isVerified = false;

        $customer->save();

        $this->sendOTP($customer->phoneNumber, $customer->verificationCode);

        return $customer->verificationCode;
    }

    /**
     * Verify Account by the given verification code
     *
     * @param int $verificationCode
     * @return Resource|false|\Illuminate\Http\Resources\Json\JsonResource|\JsonResource
     */
    public function verify(int $verificationCode)
    {
        $customer = $this->getByModel('verificationCode', $verificationCode);

        if (!$customer) {
            return false;
        }

        // clear the verification code
        $customer->verificationCode = null;
        $customer->isVerified = true;
        $customer->published = true;
        $customer->save();

        $this->generateAccessToken($customer);

        return $customer;
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

        if (user() && user()->accountType() == 'user') {
            $this->query->where('isVerified', true);
        }

        if ($club = $this->option('club')) {
            // dd($club );
            $this->query->where('subscribeClubs.club', (int) $club);
        }
    }

    /**
     * Update customer wallet balance
     *
     * @param int $customerId
     * @return void
     */
    public function updateWalletBalance(int $customerId)
    {
        $user = user() ?? $this->customersRepository->getModel($customerId);
        if ($user->accountType() === 'customer' && $user->id === $customerId) {
            $customer = $user;
        } else {
            $customer = $this->getModel($customerId);
        }
        $customer->walletBalanceDeposit = round($this->walletsRepository->getBalanceFor($customer->id, 'deposit'), 2);

        $customer->walletBalanceWithdraw = round($this->walletsRepository->getBalanceFor($customer->id, 'withdraw'), 2);

        $customer->walletBalance = round($customer->walletBalanceDeposit - $customer->walletBalanceWithdraw, 2);


        $customer->save();
    }

    /**
     * Update customer reward balance
     *
     * @param int $customerId
     * @return void
     */
    public function updateRewardBalance(int $customerId)
    {
        $user = user();

        if ($user->accountType() === 'customer' && $user->id === $customerId) {
            $customer = $user;
        } else {
            $customer = $this->getModel($customerId);
        }

        if ($customer) {
            $customer->rewardPointDeposit = $this->rewardsRepository->getBalanceFor($customer->id, 'deposit');

            $customer->rewardPointWithdraw = $this->rewardsRepository->getBalanceFor($customer->id, 'withdraw');

            $customer->rewardPoint = $customer->rewardPointDeposit - $customer->rewardPointWithdraw;

            // $this->rewardsRepository->getRemainBalanceFor($customer->id);

            $customer->save();
        }
    }

    /**
     * Get total customers based on given options
     *
     * @param array $options
     * @return int
     */
    public function total(array $options): int
    {
        $query = $this->getQuery();

        if (!empty($options['from'])) {
            $query->where('createdAt', '>=', Carbon::parse($options['from']));
        }

        if (!empty($options['to'])) {
            $query->where('createdAt', '<=', Carbon::parse($options['to']));
        }

        if (!empty($options['group'])) {
            $query->where('group.id', $options['group']);
        }

        return $query->count();
    }

    public function updateRewardPoint(int $customerId, int $point)
    {
        $customer = $this->getModel($customerId);
        $customer->rewardPoint = $customer->rewardPoint + $point;
        $customer->save();
    }

    /**
     * Update phone number
     *
     * @param Model $model
     * @param Request $request
     * @return void
     */
    public function updatePhoneNumber($model, $request)
    {
        $model->verified = true;
        $model->verificationCode = $model->newVerificationCode;
        $model->phoneNumber = $model->newPhoneNumber;

        $model->newPhoneNumber = null;
        $model->newVerificationCode = null;

        $model->save();
    }

    /**
     * Send new verification phone number
     *
     * @param Model $model
     * @param Request $request
     * @return int
     */
    public function sendVerificationToNewNumber($model, Request $request)
    {
        $model->newVerificationCode = mt_rand(1000, 9999);
        $model->newPhoneNumber = $request->phoneNumber;
        $model->save();

        $this->sendOTP($model->newPhoneNumber, $model->newVerificationCode);

        return $model->newVerificationCode;
    }

    /**
     * send random number (otp) to sms gateway
     *
     * @param string $phoneNumber
     * @param string $verificationCode
     */
    public function sendOTP(string $phoneNumber, string $verificationCode)
    {
        $sms = App::make(SMS::class);
        $message = "كود التحقق الخاص بك هو : {$verificationCode}";
        $sms->send($message, $phoneNumber);
    }

    public function updateGroup(int $customerId, CustomerGroup $group)
    {
        $this->getModel($customerId)->update([
            'group' => $group->sharedInfo(),
        ]);
    }

    /**
     * update favorites count
     *
     * @param int $customerId
     */
    public function updateFavoritesCount(int $customerId)
    {
        $customer = $this->getModel($customerId);

        $customer->favoritesCount = $this->favoritesRepository->total(['customer' => $customerId]);

        $customer->save();
    }

    public function removeCustomerGroup(int $id)
    {
        $this->getQuery()->where('group.id', $id)->update([
            'group' => null,
        ]);
    }

    public function updateCustomerGroup($group)
    {
        $customers = $this->getQuery()->where('group.id', $group->id)->get();

        foreach ($customers as $customer) {
            $customer->update([
                'group' => $group->sharedInfo(),
            ]);
        }
    }

    public function exist(Request $request): bool
    {
        $filter = [];

        $value = strtolower($request->emailOrPhone);

        // if the phone number is numeric, then search in phone numbers
        // otherwise search in email
        if (is_numeric($value) || is_numeric(str_replace('+', '', $value))) {
            $filter[] = 'phoneNumber';
        } else {
            $filter[] = 'email';
        }

        $filter[] = $value;

        $customer = Model::where(...$filter)->first();

        if (!$customer) {
            return false;
        }

        return true;
    }

    /**
     * update favorites count
     *
     * @param int $customerId
     * @param bool $isVerified
     * @return \Illuminate\Http\Resources\Json\JsonResource|\JsonResource
     */
    public function verifyPhone(int $customerId, bool $isVerified = true)
    {
        $customer = $this->getModel($customerId);

        if (!$customer) {
            throw new Exception(trans('auth.invalidData'));
        }

        $customer->isVerified = $isVerified;

        $customer->save();

        return $this->wrap($customer->refresh());
    }

    /**
     * {@inheritDoc}
     */
    public function onUpdate($model, $request, $oldModel)
    {
        if ($model->published == false) {
            $model->accessTokens = [];
            $model->accessToken = null;
            $model->save();
        }
        $this->clubReviewsRepository->updateCustomerReviews($model);
        $this->nutritionSpecialistReviewsRepository->updateCustomerReviews($model);
        $this->productReviewsRepository->updateCustomerReviews($model);
        $this->restaurantsReviewsRepository->updateCustomerReviews($model);
        if (config('app.type') === 'admin') {
            $this->customerGroupsRepository->updateCustomerGroups($request, $oldModel);
        }
    }

    /**
     * Method getCustomer
     *
     * @return void
     */
    public function getCustomer()
    {
        $customer = user();
        if ($customer) {
            return $customer;
        } else {
            $customer = $this->guestsRepository->getByModel('customerDeviceId', Visitor::getDeviceId());

            return $customer;
        }
        // if (!$customer) {
        //     $customer = $this->guestsRepository->getByModel('customerDeviceId', Visitor::getDeviceId());
        // }
        // // dd($customer);
    }

    /**
     * Method createSubscribe
     *
     * @param $subscribeClubs $subscribeClubs
     *
     * @return void
     */
    public function createSubscribe($subscribeClubs, $customer)
    {
        $customer = $this->getModel($customer['id']);
        $customer->reassociate($subscribeClubs, 'subscribeClubs')->save();
    }

    /**
     * Method deleteSubscribe
     *
     * @param $subscribeClubs $subscribeClubs
     * @param $customer $customer
     *
     * @return void
     */
    public function deleteSubscribe($subscribeClubs, $customer)
    {
        $customer = $this->getModel($customer['id']);
        $customer->disassociate($subscribeClubs, 'subscribeClubs')->save();
    }

    /**
     * Method createSubscribe
     *
     * @param $createNutritionSpecialist $createNutritionSpecialist
     *
     * @return void
     */
    public function createNutritionSpecialist($nutritionSpecialist, $customer)
    {
        $customer = $this->getModel($customer['id']);
        $customer->reassociate($nutritionSpecialist, 'nutritionSpecialist')->save();
    }

    /**
     * {@inheritdoc}
     */
    public function onDelete($model, $id)
    {
        if (config('app.type') === 'admin') {
            $this->customerGroupsRepository->deleteCustomerGroups($model);
            $this->healthyDatasRepository->removeCustomer((int) $model->id);
        }
    }

    /**
     * Method customersGroupExport
     *
     * @param $request $request
     *
     * @return void
     */
    public function customersGroupExport($rows)
    {
        $group = $this->customerGroupsRepository->get((int) request()->group);
        foreach ($rows as $key => $row) {
            $customer = $this->customersRepository->getByModel('phoneNumber', (string) $rows[$key][3]);

            if (strlen($rows[$key][3]) > 12) {
                throw new Exception('برجاء ادخال 12 رقم');
            }

            if (strlen($rows[$key][0]) < 2) {
                throw new Exception('الاسم الأول اكبر من حرفين');
            }

            if (strlen($rows[$key][1]) < 2) {
                throw new Exception('اسم العائلةاكبر من حرفين');
            }

            if (strlen($rows[$key][2]) == 0) {
                throw new Exception('برجاء أدخل البريد الالكتروني');
            }
            if (strlen($rows[$key][4]) < 6) {
                throw new Exception('كلمة المرور اكبر من 6 احرف');
            }

            if ((int) (int) $rows[$key][6] == 1) {
                $published = true;
                $isVerified = true;
            } else {
                $published = false;
                $isVerified = false;
            }
            if ($customer) {
                $customer->update([
                    'firstName' => $rows[$key][0],
                    'lastName' => $rows[$key][1],
                    'email' => $rows[$key][2],
                    'phoneNumber' => (string) $rows[$key][3],
                    'password' => (string) $rows[$key][4],
                    'published' => $published,
                    'group' => $group->sharedInfo(),
                ]);
            } else {
                $this->customersRepository->create([
                    'firstName' => $rows[$key][0],
                    'lastName' => $rows[$key][1],
                    'email' => $rows[$key][2],
                    'phoneNumber' => (string) $rows[$key][3],
                    'password' => (string) $rows[$key][4],
                    'published' => $published,
                    'isVerified' => $isVerified,
                    'group' => request()->group,
                ]);
            }
        }
    }

    /**
     * Method deleteStoreOrder
     *
     * @param $id $id
     *
     * @return void
     */
    public function deleteCustomerOrder($id)
    {
        return $this->ordersRepository->getQuery()->where('customer.id', $id)->first();
    }

    /**
     * Method sendEmailForCoupon
     *
     * @return void
     */
    public function sendEmailForRewardCustomer()
    {
        $customers = $this->customersRepository->getQuery()
            ->where('rewardPoint', '>', 0)
            ->get();
        // dd($customers);
        foreach ($customers as $key => $customer) {
            $storeNameMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'storeNameMail');
            $email = $customer['email'];
            $customerFirstName = $customer['firstName'];
            Mail::send([], [], function ($message) use ($customerFirstName, $storeNameMail, $email, $customer) {
                $message->to($email)
                    ->subject('موعد انتهاء النقاط')
                    // here comes what you want
                    ->setBody("
                    <p>
                        مرحبا   [{$customerFirstName}]
                    </p>
                    </br>
                    </br>
                    <hr>
                    </br>
                    <p>
                    لديك {$customer->rewardPoint} اوشكت علي الانتهاء قم استبدال النقاط الخاصه بك في الموعد
                    </p>
                    </br>
                    </br>
                    <hr>
                    </br>
                    مع الشكر و التقدير
                    [{$storeNameMail}]
                ", 'text/html'); // assuming text/plain
            });
        }
        echo 'done';
    }
}
