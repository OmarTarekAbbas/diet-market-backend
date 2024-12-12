<?php

namespace App\Modules\Orders\Repositories;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Modules\Orders\Models\OrderStatus;
use App\Modules\Orders\Services\DeliveryReports;
use App\Modules\Orders\Models\OrderStatusDelivery;
use App\Modules\Orders\Models\OrderDelivery as Model;
use App\Modules\Orders\Models\DeliveryReasonsRejected;
use App\Modules\Orders\Filters\OrderDelivery as Filter;
use App\Modules\Orders\Resources\OrderDelivery as Resource;
use App\Modules\DeliveryMen\Repositories\DeliveryMensRepository;
use App\Modules\DeliveryTransactions\Models\DeliveryTransaction;
use App\Modules\Orders\Resources\DeliveryReasonsNotCompletedOrder;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class OrderDeliveryRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * Repository Name
     *
     * @const string
     */
    const NAME = 'orderDelivery';

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
    const DATA = ['addressOrderCustomer', 'status', 'reason', 'paymentMethod'];

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
    const INTEGER_DATA = ['orderId', 'deliveryMenId', 'restaurantId', 'customerId'];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = ['finalPrice', 'deliveryCost', 'deliveryCommission', 'totalDistanceInt'];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = [];

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
        // 'deliveryReasonsRejected' => DeliveryReasonsRejected::class,
        // 'deliveryNotCompletedOrder' => DeliveryReasonsNotCompletedOrder::class
    ];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel::class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [
        // 'statusLog' => OrderStatusDelivery::class,
    ];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = [
        'deliveryReasonsRejected', 'reason', 'deliveryMenId', 'customerId', 'distanceToTheRestaurant', 'distanceToTheCustomer', 'totalDistance', 'totalDistanceInt', 'location', 'addressOrderCustomer', 'status', 'order', 'restaurant', 'deliveryReasonsRejected', 'customer', 'paymentMethodInfo', 'deliveryReasonsNotCompletedOrder', 'minuteToTheRestaurant', 'minuteToTheCustomer', 'deliveryMen', 'orderId', 'deliveryMenId', 'restaurantId', 'finalPrice', 'deliveryCost', 'deliveryCommission', 'minuteToTheCustomer', 'minuteToTheRestaurant', 'paymentMethod', 'totalMinute',
    ];

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

    /**
     * Order statuses list
     *
     * @const string
     */
    const DELIVERY_MEAL_HAS_NOT_BEEN_RESTAURANT_STATUS = 'mealHasNotBeenRestaurant'; //لم يتم اتخاذ حاله تم تحضير الوجبه // dashboard

    const DELIVERY_PENDING_STATUS = 'pending'; // لسه المندوب موفقش علي حاله التوصيله

    const DELIVERY_ACCEPTED_STATUS = 'accepted'; // المندوب وافق علي التوصيل

    const DELIVERY_REJECTED_STATUS = 'rejected'; // المندوب رفض التوصيل

    const DELIVERY_ON_THE_WAY_Restaurant_STATUS = 'deliveryOnTheWay'; // تأكيد استلام الطلب

    const DELIVERY_COMPLETED_STATUS = 'completed'; // المندوب وصل الاوردر

    const DELIVERY_NOTCOMPLETED_STATUS = 'notCompleted'; // المندوب موصلش الاوردر

    const DELIVERY_IS_NOT_SET_STATUS = 'deliveryIsNotSet'; // لم يتم تعين مندوب // dashboard

    const ORDER_CANCELBYCUSTOMER_STATUS = 'orderCancelByCustomer'; //  تم إلغاء الاوردر من قبل العميل // dashboard

    const ORDER_CANCELBYADMIN_STATUS = 'orderCancelByAdmin'; //  تم إلغاء الاوردر من قبل الادمن // dashboard

    const ORDER_THE_REQUEST_30SECONDS_STATUS = 'dontTookMoreThan30Seconds'; //  تم اسناد الطلب الي المندوب ومر اكثر من 30 ثانية دون قبول او رفض الطلب .
    // const DELIVERY_ON_THE_WAY_CUSTOMER_STATUS = 'deliveryOnTheWayCustomer';

    const SECONDS_STATUS = 30;

    const NEXT_ORDER_STATUS = [
        OrderDeliveryRepository::DELIVERY_PENDING_STATUS => [OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS, OrderDeliveryRepository::DELIVERY_REJECTED_STATUS],

        OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS => [OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS],

        OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS => [OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS, OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS, OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS],

        OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS => [],

        OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS => [],

        OrderDeliveryRepository::ORDER_CANCELBYCUSTOMER_STATUS => [],

        OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS => [],

        OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS => [],

        OrderDeliveryRepository::DELIVERY_IS_NOT_SET_STATUS => [],

        OrderDeliveryRepository::DELIVERY_MEAL_HAS_NOT_BEEN_RESTAURANT_STATUS => [],

    ];

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
     * Method requestOrderIsPending
     *
     * @param $requestl $requestl
     *
     * @return void
     */
    public function requestOrderIsPending($request)
    {
        $DELIVERY_PENDING_STATUS = OrderDeliveryRepository::DELIVERY_PENDING_STATUS;
        $ORDER_THE_REQUEST_30SECONDS_STATUS = OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS;

        $requestOrderIsPending = $this->orderDeliveryRepository->getQuery()->where('deliveryMenId', user()->id)->where(function ($query) use ($DELIVERY_PENDING_STATUS, $ORDER_THE_REQUEST_30SECONDS_STATUS) {
            $query->where('status', '=', $DELIVERY_PENDING_STATUS)
                ->orWhere('status', '=', $ORDER_THE_REQUEST_30SECONDS_STATUS);
        })->first();
        // dd($requestOrderIsPending->createdAt);
        $listOrders = [];
        if ($requestOrderIsPending) {
            // foreach ($requestOrderIsPendings as $key => $requestOrderIsPending) {
            $order = $this->ordersRepository->get($requestOrderIsPending->orderId);
            $restaurant = $this->restaurantsRepository->get($requestOrderIsPending->restaurantId);
            $customer = $this->customersRepository->getQuery()->where('id', (int) $requestOrderIsPending->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);
            $deliveryMen = user();
            $distanceToTheRestaurant = $this->distanceToTheRestaurant($restaurant);
            $distanceToTheCustomer = $this->distanceToTheCustomer($requestOrderIsPending->addressOrderCustomer, $restaurant);
            $totalDistance = $this->totalDistance($distanceToTheRestaurant[0], $distanceToTheCustomer[0]);
            $totalMinute = $this->totalMinute($distanceToTheRestaurant[1], $distanceToTheCustomer[1]);
            // $this->timer($requestOrderIsPending->updatedAt);
            $timeLimit = OrderDeliveryRepository::SECONDS_STATUS; //seconds
            $endTimer = Carbon::parse($requestOrderIsPending->timerDate)->addSecond($timeLimit);
            $now = Carbon::now()->format('Y-m-d H:i:s');
            if ($endTimer >= $now) {
                $now = Carbon::now();
                $requestOrderIsPending->timer = $now->diffInSeconds($endTimer);
                $requestOrderIsPending->save();
            } else {
                $requestOrderIsPending->timer = 0;
                $requestOrderIsPending->save();
            }

            $requestOrderIsPending['order'] = $order;
            $requestOrderIsPending['restaurant'] = $restaurant;
            $requestOrderIsPending['distanceToTheRestaurant'] = $distanceToTheRestaurant[0];
            $requestOrderIsPending['minuteToTheRestaurant'] = $distanceToTheRestaurant[1] . 'د';
            $requestOrderIsPending['distanceToTheCustomer'] = $distanceToTheCustomer[0];
            $requestOrderIsPending['minuteToTheCustomer'] = $distanceToTheCustomer[1] . 'د';
            $requestOrderIsPending['totalDistance'] = $totalDistance;
            $requestOrderIsPending['totalDistanceInt'] = (float) $totalDistance;
            $requestOrderIsPending['totalMinute'] = $totalMinute;
            $requestOrderIsPending['customer'] = $customer;
            $requestOrderIsPending['location'] = [
                'me' => $deliveryMen['location'],
                'restaurant' => $restaurant['location'],
                'customer' => $requestOrderIsPending['addressOrderCustomer']['location'],
            ];
            $listOrders[] = $requestOrderIsPending;
            // }
            $requestOrderUpdate = $this->orderDeliveryRepository->getQuery()->where('deliveryMenId', user()->id)->where(function ($query) use ($DELIVERY_PENDING_STATUS, $ORDER_THE_REQUEST_30SECONDS_STATUS) {
                $query->where('status', '=', $DELIVERY_PENDING_STATUS)
                    ->orWhere('status', '=', $ORDER_THE_REQUEST_30SECONDS_STATUS);
            })->first();
            $requestOrderUpdate->update([
                'distanceToTheRestaurant' => $distanceToTheRestaurant[0],
                'minuteToTheRestaurant' => $distanceToTheRestaurant[1] . 'د',
                'distanceToTheCustomer' => $distanceToTheCustomer[0],
                'minuteToTheCustomer' => $distanceToTheCustomer[1] . 'د',
                'totalDistance' => $totalDistance,
                'totalMinute' => $totalMinute,
                'totalDistanceInt' => (float) $totalDistance,
            ]);

            if ($requestOrderUpdate->timer == 0) {
                $requestOrderUpdate->status = OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS;
                $requestOrderUpdate->nextStatus = static::NEXT_ORDER_STATUS[$requestOrderUpdate->status] ?? [];
                if ($requestOrderUpdate->save()) {
                    if (user()->accountType() == 'user') {
                        $user = user()->name;
                    } elseif (user()->accountType() == 'deliveryMen') {
                        $user = user()->firstName . ' ' . user()->lastName;
                    } elseif (user()->accountType() == 'customer') {
                        $user = user()->firstName . ' ' . user()->lastName;
                    }
                    $orderDeliveryStatus = new OrderStatusDelivery([
                        'orderId' => $requestOrderUpdate['orderId'],
                        'orderDeliveryId' => $requestOrderUpdate->id,
                        'status' => OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS,
                        'creator' => user() ? user()->accountType() : null,
                        'creatorBy' => user() ? $user : null,
                    ]);
                    $orderDeliveryStatus->save();
                    $this->saveStatusLog($requestOrderUpdate->id, $orderDeliveryStatus);
                }
            }
        }

        return $listOrders;
    }

    /**
     * Method requestOrderCronJobs
     *
     * @param $request $request
     *
     * @return void
     */
    public function requestOrderCronJobs()
    {
        $DELIVERY_PENDING_STATUS = OrderDeliveryRepository::DELIVERY_PENDING_STATUS;
        $ORDER_THE_REQUEST_30SECONDS_STATUS = OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS;

        $requestOrderIsPendings = $this->orderDeliveryRepository->getQuery()->where(function ($query) use ($DELIVERY_PENDING_STATUS, $ORDER_THE_REQUEST_30SECONDS_STATUS) {
            $query->where('status', '=', $DELIVERY_PENDING_STATUS)
                ->orWhere('status', '=', $ORDER_THE_REQUEST_30SECONDS_STATUS);
        })->get();
        $listOrders = [];
        foreach ($requestOrderIsPendings as $key => $requestOrderIsPending) {
            $order = $this->ordersRepository->get($requestOrderIsPending->orderId);
            $restaurant = $this->restaurantsRepository->get($requestOrderIsPending->restaurantId);
            $customer = $this->customersRepository->getQuery()->where('id', (int) $requestOrderIsPending->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);
            $deliveryMen = user();

            // $this->timer($requestOrderIsPending->updatedAt);
            $timeLimit = OrderDeliveryRepository::SECONDS_STATUS; //seconds
            $endTimer = Carbon::parse($requestOrderIsPending->timerDate)->addSecond($timeLimit);
            $now = Carbon::now()->format('Y-m-d H:i:s');
            if ($endTimer >= $now) {
                $now = Carbon::now();
                $requestOrderIsPending->timer = $now->diffInSeconds($endTimer);
                $requestOrderIsPending->save();
            } else {
                $requestOrderIsPending->timer = 0;
                $requestOrderIsPending->save();
            }

            $requestOrderIsPending['order'] = $order;
            $requestOrderIsPending['restaurant'] = $restaurant;
            $requestOrderIsPending['customer'] = $customer;
            $listOrders[] = $requestOrderIsPending;
            // }
            $requestOrderUpdates = $this->orderDeliveryRepository->getQuery()->where(function ($query) use ($DELIVERY_PENDING_STATUS, $ORDER_THE_REQUEST_30SECONDS_STATUS) {
                $query->where('status', '=', $DELIVERY_PENDING_STATUS)
                    ->orWhere('status', '=', $ORDER_THE_REQUEST_30SECONDS_STATUS);
            })->get();
            foreach ($requestOrderUpdates as $key => $requestOrderUpdate) {
                if ($requestOrderUpdate->timer == 0) {
                    $requestOrderUpdate->status = OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS;
                    $requestOrderUpdate->nextStatus = static::NEXT_ORDER_STATUS[$requestOrderUpdate->status] ?? [];
                    if ($requestOrderUpdate->save()) {
                        $checkOrderDeliveryStatus = OrderStatusDelivery::where('orderDeliveryId', $requestOrderUpdate->id)->latest('id')->first();
                        if ($checkOrderDeliveryStatus['status'] != OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS) {
                            $orderDeliveryStatus = new OrderStatusDelivery([
                                'orderId' => $requestOrderUpdate['orderId'],
                                'orderDeliveryId' => $requestOrderUpdate->id,
                                'status' => OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS,
                                'creator' => 'السيستم',
                            ]);
                            $orderDeliveryStatus->save();
                            $this->saveStatusLog($requestOrderUpdate->id, $orderDeliveryStatus);
                        }
                    }
                }
            }
        }
    }

    /**
     * Method timer
     *
     * @param $startTime $startTime
     *
     * @return void
     */
    public function timer($requestOrderIsPending)
    {
        $timeLimit = OrderDeliveryRepository::SECONDS_STATUS; //seconds
        $endTimer = Carbon::parse($requestOrderIsPending->timerDate)->addSecond($timeLimit);
        $now = Carbon::now()->format('Y-m-d H:i:s');
        if ($endTimer >= $now) {
            $now = Carbon::now();
            $requestOrderIsPending->timer = $now->diffInSeconds($endTimer);
            $requestOrderIsPending->save();
        } else {
            $requestOrderIsPending->timer = 0;
            $requestOrderIsPending->save();
        }
    }

    /**
     * Method requestOrderAccepted
     *
     * @param $id $id
     *
     * @return void
     */
    public function requestOrderAccepted($id)
    {
        $requestOrderAccepted = Model::find($id);
        $requestOrderAccepted->status = OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS;
        $requestOrderAccepted->nextStatus = static::NEXT_ORDER_STATUS[$requestOrderAccepted->status] ?? [];
        if ($requestOrderAccepted->save()) {
            // $deliveryMen = $this->deliveryMenRepository->get((int)$requestOrderAccepted->deliveryMenId);
            // $deliveryMen['requested'] = $deliveryMen->requested + 1;
            // $deliveryMen->save();
            if (user()->accountType() == 'user') {
                $user = user()->name;
            } elseif (user()->accountType() == 'deliveryMen') {
                $user = user()->firstName . ' ' . user()->lastName;
            } elseif (user()->accountType() == 'customer') {
                $user = user()->firstName . ' ' . user()->lastName;
            }
            $orderDeliveryStatus = new OrderStatusDelivery([
                'orderId' => $requestOrderAccepted['orderId'],
                'orderDeliveryId' => $requestOrderAccepted->id,
                'status' => OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS,
                'creator' => user() ? user()->accountType() : null,
                'creatorBy' => user() ? $user : null,
            ]);

            $orderDeliveryStatus->save();

            $customer = $this->customersRepository->getQuery()->where('id', (int) $requestOrderAccepted->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);
            $deliveryMen = $this->deliveryMenRepository->get((int) $requestOrderAccepted->deliveryMenId);

            $activateSendingMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'activateSendingMail');
            $storeNameMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'storeNameMail');
            if ($requestOrderAccepted['addressOrderCustomer']['email']) {
                $email = $requestOrderAccepted['addressOrderCustomer']['email'];
            } else {
                $email = $customer->email;
            }
            if ($email) {
                if ($activateSendingMail == true) {
                    Mail::send([], [], function ($message) use ($customer, $requestOrderAccepted, $storeNameMail, $deliveryMen, $email) {
                        $message->to($email)
                            ->subject('تم قبول طلبك')
                            // here comes what you want
                            ->setBody("
                        <p>
                            مرحبا   [{$customer->firstName}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        <p>
                        تم قبول طلبك [{$requestOrderAccepted->orderId}] ، شكرًا لك على التسوق في موقعنا.
                        </p>
                        </br>
                        </br>
                        اسم مندوب التوصيل - [{$deliveryMen->firstName}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        رقم مندوب التوصيل -  [966{$deliveryMen->phoneNumber}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        رقم مركبة مندوب التوصيل - [{$deliveryMen->VehicleSerialNumber}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        نوع المركبة - {$deliveryMen->vehicleType['name'][0]['text']}
                        </p>
                        </br>
                        </br>
                        <hr>
                        مع الشكر و التقدير
                        [{$storeNameMail}]

                    ", 'text/html'); // assuming text/plain
                    });
                }
            }

            return $this->saveStatusLog($id, $orderDeliveryStatus);
        }
    }

    /**
     * Method saveStatusLog
     *
     * @param $id $id
     *
     * @return void
     */
    public function saveStatusLog($id, $orderDeliveryStatus)
    {
        $getOrderAccepted = $this->get((int) $id);

        return $getOrderAccepted->reassociate($orderDeliveryStatus->sharedInfo(), 'statusLog')->save();
    }

    /**
     * Method requestOrderRejected
     *
     * @param $id $id
     *
     * @return void
     */
    public function requestOrderRejected($id, $request)
    {
        $requestOrderRejected = Model::find((int) $id);
        if ($request->deliveryReasonsRejected) {
            $deliveryReasonsRejected = $this->deliveryReasonsRejectedsRepository->get((int) $request->deliveryReasonsRejected);
            $deliveryReasonsRejected = $deliveryReasonsRejected->sharedInfo();
        } else {
            $deliveryReasonsRejected = $request->deliveryReasonsRejectedNotes;
        }
        $requestOrderRejected->status = OrderDeliveryRepository::DELIVERY_REJECTED_STATUS;
        $requestOrderRejected->nextStatus = static::NEXT_ORDER_STATUS[$requestOrderRejected->status] ?? [];

        $requestOrderRejected->deliveryReasonsRejected = $deliveryReasonsRejected;
        $requestOrderRejected->save();
        $deliveryMen = $this->deliveryMenRepository->get((int) $requestOrderRejected->deliveryMenId);
        $deliveryMen['requested'] = $deliveryMen->requested - 1;
        $deliveryMen->save();

        if (user()->accountType() == 'user') {
            $user = user()->name;
        } elseif (user()->accountType() == 'deliveryMen') {
            $user = user()->firstName . ' ' . user()->lastName;
        } elseif (user()->accountType() == 'customer') {
            $user = user()->firstName . ' ' . user()->lastName;
        }
        $orderDeliveryStatus = new OrderStatusDelivery([
            'orderId' => $requestOrderRejected['orderId'],
            'orderDeliveryId' => $requestOrderRejected->id,
            'status' => OrderDeliveryRepository::DELIVERY_REJECTED_STATUS,
            'message' => $deliveryReasonsRejected,
            'creator' => user() ? user()->accountType() : null,
            'creatorBy' => user() ?  $user : null,

        ]);

        $orderDeliveryStatus->save();
        if ($request->deliveryReasonsRejectedNotes) {
            $resons = $request->deliveryReasonsRejectedNotes;
        } else {
            $resons = $requestOrderRejected->deliveryReasonsRejected['reason'][0]['text'];
        }

        $deliveryManger = $this->usersRepository->getQuery()->where('type', 'delivery')->first();
        $appLogoDeliveryMen = $this->settingsRepository->getSetting('deliveryMenImage', 'appLogoDeliveryMen');
        $adminEmail = $this->usersRepository->getByModel('name', 'admin');
        if ($deliveryManger) {
            Mail::send([], [], function ($message) use ($deliveryMen, $deliveryManger, $appLogoDeliveryMen, $requestOrderRejected, $resons) {
                $message->to($deliveryManger->email)
                    ->subject('رفض المندوب قبول استلام طلب التوصيل')
                    // here comes what you want
                    ->setBody("
                    <p>
                        مرحبا يا  [{$deliveryManger->name}]
                    </p>
                    </br>
                    </br>
                    <hr>
                    <img src='" . url($appLogoDeliveryMen) . "' alt='رفض المندوب قبول استلام طلب التوصيل' width='150' height='150'>
                    </br>
                    </br>
                    </br>
                    <hr>
                    <p style='text-align:inherit'>
                    قام مندوب [{$deliveryMen->firstName}]
                    </br>
                    </br>
                    <hr> برفض طلب [{$requestOrderRejected->id}]
                    </br>
                    </br>
                    <hr> بسبب [{$resons}]
                    </p>
                ", 'text/html'); // assuming text/plain
            });
            Mail::send([], [], function ($message) use ($deliveryMen, $adminEmail, $appLogoDeliveryMen, $requestOrderRejected, $resons) {
                $message->to($adminEmail->email)
                    ->subject('رفض المندوب قبول استلام طلب التوصيل')
                    // here comes what you want
                    ->setBody("
                    <p>
                        مرحبا يا  [{$adminEmail->name}]
                    </p>
                    </br>
                    </br>
                    <hr>
                    <img src='" . url($appLogoDeliveryMen) . "' alt='رفض المندوب قبول استلام طلب التوصيل' width='150' height='150'>
                    </br>
                    </br>
                    </br>
                    <hr>
                    <p style='text-align:inherit'>
                    قام مندوب [{$deliveryMen->firstName}]
                    </br>
                    </br>
                    <hr> برفض طلب [{$requestOrderRejected->id}]
                    </br>
                    </br>
                    <hr> بسبب [{$resons}]
                    </p>
                ", 'text/html'); // assuming text/plain
            });
        } else {
            Mail::send([], [], function ($message) use ($deliveryMen, $adminEmail, $appLogoDeliveryMen, $requestOrderRejected, $resons) {
                $message->to($adminEmail->email)
                    ->subject('رفض المندوب قبول استلام طلب التوصيل')
                    // here comes what you want
                    ->setBody("
                    <p>
                        مرحبا يا  [{$adminEmail->name}]
                    </p>
                    </br>
                    </br>
                    <hr>
                    <img src='" . url($appLogoDeliveryMen) . "' alt='رفض المندوب قبول استلام طلب التوصيل' width='150' height='150'>
                    </br>
                    </br>
                    </br>
                    <hr>
                    <p style='text-align:inherit'>
                    قام مندوب [{$deliveryMen->firstName}]
                    </br>
                    </br>
                    <hr> برفض طلب [{$requestOrderRejected->id}]
                    </br>
                    </br>
                    <hr> بسبب [{$resons}]
                    </p>
                ", 'text/html'); // assuming text/plain
            });
        }

        return $this->saveStatusLog($id, $orderDeliveryStatus);
    }

    /**
     * Method ordersCurrent
     *
     * @param $request $request
     *
     * @return void
     */
    public function ordersCurrent($request)
    {
        $requestOrderIsAccepteds = $this->orderDeliveryRepository->getQuery()->where('deliveryMenId', user()->id)->where('status', OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS)->get();
        $listOrders = [];
        foreach ($requestOrderIsAccepteds as $key => $requestOrderIsAccepted) {
            $order = $this->ordersRepository->get($requestOrderIsAccepted->orderId);
            $restaurant = $this->restaurantsRepository->get($requestOrderIsAccepted->restaurantId);
            $customer = $this->customersRepository->getQuery()->where('id', (int) $requestOrderIsAccepted->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);
            $deliveryMen = user();
            $requestOrderIsAccepted['order'] = $order;
            $requestOrderIsAccepted['restaurant'] = $restaurant;
            $requestOrderIsAccepted['customer'] = $customer;
            $requestOrderIsAccepted['location'] = [
                'me' => $deliveryMen['location'],
                'restaurant' => $restaurant['location'],
                'customer' => $requestOrderIsAccepted['addressOrderCustomer']['location'],
            ];
            $listOrders[] = $requestOrderIsAccepted;
        }

        return $listOrders;
    }

    /**
     * Method ordersCurrent
     *
     * @param $request $request
     *
     * @return void
     */
    public function deliveryOnTheWay($orderItems = [])
    {
        $orderItems = $this->orderDeliveryRepository->getQuery()->whereIn('id', array_map('intval', $orderItems))->get();

        $listOrders = [];

        foreach ($orderItems as $key => $orderItem) {
            $orderItem->status = OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS;
            $orderItem->nextStatus = static::NEXT_ORDER_STATUS[$orderItem->status] ?? [];
            if ($orderItem->save()) {
                $changeStatusOrder = $this->ordersRepository->getQuery()->where('id', (int) $orderItem->orderId)->first();
                $status = OrdersRepository::ON_THE_WAY_STATUS;
                $changeStatusOrder->status = $status;
                $changeStatusOrder->nextStatus = OrdersRepository::NEXT_ORDER_STATUS_DELIVERY_BY_DITE_MARKIT_FOOD[$status] ?? [];
                $changeStatusOrder->save();
                $orderStatus = new OrderStatus([
                    'orderId' => $orderItem->orderId,
                    'status' => $status,
                    'notes' => request()->notes ?? '',
                    'creator' => user() ? user()->accountType() : null,
                ]);
                $orderStatus->save();
                $customer = $this->customersRepository->getModel((int) $changeStatusOrder->customer['id']);
                $notification = $this->notificationsRepository->create([
                    'title' => trans('notifications.order.titleFood.' . $status),
                    'content' => trans('notifications.order.contentFood.' . $status),
                    'type' => 'foodOrder',
                    'user' => $customer,
                    'pushNotification' => true,
                    'extra' => [
                        'type' => 'foodOrder',
                        'status' => $status,
                        'orderId' => $changeStatusOrder->id,
                    ],
                ]);
                $this->notificationsRepository->getQuery()->where('id', $notification->id)->delete();
                if (user()->accountType() == 'user') {
                    $user = user()->name;
                } elseif (user()->accountType() == 'deliveryMen') {
                    $user = user()->firstName . ' ' . user()->lastName;
                } elseif (user()->accountType() == 'customer') {
                    $user = user()->firstName . ' ' . user()->lastName;
                }
                $orderDeliveryStatus = new OrderStatusDelivery([
                    'orderId' => $orderItem['orderId'],
                    'orderDeliveryId' => $orderItem->id,
                    'status' => OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS,
                    // 'message' => $message,
                    'creator' => user() ? user()->accountType() : null,
                    'creatorBy' => user() ? $user : null,

                ]);

                $orderDeliveryStatus->save();

                $this->saveStatusLog($orderItem->id, $orderDeliveryStatus);

                $order = $this->ordersRepository->get($orderItem->orderId);
                $restaurant = $this->restaurantsRepository->get($orderItem->restaurantId);
                $customer = $this->customersRepository->getQuery()->where('id', (int) $orderItem->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);
                $deliveryMen = user();

                $orderItem['order'] = $order;
                $orderItem['restaurant'] = $restaurant;
                $orderItem['customer'] = $customer;

                $orderItem['location'] = [
                    'me' => $deliveryMen['location'],
                    'restaurant' => $restaurant['location'],
                    'customer' => $orderItem['addressOrderCustomer']['location'],
                ];

                $listOrders[] = $orderItem;
            }
        }

        return $listOrders;
    }

    /**
     * Method reternOrderAccepted
     *
     * @param $id $id
     *
     * @return void
     */
    public function reternOrderAccepted($id)
    {
        $requestOrderIsPending = $this->orderDeliveryRepository->get($id);
        $listOrders = [];
        if ($requestOrderIsPending) {
            // foreach ($requestOrderIsPendings as $key => $requestOrderIsPending) {
            $order = $this->ordersRepository->get($requestOrderIsPending->orderId);
            $restaurant = $this->restaurantsRepository->get($requestOrderIsPending->restaurantId);
            $customer = $this->customersRepository->getQuery()->where('id', (int) $requestOrderIsPending->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);
            $deliveryMen = user();
            $requestOrderIsPending['order'] = $order;
            $requestOrderIsPending['restaurant'] = $restaurant;
            $requestOrderIsPending['customer'] = $customer;

            $requestOrderIsPending['location'] = [
                'me' => $deliveryMen['location'],
                'restaurant' => $restaurant['location'],
                'customer' => $requestOrderIsPending['addressOrderCustomer']['location'],
            ];
            $listOrders[] = $requestOrderIsPending;
            // }
        }

        return $listOrders;
    }

    /**
     * Method listCurrentAndnewRequest
     *
     * @param $request $request
     *
     * @return void
     */
    public function listCurrentAndDelivertOnTheWay($request)
    {
        $DELIVERY_ACCEPTED_STATUS = OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS;
        $DELIVERY_ON_THE_WAY_Restaurant_STATUS = OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS;

        $requestOrderIsAccepteds = $this->orderDeliveryRepository->getQuery()->where('deliveryMenId', user()->id)->where(function ($query) use ($DELIVERY_ACCEPTED_STATUS, $DELIVERY_ON_THE_WAY_Restaurant_STATUS) {
            $query->where('status', '=', $DELIVERY_ACCEPTED_STATUS)
                ->orWhere('status', '=', $DELIVERY_ON_THE_WAY_Restaurant_STATUS);
        })->get();

        $listOrders = [];
        foreach ($requestOrderIsAccepteds as $key => $requestOrderIsAccepted) {
            $order = $this->ordersRepository->get($requestOrderIsAccepted->orderId);
            $restaurant = $this->restaurantsRepository->get($requestOrderIsAccepted->restaurantId);
            $customer = $this->customersRepository->getQuery()->where('id', (int) $requestOrderIsAccepted->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);
            // dd($customer);
            $deliveryMen = user();
            $requestOrderIsAccepted['order'] = $order;
            $requestOrderIsAccepted['restaurant'] = $restaurant;
            $requestOrderIsAccepted['customer'] = $customer;
            $requestOrderIsAccepted['location'] = [
                'me' => $deliveryMen['location'],
                'restaurant' => $restaurant['location'],
                'customer' => $requestOrderIsAccepted['addressOrderCustomer']['location'],
            ];
            $listOrders[] = $requestOrderIsAccepted;
        }

        return $listOrders;
    }

    /**
     * Method show
     *
     * @param $id $id
     *
     * @return void
     */
    public function show($id)
    {
        $requestOrderIsPending = $id;
        if ($requestOrderIsPending) {
            $order = $this->ordersRepository->get($requestOrderIsPending->orderId);
            $restaurant = $this->restaurantsRepository->get($requestOrderIsPending->restaurantId);
            $customer = $this->customersRepository->getQuery()->where('id', (int) $requestOrderIsPending->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);
            $deliveryMen = user();
            $requestOrderIsPending['order'] = $order;
            $requestOrderIsPending['restaurant'] = $restaurant;
            $requestOrderIsPending['customer'] = $customer;

            $requestOrderIsPending['location'] = [
                'me' => $deliveryMen['location'],
                'restaurant' => $restaurant['location'],
                'customer' => $requestOrderIsPending['addressOrderCustomer']['location'],
            ];

            return $requestOrderIsPending;
        }
    }

    /**
     * Method deliveryCompletedOrder
     *
     * @param $id $id
     * @param $request $request
     *
     * @return void
     */
    public function deliveryCompletedOrder($id, $request)
    {
        $orderDelivery = Model::find($id);
        $orderDelivery['status'] = OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS;
        $orderDelivery->nextStatus = static::NEXT_ORDER_STATUS[$orderDelivery->status] ?? [];
        if ($orderDelivery->save()) {
            $changeStatusOrder = $this->ordersRepository->getQuery()->where('id', (int) $orderDelivery->orderId)->first();
            $status = OrdersRepository::COMPLETED_STATUS;
            $changeStatusOrder->status = $status;
            $changeStatusOrder->nextStatus = OrdersRepository::NEXT_ORDER_STATUS_DELIVERY_BY_DITE_MARKIT_FOOD[$status] ?? [];
            $changeStatusOrder->save();
            $orderStatus = new OrderStatus([
                'orderId' => $orderDelivery->orderId,
                'status' => $status,
                'notes' => request()->notes ?? '',
                'creator' => user() ? user()->accountType() : null,
            ]);
            $orderStatus->save();
            $this->transactionsRepository->add($changeStatusOrder);
            $customer = $this->customersRepository->getModel((int) $changeStatusOrder->customer['id']);
            $notification = $this->notificationsRepository->create([
                'title' => trans('notifications.order.titleFood.' . $status),
                'content' => trans('notifications.order.contentFood.' . $status),
                'type' => 'foodOrder',
                'user' => $customer,
                'pushNotification' => true,
                'extra' => [
                    'type' => 'foodOrder',
                    'status' => $status,
                    'orderId' => $changeStatusOrder->id,
                ],
            ]);
            $this->notificationsRepository->getQuery()->where('id', $notification->id)->delete();

            if (user()->accountType() == 'user') {
                $user = user()->name;
            } elseif (user()->accountType() == 'deliveryMen') {
                $user = user()->firstName . ' ' . user()->lastName;
            } elseif (user()->accountType() == 'customer') {
                $user = user()->firstName . ' ' . user()->lastName;
            }
            $orderDeliveryStatus = new OrderStatusDelivery([
                'orderId' => $orderDelivery['orderId'],
                'orderDeliveryId' => $orderDelivery->id,
                'status' => OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS,
                'creator' => user() ? user()->accountType() : null,
                'creatorBy' => user() ? $user : null,

            ]);
            $orderDeliveryStatus->save();

            $deliveryMenCost = $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenCost');
            $deliveryCommission = $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission');
            $commissionDiteMarket = $deliveryMenCost - $deliveryCommission;
            $title = "طلب توصيل [#{$orderDelivery->id}] عمولة التوصيل [{$deliveryCommission} ر.س]";

            if ($changeStatusOrder->paymentMethod == 'cashOnDelivery') {
                // $amount = $commissionDiteMarket; //نظام القديم
                $amount = $changeStatusOrder->finalPrice - $deliveryCommission; //نظام الجديد
                $this->walletDeliveryRepository->withdraw([
                    'delivery' => $orderDelivery['deliveryMenId'],
                    'amount' => $amount,
                    'orderId' => $orderDelivery->id,
                    'pushNotification' => true,
                    'title' => $title,
                    // 'reason' => $title,
                    'amount' => $amount,
                    'paymentMethod' => $changeStatusOrder->paymentMethod,
                    'commissionDiteMarket' => $commissionDiteMarket,
                    'totalAmountOrder' => $changeStatusOrder->finalPrice,
                ]);
            } else {
                // $amount = $changeStatusOrder->finalPrice - $commissionDiteMarket; //نظام القديم
                $amount = $deliveryCommission; //نظام الجديد
                $this->walletDeliveryRepository->deposit([
                    'delivery' => $orderDelivery['deliveryMenId'],
                    'amount' => $amount,
                    'orderId' => $orderDelivery->id,
                    'pushNotification' => true,
                    'title' => $title,
                    // 'reason' => $title,
                    'amount' => $amount,
                    'paymentMethod' => $changeStatusOrder->paymentMethod,
                    'commissionDiteMarket' => $commissionDiteMarket,
                    'totalAmountOrder' => $changeStatusOrder->finalPrice,
                ]);
            }
            $delivery = $this->deliveryMenRepository->getModel($orderDelivery->deliveryMenId);
            $deliveryTransactions = new DeliveryTransaction([
                'deliveryMan' => $delivery->only(['id', 'firstName', 'lastName', 'email']),
                'amount' => $changeStatusOrder->finalPrice - $commissionDiteMarket,
                'commissionDiteMarket' => $commissionDiteMarket,
                'deliveryCommission' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'),
                'orderDelivery' => $orderDelivery->id,
                'totalAmountOrder' => $changeStatusOrder->finalPrice - $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'),
                'deliveryStatus' => OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS,
                'paymentMethod' => $changeStatusOrder->paymentMethod,
                'published' => true,

            ]);
            $deliveryTransactions->save();

            $customer = $this->customersRepository->getQuery()->where('id', (int) $orderDelivery->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);
            $deliveryMen = $this->deliveryMenRepository->get((int) $orderDelivery->deliveryMenId);

            $activateSendingMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'activateSendingMail');
            $storeNameMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'storeNameMail');
            if ($orderDelivery['addressOrderCustomer']['email']) {
                $email = $orderDelivery['addressOrderCustomer']['email'];
            } else {
                $email = $customer->email;
            }
            if ($email) {
                if ($activateSendingMail == true) {
                    Mail::send([], [], function ($message) use ($customer, $orderDelivery, $storeNameMail, $deliveryMen, $email) {
                        $message->to($email)
                            ->subject('تم تسليم طلبك')
                            // here comes what you want
                            ->setBody("
                        <p>
                            مرحبا   [{$customer->firstName}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        <p>
                        طلبك- تم تسليم [{$orderDelivery->orderId}] بنجاح. شكرا لشراء منتج من متجرنا.
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
            }

            $this->saveStatusLog($id, $orderDeliveryStatus);
            $deliveryMen = $this->deliveryMenRepository->get((int) $orderDelivery->deliveryMenId);
            $deliveryMen->reassociate($orderDelivery, 'orders')->save();
            $deliveryMen['requested'] = $deliveryMen->requested - 1;
            $checkCountUpdateStatuOrders = $this->checkCountUpdateStatuOrders($deliveryMen);
            // dd($checkCountUpdateStatuOrders);
            if ($checkCountUpdateStatuOrders == 0) {
                if ($deliveryMen['NewPublished'] == false) {
                    $deliveryMen->save();

                    return false;
                }
            }

            return $deliveryMen->save();
        }
        // return $listOrders;
    }

    /**
     * Method logOutForPublished
     *
     * @param $id $id
     *
     * @return void
     */
    public function logOutForPublished($id)
    {
        $deliveryMen = $this->deliveryMenRepository->get($id);
        $deliveryMen['NewPublished'] = true;
        $deliveryMen['published'] = false;
        $deliveryMen['status'] = false;
        $deliveryMen['accessTokens'] = [];
        $deliveryMen['accessToken'] = null;
        $deliveryMen['devices'] = [];
        $deliveryMen->save();
    }

    /**
     * Method deliveryNotCompletedOrder
     *
     * @param $id $id
     * @param $request $request
     *
     * @return void
     */
    public function deliveryNotCompletedOrder($id, $request)
    {
        $deliveryNotCompletedOrder = Model::find((int) $id);

        if ($request->deliveryReasonsNotCompletedOrder) {
            $deliveryReasonsNotCompletedOrder = $this->deliveryReasonsNotCompletedOrdersRepository->get((int) $request->deliveryReasonsNotCompletedOrder);

            $deliveryReasonsNotCompletedOrderVal = $deliveryReasonsNotCompletedOrder->sharedInfo();
        } else {
            $deliveryReasonsNotCompletedOrderVal = $request->deliveryReasonsNotCompletedOrderNotes;
        }

        $deliveryNotCompletedOrder->status = OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS;
        $deliveryNotCompletedOrder->nextStatus = static::NEXT_ORDER_STATUS[$deliveryNotCompletedOrder->status] ?? [];
        $deliveryNotCompletedOrder->deliveryReasonsNotCompletedOrder = $deliveryReasonsNotCompletedOrderVal;
        $deliveryNotCompletedOrder->save();

        $changeStatusOrder = $this->ordersRepository->getQuery()->where('id', (int) $deliveryNotCompletedOrder->orderId)->first();
        $status = OrdersRepository::COMPLETED_STATUS;
        $changeStatusOrder->status = $status;
        $changeStatusOrder->nextStatus = OrdersRepository::NEXT_ORDER_STATUS_DELIVERY_BY_DITE_MARKIT_FOOD[$status] ?? [];
        $changeStatusOrder->save();
        $orderStatus = new OrderStatus([
            'orderId' => $deliveryNotCompletedOrder->orderId,
            'status' => $status,
            'notes' => request()->notes ?? '',
            'creator' => user() ? user()->accountType() : null,
        ]);
        $orderStatus->save();

        // $customer =  $this->customersRepository->getModel((int)$changeStatusOrder->customer['id']);
        // $this->notificationsRepository->create([
        //     'title' => trans('notifications.order.title.' . $status),
        //     'content' => trans('notifications.order.content.' . $status),
        //     'type' => 'order',
        //     'user' => $customer,
        //     'pushNotification' => true,
        //     'extra' => [
        //         'type' => 'order',
        //         'status' => $status,
        //         'orderId' => $changeStatusOrder->id,
        //     ],
        // ]);

        if (user()->accountType() == 'user') {
            $user = user()->name;
        } elseif (user()->accountType() == 'deliveryMen') {
            $user = user()->firstName . ' ' . user()->lastName;
        } elseif (user()->accountType() == 'customer') {
            $user = user()->firstName . ' ' . user()->lastName;
        }
        $orderDeliveryStatus = new OrderStatusDelivery([
            'orderId' => $deliveryNotCompletedOrder['orderId'],
            'orderDeliveryId' => $deliveryNotCompletedOrder->id,
            'status' => OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS,
            // 'message' => $message,
            'creator' => user() ? user()->accountType() : null,
            'creatorBy' => user() ? $user : null,

        ]);

        $orderDeliveryStatus->save();
        $changeStatusOrder = $this->ordersRepository->getQuery()->where('id', (int) $deliveryNotCompletedOrder->orderId)->first();

        $deliveryMenCost = $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenCost');
        $deliveryCommission = $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission');
        $commissionDiteMarket = $deliveryMenCost - $deliveryCommission;
        $title = "طلب توصيل [#{$deliveryNotCompletedOrder->id}] عمولة التوصيل [{$deliveryCommission} ر.س]";

        if ($changeStatusOrder->paymentMethod == 'cashOnDelivery') {
            // $amount = $commissionDiteMarket; //نظام القديم
            $amount = $deliveryCommission; //نظام الجديد
            $this->walletDeliveryRepository->deposit([
                'delivery' => $deliveryNotCompletedOrder['deliveryMenId'],
                'amount' => $amount,
                'orderId' => $deliveryNotCompletedOrder->id,
                'pushNotification' => true,
                'title' => $title,
                // 'reason' => $title,
                'amount' => $amount,
                'paymentMethod' => $changeStatusOrder->paymentMethod,
                'commissionDiteMarket' => $commissionDiteMarket,
                'totalAmountOrder' => $changeStatusOrder->finalPrice,
            ]);
        } else {
            // $amount = $changeStatusOrder->finalPrice - $commissionDiteMarket; //نظام القديم
            $amount = $deliveryCommission; //نظام الجديد
            $this->walletDeliveryRepository->deposit([
                'delivery' => $deliveryNotCompletedOrder['deliveryMenId'],
                'amount' => $amount,
                'orderId' => $deliveryNotCompletedOrder->id,
                'pushNotification' => true,
                'title' => $title,
                // 'reason' => $title,
                'amount' => $amount,
                'paymentMethod' => $changeStatusOrder->paymentMethod,
                'commissionDiteMarket' => $commissionDiteMarket,
                'totalAmountOrder' => $changeStatusOrder->finalPrice,
            ]);
        }

        $delivery = $this->deliveryMenRepository->getModel($deliveryNotCompletedOrder->deliveryMenId);
        $deliveryTransactions = new DeliveryTransaction([
            'deliveryMan' => $delivery->only(['id', 'firstName', 'lastName', 'email']),
            'amount' => $changeStatusOrder->finalPrice - $commissionDiteMarket,
            'commissionDiteMarket' => $commissionDiteMarket,
            'totalAmountOrder' => $changeStatusOrder->finalPrice - $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'),
            'deliveryCommission' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'),
            'orderDelivery' => $deliveryNotCompletedOrder->id,
            'deliveryStatus' => OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS,
            'paymentMethod' => $changeStatusOrder->paymentMethod,
            'published' => true,
        ]);
        $deliveryTransactions->save();
        if ($request->deliveryReasonsNotCompletedOrderNotes) {
            $resons = $request->deliveryReasonsNotCompletedOrderNotes;
        } else {
            $resons = $deliveryNotCompletedOrder->deliveryReasonsNotCompletedOrder['reason'][0]['text'];
        }
        $deliveryManger = $this->usersRepository->getQuery()->where('type', 'delivery')->first();
        $appLogoDeliveryMen = $this->settingsRepository->getSetting('deliveryMenImage', 'appLogoDeliveryMen');
        $adminEmail = $this->usersRepository->getByModel('name', 'admin');
        $deliveryMen = $this->deliveryMenRepository->get((int) $deliveryNotCompletedOrder->deliveryMenId);

        if ($deliveryManger) {
            $emails = [$deliveryManger->email];
            Mail::send([], [], function ($message) use ($deliveryMen, $emails, $deliveryManger, $appLogoDeliveryMen, $deliveryNotCompletedOrder, $resons) {
                $message->to($emails)
                    ->subject('عدم استلام الطلب من قبل العميل')
                    // here comes what you want
                    ->setBody("
                    <p>
                        مرحبا يا  [{$deliveryManger->name}]
                    </p>
                    </br>
                    </br>
                    <hr>
                    <img src='" . url($appLogoDeliveryMen) . "' alt='عدم استلام الطلب من قبل العميل' width='150' height='150'>
                    </br>
                    </br>
                    </br>
                    <hr>
                    <p>
                    قام مندوب [{$deliveryMen->firstName}]
                    </br>
                    </br>
                    </br>
                    <hr>
                    بتغيير حالة طلب [{$deliveryNotCompletedOrder->id}]
                    </br>
                    </br>
                    </br>
                    <hr>
                    إلى لم يتم استلامها بسبب [{$resons}]
                    </p>
                ", 'text/html'); // assuming text/plain
                // dd($message);
            });
            $emails = [$adminEmail->email];
            Mail::send([], [], function ($message) use ($deliveryMen, $emails, $adminEmail, $appLogoDeliveryMen, $deliveryNotCompletedOrder, $resons) {
                $message->to($emails)
                    ->subject('عدم استلام الطلب من قبل العميل')
                    // here comes what you want
                    ->setBody("
                    <p>
                        مرحبا يا  [{$adminEmail->name}]
                    </p>
                    </br>
                    </br>
                    <hr>
                    <img src='" . url($appLogoDeliveryMen) . "' alt='عدم استلام الطلب من قبل العميل' width='150' height='150'>
                    </br>
                    </br>
                    </br>
                    <hr>
                    <p>
                    قام مندوب [{$deliveryMen->firstName}]
                    </br>
                    </br>
                    </br>
                    <hr>
                    بتغيير حالة طلب [{$deliveryNotCompletedOrder->id}]
                    </br>
                    </br>
                    </br>
                    <hr>
                    إلى لم يتم استلامها بسبب [{$resons}]
                    </p>
                ", 'text/html'); // assuming text/plain
                // dd($message);
            });
        } else {
            $emails = [$adminEmail->email];
            Mail::send([], [], function ($message) use ($deliveryMen, $emails, $adminEmail, $appLogoDeliveryMen, $deliveryNotCompletedOrder, $resons) {
                $message->to($emails)
                    ->subject('عدم استلام الطلب من قبل العميل')
                    // here comes what you want
                    ->setBody("
                    <p>
                        مرحبا يا  [{$adminEmail->name}]
                    </p>
                    </br>
                    </br>
                    <hr>
                    <img src='" . url($appLogoDeliveryMen) . "' alt='عدم استلام الطلب من قبل العميل' width='150' height='150'>
                    </br>
                    </br>
                    </br>
                    <hr>
                    <p>
                    قام مندوب [{$deliveryMen->firstName}]
                    </br>
                    </br>
                    </br>
                    <hr>
                    بتغيير حالة طلب [{$deliveryNotCompletedOrder->id}]
                    </br>
                    </br>
                    </br>
                    <hr>
                    إلى لم يتم استلامها بسبب [{$resons}]
                    </p>
                ", 'text/html'); // assuming text/plain
                // dd($message);
            });
        }


        $this->saveStatusLog($id, $orderDeliveryStatus);
        $deliveryMen->reassociate($deliveryNotCompletedOrder, 'orders')->save();
        $deliveryMen['requested'] = $deliveryMen->requested - 1;
        $checkCountUpdateStatuOrders = $this->checkCountUpdateStatuOrders($deliveryMen);
        // dd($checkCountUpdateStatuOrders);
        if ($checkCountUpdateStatuOrders == 0) {
            if ($deliveryMen['NewPublished'] == false) {
                $deliveryMen->save();

                return false;
            }
        }

        return $deliveryMen->save();
    }

    /**
     * Method checkUpdateStatuOrders
     *
     * @param $user $user
     *
     * @return void
     */
    public function checkUpdateStatuOrders($user)
    {
        $DELIVERY_ACCEPTED_STATUS = OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS;
        $DELIVERY_ON_THE_WAY_Restaurant_STATUS = OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS;

        return $this->orderDeliveryRepository->getQuery()->where('deliveryMenId', $user->id)->where(function ($query) use ($DELIVERY_ACCEPTED_STATUS, $DELIVERY_ON_THE_WAY_Restaurant_STATUS) {
            $query->where('status', '=', $DELIVERY_ACCEPTED_STATUS)
                ->orWhere('status', '=', $DELIVERY_ON_THE_WAY_Restaurant_STATUS);
        })->first();
    }

    /**
     * Method checkCountUpdateStatuOrders
     *
     * @param $user $user
     *
     * @return void
     */
    public function checkCountUpdateStatuOrders($user)
    {
        $DELIVERY_ACCEPTED_STATUS = OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS;
        $DELIVERY_ON_THE_WAY_Restaurant_STATUS = OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS;

        return $this->orderDeliveryRepository->getQuery()->where('deliveryMenId', $user->id)->where(function ($query) use ($DELIVERY_ACCEPTED_STATUS, $DELIVERY_ON_THE_WAY_Restaurant_STATUS) {
            $query->where('status', '=', $DELIVERY_ACCEPTED_STATUS)
                ->orWhere('status', '=', $DELIVERY_ON_THE_WAY_Restaurant_STATUS);
        })->count();
    }

    /**
     * Method distanceToTheRestaurant
     *
     * @param $restaurant $restaurant
     *
     * @return void
     */
    public function distanceToTheRestaurant($restaurant, $delevery = null)
    {
        $locationRestaurant = $restaurant['location'];
        $restaurantLocation = [
            $locationRestaurant['coordinates'][0],
            $locationRestaurant['coordinates'][1],
        ];
        $key = KEY_GOOGLE_MAB;
        if ($delevery) {
            $deliveryMen = $this->deliveryMenRepository->get((int) $delevery);
        } else {
            $deliveryMen = user();
        }
        // dd($deliveryMen);
        $locationDeliveryMen = [
            $deliveryMen['location']['coordinates'][0],
            $deliveryMen['location']['coordinates'][1],
        ];

        $URL = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$locationDeliveryMen[0]},{$locationDeliveryMen[1]}&destinations={$restaurantLocation[0]},{$restaurantLocation[1]}&key={$key}";

        return $this->getCurlExec($URL);
    }

    /**
     * Method distanceToTheCustomer
     *
     * @param $address $address
     *
     * @return void
     */
    public function distanceToTheCustomer($address, $restaurant)
    {
        $locationCustomer = $address['location'];
        $customerLocation = [
            $locationCustomer['coordinates'][0],
            $locationCustomer['coordinates'][1],
        ];
        $key = KEY_GOOGLE_MAB;
        $locationRestaurant = [
            $restaurant['location']['coordinates'][0],
            $restaurant['location']['coordinates'][1],
        ];

        $URL = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$locationRestaurant[0]},{$locationRestaurant[1]}&destinations={$customerLocation[0]},{$customerLocation[1]}&key={$key}";

        return $this->getCurlExec($URL);
    }

    /**
     * Method getCurlExec
     *
     * @param $URL $URL
     *
     * @return void
     */
    public function getCurlExec($URL)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);
        $response = curl_exec($curl);
        $decodeResponseLocation = json_decode($response, true); // Set second argument as TRUE
        $rowResponseLocation = $decodeResponseLocation['rows'];
        foreach ($rowResponseLocation as $row) {
            $elementDistance = $row['elements'];
            foreach ($elementDistance as $distance) {
                $getdistance = $distance['distance']['text'] ?? 0;
            }
            foreach ($elementDistance as $minute) {
                $getSecond = $minute['duration']['value'] ?? 0;
            }
        }
        $minutes = round($getSecond / 60);
        // dd($getdistance, $minutes);
        return [$getdistance, $minutes];
    }

    
    /**
     * It takes two strings, removes the ' km' from the end of each string, converts the strings to
     * floats, adds the floats together, and returns the sum as a string with ' km' appended to the end
     * 
     * @param distanceToTheRestaurant The distance from the restaurant to the customer.
     * @param distanceToTheCustomer The distance from the restaurant to the customer.
     * 
     * @return The total distance in kilometers.
     */
    public function totalDistance($distanceToTheRestaurant, $distanceToTheCustomer)
    {
        $distanceToTheRestaurant = trim($distanceToTheRestaurant, ' km');
        $distanceToTheCustomer = trim($distanceToTheCustomer, ' km');
        $totalDistance = (float) $distanceToTheRestaurant + (float) $distanceToTheCustomer;

        return $totalDistance . ' km';
    }

    /**
     * It takes two parameters, adds them together, and returns the result
     * 
     * @param distanceToTheRestaurant The distance from the restaurant to the customer.
     * @param distanceToTheCustomer The distance from the restaurant to the customer.
     * 
     * @return The total minutes of the distance to the restaurant and the distance to the customer.
     */
    public function totalMinute($distanceToTheRestaurant, $distanceToTheCustomer)
    {
        $totalMinute = (float) $distanceToTheRestaurant + (float) $distanceToTheCustomer;

        return $totalMinute . 'د';
    }

    /**
     * Method deliveryLocation
     *
     * @return void
     */
    public function deliveryLocation()
    {
        $deliveryMen = user();

        return $deliveryMen['location'];
    }

    /**
     * Method restaurantLocation
     *
     * @return void
     */
    public function restaurantLocation()
    {
        $deliveryMen = user();
        //this location Delivery Men
        $locationDeliveryMen = [
            $deliveryMen['location']['coordinates'][0],
            $deliveryMen['location']['coordinates'][1],
        ];
        $restaurants = $this->restaurantsRepository->getQuery()->where('published', true)->get();
        $arraySubscribeClub = [];
        foreach ($restaurants as $key => $restaurant) {
            //this location restaurant
            $restaurantLocation = [
                $restaurant['location']['coordinates'][0],
                $restaurant['location']['coordinates'][1],
            ];

            //key Google Map
            $key = KEY_GOOGLE_MAB;
            $URL = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$locationDeliveryMen[0]},{$locationDeliveryMen[1]}&destinations={$restaurantLocation[0]},{$restaurantLocation[1]}&key={$key}";
            //Method getCurlExec
            $getkm = $this->getCurlExec($URL);
            $getkm2 = trim($getkm[0], ' km');
            $getkm2 = intval(preg_replace('/[^\d.]/', '', $getkm2));
            $deliveryMenRangeInKm = $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenRangeInKm');

            // dd($getkm[0] ,$deliveryMenRangeInKm , (float) $getkm[0]);

            if ($getkm2 > $deliveryMenRangeInKm) { // 30Km For Admin Setting
                continue;
            } else {
                $restaurantOnly['id'] = $restaurant->id;
                $restaurantOnly['logo'] = url($restaurant->logoImage);
                $restaurantOnly['location'] = $restaurant->location;
                $restaurantOnly['Km'] = $getkm[0];
                $restaurantOnly['minutes'] = $getkm[1] . 'د';
                $arraySubscribeClub[] = $restaurantOnly;
            }
        }

        return $arraySubscribeClub;
    }

    /**
     * Method orderRecords
     *
     * @param $request $request
     *
     * @return
     */
    public function orderRecords($request)
    {
        $DELIVERY_COMPLETED_STATUS = OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS;
        $DELIVERY_NOTCOMPLETED_STATUS = OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS;
        $ORDER_CANCELBYADMIN_STATUS = OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS;

        return $this->orderDeliveryRepository->getQuery()->where('deliveryMenId', user()->id)->where(function ($query) use ($DELIVERY_COMPLETED_STATUS, $DELIVERY_NOTCOMPLETED_STATUS, $ORDER_CANCELBYADMIN_STATUS) {
            $query->where('status', '=', $DELIVERY_COMPLETED_STATUS)
                ->orWhere('status', '=', $DELIVERY_NOTCOMPLETED_STATUS)
                ->orWhere('status', '=', $ORDER_CANCELBYADMIN_STATUS);
        })->orderBy('updatedAt', 'desc');
    }

    /**
     * Method orderRecordPaginate
     *
     * @param $request $request
     *
     * @return
     */
    public function orderRecordPaginate($request)
    {
        $DELIVERY_COMPLETED_STATUS = OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS;
        $DELIVERY_NOTCOMPLETED_STATUS = OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS;
        $ORDER_CANCELBYADMIN_STATUS = OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS;

        return $this->orderDeliveryRepository->getQuery()->where('deliveryMenId', user()->id)->where(function ($query) use ($DELIVERY_COMPLETED_STATUS, $DELIVERY_NOTCOMPLETED_STATUS, $ORDER_CANCELBYADMIN_STATUS) {
            $query->where('status', '=', $DELIVERY_COMPLETED_STATUS)
                ->orWhere('status', '=', $DELIVERY_NOTCOMPLETED_STATUS)
                ->orWhere('status', '=', $ORDER_CANCELBYADMIN_STATUS);
        })->paginate(15);
    }

    /**
     * Method getPaginateInfoReturnedOrders
     *
     * @return void
     */
    public function getPaginateInforOrderRecords($request)
    {
        $data = $this->orderRecordPaginate($request);

        return $this->paginationInfo = [
            'currentResults' => $data->count(),
            'totalRecords' => $data->total(),
            'numberOfPages' => $data->lastPage(),
            'itemsPerPage' => $data->perPage(),
            'currentPage' => $data->currentPage(),
        ];
    }

    /**
     * Method lastOrdeDelivered
     *
     * @param $request $request
     *
     * @return void
     */
    public function lastOrdeDelivered($request)
    {
        $orderRecords = $this->orderRecords($request);
        $listCompleted = $orderRecords->first();
        // dd($listCompleted);
        if ($listCompleted) {
            $order = $this->ordersRepository->get($listCompleted->orderId);
            $restaurant = $this->restaurantsRepository->get($listCompleted->restaurantId);
            $customer = $this->customersRepository->getQuery()->where('id', (int) $listCompleted->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);

            $deliveryMen = user();
            $listCompleted['order'] = $order;
            $listCompleted['restaurant'] = $restaurant;
            $listCompleted['customer'] = $customer;
            $listCompleted['location'] = [
                'me' => $deliveryMen['location'],
                'restaurant' => $restaurant['location'],
                'customer' => $listCompleted['addressOrderCustomer']['location'],
            ];

            return $listCompleted;
        }
    }

    /**
     * Method listCompleted
     *
     * @param $request $request
     *
     * @return void
     */
    public function listCompleted($request)
    {
        $orderRecords = $this->orderRecords($request);

        if ($request->has('date')) {
            $startOfDay = Carbon::parse($request->date);
            $endOfDay = Carbon::parse($request->date);
            $orderRecords->whereBetween('updatedAt', [$startOfDay->startOfDay(), $endOfDay->endOfDay()]);
        }

        if ($request->has('from')) {
            $startOfDay = Carbon::parse($request->from);
            $endOfDay = Carbon::parse($request->to);
            $orderRecords->whereBetween('updatedAt', [$startOfDay->startOfDay(), $endOfDay->endOfDay()]);
        }

        // if ($request->has('to')) {
        //     $startOfDay = Carbon::parse($request->from);
        //     $endOfDay = Carbon::parse($request->to);
        //     $orderRecords->whereBetween('createdAt', [$startOfDay->startOfDay(), $endOfDay->endOfDay()]);
        // }

        $listCompleteds = $orderRecords->paginate(15);
        $listOrders = [];
        foreach ($listCompleteds as $key => $listCompleted) {
            $order = $this->ordersRepository->get($listCompleted->orderId);
            $restaurant = $this->restaurantsRepository->get($listCompleted->restaurantId);
            $customer = $this->customersRepository->getQuery()->where('id', (int) $listCompleted->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);

            $deliveryMen = user();
            $listCompleted['order'] = $order;
            $listCompleted['restaurant'] = $restaurant;
            $listCompleted['customer'] = $customer;
            $listCompleted['location'] = [
                'me' => $deliveryMen['location'],
                'restaurant' => $restaurant['location'],
                'customer' => $listCompleted['addressOrderCustomer']['location'],
            ];
            $listOrders[] = $listCompleted;
        }

        return $listOrders;
    }

    /**
     * Method countOrders
     *
     * @param $request $request
     *
     * @return void
     */
    public function countOrders($request)
    {
        $listCompleteds = $this->orderRecords($request);

        if ($request->has('date')) {
            $startOfDay = Carbon::parse($request->date);
            $endOfDay = Carbon::parse($request->date);
            $listCompleteds->whereBetween('updatedAt', [$startOfDay->startOfDay(), $endOfDay->endOfDay()]);
        }

        // if ($request->has('from')) {
        //     $listCompleteds->where('createdAt', '>=', Carbon::parse($request['from']));
        // }

        // if ($request->has('to')) {
        //     $listCompleteds->where('createdAt', '<=', Carbon::parse($request['to']));
        // }
        if ($request->has('from')) {
            $startOfDay = Carbon::parse($request->from);
            $endOfDay = Carbon::parse($request->to);
            $listCompleteds->whereBetween('updatedAt', [$startOfDay->startOfDay(), $endOfDay->endOfDay()]);
        }

        $countOrders = $listCompleteds->count();
        // dd($countOrders);
        return $countOrders;
    }

    /**
     * Method countSumKm
     *
     * @param $request $request
     *
     * @return void
     */
    public function countSumKm($request)
    {
        $listCompleteds = $this->orderRecords($request);

        if ($request->has('date')) {
            $startOfDay = Carbon::parse($request->date);
            $endOfDay = Carbon::parse($request->date);
            $listCompleteds->whereBetween('updatedAt', [$startOfDay->startOfDay(), $endOfDay->endOfDay()]);
        }

        // if ($request->has('from')) {
        //     $listCompleteds->where('createdAt', '>=', Carbon::parse($request['from']));
        // }

        // if ($request->has('to')) {
        //     $listCompleteds->where('createdAt', '<=', Carbon::parse($request['to']));
        // }
        if ($request->has('from')) {
            $startOfDay = Carbon::parse($request->from);
            $endOfDay = Carbon::parse($request->to);
            $listCompleteds->whereBetween('updatedAt', [$startOfDay->startOfDay(), $endOfDay->endOfDay()]);
        }

        $countOrders = $listCompleteds->sum('totalDistanceInt');
        // dd($countOrders);
        return round($countOrders);
    }

    /**
     * Method countSumProfits
     *
     * @param $request $request
     *
     * @return void
     */
    public function countSumProfits($request)
    {
        $listCompleteds = $this->orderRecords($request);

        if ($request->has('date')) {
            $startOfDay = Carbon::parse($request->date);
            $endOfDay = Carbon::parse($request->date);
            $listCompleteds->whereBetween('updatedAt', [$startOfDay->startOfDay(), $endOfDay->endOfDay()]);
        }

        // if ($request->has('from')) {
        //     $listCompleteds->where('createdAt', '>=', Carbon::parse($request['from']));
        // }

        // if ($request->has('to')) {
        //     $listCompleteds->where('createdAt', '<=', Carbon::parse($request['to']));
        // }
        if ($request->has('from')) {
            $startOfDay = Carbon::parse($request->from);
            $endOfDay = Carbon::parse($request->to);
            $listCompleteds->whereBetween('updatedAt', [$startOfDay->startOfDay(), $endOfDay->endOfDay()]);
        }

        $countOrders = $listCompleteds->sum('deliveryCommission');

        return $countOrders;
    }

    /**
     * Method dateOrder
     *
     * @param $request $request
     *
     * @return void
     */
    public function dateOrder($request)
    {
        return Carbon::parse($request->date)->translatedFormat('l');
    }

    /**
     * Method dateText
     *
     * @param $request $request
     *
     * @return void
     */
    public function dateText($request)
    {
        return Carbon::parse($request->date)->translatedFormat('F d');
    }

    /**
     * Method dateTextYear
     *
     * @param $request $request
     *
     * @return void
     */
    public function dateTextYear($request)
    {
        return Carbon::parse($request->date)->translatedFormat('Y');
    }

    //List Function For Dashboard

    /**
     * Method listOrderForDshboard
     *
     * @param $request $request
     *
     * @return void
     */
    public function listOrderForDshboard($request)
    {
        $listOrderForDshboards = $this->listOrderQuery($request);
        $listOrders = [];
        foreach ($listOrderForDshboards as $key => $listOrderForDshboard) {
            $order = $this->ordersRepository->get($listOrderForDshboard->orderId);
            $restaurant = $this->restaurantsRepository->get($listOrderForDshboard->restaurantId);
            $customer = $this->customersRepository->getQuery()->where('id', (int) $listOrderForDshboard->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);
            $deliveryMen = $this->deliveryMenRepository->get((int) $listOrderForDshboard->deliveryMenId);
            $listOrderForDshboard['order'] = $order;
            $listOrderForDshboard['restaurant'] = $restaurant;
            $listOrderForDshboard['customer'] = $customer;
            $listOrderForDshboard['deliveryMen'] = $deliveryMen;
            $listOrderForDshboard['location'] = [
                'deliveryMen' => $deliveryMen['location'] ?? null,
                'restaurant' => $restaurant['location'],
                'customer' => $listOrderForDshboard['addressOrderCustomer']['location'] ?? null,
            ];
            $listOrders[] = $listOrderForDshboard;
        }

        return $listOrders;
    }

    /**
     * Method listOrderQuery
     *
     * @param $request $request
     *
     * @return
     */
    public function listOrderQuery($request)
    {
        $listOrderForDshboards = $this->orderDeliveryRepository->getQuery();

        if ($request->status == OrderDeliveryRepository::DELIVERY_PENDING_STATUS) {
            $listOrderForDshboards->where('status', OrderDeliveryRepository::DELIVERY_PENDING_STATUS);
        } elseif ($request->status == OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS) {
            $listOrderForDshboards->where('status', OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS);
        } elseif ($request->status == OrderDeliveryRepository::DELIVERY_REJECTED_STATUS) {
            $listOrderForDshboards->where('status', OrderDeliveryRepository::DELIVERY_REJECTED_STATUS);
        } elseif ($request->status == OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS) {
            $listOrderForDshboards->where('status', OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS);
        } elseif ($request->status == OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS) {
            $listOrderForDshboards->where('status', OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS);
        } elseif ($request->status == OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS) {
            $listOrderForDshboards->where('status', OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS);
        } elseif ($request->status == OrderDeliveryRepository::DELIVERY_IS_NOT_SET_STATUS) {
            $listOrderForDshboards->where('status', OrderDeliveryRepository::DELIVERY_IS_NOT_SET_STATUS);
        } elseif ($request->status == OrderDeliveryRepository::DELIVERY_MEAL_HAS_NOT_BEEN_RESTAURANT_STATUS) {
            $listOrderForDshboards->where('status', OrderDeliveryRepository::DELIVERY_MEAL_HAS_NOT_BEEN_RESTAURANT_STATUS);
        } elseif ($request->status == OrderDeliveryRepository::ORDER_CANCELBYCUSTOMER_STATUS) {
            $listOrderForDshboards->where('status', OrderDeliveryRepository::ORDER_CANCELBYCUSTOMER_STATUS);
        } elseif ($request->status == OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS) {
            $listOrderForDshboards->where('status', OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS);
        } elseif ($request->status == OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS) {
            $listOrderForDshboards->where('status', OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS);
        } elseif ($request->status == 'allStatus') {
            $listOrderForDshboards = $this->orderDeliveryRepository->getQuery();
        } else {
            $listOrderForDshboards = $this->orderDeliveryRepository->getQuery();
        }

        if ($request->has('id')) {
            $listOrderForDshboards->where('id', (int) $request->id);
        }
        if ($request->has('deliveryMenId')) {
            $listOrderForDshboards->where('deliveryMenId', (int) $request->deliveryMenId);
        }

        if ($request->has('restaurantId')) {
            $listOrderForDshboards->where('restaurantId', (int) $request->restaurantId);
        }

        if ($request->has('date')) {
            $startOfDay = Carbon::parse($request->date);
            $endOfDay = Carbon::parse($request->date);
            $listOrderForDshboards->whereBetween('createdAt', [$startOfDay->startOfDay(), $endOfDay->endOfDay()]);
        }

        if ($request->has('from')) {
            $listOrderForDshboards->where('createdAt', '>=', Carbon::parse($request['from']));
        }

        if ($request->has('to')) {
            $listOrderForDshboards->where('createdAt', '<=', Carbon::parse($request['to']));
        }

        $listOrderForDshboards = $listOrderForDshboards->orderBy('id', 'DESC')->paginate(15);

        return $listOrderForDshboards;
    }

    /**
     * Method getPaginateInfoReturnedOrders
     *
     * @return void
     */
    public function getPaginateInforOrders($request)
    {
        $data = $this->listOrderQuery($request);

        return $this->paginationInfo = [
            'currentResults' => $data->count(),
            'totalRecords' => $data->total(),
            'numberOfPages' => $data->lastPage(),
            'itemsPerPage' => $data->perPage(),
            'currentPage' => $data->currentPage(),
        ];
    }

    /**
     * Method countMealHasNotBeenRestaurant
     *
     * @param $request $request
     *
     * @return void
     */
    public function countMealHasNotBeenRestaurant()
    {
        $countMealHasNotBeenRestaurant = $this->orderDeliveryRepository->getQuery()->where('status', OrderDeliveryRepository::DELIVERY_MEAL_HAS_NOT_BEEN_RESTAURANT_STATUS)->count();

        return $countMealHasNotBeenRestaurant;
    }

    /**
     * Method countDontTookMoreThan30Seconds
     *
     * @return void
     */
    public function countDontTookMoreThan30Seconds()
    {
        $countDontTookMoreThan30Seconds = $this->orderDeliveryRepository->getQuery()->where('status', OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS)->count();

        return $countDontTookMoreThan30Seconds;
    }

    /**
     * Method countPending
     *
     * @param $request $request
     *
     * @return void
     */
    public function countPending()
    {
        $countPending = $this->orderDeliveryRepository->getQuery()->where('status', OrderDeliveryRepository::DELIVERY_PENDING_STATUS)->count();

        return $countPending;
    }

    /**
     * Method countAccepted
     *
     * @return void
     */
    public function countAccepted()
    {
        $countAccepted = $this->orderDeliveryRepository->getQuery()->where('status', OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS)->count();

        return $countAccepted;
    }

    /**
     * Method countOrderCancel
     *
     * @return void
     */
    public function countOrderCancelByCustomer()
    {
        $countOrderCancelByCustomer = $this->orderDeliveryRepository->getQuery()->where('status', OrderDeliveryRepository::ORDER_CANCELBYCUSTOMER_STATUS)->count();

        return $countOrderCancelByCustomer;
    }

    /**
     * Method countOrderCancelByAdmin
     *
     * @return void
     */
    public function countOrderCancelByAdmin()
    {
        $countOrderCancelByAdmin = $this->orderDeliveryRepository->getQuery()->where('status', OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS)->count();

        return $countOrderCancelByAdmin;
    }

    /**
     * Method countRejected
     *
     * @return void
     */
    public function countRejected()
    {
        $countRejected = $this->orderDeliveryRepository->getQuery()->where('status', OrderDeliveryRepository::DELIVERY_REJECTED_STATUS)->count();

        return $countRejected;
    }

    /**
     * Method countDeliveryOnTheWayRestaurant
     *
     * @return void
     */
    public function countDeliveryOnTheWayRestaurant()
    {
        $countDeliveryOnTheWayRestaurant = $this->orderDeliveryRepository->getQuery()->where('status', OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS)->count();

        return $countDeliveryOnTheWayRestaurant;
    }

    /**
     * Method countCompleted
     *
     * @return void
     */
    public function countCompleted()
    {
        $countCompleted = $this->orderDeliveryRepository->getQuery()->where('status', OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS)->count();

        return $countCompleted;
    }

    /**
     * Method countNotCompleted
     *
     * @return void
     */
    public function countNotCompleted()
    {
        $countNotCompleted = $this->orderDeliveryRepository->getQuery()->where('status', OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS)->count();

        return $countNotCompleted;
    }

    /**
     * Method countAll
     *
     * @return void
     */
    public function countAll()
    {
        $countAll = $this->orderDeliveryRepository->getQuery()->count();

        return $countAll;
    }

    /**
     * Method countDeliveryIsNotSet
     *
     * @return void
     */
    public function countDeliveryIsNotSet()
    {
        $countDeliveryIsNotSet = $this->orderDeliveryRepository->getQuery()->where('status', OrderDeliveryRepository::DELIVERY_IS_NOT_SET_STATUS)->count();

        return $countDeliveryIsNotSet;
    }

    /**
     * Method showDashoard
     *
     * @param $id $id
     *
     * @return void
     */
    public function showDashoard($orderDelivery)
    {
        $requestOrderIsPending = $orderDelivery;
        if ($requestOrderIsPending) {
            $order = $this->ordersRepository->get($requestOrderIsPending->orderId);
            $restaurant = $this->restaurantsRepository->get($requestOrderIsPending->restaurantId);
            $customer = $this->customersRepository->getQuery()->where('id', (int) $requestOrderIsPending->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);
            $deliveryMen = $this->deliveryMenRepository->get((int) $requestOrderIsPending->deliveryMenId);
            $requestOrderIsPending['order'] = $order;
            $requestOrderIsPending['restaurant'] = $restaurant;
            $requestOrderIsPending['customer'] = $customer;
            $requestOrderIsPending['deliveryMen'] = $deliveryMen;

            $requestOrderIsPending['location'] = [
                'deliveryMen' => $deliveryMen['location'] ?? null,
                'restaurant' => $restaurant['location'],
                'customer' => $requestOrderIsPending['addressOrderCustomer']['location'],
            ];

            return $requestOrderIsPending;
        }
    }

    /**
     * Method update Manual Delivery Assignment
     * update Manual Delivery Assignment
     * @param $id $id
     * @param $request $request
     *
     * @return void
     */
    public function updateManualDeliveryAssignment($id, $request)
    {
        $updateManualDeliveryAssignment = $this->orderDeliveryRepository->get($id);

        $restaurant = $this->restaurantsRepository->get((int) $updateManualDeliveryAssignment->restaurantId);

        $deliveryMen = $this->deliveryMenRepository->getQuery()->where('id', (int) $request->deliveryMenId)->where('approved', DeliveryMensRepository::APPROVED_STATUS)->where('status', true)->where('published', true)->where('requested', '<', 2)->first();
        $deliveryMenStatus = $this->deliveryMenRepository->getQuery()->where('id', (int) $request->deliveryMenId)->where('approved', DeliveryMensRepository::APPROVED_STATUS)->where('status', true)->where('published', true)->first();

        if ($deliveryMenStatus == null) {
            throw new Exception('المندوب غير متاح حاليا');
        }
        if ($deliveryMen == null) {
            throw new Exception('المندوب معه 2 اوردر ولا يمكن استناد اكثر من ذالك');
        }

        $changeStatusOrder = $this->ordersRepository->getQuery()->where('id', (int) $updateManualDeliveryAssignment->orderId)->first();
        $changeStatusOrder->deliveryName = $deliveryMen->firstName . ' ' . $deliveryMen->lastName;
        $changeStatusOrder->save();

        //In the delivery pocket, I accept the order, or we go to the restaurant to receive the order
        $orderDelivery = $this->orderDeliveryRepository->getQuery()->where('deliveryMenId', (int) $request->deliveryMenId)->where('status', OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS)->first();
        //The order from the restaurant is not the same restaurant as the new order
        if ($orderDelivery && $orderDelivery['restaurantId'] != $restaurant['id']) {
            throw new Exception('المندوب معه اوردر لمطعم اخر لا يمكن توصيل لاكثر من مطعم في نفس الوقت');
        }

        $distanceToTheRestaurant = $this->orderDeliveryRepository->distanceToTheRestaurant($restaurant, (int) $request->deliveryMenId);
        $distanceToTheCustomer = $this->orderDeliveryRepository->distanceToTheCustomer($updateManualDeliveryAssignment->addressOrderCustomer, $restaurant);
        $totalDistance = $this->orderDeliveryRepository->totalDistance($distanceToTheRestaurant[0], $distanceToTheCustomer[0]);
        $totalMinute = $this->orderDeliveryRepository->totalMinute($distanceToTheRestaurant[1], $distanceToTheCustomer[1]);

        if ((int) $updateManualDeliveryAssignment->deliveryMenId != (int) $request->deliveryMenId) {
            if ($updateManualDeliveryAssignment->deliveryMenId) {
                $deliveryMenOld = $this->deliveryMenRepository->getModel((int) $updateManualDeliveryAssignment->deliveryMenId);
                if ($updateManualDeliveryAssignment->status == OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS) {
                    $deliveryMenOld['requested'] = $deliveryMenOld->requested - 1;
                    $deliveryMenOld->save();
                }
                $this->notificationsRepository->create([
                    'title' => 'تم سحب الاوردر منك وتوصيله لمندوب اخر',
                    'content' => 'تم سحب الاوردر منك وتوصيله لمندوب اخر',
                    'type' => 'pullOrder',
                    'user' => $deliveryMenOld,
                    'pushNotification' => true,
                    'extra' => [
                        'type' => 'pullOrder',
                        'orderId' => $updateManualDeliveryAssignment->id,
                        'notificationCount' => $deliveryMenOld->totalNotifications + 1,
                        'status' => OrderDeliveryRepository::DELIVERY_PENDING_STATUS,
                    ],
                ]);
            }
            // $deliveryMen = $this->deliveryMenRepository->get((int)$request->deliveryMenId);
            // $deliveryMen['requested'] = $deliveryMen->requested + 1;
            // $deliveryMen->save();
        }

        if ($updateManualDeliveryAssignment->status == OrderDeliveryRepository::DELIVERY_IS_NOT_SET_STATUS || $updateManualDeliveryAssignment->status == OrderDeliveryRepository::DELIVERY_REJECTED_STATUS) {
            $deliveryMen = $this->deliveryMenRepository->get((int) $request->deliveryMenId);
            $deliveryMen['requested'] = $deliveryMen->requested + 1;
            $deliveryMen->save();
        } elseif ($updateManualDeliveryAssignment->status == OrderDeliveryRepository::ORDER_THE_REQUEST_30SECONDS_STATUS) {
            if ((int) $updateManualDeliveryAssignment->deliveryMenId != (int) $request->deliveryMenId) {
                $deliveryMen = $this->deliveryMenRepository->get((int) $request->deliveryMenId);
                $deliveryMen['requested'] = $deliveryMen->requested + 1;
                $deliveryMen->save();
            }
        }


        $updateManualDeliveryAssignment->update([
            'deliveryMenId' => (int) $request->deliveryMenId,
            'timer' => OrderDeliveryRepository::SECONDS_STATUS,
            'status' => OrderDeliveryRepository::DELIVERY_PENDING_STATUS,
            'nextStatus' => static::NEXT_ORDER_STATUS[OrderDeliveryRepository::DELIVERY_PENDING_STATUS] ?? [],
            'distanceToTheRestaurant' => $distanceToTheRestaurant[0],
            'minuteToTheRestaurant' => $distanceToTheRestaurant[1] . 'د',
            'distanceToTheCustomer' => $distanceToTheCustomer[0],
            'minuteToTheCustomer' => $distanceToTheCustomer[1] . 'د',
            'totalDistance' => $totalDistance,
            'totalDistanceInt' => (float) $totalDistance,
            'totalMinute' => $totalMinute,
            'timerDate' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        if (user()->accountType() == 'user') {
            $user = user()->name;
        } elseif (user()->accountType() == 'deliveryMen') {
            $user = user()->firstName . ' ' . user()->lastName;
        } elseif (user()->accountType() == 'customer') {
            $user = user()->firstName . ' ' . user()->lastName;
        }
        $orderDeliveryStatus = new OrderStatusDelivery([
            'orderId' => $updateManualDeliveryAssignment['orderId'],
            'orderDeliveryId' => $updateManualDeliveryAssignment->id,
            'status' => OrderDeliveryRepository::DELIVERY_PENDING_STATUS,
            'creator' => user() ? user()->accountType() : null,
            'creatorBy' => user() ? $user : null,
            'deliveryMen' => $deliveryMen->firstName . ' ' . $deliveryMen->lastName,

        ]);

        $deliveryMenModel = $this->deliveryMenRepository->getModel((int) $request->deliveryMenId);

        $this->notificationsRepository->create([
            'title' => "تم اسناد طلب جديد [#{$updateManualDeliveryAssignment->id}] قم بقبول الطلب",
            'content' => "تم اسناد طلب جديد [#{$updateManualDeliveryAssignment->id}] قم بقبول الطلب",
            'type' => 'order',
            'user' => $deliveryMenModel,
            'pushNotification' => true,
            'extra' => [
                'type' => 'order',
                'orderId' => $updateManualDeliveryAssignment->id,
                'notificationCount' => $deliveryMenModel->totalNotifications + 1,
                'status' => OrderDeliveryRepository::DELIVERY_PENDING_STATUS,
            ],
        ]);



        $orderDeliveryStatus->save();

        return $this->saveStatusLog($updateManualDeliveryAssignment->id, $orderDeliveryStatus);
    }

    /**
     * Method changeStatus
     *
     * @param $order $order
     * @param $status $status
     * @param $request $request
     *
     * @return void
     */
    public function changeStatus($order, $status, $request = null)
    {
        $order = $this->orderDeliveryRepository->get((int) $order);

        if ($status == OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS) {
            if ($order->status == OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS || $order->status == OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS) {
                $changeStatusOrder = $this->ordersRepository->getQuery()->where('id', (int) $order->orderId)->first();

                $deliveryMenCost = $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenCost');
                $deliveryCommission = $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission');
                $commissionDiteMarket = $deliveryMenCost - $deliveryCommission;
                $title = "طلب توصيل [#{$order->id}] عمولة التوصيل [{$deliveryCommission} ر.س]";
                $amount = $deliveryCommission; //نظام الجديد
                $this->walletDeliveryRepository->deposit([
                    'delivery' => $order['deliveryMenId'],
                    'amount' => $amount,
                    'orderId' => $order->id,
                    'pushNotification' => true,
                    'title' => $title,
                    // 'reason' => $title,
                    'amount' => $amount,
                    'paymentMethod' => $changeStatusOrder->paymentMethod,
                    'commissionDiteMarket' => $commissionDiteMarket,
                    'totalAmountOrder' => $changeStatusOrder->finalPrice,
                ]);
                $delivery = $this->deliveryMenRepository->getModel($order->deliveryMenId);
                $deliveryTransactions = new DeliveryTransaction([
                    'deliveryMan' => $delivery->only(['id', 'firstName', 'lastName', 'email']),
                    'amount' => $changeStatusOrder->finalPrice - $commissionDiteMarket,
                    'commissionDiteMarket' => $commissionDiteMarket,
                    'deliveryCommission' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'),
                    'orderDelivery' => $order->id,
                    'totalAmountOrder' => $changeStatusOrder->finalPrice - $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'),
                    'deliveryStatus' => OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS,
                    'paymentMethod' => $changeStatusOrder->paymentMethod,
                    'published' => true,

                ]);
                $deliveryTransactions->save();
            }
        }

        $order->update([
            'status' => $status,
            'nextStatus' => static::NEXT_ORDER_STATUS[$status] ?? [],
        ]);
        if (user()->accountType() == 'user') {
            $user = user()->name;
        } elseif (user()->accountType() == 'deliveryMen') {
            $user = user()->firstName . ' ' . user()->lastName;
        } elseif (user()->accountType() == 'customer') {
            $user = user()->firstName . ' ' . user()->lastName;
        }
        $orderDeliveryStatus = new OrderStatusDelivery([
            'orderId' => $order['orderId'],
            'orderDeliveryId' => $order->id,
            'status' => $status,
            'creator' => user() ? user()->accountType() : null,
            'creatorBy' => user() ? $user : null,

        ]);

        if ($status == OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS) {
            $deliveryMenOld = $this->deliveryMenRepository->getModel((int) $order->deliveryMenId);
            $this->notificationsRepository->create([
                'title' => "تم إلغاء الطلب [#{$order->id}]",
                'content' => "تم إلغاء الطلب [#{$order->id}]",
                'type' => 'cancelOrder',
                'user' => $deliveryMenOld,
                'pushNotification' => true,
                'extra' => [
                    'type' => 'cancelOrder',
                    'orderId' => $order->id,
                    'notificationCount' => $deliveryMenOld->totalNotifications + 1,
                    'status' => OrderDeliveryRepository::ORDER_CANCELBYADMIN_STATUS,
                ],
            ]);
            $deliveryMen = $this->deliveryMenRepository->get((int) $order->deliveryMenId);
            $deliveryMen['requested'] = $deliveryMen->requested - 1;
            $deliveryMen->save();
            $deliveryMen->reassociate($order, 'orders')->save();
        } elseif ($status == OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS) {
            $deliveryMenModel = $this->deliveryMenRepository->getModel((int) $order->deliveryMenId);
            $restaurant = $this->restaurantsRepository->get((int) $order->restaurantId);

            //In the delivery pocket, I accept the order, or we go to the restaurant to receive the order
            $orderDelivery = $this->orderDeliveryRepository->getQuery()->where('deliveryMenId', (int) $order->deliveryMenId)->where('status', OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS)->first();

            //The order from the restaurant is not the same restaurant as the new order
            if ($orderDelivery && $orderDelivery['restaurantId'] != $restaurant['id']) {
                // $order->update([
                //     'status' => OrderDeliveryRepository::DELIVERY_PENDING_STATUS,
                //     'nextStatus' => static::NEXT_ORDER_STATUS[OrderDeliveryRepository::DELIVERY_PENDING_STATUS] ?? []
                // ]);
                return false;
            }

            $this->notificationsRepository->create([
                'title' => "تم قبول طلب [#{$order->id}] ومتجه الي المطعم ",
                'content' => "تم قبول طلب [#{$order->id}] ومتجه الي المطعم ",
                'type' => 'acceptedOrder',
                'user' => $deliveryMenModel,
                'pushNotification' => true,
                'extra' => [
                    'type' => 'acceptedOrder',
                    'orderId' => $order->id,
                    'notificationCount' => $deliveryMenModel->totalNotifications + 1,
                    'status' => $status,
                ],
            ]);


            $customer = $this->customersRepository->getQuery()->where('id', (int) $order->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);
            $deliveryMen = $this->deliveryMenRepository->get((int) $order->deliveryMenId);
            $activateSendingMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'activateSendingMail');
            $storeNameMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'storeNameMail');
            if ($orderDelivery['addressOrderCustomer']['email']) {
                $email = $orderDelivery['addressOrderCustomer']['email'];
            } else {
                $email = $customer->email;
            }
            if ($email) {
                if ($activateSendingMail == true) {
                    Mail::send([], [], function ($message) use ($customer, $order, $email, $storeNameMail, $deliveryMen) {
                        $message->to($email)
                            ->subject('تم قبول طلبك')
                            // here comes what you want
                            ->setBody("
                        <p>
                            مرحبا   [{$customer->firstName}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        <p>
                        تم قبول طلبك [{$order->orderId}] ، شكرًا لك على التسوق في موقعنا.
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        اسم مندوب التوصيل - [{$deliveryMen->firstName}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        رقم مندوب التوصيل -  [966{$deliveryMen->phoneNumber}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        رقم مركبة مندوب التوصيل - [{$deliveryMen->VehicleSerialNumber}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        نوع المركبة - {$deliveryMen->vehicleType['name'][0]['text']}
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
            }
        } elseif ($status == OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS) {
            $deliveryMenModel = $this->deliveryMenRepository->getModel((int) $order->deliveryMenId);

            $this->notificationsRepository->create([
                'title' => "تم استلام الطلب [#{$order->id}] من المطعم ومتجه الي العميل",
                'content' => "تم استلام الطلب [#{$order->id}] من المطعم ومتجه الي العميل",
                'type' => 'deliveryOnTheWayOrder',
                'user' => $deliveryMenModel,
                'pushNotification' => true,
                'extra' => [
                    'type' => 'deliveryOnTheWayOrder',
                    'orderId' => $order->id,
                    'notificationCount' => $deliveryMenModel->totalNotifications + 1,
                    'status' => $status,
                ],
            ]);

            $changeStatusOrder = $this->ordersRepository->getQuery()->where('id', (int) $order->orderId)->first();
            $status = OrdersRepository::ON_THE_WAY_STATUS;
            $changeStatusOrder->status = $status;
            $changeStatusOrder->nextStatus = OrdersRepository::NEXT_ORDER_STATUS_DELIVERY_BY_DITE_MARKIT_FOOD[$status] ?? [];
            $changeStatusOrder->save();
            $orderStatus = new OrderStatus([
                'orderId' => $order->orderId,
                'status' => $status,
                'notes' => request()->notes ?? '',
                'creator' => user() ? user()->accountType() : null,
            ]);
            $orderStatus->save();
            $customer = $this->customersRepository->getModel((int) $changeStatusOrder->customer['id']);
            $notification = $this->notificationsRepository->create([
                'title' => trans('notifications.order.titleFood.' . $status),
                'content' => trans('notifications.order.contentFood.' . $status),
                'type' => 'foodOrder',
                'user' => $customer,
                'pushNotification' => true,
                'extra' => [
                    'type' => 'foodOrder',
                    'status' => $status,
                    'orderId' => $changeStatusOrder->id,
                ],
            ]);
            $this->notificationsRepository->getQuery()->where('id', $notification->id)->delete();
        } elseif ($status == OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS) {
            $deliveryMenModel = $this->deliveryMenRepository->getModel((int) $order->deliveryMenId);
            $this->notificationsRepository->create([
                'title' => "تم تسليم الطلب [#{$order->id}] الي العميل",
                'content' => "تم تسليم الطلب [#{$order->id}] الي العميل",
                'type' => 'completedOrder',
                'user' => $deliveryMenModel,
                'pushNotification' => true,
                'extra' => [
                    'type' => 'completedOrder',
                    'orderId' => $order->id,
                    'notificationCount' => $deliveryMenModel->totalNotifications + 1,
                    'status' => $status,
                ],
            ]);

            $changeStatusOrder = $this->ordersRepository->getQuery()->where('id', (int) $order->orderId)->first();
            $status = OrdersRepository::COMPLETED_STATUS;
            $changeStatusOrder->status = $status;
            $changeStatusOrder->nextStatus = OrdersRepository::NEXT_ORDER_STATUS_DELIVERY_BY_DITE_MARKIT_FOOD[$status] ?? [];
            $changeStatusOrder->save();
            $this->transactionsRepository->add($changeStatusOrder);

            $orderStatus = new OrderStatus([
                'orderId' => $order->orderId,
                'status' => $status,
                'notes' => request()->notes ?? '',
                'creator' => user() ? user()->accountType() : null,
            ]);
            $orderStatus->save();
            $customer = $this->customersRepository->getModel((int) $changeStatusOrder->customer['id']);
            $notification = $this->notificationsRepository->create([
                'title' => trans('notifications.order.titleFood.' . $status),
                'content' => trans('notifications.order.contentFood.' . $status),
                'type' => 'foodOrder',
                'user' => $customer,
                'pushNotification' => true,
                'extra' => [
                    'type' => 'foodOrder',
                    'status' => $status,
                    'orderId' => $changeStatusOrder->id,
                ],
            ]);
            $this->notificationsRepository->getQuery()->where('id', $notification->id)->delete();


            $deliveryMenCost = $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenCost');
            $deliveryCommission = $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission');
            $commissionDiteMarket = $deliveryMenCost - $deliveryCommission;
            $title = "طلب توصيل [#{$order->id}] عمولة التوصيل [{$deliveryCommission} ر.س]";

            if ($changeStatusOrder->paymentMethod == 'cashOnDelivery') {
                // $amount = $commissionDiteMarket; //نظام القديم
                $amount = $changeStatusOrder->finalPrice - $deliveryCommission; //نظام الجديد
                $this->walletDeliveryRepository->withdraw([
                    'delivery' => $order['deliveryMenId'],
                    'amount' => $amount,
                    'orderId' => $order->id,
                    'pushNotification' => true,
                    'title' => $title,
                    // 'reason' => $title,
                    'amount' => $amount,
                    'paymentMethod' => $changeStatusOrder->paymentMethod,
                    'commissionDiteMarket' => $commissionDiteMarket,
                    'totalAmountOrder' => $changeStatusOrder->finalPrice,
                ]);
            } else {
                // $amount = $changeStatusOrder->finalPrice - $commissionDiteMarket; //نظام القديم
                $amount = $deliveryCommission; //نظام الجديد
                $this->walletDeliveryRepository->deposit([
                    'delivery' => $order['deliveryMenId'],
                    'amount' => $amount,
                    'orderId' => $order->id,
                    'pushNotification' => true,
                    'title' => $title,
                    // 'reason' => $title,
                    'amount' => $amount,
                    'paymentMethod' => $changeStatusOrder->paymentMethod,
                    'commissionDiteMarket' => $commissionDiteMarket,
                    'totalAmountOrder' => $changeStatusOrder->finalPrice,
                ]);
            }
            $delivery = $this->deliveryMenRepository->getModel($order->deliveryMenId);
            $deliveryTransactions = new DeliveryTransaction([
                'deliveryMan' => $delivery->only(['id', 'firstName', 'lastName', 'email']),
                'amount' => $changeStatusOrder->finalPrice - $commissionDiteMarket,
                'commissionDiteMarket' => $commissionDiteMarket,
                'deliveryCommission' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'),
                'orderDelivery' => $order->id,
                'totalAmountOrder' => $changeStatusOrder->finalPrice - $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'),
                'deliveryStatus' => OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS,
                'paymentMethod' => $changeStatusOrder->paymentMethod,
                'published' => true,

            ]);
            $deliveryTransactions->save();


            $customer = $this->customersRepository->getQuery()->where('id', (int) $order->customerId)->first(['id', 'firstName', 'lastName', 'email', 'phoneNumber']);
            $deliveryMen = $this->deliveryMenRepository->get((int) $order->deliveryMenId);

            $activateSendingMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'activateSendingMail');
            $storeNameMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'storeNameMail');
            if ($order['addressOrderCustomer']['email']) {
                $email = $order['addressOrderCustomer']['email'];
            } else {
                $email = $customer->email;
            }
            if ($email) {
                if ($activateSendingMail == true) {
                    Mail::send([], [], function ($message) use ($customer, $email, $order, $storeNameMail) {
                        $message->to($email)
                            ->subject('تم تسليم طلبك')
                            // here comes what you want
                            ->setBody("
                        <p>
                            مرحبا   [{$customer->firstName}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        <p>
                        طلبك- تم تسليم [{$order->orderId}] بنجاح. شكرا لشراء منتج من متجرنا.
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
            }
            $deliveryMen = $this->deliveryMenRepository->get((int) $order->deliveryMenId);
            $deliveryMen['requested'] = $deliveryMen->requested - 1;
            $deliveryMen->reassociate($order, 'orders')->save();

            $checkCountUpdateStatuOrders = $this->checkCountUpdateStatuOrders($deliveryMen);
            // dd($checkCountUpdateStatuOrders);
            if ($checkCountUpdateStatuOrders == 0) {
                if ($deliveryMen['NewPublished'] == false) {
                    $deliveryMen->save();
                    $this->logOutForPublished((int) $deliveryMen->id);
                }
            } else {
                $deliveryMen->save();
            }
        } elseif ($status == OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS) {
            $deliveryMenModel = $this->deliveryMenRepository->getModel((int) $order->deliveryMenId);
            $this->notificationsRepository->create([
                'title' => "عدم استلام الطلب  [#{$order->id}] من قبل العميل",
                'content' => "عدم استلام الطلب  [#{$order->id}] من قبل العميل",
                'type' => 'notCompletedOrder',
                'user' => $deliveryMenModel,
                'pushNotification' => true,
                'extra' => [
                    'type' => 'notCompletedOrder',
                    'orderId' => $order->id,
                    'notificationCount' => $deliveryMenModel->totalNotifications + 1,
                    'status' => $status,
                ],
            ]);

            $changeStatusOrder = $this->ordersRepository->getQuery()->where('id', (int) $order->orderId)->first();
            $status = OrdersRepository::COMPLETED_STATUS;
            $changeStatusOrder->status = $status;
            $changeStatusOrder->nextStatus = OrdersRepository::NEXT_ORDER_STATUS_DELIVERY_BY_DITE_MARKIT_FOOD[$status] ?? [];
            $changeStatusOrder->save();
            $orderStatus = new OrderStatus([
                'orderId' => $order->orderId,
                'status' => $status,
                'notes' => request()->notes ?? '',
                'creator' => user() ? user()->accountType() : null,
            ]);
            $orderStatus->save();

            $deliveryMenCost = $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenCost');
            $deliveryCommission = $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission');
            $commissionDiteMarket = $deliveryMenCost - $deliveryCommission;
            $title = "طلب توصيل [#{$order->id}] عمولة التوصيل [{$deliveryCommission} ر.س]";

            if ($changeStatusOrder->paymentMethod == 'cashOnDelivery') {
                // $amount = $commissionDiteMarket; //نظام القديم
                $amount = $deliveryCommission; //نظام الجديد
                $this->walletDeliveryRepository->deposit([
                    'delivery' => $order['deliveryMenId'],
                    'amount' => $amount,
                    'orderId' => $order->id,
                    'pushNotification' => true,
                    'title' => $title,
                    // 'reason' => $title,
                    'amount' => $amount,
                    'paymentMethod' => $changeStatusOrder->paymentMethod,
                    'commissionDiteMarket' => $commissionDiteMarket,
                    'totalAmountOrder' => $changeStatusOrder->finalPrice,
                ]);
            } else {
                // $amount = $changeStatusOrder->finalPrice - $commissionDiteMarket; //نظام القديم
                $amount = $deliveryCommission; //نظام الجديد
                $this->walletDeliveryRepository->deposit([
                    'delivery' => $order['deliveryMenId'],
                    'amount' => $amount,
                    'orderId' => $order->id,
                    'pushNotification' => true,
                    'title' => $title,
                    // 'reason' => $title,
                    'amount' => $amount,
                    'paymentMethod' => $changeStatusOrder->paymentMethod,
                    'commissionDiteMarket' => $commissionDiteMarket,
                    'totalAmountOrder' => $changeStatusOrder->finalPrice,
                ]);
            }
            $delivery = $this->deliveryMenRepository->getModel($order->deliveryMenId);
            $deliveryTransactions = new DeliveryTransaction([
                'deliveryMan' => $delivery->only(['id', 'firstName', 'lastName', 'email']),
                'amount' => $changeStatusOrder->finalPrice - $commissionDiteMarket,
                'commissionDiteMarket' => $commissionDiteMarket,
                'deliveryCommission' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'),
                'orderDelivery' => $order->id,
                'totalAmountOrder' => $changeStatusOrder->finalPrice - $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'),
                'deliveryStatus' => OrderDeliveryRepository::DELIVERY_NOTCOMPLETED_STATUS,
                'paymentMethod' => $changeStatusOrder->paymentMethod,
                'published' => true,

            ]);
            $deliveryTransactions->save();

            $deliveryMen = $this->deliveryMenRepository->get((int) $order->deliveryMenId);
            $deliveryMen['requested'] = $deliveryMen->requested - 1;
            $deliveryMen->reassociate($order, 'orders')->save();
            $checkCountUpdateStatuOrders = $this->checkCountUpdateStatuOrders($deliveryMen);
            // dd($checkCountUpdateStatuOrders);
            if ($checkCountUpdateStatuOrders == 0) {
                if ($deliveryMen['NewPublished'] == false) {
                    $deliveryMen->save();
                    $this->logOutForPublished((int) $deliveryMen->id);
                }
            } else {
                $deliveryMen->save();
            }
        } elseif ($status == OrderDeliveryRepository::DELIVERY_REJECTED_STATUS) {
            $deliveryMenModel = $this->deliveryMenRepository->getModel((int) $order->deliveryMenId);
            $this->notificationsRepository->create([
                'title' => "تم رفض طلب [#{$order->id}] من قبل المندوب ",
                'content' => "تم رفض طلب [#{$order->id}] من قبل المندوب ",
                'type' => 'rejectedOrder',
                'user' => $deliveryMenModel,
                'pushNotification' => true,
                'extra' => [
                    'type' => 'rejectedOrder',
                    'orderId' => $order->id,
                    'notificationCount' => $deliveryMenModel->totalNotifications + 1,
                    'status' => $status,
                ],
            ]);
            $deliveryMen = $this->deliveryMenRepository->get((int) $order->deliveryMenId);
            $deliveryMen['requested'] = $deliveryMen->requested - 1;
            // if ($deliveryMen->NewPublished == false) {
            //     $deliveryMen->NewPublished = true;
            //     $deliveryMen->published = false;
            //     $this->deliveryMenRepository->logOutForPublishedEquelFalse();
            // }
            $deliveryMen->save();
        } else {
            $deliveryMenModel = $this->deliveryMenRepository->getModel((int) $order->deliveryMenId);

            $this->notificationsRepository->create([
                'title' => "تم اسناد طلب جديد [#{$order->id}] قم بقبول الطلب",
                'content' => "تم اسناد طلب جديد [#{$order->id}] قم بقبول الطلب",
                'type' => 'order',
                'user' => $deliveryMenModel,
                'pushNotification' => true,
                'extra' => [
                    'type' => 'order',
                    'orderId' => $order->id,
                    'notificationCount' => $deliveryMenModel->totalNotifications + 1,
                    'status' => $status,
                ],
            ]);
        }

        $orderDeliveryStatus->save();

        return $this->saveStatusLog($order->id, $orderDeliveryStatus);
    }

    /**
     * Method deleteOrderByDelivery
     *
     * @param $id $id
     * @param $model $model
     *
     * @return void
     */
    public function deleteOrderByDelivery($id, $model)
    {
        $deliveryIds = Model::where('deliveryMenId', (int) $id)->get();
        foreach ($deliveryIds as $key => $deliveryId) {
            $deliveryId->delete();
        }
    }

    /**
     * Get Orders Reports Generator
     *
     * @return DeliveryReports
     */
    public function reports(): DeliveryReports
    {
        return new DeliveryReports($this);
    }

    /**
     * Method cantAssentMoreThan2
     *
     * @return void
     */
    public function cantAssentMoreThan2()
    {
        $DELIVERY_ACCEPTED_STATUS = OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS;
        $DELIVERY_ON_THE_WAY_Restaurant_STATUS = OrderDeliveryRepository::DELIVERY_ON_THE_WAY_Restaurant_STATUS;

        return $this->orderDeliveryRepository->getQuery()->where('deliveryMenId', user()->id)->where(function ($query) use ($DELIVERY_ACCEPTED_STATUS, $DELIVERY_ON_THE_WAY_Restaurant_STATUS) {
            $query->where('status', '=', $DELIVERY_ACCEPTED_STATUS)
                ->orWhere('status', '=', $DELIVERY_ON_THE_WAY_Restaurant_STATUS);
        })->count();
    }
}
