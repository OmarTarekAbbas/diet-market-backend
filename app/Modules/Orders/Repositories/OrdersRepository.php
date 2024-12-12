<?php

namespace App\Modules\Orders\Repositories;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
    use Illuminate\Support\Facades\App;
use App\Modules\Orders\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Modules\Orders\Models\OrderItem;
use App\Modules\General\Helpers\Currency;
use App\Modules\Banks\Models\BankTransfer;
use App\Modules\Customers\Models\Customer;
use App\Modules\Orders\Models\OrderStatus;
use App\Modules\Orders\Models\Order as Model;
use App\Modules\Orders\Services\OrdersReports;
use App\Modules\AddressBook\Models\AddressBook;
use App\Modules\Orders\Filters\Order as Filter;
use App\Modules\Orders\Models\OrderStatusDelivery;
use App\Modules\Orders\Resources\Order as Resource;
use App\Modules\Services\Contracts\PaymentGatewayResponse;
use App\Modules\Services\Exceptions\InvalidPaymentException;
use App\Modules\General\Services\Payments\Methods\NoonPayments;
use App\Modules\DeliveryMen\Repositories\DeliveryMensRepository;
use App\Modules\Services\Gateways\OTO;
use App\Modules\StoreManagers\Models\StoreManager;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;
use Illuminate\Support\Collection;

class OrdersRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'orders';

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
    const DATA = [
        'shippingMethod', 'productsType', 'paymentMethod', 'notes',
        'type',
        'expectedDeliveryIn', 'insideWhereType'
    ];

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
    const ARRAYBLE_DATA = [
        'currency',
    ];

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
    const FLOAT_DATA = ['subTotal', 'shippingFees', 'totalQuantity', 'finalPrice', 'cashOnDeliveryPrice', 'subWidthProduct'];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = ['isAmountFull', 'isCheckAmountFull', 'isGetLastOrderIsNotReview'];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [
        'bankTransfer' => BankTransfer::class,
        'shippingAddress' => AddressBook::class,
        // 'seller' => StoreManager::class,
        // 'restaurantManger' => RestaurantManager::class,
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
        'bankTransfer', 'isAmountFull', 'isCheckAmountFull', 'checkOutId', 'paymentMethod', 'currency', 'status', 'subTotal', 'shippingFees', 'totalQuantity', 'finalPrice', 'notes', 'customer', 'paymentInfo', 'cancelingReason', 'returnedAmount',
        'nextStatus', 'wallet', 'taxes', 'originalPrice', 'requestReturning', 'partialReturn', 'rewardPoints', 'totalQuantity', 'subscription', 'shippingMethod',
        'rating', 'firstWeek', 'productsType', 'secondWeek', 'thirdWeek', 'fourthWeek', 'returningReason', 'expectedDeliveryIn', 'fromTime', 'toTime', 'deliveryType', 'restaurantsReviews', 'clubsReviews', 'nutritionSpecialist', 'nutritionSpecialistsReviews', 'listReturnedOrderItems', 'cashOnDeliveryPrice', 'deliveryName', 'deliveryDate', 'coupon', 'shippingAddress', 'restaurantManager', 'club', 'mainBranchClub', 'nutritionSpecialist', 'isGetLastOrderIsNotReview', 'insideWhereType',
    ];

    const DATE_DATA = [];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'in' => [
            'status',
        ],
        'int' => [
            'id',
            'customer' => 'customer.id',
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
     * Order statuses list
     *
     * @const string
     */
    const PAYING_STATUS = 'paying';

    const PENDING_STATUS = 'pending';

    const PROCESSING_STATUS = 'processing';

    const WAITING_TO_PICKUP_STATUS = 'waitingToPickup';

    const ON_THE_WAY_STATUS = 'onTheWay';

    const DELIVERY_ON_THE_WAY_STATUS = 'deliveryOnTheWay';

    const COMPLETED_STATUS = 'completed';

    const REQUEST_RETURNING_STATUS = 'requestReturning';

    const REQUEST_RETURNING_PENDING_STATUS = 'requestReturningPending';

    const REQUEST_RETURNING_ACCEPTED_STATUS = 'requestReturningAccepted';

    const REQUEST_RETURNING_REJECTED_STATUS = 'requestReturningRejected';

    const RETURNED_STATUS = 'returned';

    const REQUEST_PARTIAL_RETURN_STATUS = 'requestPartialReturn';

    const PARTIALLY_RETURNED_STATUS = 'partiallyReturned';

    const CANCELED_STATUS = 'canceled';

    const ADMIN_CANCELED_STATUS = 'adminCanceled';

    const UNSUBSCRIBE_STATUS = 'unsubscribe';

    const ALTERNATIVE_PRODUCT = 'alternativeProduct';

    const WALLET_PRODUCT = 'walletProduct';

    /**
     * Payment methods list
     *
     * @const string
     */
    const CASH_ON_DELIVERY = 'cashOnDelivery';

    const VISA_PAYMENT_METHOD = 'VISA';

    const MADA_PAYMENT_METHOD = 'MADA';

    const MASTER_PAYMENT_METHOD = 'MASTER';

    const WALLET_PAYMENT_METHOD = 'wallet';

    const BANK_TRANSFER_METHOD = 'bankTransfer';

    const APPLE_PAY_PAYMENT_METHOD = 'APPLEPAY';

    /**
     * Next allowed status
     *
     * @const array
     */
    const NEXT_ORDER_STATUS = [
        OrdersRepository::PAYING_STATUS => [OrdersRepository::PENDING_STATUS],
        OrdersRepository::PENDING_STATUS => [OrdersRepository::PROCESSING_STATUS, OrdersRepository::CANCELED_STATUS, OrdersRepository::ADMIN_CANCELED_STATUS],
        OrdersRepository::PROCESSING_STATUS => [OrdersRepository::ON_THE_WAY_STATUS, OrdersRepository::WAITING_TO_PICKUP_STATUS, OrdersRepository::CANCELED_STATUS, OrdersRepository::ADMIN_CANCELED_STATUS],
        OrdersRepository::ON_THE_WAY_STATUS => [OrdersRepository::COMPLETED_STATUS, OrdersRepository::ADMIN_CANCELED_STATUS],
        // OrdersRepository::DELIVERY_ON_THE_WAY_STATUS => [OrdersRepository::COMPLETED_STATUS, OrdersRepository::DELIVERY_RETURNED_ORDER],
        OrdersRepository::WAITING_TO_PICKUP_STATUS => [OrdersRepository::COMPLETED_STATUS, OrdersRepository::ADMIN_CANCELED_STATUS],
        OrdersRepository::COMPLETED_STATUS => [OrdersRepository::REQUEST_RETURNING_STATUS, OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS],

        // OrdersRepository::REQUEST_RETURNING_STATUS => [OrdersRepository::RETURNED_STATUS, OrdersRepository::COMPLETED_STATUS, OrdersRepository::DELIVERY_ON_THE_WAY_STATUS],

        OrdersRepository::REQUEST_RETURNING_STATUS => [OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS, OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS],

        // OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS => [OrdersRepository::PARTIALLY_RETURNED_STATUS, OrdersRepository::COMPLETED_STATUS, OrdersRepository::DELIVERY_ON_THE_WAY_STATUS],
        // if canceled, can not be changed by anyone
        // OrdersRepository::COMPLETED_STATUS => [OrdersRepository::REQUEST_RETURNING_STATUS, OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS, OrdersRepository::REQUEST_RETURNING_PENDING_STATUS],
        OrdersRepository::COMPLETED_STATUS => [],

        // OrdersRepository::REQUEST_RETURNING_STATUS => [OrdersRepository::RETURNED_STATUS, OrdersRepository::COMPLETED_STATUS, OrdersRepository::DELIVERY_ON_THE_WAY_STATUS, OrdersRepository::REQUEST_RETURNING_PENDING_STATUS],

        OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS => [OrdersRepository::PARTIALLY_RETURNED_STATUS, OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS],

        OrdersRepository::PARTIALLY_RETURNED_STATUS => [OrdersRepository::ALTERNATIVE_PRODUCT, OrdersRepository::WALLET_PRODUCT],
        // OrdersRepository::REQUEST_RETURNING_PENDING_STATUS => [OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS, OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS],
        OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS => [OrdersRepository::ALTERNATIVE_PRODUCT, OrdersRepository::WALLET_PRODUCT],
        OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS => [],
        OrdersRepository::ALTERNATIVE_PRODUCT => [OrdersRepository::REQUEST_RETURNING_STATUS],
        OrdersRepository::WALLET_PRODUCT => [OrdersRepository::REQUEST_RETURNING_STATUS],
        // if canceled, can not be changed by anyone
        OrdersRepository::CANCELED_STATUS => [],
        OrdersRepository::ADMIN_CANCELED_STATUS => [],
    ];

    /**
     * Next allowed status
     *
     * @const array
     */
    const NEXT_ORDER_STATUS_RETURNED_STATUS = [
        OrdersRepository::COMPLETED_STATUS => [OrdersRepository::REQUEST_RETURNING_STATUS],
        OrdersRepository::REQUEST_RETURNING_STATUS => [OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS, OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS],
        OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS => [OrdersRepository::PARTIALLY_RETURNED_STATUS, OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS],
        OrdersRepository::PARTIALLY_RETURNED_STATUS => [OrdersRepository::ALTERNATIVE_PRODUCT, OrdersRepository::WALLET_PRODUCT],
        OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS => [OrdersRepository::ALTERNATIVE_PRODUCT, OrdersRepository::WALLET_PRODUCT],
        OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS => [],
        OrdersRepository::ALTERNATIVE_PRODUCT => [],
        OrdersRepository::WALLET_PRODUCT => [],
        OrdersRepository::CANCELED_STATUS => [],
        OrdersRepository::ADMIN_CANCELED_STATUS => [],
    ];

    /**
     * Next allowed status
     *
     * @const array
     */
    const NEXT_ORDER_STATUS_FOOD = [
        OrdersRepository::PAYING_STATUS => [OrdersRepository::PENDING_STATUS],
        OrdersRepository::PENDING_STATUS => [OrdersRepository::PROCESSING_STATUS, OrdersRepository::CANCELED_STATUS],
        OrdersRepository::PROCESSING_STATUS => [OrdersRepository::ON_THE_WAY_STATUS, OrdersRepository::CANCELED_STATUS],
        OrdersRepository::ON_THE_WAY_STATUS => [OrdersRepository::COMPLETED_STATUS],
        OrdersRepository::COMPLETED_STATUS => [],

        // OrdersRepository::REQUEST_RETURNING_STATUS => [OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS, OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS],

        // OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS => [OrdersRepository::PARTIALLY_RETURNED_STATUS, OrdersRepository::COMPLETED_STATUS, OrdersRepository::DELIVERY_ON_THE_WAY_STATUS],
        // if canceled, can not be changed by anyone

        // OrdersRepository::COMPLETED_STATUS => [OrdersRepository::REQUEST_RETURNING_STATUS, OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS, OrdersRepository::REQUEST_RETURNING_PENDING_STATUS],

        // OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS => [OrdersRepository::PARTIALLY_RETURNED_STATUS, OrdersRepository::COMPLETED_STATUS, OrdersRepository::DELIVERY_ON_THE_WAY_STATUS, OrdersRepository::REQUEST_RETURNING_PENDING_STATUS],

        OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS => [],
        OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS => [],
        // if canceled, can not be changed by anyone
        OrdersRepository::CANCELED_STATUS => [],
        OrdersRepository::ADMIN_CANCELED_STATUS => [],
    ];

    /**
     * Next allowed status
     *
     * @const array
     */
    const NEXT_ORDER_STATUS_DELIVERY_BY_DITE_MARKIT_FOOD = [
        OrdersRepository::PAYING_STATUS => [OrdersRepository::PENDING_STATUS],
        OrdersRepository::PENDING_STATUS => [OrdersRepository::PROCESSING_STATUS, OrdersRepository::CANCELED_STATUS],
        OrdersRepository::PROCESSING_STATUS => [OrdersRepository::CANCELED_STATUS],
        OrdersRepository::ON_THE_WAY_STATUS => [],
        OrdersRepository::COMPLETED_STATUS => [],
        // if canceled, can not be changed by anyone
        OrdersRepository::CANCELED_STATUS => [],
        OrdersRepository::ADMIN_CANCELED_STATUS => [],
    ];

    /**
     * Next allowed status
     *
     * @const array
     */
    const NEXT_ORDER_STATUS_CLUBS = [
        OrdersRepository::PAYING_STATUS => [OrdersRepository::PENDING_STATUS],
        OrdersRepository::PENDING_STATUS => [OrdersRepository::COMPLETED_STATUS, OrdersRepository::CANCELED_STATUS, OrdersRepository::ADMIN_CANCELED_STATUS],
        OrdersRepository::COMPLETED_STATUS => [OrdersRepository::UNSUBSCRIBE_STATUS],
        OrdersRepository::UNSUBSCRIBE_STATUS => [],
    ];

    /**
     * Next allowed status
     *
     * @const array
     */
    const NEXT_ORDER_STATUS_NUTRITION = [
        OrdersRepository::PAYING_STATUS => [OrdersRepository::PENDING_STATUS],
        OrdersRepository::PENDING_STATUS => [OrdersRepository::PROCESSING_STATUS, OrdersRepository::CANCELED_STATUS, OrdersRepository::ADMIN_CANCELED_STATUS],
        OrdersRepository::PROCESSING_STATUS => [OrdersRepository::COMPLETED_STATUS, OrdersRepository::CANCELED_STATUS, OrdersRepository::ADMIN_CANCELED_STATUS],
        OrdersRepository::COMPLETED_STATUS => [],
        OrdersRepository::CANCELED_STATUS => [],
        OrdersRepository::ADMIN_CANCELED_STATUS => [],
    ];

    /**
     * Shipping Methods
     *
     * @const string
     */
    const HOME_SHIPPING = 'inHome';

    const PICKUP_FROM_STORE_SHIPPING = 'inStore';

    const HAND_TO_HAND = 'handToHand';

    const SHIPPING_COMPANY = 'shippingCompany';

    /**
     * {@inheritDoc}
     */
    protected function records(Collection $orders): Collection
    {
        $currentUser = user();

        if (!$currentUser instanceof StoreManager) return parent::records($orders);

        // update orders on the fly
        return parent::records($orders->map(function ($order) use ($currentUser) {
            // get only the order items that are related to the current store manager
            // update the total price of the order based on only the items of the products that are related to the current store manager
            $items = collect($order->items)->where('seller.id', $currentUser->id);
            $taxesSeller = $this->taxesSeller($items->sum('totalPrice'));
            $finalPrice = $items->sum('totalPrice') + $order->shippingFees / count($order['seller']);
            $order->items = $items->toArray();
            $order->subTotal = $items->sum('totalPrice');
            $order->shippingFees = $order->shippingFees / count($order['seller']);
            $order->taxes = $taxesSeller[1];
            $order->totalQuantity = $items->sum('quantity');
            $order->finalPrice = $finalPrice;
            $order->originalPrice = $taxesSeller[0];
            $order->wallet = $finalPrice;
            $order->subWidthProduct = $items->sum('widthProduct');
            return $order;
        }));
    }

    /**
     * I'm trying to get the order items that are related to the current store manager and update the
     * total price of the order based on only the items of the products that are related to the current
     * store manager
     * 
     * @param id the id of the order
     * 
     * @return The order object with the items that are related to the current store manager.
     */
    public function get($id)
    {
        $order = $this->getModel($id);

        if (!$order) return null;

        $currentUser = user();

        if (!$currentUser instanceof StoreManager) return $this->wrap($order);

        // update orders on the fly
        // get only the order items that are related to the current store manager
        // update the total price of the order based on only the items of the products that are related to the current store manager
        $items = collect($order->items)->where('seller.id', $currentUser->id);
        $taxesSeller = $this->taxesSeller($items->sum('totalPrice'));
        $finalPrice = $items->sum('totalPrice') + $order->shippingFees / count($order['seller']);
        $order->items = $items->toArray();
        $order->subTotal = $items->sum('totalPrice');
        $order->shippingFees = $order->shippingFees / count($order['seller']);
        $order->taxes = $taxesSeller[1];
        $order->totalQuantity = $items->sum('quantity');
        $order->finalPrice = $finalPrice;
        $order->originalPrice = $taxesSeller[0];
        $order->wallet = $finalPrice;
        $order->subWidthProduct = $items->sum('widthProduct');
        return $this->wrap($order);
    }

    /**
     * It takes the total price of the order and subtracts the total price of the order without taxes
     * 
     * @param totalPrice The total price of the order
     * 
     * @return an array of two values.
     */
    public function taxesSeller($totalPrice)
    {
        $taxes = $this->settingsRepository->getOrderTaxes();

        $originalPrice = $this->totalWithoutTaxes($totalPrice, $taxes);
        $allTotalPrice = round($totalPrice - $originalPrice, 2);
        return [$originalPrice, $allTotalPrice];
    }

    /**
     * Get Total Without Taxes
     *
     * @param $originalTotal
     * @param $taxes
     * @return float
     */
    public function totalWithoutTaxes($originalTotal, $taxes, $rounding = true)
    {
        $price = $originalTotal / (1 + ($taxes / 100));

        return $rounding ? round($price, 2) : $price;
    }

    /**
     * Change order item status
     * 
     * @param  int $orderItemId
     * @param  string $status
     * @return Order | null
     */
    public function changeOrderItemStatus(int $orderItemId, string $status, $request)
    {
        $orderItem = OrderItem::find($orderItemId);

        if (!$orderItem) return null;

        $orderItem->status = $status;

        $orderItem->nextStatus = static::NEXT_ORDER_STATUS[$status] ?? [];

        $orderItem->save();

        $order = Order::find($orderItem->orderId);

        if (!$order) return null;

        $order->reassociate($orderItem, 'items');

        $allItemsStatusesAreChanged = true;

        // PATCH /orders/items/{id}/change-status

        foreach ($order->items as $item) {
            if ($item['status'] !== $status) {
                $allItemsStatusesAreChanged = false;
                break;
            }
        }

        if ($allItemsStatusesAreChanged) {
            $order->status = $orderItem->status;
            $order->nextStatus = static::NEXT_ORDER_STATUS[$orderItem->status] ?? [];
            $customer = $this->getCustomer($order);
            $notification = $this->notificationsRepository->create([
                'title' => trans('notifications.order.title.' . $status),
                'content' => trans('notifications.order.content.' . $status),
                'type' => 'productOrder',
                'user' => $customer,
                'pushNotification' => true,
                'extra' => [
                    'type' => 'productOrder',
                    'status' => $status,
                    'orderId' => $order->id,
                ],
            ]);
            $this->notificationsRepository->getQuery()->where('id', $notification->id)->delete();
        }

        $order->save();

        $currentSeller = user();
        $itemsCount = collect($order->items)->where('seller.id', $currentSeller->id)->count();
        $itemsProcessing = collect($order->items)->where('seller.id', $currentSeller->id)->where('status', 'processing')->count();
        $items = collect($order->items)->where('seller.id', $currentSeller->id);
        $amount = $items->sum('totalPrice') + $order->shippingFees / count($order['seller']);
        $packageWeight = $items->sum('widthProduct');
        // make curl api oto 
        if ($itemsCount === $itemsProcessing) {
            $oto = App::make(OTO::class);
            $oto->createOrder($order, $currentSeller, $amount, $itemsCount, $packageWeight, $items);
        }


        return $order;
    }

    /**
     * It takes an order id, a status, and a request, finds the order, changes the status of the order
     * items, reassociates the order items with the order, and then saves the order
     * 
     * @param int orderId The id of the order
     * @param string status the status of the order
     * @param request 
     */
    public function changeOrderItemByOrderStatus(int $orderId, string $status, $request)
    {
        $order = Order::find($orderId);

        if (!$order) return null;

        $orderItems = collect($order->items);

        foreach ($orderItems as $orderItemCollect) {
            $orderItem = OrderItem::find((int)$orderItemCollect['id']);
            $orderItem->status = $status;
            $orderItem->nextStatus = static::NEXT_ORDER_STATUS[$status] ?? [];
            $orderItem->save();
            $order->reassociate($orderItem, 'items');
            $order->save();
        }

        if ($status === 'processing') {
            if ($order->productsType === 'products') {
                foreach ($orderItems as $orderItem) {
                    $orderItem = OrderItem::find((int)$orderItem['id']);
                    $items = collect($order->items)->where('seller.id', (int)$orderItem->seller['id']);
                    $amount = $items->sum('totalPrice') + $order->shippingFees / count($order['seller']);
                    $packageWeight = $items->sum('widthProduct');
                    $itemsCount = 1;
                    $oto = App::make(OTO::class);
                    $oto->createOrder($order, $orderItem->seller, $amount, $itemsCount, $packageWeight, $items);
                }
            }
        }

        // return $order;
    }

    /**
     * It changes the status of the order item to the status of the order
     * 
     * @param int orderId The order id
     * @param string statusOto pickedUp or delivered
     * @param int sellerId The seller's id
     * @param request 
     */
    public function changeOrderItemByOtoOrderStatus(int $orderId, string $statusOto, int $sellerId, $printAWBURL, $request)
    {
        $order = Order::find($orderId);
        if (!$order) return null;

        if ($statusOto === 'pickedUp') {
            $status = static::ON_THE_WAY_STATUS;
        } elseif ($statusOto === 'delivered') {
            $status = static::COMPLETED_STATUS;
        }

        $orderItems = collect($order->items)->where('seller.id', $sellerId);
        foreach ($orderItems as $orderItemCollect) {
            $orderItem = OrderItem::find((int)$orderItemCollect['id']);
            $orderItem->status = $status;
            $orderItem->nextStatus = static::NEXT_ORDER_STATUS[$status] ?? [];
            $orderItem->printAWBURL = $printAWBURL;
            $orderItem->save();
            $order->reassociate($orderItem, 'items');

            $allItemsStatusesAreChanged = true;

            foreach ($order->items as $item) {
                if ($item['status'] !== $status) {
                    $allItemsStatusesAreChanged = false;
                    break;
                }
            }

            if ($allItemsStatusesAreChanged) {
                $order->status = $orderItem->status;
                $order->nextStatus = static::NEXT_ORDER_STATUS[$orderItem->status] ?? [];
                $customer = $this->getCustomer($order);
                $notification = $this->notificationsRepository->create([
                    'title' => trans('notifications.order.title.' . $status),
                    'content' => trans('notifications.order.content.' . $status),
                    'type' => 'productOrder',
                    'user' => $customer,
                    'pushNotification' => true,
                    'extra' => [
                        'type' => 'productOrder',
                        'status' => $status,
                        'orderId' => $order->id,
                    ],
                ]);
                $this->notificationsRepository->getQuery()->where('id', $notification->id)->delete();
            }
            $order->save();
        }
    }

    /**
     * I'm trying to update the orderItem's lengthBox, weightBox, and heightBox based on the request
     * data.
     * @param orderId the id of the order
     * @param request 
     * 
     * @return The order object is being returned.
     */
    public function updateBoxItemBySeller(int $orderId, $request)
    {
        $order = Order::find($orderId);

        if (!$order) return null;

        $currentUser = user();

        if (!$currentUser instanceof StoreManager) return $this->wrap($order);

        $orderItems = collect($order->items)->where('seller.id', $currentUser->id);

        if ($request['selectBox'] === 'manual') {
            $lengthBox = $request['lengthBox'];
            $weightBox = $request['weightBox'];
            $heightBox = $request['heightBox'];
        } else {
            $selectSizeBox = $this->productPackageSizesRepository->get($request['selectSizeBox']);
            $lengthBox = $selectSizeBox['lengthBox'];
            $weightBox = $selectSizeBox['weightBox'];
            $heightBox = $selectSizeBox['heightBox'];
        }

        foreach ($orderItems as $orderItem) {
            $orderItem = OrderItem::find((int)$orderItem['id']);
            $orderItem->lengthBox = $lengthBox;
            $orderItem->weightBox = $weightBox;
            $orderItem->heightBox = $heightBox;
            $orderItem->selectBox = $request['selectBox'];
            $orderItem->save();

            $order->reassociate($orderItem, 'items');
            $order->save();
        }
        $items = collect($order->items)->where('seller.id', $currentUser->id);
        $taxesSeller = $this->taxesSeller($items->sum('totalPrice'));
        $finalPrice = $items->sum('totalPrice') + $order->shippingFees / count($order['seller']);
        $order->items = $items->toArray();
        $order->subTotal = $items->sum('totalPrice');
        $order->shippingFees = $order->shippingFees / count($order['seller']);
        $order->taxes = $taxesSeller[1];
        $order->totalQuantity = $items->sum('quantity');
        $order->finalPrice = $finalPrice;
        $order->originalPrice = $taxesSeller[0];
        $order->wallet = $finalPrice;
        $order->subWidthProduct = $items->sum('widthProduct');
        return $this->wrap($order);
    }

    /**
     * I'm trying to update the orderItem's lengthBox, weightBox, and heightBox with the values from
     * the request
     * 
     * @param orderId The id of the order
     * @param request 
     */
    public function updateBoxItem($orderId, $request)
    {
        $order = Order::find($orderId);

        if (!$order) return null;

        $orderItems = collect($order->items);

        if ($request['selectBox'] === 'manual') {
            $lengthBox = $request['lengthBox'];
            $weightBox = $request['weightBox'];
            $heightBox = $request['heightBox'];
        } else {
            $selectSizeBox = $this->productPackageSizesRepository->get($request['selectSizeBox']);
            $lengthBox = $selectSizeBox['lengthBox'];
            $weightBox = $selectSizeBox['weightBox'];
            $heightBox = $selectSizeBox['heightBox'];
        }
        foreach ($orderItems as $orderItem) {
            $orderItem = OrderItem::find((int)$orderItem['id']);
            $orderItem->lengthBox = $lengthBox;
            $orderItem->weightBox = $weightBox;
            $orderItem->heightBox = $heightBox;
            $orderItem->save();
            $order->reassociate($orderItem, 'items');
            $order->save();
        }
        return $this->wrap($order);
    }

    /**
     * It takes an order ID and an array of order item IDs, and returns an OTO object
     * 
     * @param orderId The order ID of the order you want to return.
     * @param returnedItems An array of order item IDs that are being returned.
     * 
     * @return The return request is being returned.
     */
    public function requestReturningOto($order, $orderId, $returnedItems)
    {
        $orderItem = OrderItem::find((int)$returnedItems[0]);
        if (!$orderItem) return null;

        $oto = App::make(OTO::class);
        return $oto->OtoOrderReturnRequest($order, $orderId, $orderItem);
    }

    /**
     * Set any extra data or columns that need more customizations
     * Please note this method is triggered on create or update call
     *
     * @param mixed $model
     * @param \Illuminate\Http\Request $request
     * @return  void
     * @throws Exception
     */
    protected function setData($model, $request)
    {
        if (user()->AccountType() == 'user' && !$model->id) {
            $this->adminCreateOrder($model, $request);

            return;
        }

        $customer = $this->getCustomer($model);

        $model->customer = $customer->sharedInfo();

        if ($request->type == "products") {
            $cart = $this->cartRepository->wrap($customer->cart);
        } elseif ($request->type == "food") {
            $cart = $this->cartRepository->wrap($customer->cartMeal);
        }

        if ($request->type == 'products') {
            if ($cart['shippingFees']) {
                $model->shippingFees = $cart['shippingFees'];
                $model->expectedDeliveryIn = $cart['expectedDeliveryIn'];
                $model->shippingMethod = $cart['shippingMethod'];
                $model->shippingAddress = $cart['shippingAddress'];
            } else {
                $model->shippingFees = 0.0;
            }
        } elseif ($request->type == 'food' && $request->deliveryType == 'inHome') {
            $restaurant = $this->restaurantsRepository->get($cart['restaurant']['id']);
            if ($restaurant['delivery'] == false) {
                $deliveryValue = $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenCost');
            } else {
                $deliveryValue = $restaurant['deliveryValue'];
            }

            $model->shippingFees = $deliveryValue;
            $model->expectedDeliveryIn = trans('products.minutes', ['value' => $restaurant->getDeliveryTime()]);
            $model->deliveryType = $request->deliveryType;
            $model->shippingAddress = $cart['shippingAddress'];
        } elseif ($request->type == 'food' && $request->deliveryType == 'inStore') {
            $restaurant = $this->restaurantsRepository->wrapMany($this->restaurantsRepository->get($cart['restaurant']['id']));
            // dd($restaurant['isClosed']->resource);
            if (!$restaurant['isClosed']->resource) {
                $storeOpenFrom = strtotime($restaurant['openToday']['open']);
                $storeOpenTo = strtotime($restaurant['openToday']['close']);
                $requestFrom = strtotime($request['fromTime']);
                $requestTo = strtotime($request['toTime']);
                // if ($storeOpenTo == '00:00') {
                //     $storeOpenTo = '23:59';
                // }
                // dd($storeOpenFrom ,$requestFrom , $storeOpenTo , $requestTo);
                // if ($storeOpenFrom > $requestFrom || $storeOpenTo < $requestTo) {
                //     throw new Exception(trans('general.outOfWorkingHours', ['from' => $restaurant['openToday']['open'], 'to' => $restaurant['openToday']['close']]));
                // }
            }
            $model->fromTime = $request->fromTime;
            $model->toTime = $request->toTime;
            $model->deliveryType = $request->deliveryType;
            $model->shippingFees = 0.0;
        }

        if ($request->type == 'food') {
            $restaurantMinimumOrders = $this->restaurantsRepository->get($cart['restaurant']['id']);
            if ($cart['totalPrice'] < $restaurantMinimumOrders->minimumOrders) {
                throw new Exception(trans('general.minimumOrdersCart', ['value' => $restaurantMinimumOrders->minimumOrders]));
            }

            $restaurant = $this->restaurantsRepository->wrapMany($this->restaurantsRepository->get($cart['restaurant']['id']));

            if ($restaurant['isClosed']->resource == true || $restaurant['isBusy']->resource == true) {
                throw new Exception(trans('general.restaurantIsClosed'));
            }
        }

        $subTotal = 0;
        $subWidthProduct = 0;

        $rewardPoints = 0;

        $totalQuantity = 0;

        $products = [];

        $model->productsType = $request->type ?? 'direct';
        $model->insideWhereType = $request->insideWhereType ?? 'mobile';

        if ($request->type == 'products') {
            // $this->setVendorsOrders($model, $cart, $request);
            $items = $cart['items'];

            $itemStatus = static::PENDING_STATUS;

            foreach ($items as $cartItem) {
                $productId = $cartItem['product']['id'];

                $totalQuantity += $cartItem['quantity'];

                $product = $this->productsRepository->getModel($productId);

                if (!$product) {
                    throw new Exception(trans('products.notFoundProduct', ['name' => $cartItem['product']['name']]));
                }

                $productData = $product->sharedInfo();

                unset($productData['options']);
                $taxesSeller = $this->taxesSeller($cartItem['totalPrice']);
                $orderItem = OrderItem::create([
                    'orderId' => $model->id ?: $model->getNextId(),
                    'product' => $productData,
                    'rewardPoints' => $cartItem['rewardPoints'],
                    'price' => $cartItem['price'],
                    'quantity' => $cartItem['quantity'],
                    'totalPrice' => $cartItem['totalPrice'],
                    'options' => $cartItem['options'] ?? [],
                    'notes' => $cartItem['notes'] ?? null,
                    'skuProduct' => $cartItem['skuProduct'] ?? null,
                    'widthProduct' => $cartItem['widthProduct'] ?? null,
                    'seller' => $cartItem['seller'] ?? null,
                    'taxes' => $taxesSeller[1] ?? null,
                    'originalPrice' => $taxesSeller[0] ?? null,
                    'type' => $cartItem['type'],
                    'subscription' => $cartItem['subscription'],
                    'customerId' => user()->id,
                    'status' =>  $itemStatus,
                    'nextStatus' => static::NEXT_ORDER_STATUS[$itemStatus] ?? [],
                ]);

                $subTotal += $cartItem['totalPrice'];
                $subWidthProduct += $cartItem['widthProduct'];

                $rewardPoints += $orderItem['rewardPoints'];

                $products[] = $orderItem->sharedInfo();
            }

            $model->totalQuantity = $totalQuantity;
            $model->items = $products;
            $model->rewardPoints = $rewardPoints;
            $model->specialDiscount = $cart['specialDiscount'];

            $model->subTotal = $subTotal;

            $model->subWidthProduct = $subWidthProduct;

            // is is a passing object from the controller as the orders controller
            // is validating the coupon before getting into creating the order
            // todo : need to review
            if ($cart['coupon']) {
                $coupon = $this->couponsRepository->getValidCoupon($cart['coupon']['code'], $request);
                if ($coupon) {
                    $model->coupon = $coupon->sharedInfo();
                    $model->couponDiscount = $coupon->couponDiscount;
                    $this->couponsRepository->increaseTotalUses($coupon);
                    $this->rewardsRepository->checkCoupon($cart['coupon']['code']);
                }
            }

            $model->wallet = 0;

            $model->taxes = $cart['taxes'];
            $model->originalPrice = $cart['originalPrice'];
            $model->totalQuantity = $cart['totalQuantity'];

            $model->usedRewardPoints = $cart['usedRewardPoints'];
            $model->rewordDiscount = $cart['rewordDiscount'];
            $model->isActiveRewardPoints = $cart['isActiveRewardPoints'];

            $model->finalPrice = $model->subTotal + $model->shippingFees - ($model->couponDiscount ?? 0) - ($model->specialDiscount ?? 0) - $model->rewordDiscount;

            $model->finalPrice = round($model->finalPrice, 2);

            if ($customer->walletBalance) {
                if ($customer->walletBalance >= $model->finalPrice) {
                    if ($request->paymentMethod == static::WALLET_PAYMENT_METHOD) {
                        $model->wallet = round($model->finalPrice, 2);
                        $model->finalPrice = $model->wallet;
                    }
                } elseif ($customer->walletBalance < 0) {
                    $model->wallet = round($customer->walletBalance, 2);
                    $model->finalPrice -= $model->wallet;
                }
                $model->finalPrice = round($model->finalPrice, 2);
            }

            if ($cart['seller']) {
                $cartSellers = $cart['seller'];
                foreach ($cartSellers as $cartSeller) {
                    $model->reassociate($cartSeller, 'seller');
                }
            }
            $model->currency = Currency::getCurrency();
        } elseif ($request->type == 'food') {
            //start food
            $items = $request->type == 'auction' ? $cart['auctionItems'] : $cart['items'];

            foreach ($items as $cartItem) {
                $productId = $cartItem['product']['id'];

                $totalQuantity += $cartItem['quantity'];

                $product = $this->productsRepository->getModel($productId);

                if (!$product) {
                    throw new Exception(trans('products.notFoundProduct', ['name' => $cartItem['product']['name']]));
                }

                $productData = $product->sharedInfo();

                unset($productData['options']);

                $orderItem = OrderItem::create([
                    'orderId' => $model->id ?: $model->getNextId(),
                    'product' => $productData,
                    'rewardPoints' => $cartItem['rewardPoints'],
                    'price' => $cartItem['price'],
                    'quantity' => $cartItem['quantity'],
                    'totalPrice' => $cartItem['totalPrice'],
                    'options' => $cartItem['options'] ?? [],
                    'notes' => $cartItem['notes'] ?? null,
                    'type' => $cartItem['type'],
                    'subscription' => $cartItem['subscription'],
                    'customerId' => user()->id,
                ]);

                $subTotal += $cartItem['totalPrice'];

                $rewardPoints += $orderItem['rewardPoints'];

                $products[] = $orderItem->sharedInfo();
            }

            $model->totalQuantity = $totalQuantity;
            $model->items = $products;
            $model->rewardPoints = $rewardPoints;
            $model->specialDiscount = $cart['specialDiscount'];
            $model->subTotal = $subTotal;

            // is is a passing object from the controller as the orders controller
            // is validating the coupon before getting into creating the order
            // todo : need to review
            if ($cart['coupon']) {
                $coupon = $this->couponsRepository->getValidCoupon($cart['coupon']['code'], $request);
                if ($coupon) {
                    $model->coupon = $coupon->sharedInfo();
                    $model->couponDiscount = $coupon->couponDiscount;
                    $this->couponsRepository->increaseTotalUses($coupon);
                    $this->rewardsRepository->checkCoupon($cart['coupon']['code']);
                }
            }

            $model->wallet = 0;

            $model->taxes = $cart['taxes'];
            $model->originalPrice = $cart['originalPrice'];
            $model->totalQuantity = $cart['totalQuantity'];

            $model->usedRewardPoints = $cart['usedRewardPoints'];
            $model->rewordDiscount = $cart['rewordDiscount'];
            $model->isActiveRewardPoints = $cart['isActiveRewardPoints'];

            $model->finalPrice = $model->subTotal + $model->shippingFees - ($model->couponDiscount ?? 0) - ($model->specialDiscount ?? 0) - $model->rewordDiscount;

            if ($request->type == 'food') {
                if ($request->paymentMethod == static::CASH_ON_DELIVERY) {
                    $restaurant = $this->restaurantsRepository->get((int) $cart->restaurant['id']);

                    if ($restaurant && $restaurant['cashOnDelivery'] == true) {
                        $cashOnDeliveryPrice = $restaurant['cashOnDelivery'];
                        $model->cashOnDeliveryPrice = $cashOnDeliveryPrice;
                        $model->finalPrice += $cashOnDeliveryPrice;
                    } else {
                        $cashOnDeliveryPrice = $this->settingsRepository->getSetting('restaurant', 'cashOnDeliveryPrice');
                        $model->cashOnDeliveryPrice = $cashOnDeliveryPrice;
                        $model->finalPrice += $cashOnDeliveryPrice;
                    }
                }
            }

            $model->finalPrice = round($model->finalPrice, 2);

            if ($customer->walletBalance) {
                if ($customer->walletBalance >= $model->finalPrice) {
                    if ($request->paymentMethod == static::WALLET_PAYMENT_METHOD) {
                        $model->wallet = round($model->finalPrice, 2);
                        $model->finalPrice = $model->wallet;
                    }
                } elseif ($customer->walletBalance < 0) {
                    $model->wallet = round($customer->walletBalance, 2);
                    $model->finalPrice -= $model->wallet;
                }
                $model->finalPrice = round($model->finalPrice, 2);
            }

            if ($cart['restaurantManager']) {
                $restaurantManagers = $this->restaurantManagersRepository->get($cart['restaurantManager']['id']);
                if (!$restaurantManagers) {
                    throw new Exception('عفوا لا يوجد مدير لهذا المطعم');
                }
                $model->restaurantManager = $restaurantManagers->sharedInfo();
            }

            $model->currency = Currency::getCurrency();

            //end food
        } elseif ($request->type == 'nutritionSpecialist') {
            $product = $this->nutritionSpecialistMangersRepository->getModel($request->nutritionSpecialist ?? $model->nutritionSpecialist['id']);
            if (!$product) {
                throw new Exception(trans('products.notFoundProduct'));
            }
            $productData = $product->sharedInfo();

            $orderItem = OrderItem::create([
                'orderId' => $model->id ?: $model->getNextId(),
                'product' => $productData,
                'rewardPoints' => $product['nutritionSpecialist']['rewardPoints'],
                'price' => $product['nutritionSpecialist']['finalPrice'],
                'totalPrice' => $product['nutritionSpecialist']['finalPrice'],
                'type' => 'nutritionSpecialist',
                'startTime' => $request->time,
                'endTime' => Carbon::parse($request->time)->addHours(-8)->format('H:i'),
                'date' => $request->data,
            ]);

            $subTotal += $product['nutritionSpecialist']['finalPrice'];

            $rewardPoints += $product['nutritionSpecialist']['rewardPoints'];

            $products[] = $orderItem->sharedInfo();
            $model->items = $products;

            $model->rewardPoints = $rewardPoints;

            $model->startTime = $request->time;
            $model->endTime = Carbon::parse($request->time)->addHours(-8)->format('H:i');
            $model->date = $request->data;

            $model->subTotal = $subTotal;
            $model->wallet = 0;
            $customer = $customer->refresh();
            if ($customer->group) {
                foreach ($customer->group['nameGroup']  as $nameGroup) {
                    if ($nameGroup['name'] != 'reserveSpecialist') {
                        continue;
                    } else {
                        $model->specialDiscount = (($subTotal * $customer->group['specialDiscount']) / 100);
                    }
                }
            }
            $model->usedRewardPoints = $product['usedRewardPoints'];
            $model->finalPrice = $model->subTotal - ($model->couponDiscount ?? 0) - ($model->specialDiscount ?? 0);

            if ($request->type == 'nutritionSpecialist') {
                if ($request->paymentMethod == static::CASH_ON_DELIVERY) {
                    $cashOnDeliveryPrice = $this->settingsRepository->getSetting('nutritionSpecialist', 'cashOnDeliveryPrice');
                    $model->cashOnDeliveryPrice = $cashOnDeliveryPrice;
                    $model->finalPrice += $cashOnDeliveryPrice;
                }
            }

            $model->finalPrice = round($model->finalPrice, 2);

            if ($customer->walletBalance) {
                if ($customer->walletBalance >= $model->finalPrice) {
                    if ($request->paymentMethod == static::WALLET_PAYMENT_METHOD) {
                        $model->wallet = round($model->finalPrice, 2);
                        $model->finalPrice = $model->wallet;
                    }
                } elseif ($customer->walletBalance < 0) {
                    $model->wallet = round($customer->walletBalance, 2);
                    $model->finalPrice -= $model->wallet;
                }
                $model->finalPrice = round($model->finalPrice, 2);
            }


            $model->nutritionSpecialist = $product->sharedInfo();
            $model->nutritionSpecialistManager = $product->sharedInfo();
            $model->currency = Currency::getCurrency();
        } else {
            if (config('app.type') === 'site') {
                $product = $this->packagesClubsRepository->getModel($request->idpackagesClubs ?? $model->idpackagesClubs);
                // $club = $this->packagesClubsRepository->getModel($request->idpackagesClubs);
                if (!$product) {
                    throw new Exception(trans('products.notFoundProduct', ['name' => $product['name']]));
                }
                $productData = $product->sharedInfo();

                $subscribeStartAt = Carbon::now()->format('Y-m-d');

                $subscribeEndAt = Carbon::now()->addMonth($product->monthsNumber)->subDays()->format('Y-m-d');
                $orderItem = OrderItem::create([
                    'orderId' => $model->id ?: $model->getNextId(),
                    'product' => $productData,
                    'rewardPoints' => $product['rewardPoints'],
                    'price' => $product['finalPrice'],
                    'totalPrice' => $product['finalPrice'],
                    'type' => 'clubs',
                    'subscribeStartAt' => $subscribeStartAt,
                    'subscribeEndAt' => $subscribeEndAt,
                    'club' => $product['club']['id'],
                ]);

                $subTotal += $product['finalPrice'];

                $rewardPoints += $orderItem['rewardPoints'];

                $products[] = $orderItem->sharedInfo();
                $model->items = $products;

                $model->rewardPoints = $rewardPoints;
                $model->idpackagesClubs = $request->idpackagesClubs;

                $model->subTotal = $subTotal;
                $model->wallet = 0;

                $customer = $customer->refresh();

                if ($customer->group) {
                    foreach ($customer->group['nameGroup']  as $nameGroup) {
                        if ($nameGroup['name'] != 'clubSubscription') {
                            continue;
                        } else {
                            $model->specialDiscount = (($subTotal * $customer->group['specialDiscount']) / 100);
                        }
                    }
                }


                $model->usedRewardPoints = $product['usedRewardPoints'];
                $model->finalPrice = $model->subTotal - ($model->couponDiscount ?? 0) - ($model->specialDiscount ?? 0);

                if ($request->type == 'clubs') {
                    if ($request->paymentMethod == static::CASH_ON_DELIVERY) {
                        $cashOnDeliveryPrice = $this->settingsRepository->getSetting('club', 'cashOnDeliveryPrice');
                        $model->cashOnDeliveryPrice = $cashOnDeliveryPrice;
                        $model->finalPrice += $cashOnDeliveryPrice;
                    }
                }

                $model->finalPrice = round($model->finalPrice, 2);
                // if ($request->paymentMethod == static::WALLET_PAYMENT_METHOD) {
                //     if ($customer->walletBalance) {
                //         if ($customer->walletBalance >= $model->finalPrice) {
                //             $model->wallet = round($model->finalPrice, 2);
                //             $model->finalPrice = 0;
                //         } else {
                //             $model->wallet = round($customer->walletBalance, 2);
                //             $model->finalPrice -= $model->wallet;
                //         }
                //         $model->finalPrice = round($model->finalPrice, 2);
                //     }
                // }
                if ($customer->walletBalance) {
                    if ($customer->walletBalance >= $model->finalPrice) {
                        if ($request->paymentMethod == static::WALLET_PAYMENT_METHOD) {
                            $model->wallet = round($model->finalPrice, 2);
                            $model->finalPrice = $model->wallet;
                        }
                    } elseif ($customer->walletBalance < 0) {
                        $model->wallet = round($customer->walletBalance, 2);
                        $model->finalPrice -= $model->wallet;
                    }
                    $model->finalPrice = round($model->finalPrice, 2);
                }

                $club = $this->clubsRepository->get($product['club']['id']);

                $model->club = $club->sharedInfo();
                $clubManager = $this->clubManagersRepository->getQuery()->where('club.id', $product['club']['id'])->first();

                if (!$clubManager) {
                    throw new Exception('عفوا لا يوجد مدير لهذا النادي');
                }

                $model->clubManager = $clubManager->sharedInfo();

                $model->currency = Currency::getCurrency();
            }
        }
    }

    /**
     * create Order from admin
     *
     * @param mixed $model
     * @param \Illuminate\Http\Request $request
     * @return  void
     * @throws Exception
     */
    public function adminCreateOrder($model, $request)
    {
        $finalPrice = 0;

        $customer = $this->customersRepository->getModel($request->customer);

        $model->customer = $customer->sharedInfo();

        $seller = [];

        $totalQuantity = 0;

        $subTotal = 0;

        $rewardPoints = 0;

        foreach ($request->items as $item) {
            $product = $this->productsRepository->getModel($item['id']);

            // dd($product);
            if ($product['quantity'] < $item['quantity']) {
                // throw new Exception(trans('products.unavailableStockAdmin', ['name' => $product['name']]));
            }
            $seller = $product['seller'];

            $totalQuantity += $item['quantity'];

            $finalPrice = $this->cartRepository->getProductPrice($product);

            $price = round($finalPrice, 2);

            $totalPrice = round($finalPrice * $item['quantity'], 2);

            $productData = $product->sharedInfo();

            $orderItem = OrderItem::create([
                'orderId' => $model->id ?: $model->getNextId(),
                'product' => $productData,
                'rewardPoints' => 0,
                'price' => $price,
                'quantity' => $item['quantity'],
                'totalPrice' => $totalPrice,
                'options' => $item['options'] ?? [],
            ]);

            $subTotal += $totalPrice;

            $rewardPoints += 0;

            $products[] = $orderItem->sharedInfo();
        }


        $address = $this->addressBooksRepository->getValidAddress($request->address, $request->customer);

        if ($address) {
            $model->shippingAddress = $address->sharedInfo();
        } else {
            throw new Exception(trans('cart.cannotUseThisAddress'));
        }

        if (empty($address->city['id'])) {
            throw new Exception(trans('cart.cannotUseThisAddress'));
        }

        $shippingMethod = $request->shippingMethod;
        if ($shippingMethod == 'shippingCompany') {
            $shipping = $this->shippingCostsRepository->getByCity((int) $address->city['id']);


            if ($shipping && $shipping->cost) {
                $model->shippingFees = $shipping->cost;
                $model->expectedDeliveryIn = $shipping->expectedDeliveryIn;
                $model->shippingMethod = $shippingMethod;
                $finalPrice += $model->shippingFees;
            } else {
                throw new Exception(trans('cart.cannotUseThisShippingMethod'));
            }
        } elseif ($shippingMethod == 'handToHand') {
            $model->shippingFees = 0.0;
            $model->shippingMethod = $shippingMethod;
            $finalPrice += 0;
        } else {
            throw new Exception(trans('cart.cannotUseThisShippingMethod'));
        }

        $model->totalQuantity = $totalQuantity;

        $model->items = $products;

        $model->rewardPoints = $rewardPoints;

        $model->subTotal = $subTotal;

        // is is a passing object from the controller as the orders controller
        // is validating the coupon before getting into creating the order
        if ($request->couponCode) {
            $coupon = $this->couponsRepository->getValidCoupon($request->couponCode, $request);
            if ($coupon) {
                $model->coupon = $coupon->sharedInfo();
                $model->couponDiscount = $coupon->couponDiscount;
                $this->couponsRepository->increaseTotalUses($coupon);
            }
        }

        $model->wallet = 0;

        $taxes = $this->settingsRepository->getOrderTaxes();

        $originalPrice = round($model->subTotal / (1 + ($taxes / 100)), 2);

        $model->taxes = round($model->subTotal - $originalPrice, 2);

        $model->originalPrice = $originalPrice;

        $model->usedRewardPoints = 0;
        $model->rewordDiscount = 0;
        $model->isActiveRewardPoints = false;
        $model->finalPrice = $model->subTotal + $model->shippingFees - ($model->couponDiscount ?? 0) - $model->rewordDiscount;

        $model->finalPrice = round($model->finalPrice, 2);

        if ($customer->walletBalance) {
            if ($customer->walletBalance >= $model->finalPrice) {
                // dd($customer->walletBalance,$model->finalPrice);
                if ($request->paymentMethod == static::WALLET_PAYMENT_METHOD) {
                    $model->wallet = round($model->finalPrice, 2);
                    $model->finalPrice = $model->wallet;
                }
            } elseif ($customer->walletBalance < 0) {
                $model->wallet = round($customer->walletBalance, 2);
                $model->finalPrice -= $model->wallet;
            }
            $model->finalPrice = round($model->finalPrice, 2);
        }

        $model->currency = Currency::getCurrency();

        $model->seller = $this->storeManagersRepository->sharedInfo($seller['id']);
    }

    /**
     * Update order amount of returned orders from a wallet balance
     *
     * @param Wallet $wallet
     * @return void
     */
    public function updateReturnedAmount($wallet)
    {
        Model::where('id', $wallet->orderId)->update([
            'returnedAmount' => $wallet->amount,
        ]);
    }

    /**
     * Check if order is rated before
     *
     * @param Order $order
     * @return bool
     */
    public function isRatedBefore(Model $order): bool
    {
        return isset($order->rating);
    }

    /**
     * Get customer model for the given order
     *
     * @param Model $model
     * @return Customer
     */
    private function getCustomer(Model $model)
    {
        $user = user();

        if ($user && $user->accountType() === 'customer') {
            return $user;
        }

        return $this->customersRepository->getModel($model->customer['id']);
    }

    /**
     * {@inheritDoc}
     */
    public function onCreate($order, $request)
    {
        // dd('sdsd');
        if (strtoupper($order->paymentMethod) === strtoupper(static::BANK_TRANSFER_METHOD)) {
            $this->bankTransfersRepository->createFromOrder($order->id, $request);
        }

        if (in_array(strtoupper($order->paymentMethod), [static::VISA_PAYMENT_METHOD, static::MADA_PAYMENT_METHOD, static::MASTER_PAYMENT_METHOD, static::APPLE_PAY_PAYMENT_METHOD])) {
            $status = static::PAYING_STATUS;
            $noonPay = App::make(NoonPayments::class);
            // dd($order);
            $order->checkOutId = $noonPay->initiate($order->id, $order->finalPrice, $order->paymentMethod);
        } else {
            // if ($order->paymentMethod == static::WALLET_PAYMENT_METHOD) {
            $this->deductFromWallet($order);
            // }

            // $status = static::PENDING_STATUS;
            // if ($request->type == 'clubs') {
            //     $status = static::COMPLETED_STATUS;
            // } else {
            //     $status = static::PENDING_STATUS;
            // }

            $status = static::PENDING_STATUS;
            // dd($status);
            if (user()->AccountType() != 'user') {
                $customerCart = $this->getCustomer($order)->getCart();
                // $customerCart->flush(null, $order->productsType);
                if ($request->type == 'food') {
                    $customerCart->flushFood(null, $order->productsType);
                } else {
                    $customerCart->flushPrdouct(null, $order->productsType);
                }
            }
        }
        $this->changeStatus($order, $status, $request ?? '');
    }

    /**
     * If customer has enough wallet balance, then we will deduct
     * that amount from the order
     *
     * @param Model $order
     * @return void
     */
    private function deductFromWallet(Model $order)
    {
        if (!$order->wallet) {
            return;
        }

        $this->walletsRepository->withdraw([
            'customer' => $order->customer['id'],
            'amount' => $order->wallet,
            'orderId' => $order->id,
            'title' => trans('orders.paidOrderByWallet'),
            'reason' => trans('orders.newOrder'),
        ]);
    }

    /**
     * Reorder the given order id and put its items in the cart again
     * If selected Items has ids, then it will be used to only add these items
     * instead of the entire items
     *
     * @param int $orderId
     * @param array $selectedItems
     * @return bool
     */
    public function reorder($orderId, array $selectedItems = [])
    {
        // dd('sdsd');
        $order = $this->getCustomerOrder($orderId);
        // dd($selectedItems);
        if (!$order) {
            return false;
        }

        $requestData = $this->request->all();

        $items = [];
        foreach ($order->items as $item) {
            if ($selectedItems && !in_array($item['id'], $selectedItems)) {
                continue;
            }
            $options = [];

            foreach ($item['options'] as $option) {
                // dd($option);
                $options[] = [
                    'id' => $option['id'],
                    'values' => Arr::pluck($option['values'], 'id'),
                ];
            }
            $items[] = [
                'item' => $item['product']['id'],
                'quantity' => $item['quantity'],
                'options' => $options ?? [],
                'type' => $item['type'],
                'subscription' => $item['subscription'],
            ];
        }

        if (!$items) {
            return false;
        }

        $customer = user();

        $customer->getCart()->addMultiple($items, $customer->id);

        return true;
    }

    /**
     * Get order by checkout id and make sure it is in paying status as well
     *
     * @param string $checkoutId
     * @return Model
     * @throws Exception
     */
    public function getByCheckOutId($checkOutId)
    {
        // dd($checkOutId);
        $order = $this->getByModel('checkOutId.order.id', (int) $checkOutId);

        if (!$order) {
            throw new Exception('notFound');
        } elseif ($order->status !== static::PAYING_STATUS) {
            throw new Exception('invalidStatus');
        }

        return $order;
    }

    /**
     * Confirm Order
     *
     * @param Model $order
     * @param Request $request
     * @return Resource|array|\Illuminate\Http\Resources\Json\JsonResource|\JsonResource
     * @throws InvalidPaymentException
     * @throws Exception
     */
    public function confirmPayment(Model $order)
    {
        $noonPay = App::make(NoonPayments::class);
        // dd($order->id, $order->checkOutId['order']['id'], $order->paymentMethod);
        $paymentResponse = $noonPay->confirm($order->id, $order->checkOutId['order']['id'], $order->paymentMethod);
        // dd($paymentResponse->hasFailed());
        // dd($paymentResponse);

        if ($paymentResponse->hasFailed()) {
            $order->paymentInfo = [
                'code' => $paymentResponse->getStatusCode(),
                'status' => $paymentResponse->getStatus(),
                'message' => $paymentResponse->getMessage(),
            ];

            $order->save();

            throw new InvalidPaymentException($paymentResponse);
        }

        $order->paymentInfo = [
            'code' => $paymentResponse->getStatusCode(),
            'status' => $paymentStatus = $paymentResponse->getStatus(),
            'message' => $paymentResponse->getMessage(),
        ];
        // dd($paymentStatus, $paymentResponse->getMessage());
        if ($paymentStatus === PaymentGatewayResponse::COMPLETED) {
            // $user = user();
            // dd($order->wallet,$order->customer);
            if ($order->wallet < 0) {
                $this->deductFromWallet($order);
            }
            $customerCart = $this->getCustomer($order)->getCart();

            // dd($customerCart);

            if ($order->productsType == 'food') {
                $customerCart->flushFood(null, $order->productsType);
            } else {
                $customerCart->flushPrdouct(null, $order->productsType);
            }

            return $this->changeStatus($order, static::PENDING_STATUS);
        }

        return $this->wrap($order);
    }

    /**
     * check check out id if not exists
     *
     * @param Model $order
     * @param Request $request
     * @return bool
     */
    private function saveCheckOutId(Model $order, Request $request)
    {
        $order->checkOutId = $request->id;
        $order->save();

        return true;
    }

    /**
     * Change Order Status
     *
     * @param int|Model $order
     * @param string $status
     * @param  $request
     * @param string|null $cancelingReason
     * @param array $returnedItems
     * @return Resource|null
     */
    public function changeStatus($order, $status, $request = null, string $cancelingReason = null, array $returnedItems = [])
    {
        if ($status == OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS || $status == OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS || $status == OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS || $status == OrdersRepository::ALTERNATIVE_PRODUCT || $status == OrdersRepository::WALLET_PRODUCT) {
            $orderItem = $this->orderItemsRepository->getQuery()->where('id', $order)->first();
            $orderItem->nextStatus = static::NEXT_ORDER_STATUS_RETURNED_STATUS[$status] ?? [];
            $orderItem->status = $status;
            $orderItem->save();
            // dd($orderItem);
            if ($status == OrdersRepository::WALLET_PRODUCT) {
                if ($orderItem->type == 'products') {
                    // dd($orderItem, $status);
                    $this->productsRepository->updateSaleReturning($orderItem);
                    $this->returnToWalletReturn($orderItem);
                    $this->withdrawWalletSeller($orderItem);
                }
            }
            $orderStatus = new OrderStatus([
                'orderId' => $orderItem['orderId'],
                'orderItem' => $order,
                'status' => $status,
                'notes' => request()->notes ?? '',
                'cancelingReason' => $cancelingReason,
                'creator' => user() ? user()->accountType() : null,
            ]);
            // dd($orderStatus);
            $orderStatus->save();
            $order = $this->getModel($orderItem['orderId']);
            $order->associate($orderStatus->sharedInfo(), 'statusLog');
            $customer = $this->getCustomer($order);
            Mail::send([], [], function ($message) use ($customer, $order, $status) {
                $message->to($customer['email'])
                    ->subject('طلب ارجاع')
                    // here comes what you want
                    ->setBody("
                    <p>
                        مرحبا عزيزي {$customer['firstName']}
                    </p>
                    <br>
                    <br>
                    <br>
                    <hr>
                    <p>
                    تم تغير طلب الارجاع الخاص بك [{$order->id}] الي [{$status}]
                    </p>
                ", 'text/html'); // assuming text/plain
            });
        }
        // dd($order->productsType);
        // dd($order, $status);
        if (is_numeric($order)) {
            $order = $this->getModel($order);
        }

        if (!$order) {
            return null;
        }



        // $order->status = $status;
        // dd($request->all());

        if ($status !== static::PAYING_STATUS) {
            if ($status == OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS || $status == OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS || $status == OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS || $status == OrdersRepository::ALTERNATIVE_PRODUCT || $status == OrdersRepository::WALLET_PRODUCT || $status == OrdersRepository::REQUEST_RETURNING_STATUS) {
                // dd('sdsd');
            } else {
                $orderStatus = new OrderStatus([
                    'orderId' => $order->id,
                    'status' => $status,
                    'notes' => request()->notes ?? '',
                    'cancelingReason' => $cancelingReason,
                    'creator' => user() ? user()->accountType() : null,
                ]);



                // if ($cancelingReason) {
                // dd($order->paymentMethod);
                if (in_array($status, [static::ADMIN_CANCELED_STATUS, static::CANCELED_STATUS])) {
                    if (request()->url() == url('api/orders/' . request()->route('orderId') . '/canceled')) {
                        if ($order->productsType == 'food' && $order->deliveryType == 'inHome' && $order->restaurantManager['restaurant']['delivery'] == false) {
                            // cancel Order Delivery By customer
                            $this->cancelOrderDeliveryBycustomer($order, $status);
                        }
                    } else {
                        if ($order->productsType == 'food' && $order->deliveryType == 'inHome' && $order->restaurantManager['restaurant']['delivery'] == false) {
                            // cancel Order Delivery By customer
                            $this->cancelOrderDeliveryByAdminAndSendNotifications($order, $status);
                        }
                    }

                    $order->cancelingReason = $cancelingReason;
                    // dd($cancelingReason);
                    if ($cancelingReason) {
                        $this->cancelingReasonsRepository->onReasonCancel($cancelingReason);
                    }
                    $options = [];
                    $customer = $this->usersRepository->list($options);
                    // dd($customer[0]);
                    $adminEmail = $this->usersRepository->getByModel('name', 'admin');
                    // dd($adminEmail);
                    if ($order->productsType == 'products') {
                        Mail::send([], [], function ($message) use ($customer, $order, $adminEmail) {
                            $url = 'https://dashboard.diet.market/orders-stores/' . $order->id;
                            $message->to($adminEmail['email'])
                                ->subject('إلغاء الطلب')
                                // here comes what you want
                                ->setBody("
                                    <p>
                                        مرحبا بك {$adminEmail['email']}
                                    </p>
                                    <p>
                                    تم إلغاء طلب رقم

                                    <a href='[{$url}]'>[{$order->id}][{$url}]</a>
                                    </p>
                                ", 'text/html'); // assuming text/plain
                        });


                        $orderSellers = $order['seller'];
                        foreach ($orderSellers as $key => $orderSeller) {
                            Mail::send([], [], function ($message) use ($customer, $order, $orderSeller) {
                                $url = 'https://dashboard.diet.market/store/orders/' . $order->id;
                                $message->to($orderSeller['email'])
                                    ->subject('إلغاء الطلب')
                                    // here comes what you want
                                    ->setBody("
                                        <p>
                                            مرحبا بك {$orderSeller['firstName']}
                                        </p>
                                        <p>
                                        تم إلغاء طلب رقم

                                        <a href='[{$url}]'>[{$order->id}][{$url}]</a>
                                        </p>
                                    ", 'text/html'); // assuming text/plain
                            });
                        }
                    }
                    // dd($order->paymentMethod);

                    // if (in_array(strtoupper($order->paymentMethod), [static::VISA_PAYMENT_METHOD, static::MADA_PAYMENT_METHOD, static::MASTER_PAYMENT_METHOD, static::APPLE_PAY_PAYMENT_METHOD, static::WALLET_PAYMENT_METHOD])) {
                    //     dd('omar');
                    //     $this->returnToWallet($order);
                    // }
                    if ($order->paymentMethod == static::VISA_PAYMENT_METHOD || $order->paymentMethod == static::MADA_PAYMENT_METHOD || $order->paymentMethod == static::MASTER_PAYMENT_METHOD || $order->paymentMethod == static::APPLE_PAY_PAYMENT_METHOD || $order->paymentMethod == static::WALLET_PAYMENT_METHOD) {
                        if ($order->productsType == 'products' || $order->productsType == 'food') {
                            $this->returnToWallet($order);
                        }
                    }
                }

                if (in_array($status, [static::REQUEST_PARTIAL_RETURN_STATUS, static::REQUEST_RETURNING_STATUS])) {
                    $order->returningReason = $cancelingReason;
                    $this->returningReasonsRepository->incrementUsage($cancelingReason);
                }
                // }

                $orderStatus->save();
                $order->associate($orderStatus->sharedInfo(), 'statusLog');
                $customer = $this->getCustomer($order);

                if ($status === static::PENDING_STATUS) {
                    // dd($customer);
                    $this->customersRepository->updateTotalOrders($customer->id);

                    if ($order->productsType == 'nutritionSpecialist') {
                        $customer = $this->getCustomer($order);
                        // dd($customer);
                        $this->customersRepository->createNutritionSpecialist($order->items[0], $customer);
                    }
                    if ($order->productsType == 'clubs') {
                        $clubManager = $this->clubManagersRepository->get((int) $order['clubManager']['id']);
                        $admin = $this->usersRepository->getByModel('name', 'admin');
                        Mail::send([], [], function ($message) use ($order, $clubManager, $admin) {
                            $url = 'https://dashboard.diet.market/subscription-requests/' . $order->id;
                            $message->to($clubManager['email'], $admin['email'])
                                ->subject('تم اشتراك في النادي ')
                                // here comes what you want
                                ->setBody("
                            <p>
                                مرحبا بك {$clubManager['name']}
                            </p>
                            <p>
                            يوجد اشتراك جديد [{$order->id}]
                            </br>
                            </br>
                            <hr>
                            </br>
                            بواسطة [{$order->customer['firstName']}]
                            </br>
                            </br>
                            <hr>
                            </br>
                            طريقه الدفع  [{$order->paymentMethod}]
                            </br>
                            </br>
                            <hr>
                            </br>
                            راجع الاشتراك
                            <a href='{$url}'>
                            [{$url}]</a>
                            </p>
                            ", 'text/html'); // assuming text/plain
                        });
                    }

                    if ($order->productsType == 'food' && $order->deliveryType == 'inHome' && $order->restaurantManager['restaurant']['delivery'] == false) {
                        // create order for delivery men
                        $this->createOrderAndCreateRequest($order, $status);
                    }

                    if ($order->productsType == 'nutritionSpecialist') {
                        $nutritionSpecialistManager = $this->nutritionSpecialistMangersRepository->get($order['nutritionSpecialistManager']['id']);
                        $admin = $this->usersRepository->getByModel('name', 'admin');
                        Mail::send([], [], function ($message) use ($order, $nutritionSpecialistManager, $admin) {
                            $url = 'https://dashboard.diet.market/reservations/' . $order->id;
                            $message->to($nutritionSpecialistManager['email'], $admin['email'])
                                ->subject('تم حجز موعد')
                                ->setBody("
                            <p>
                                مرحبا بك {$nutritionSpecialistManager['name'][0]['text']}
                            </p>
                            <p>
                            يوجد حجز جديد [{$order->id}]
                            </br>
                            </br>
                            <hr>
                            </br>
                            طريقه الدفع  [{$order->paymentMethod}]
                            </br>
                            </br>
                            <hr>

                            </br>
                            <a href='{$url}'>
                            [{$url}]</a>
                            </p>
                            ", 'text/html'); // assuming text/plain
                        });
                    }
                } else {
                }

                if ($order->productsType == 'food' && $order->deliveryType == 'inHome' && $order->restaurantManager['restaurant']['delivery'] == false) {
                    if ($status === static::PROCESSING_STATUS) {
                        // send order for delivery men
                        $designationDeliveryMenAutomatically = $this->settingsRepository->getSetting('deliveryMen', 'designationDeliveryMenAutomatically');
                        if ($designationDeliveryMenAutomatically == true) {
                            $this->sendOrderForDelivery($order, $status);
                        }
                    }
                }

                if ($order->productsType == 'products') {
                    $notification = $this->notificationsRepository->create([
                        'title' => trans('notifications.order.title.' . $status),
                        'content' => trans('notifications.order.content.' . $status),
                        'type' => 'productOrder',
                        'user' => $customer,
                        'pushNotification' => true,
                        'extra' => [
                            'type' => 'productOrder',
                            'status' => $status,
                            'orderId' => $order->id,
                        ],
                    ]);
                    $this->notificationsRepository->getQuery()->where('id', $notification->id)->delete();
                } elseif ($order->productsType == 'food') {
                    $notification = $this->notificationsRepository->create([
                        'title' => trans('notifications.order.titleFood.' . $status),
                        'content' => trans('notifications.order.contentFood.' . $status),
                        'type' => 'foodOrder',
                        'user' => $customer,
                        'pushNotification' => true,
                        'extra' => [
                            'type' => 'foodOrder',
                            'status' => $status,
                            'orderId' => $order->id,
                        ],
                    ]);
                    $this->notificationsRepository->getQuery()->where('id', $notification->id)->delete();
                } elseif ($order->productsType == 'clubs') {
                    $notification = $this->notificationsRepository->create([
                        'title' => trans('notifications.order.titleClub.' . $status),
                        'content' => trans('notifications.order.titleClub.' . $status),
                        'type' => 'clubOrder',
                        'user' => $customer,
                        'pushNotification' => true,
                        'extra' => [
                            'type' => 'clubOrder',
                            'status' => $status,
                            'orderId' => $order->id,
                        ],
                    ]);
                    $this->notificationsRepository->getQuery()->where('id', $notification->id)->delete();
                } elseif ($order->productsType == 'nutritionSpecialist') {
                    $notification = $this->notificationsRepository->create([
                        'title' => trans('notifications.order.titleNutritionSpecialist.' . $status),
                        'content' => trans('notifications.order.titleNutritionSpecialist.' . $status),
                        'type' => 'nutritionSpecialistOrder',
                        'user' => $customer,
                        'pushNotification' => true,
                        'extra' => [
                            'type' => 'nutritionSpecialistOrder',
                            'status' => $status,
                            'orderId' => $order->id,
                        ],
                    ]);
                    $this->notificationsRepository->getQuery()->where('id', $notification->id)->delete();
                }
            }
        }
        // if ($status === static::REQUEST_PARTIAL_RETURN_STATUS) {
        //     $order->partialReturn = true;
        // }

        if (in_array($status, [OrdersRepository::REQUEST_RETURNING_STATUS, OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS])) {
            // dd(count($order['items']) - 1);
            if (count($order['items']) > 1) {
                $order->partialReturn = true;
            } else {
                $order->requestReturning = true;
            }
        }

        if ($returnedItems) {
            $orderItems = OrderItem::whereIn('id', array_map('intval', $returnedItems))->get();
            $ReturningReason = $this->returningReasonsRepository->sharedInfo((int) $request->returningReason);
            $PackagingStatus = $this->packagingStatusRepository->sharedInfo((int) $request->packagingStatus);
            // dd($ReturningReason);
            foreach ($orderItems as $orderItem) {
                $orderItem->requestReturning = true;
                $orderItem->ReturningReason = $ReturningReason;
                $orderItem->PackagingStatus = $PackagingStatus;
                $orderItem->isCountSeen = true;
                // dd($status);
                if ($order->productsType == 'products') {
                    // if ($status == 'requestReturning') {
                    //     // $order->status = $status;
                    // }
                    $orderItem->nextStatus = static::NEXT_ORDER_STATUS_RETURNED_STATUS[$status] ?? [];
                    $orderItem->status = $status;
                }

                $orderItem->save();
                $orderStatus = new OrderStatus([
                    'orderId' => $orderItem['orderId'],
                    'orderItem' => $orderItem['id'],
                    'status' => $status,
                    'notes' => request()->notes ?? '',
                    'cancelingReason' => $cancelingReason,
                    'creator' => user() ? user()->accountType() : null,
                ]);
                $orderStatus->save();
                $order = $this->getModel($orderItem['orderId']);
                $order->associate($orderStatus->sharedInfo(), 'statusLog');
                $order->reassociate($orderItem, 'items');

                // $order->associate($orderItem->sharedInfo(), 'items');

                $customer = $this->getCustomer($order);
                if ($order->productsType == 'products') {
                    $admin = $this->usersRepository->getByModel('name', 'admin');
                    $store = $this->storesRepository->get((int) $orderItem['product']['storeManager']['store']['id']);
                    $customer = user()->firstName;
                    Mail::send([], [], function ($message) use ($admin, $store, $customer, $order, $orderItem) {
                        $url = 'https://dashboard.diet.market/orders-stores/returns/' . $order->id;
                        $message->to($admin['email'])
                            ->subject('يوجد حالة ارجاع ')
                            ->setBody("
                                        <p>
                                            مرحبا بك {$admin['name']}
                                        </p>
                                        <p>
                                        اسم المتجر {$store['name'][0]['text']}
                                        </br>
                                        </br>
                                        <hr>
                                        </br>
                                        بواسطة  [{$customer}]
                                        </br>
                                        </br>
                                        <hr>
                                            لديك طلب ارجاع جديد رقم
                                            [{$order->id}]
                                            ارجاع رقم
                                            </br>
                                            </br>
                                            <hr>
                                            [{$orderItem->id}]
                                            حاله الارجاع
                                            قيد الإنتظار
                                            </br>
                                            </br>
                                            <hr>
                                        </br>
                                        <a href='{$url}'>
                                        [{$url}]</a>
                                        </p>
                             ", 'text/html'); // assuming text/plain
                    });
                    $customer = user();
                    Mail::send([], [], function ($message) use ($customer, $store, $order, $orderItem) {
                        $message->to($customer->email)
                            ->subject('طالب ارجاع')
                            ->setBody("
                                        <p>
                                            مرحبا عزيزي {$customer->firstName}
                                        </p>
                                        <p>
                                    [قيد الإنتظار ] الي[{$orderItem->id}]   تم تغير طلب الارجاع الخاص بك
                                        </br>
                                        <p>
                                        اسم المتجر {$store['name'][0]['text']}
                                        </br>
                                        </br>
                                        </br>
                                            <hr>
                                        </br>
                             ", 'text/html'); // assuming text/plain
                    });
                }
            }
        }


        if ($status === static::COMPLETED_STATUS) {
            $this->transactionsRepository->add($order);

            $this->saveMoneyInTheServiceProvidersWallet($order);

            /*
            if ($order->usedRewardPoints) {
                $this->rewardsRepository->withdraw([
                    'points' => $order->usedRewardPoints,
                    'customer' => $customer->id,
                    'orderId' => $order->id,
                    // please update the translation
                    'notes' => 'خصم من حسابك بعض الرصيد مقابل المنتجات',
                    'title' => "خصم {$order->usedRewardPoints} نقطة",
                    'reason' => "خصم {$order->usedRewardPoints} نقطة",
                ]);
            }
            `
            */


            $maxRedemedPoints = $this->settingsRepository->getSetting('reward', 'maxRedemedPoints') ?? 1000;
            $rewardStatus = $this->settingsRepository->getSetting('reward', 'rewardStatus') ?? true;

            if ($rewardStatus) {
                if ($customer->rewardPoint < $maxRedemedPoints) {
                    $final = round($order->finalPrice);

                    $title = [];

                    if ($order->productsType == 'clubs') {
                        $title[0]['text'] = "Club Booking Number  {$order->id}";
                        $title[0]['localeCode'] = "en";
                        $title[1]['text'] = "اشتراك نادى رقم {$order->id}";
                        $title[1]['localeCode'] = "ar";
                    } elseif ($order->productsType == 'nutritionSpecialist') {
                        $title[0]['text'] = "Consulting Booking Number  {$order->id}";
                        $title[0]['localeCode'] = "en";
                        $title[1]['text'] = "حجز استشارة  رقم {$order->id}";
                        $title[1]['localeCode'] = "ar";
                    } else {
                        $title[0]['text'] = "Order Number  {$order->id}";
                        $title[0]['localeCode'] = "en";
                        $title[1]['text'] = "‌طلب‌ ‌رقم‌ {$order->id}";
                        $title[1]['localeCode'] = "ar";
                    }

                    $this->customersRepository->updateRewardBalance($customer->id);

                    $customer = $this->getCustomer($order);

                    if (($customer->rewardPoint + $final) > $maxRedemedPoints) {
                        $final = $maxRedemedPoints - $customer->rewardPoint;
                    }

                    $this->rewardsRepository->deposit([
                        'status' => 'active',
                        'points' => $final,
                        'customer' => $customer->id,
                        'orderId' => $order->id,
                        'title' => $title,
                    ]);
                }
            }


            // dd($order->items);
            // $this->productsRepository->updateSales($order->items);


            // if ($request->type == 'food' || $request->type == 'products') {
            //     $this->productsRepository->updateSales($order->items, $status);
            // }
            if ($order->productsType == 'clubs') {
                $customer = $this->getCustomer($order);
                $this->customersRepository->createSubscribe($order->items[0], $customer);
            }
        }
        if (in_array($status, [
            OrdersRepository::PENDING_STATUS,
        ])) {
            if ($order->productsType == 'products') {
                $this->productsRepository->updateSales($order->items, $status);
            }
        }
        // dd('sdsd');
        if ($order->productsType == 'clubs') {
            if ($status === static::UNSUBSCRIBE_STATUS) {
                $this->customersRepository->deleteSubscribe($order->items[0], $customer);
                $this->returnToWallet($order);
            }
        }

        if ($order->productsType == 'clubs') {
            $order->status = $status;
            $order->nextStatus = static::NEXT_ORDER_STATUS_CLUBS[$order->status] ?? [];
        } elseif ($order->productsType == 'nutritionSpecialist') {
            $order->status = $status;
            $order->nextStatus = static::NEXT_ORDER_STATUS_NUTRITION[$order->status] ?? [];
        } elseif ($order->productsType == 'food') {
            if ($order['restaurantManager']['restaurant']['delivery'] == false && $order->deliveryType == 'inHome') {
                $order->status = $status;
                $order->nextStatus = static::NEXT_ORDER_STATUS_DELIVERY_BY_DITE_MARKIT_FOOD[$order->status] ?? [];
            } else {
                $order->status = $status;
                $order->nextStatus = static::NEXT_ORDER_STATUS_FOOD[$order->status] ?? [];
            }
            $order->isGetLastOrderIsNotReview = false;
        } else {
            // dd($order->status);
            if ($order->status == 'completed') {
                $order->nextStatus = [];
                $order->status = 'completed';
            } else {
                $order->status = $status;
                $order->nextStatus = static::NEXT_ORDER_STATUS[$order->status] ?? [];
            }
        }
        // if ($order->productsType == 'products') {
        //     // return amount to wallet
        //     if ($status === static::WALLET_PRODUCT) {
        //         $this->returnToWallet($order);
        //     }
        // } else {
        //     // return amount to wallet
        //     if ($status === static::WALLET_PRODUCT) {
        //         $this->returnToWallet($order);
        //     }
        // }



        $order->save();

        return $this->wrap($order);
    }

    /**
     * return order amount as wallet balance
     *
     * @param Model $order
     * @return void
     */
    private function returnToWallet(Model $order)
    {
        // dd($order);
        $amount = collect($order['items'])->whereNotNull('requestReturning')->sum('totalPrice');

        $shipppingFees = $order['shippingFees'] ?? 0;

        $amount = $amount == 0 ? $order->finalPrice : $amount;
        if ($order->productsType == 'products') {
            $title = trans('notifications.wallet.orderReturnedAmount', ['value' => $amount]) . ' ' . $order->id;
        } elseif ($order->productsType == 'food') {
            $title = trans('notifications.wallet.orderReturnedAmount', ['value' => $amount]) . ' ' . $order->id  . 'مطاعم ';
        } elseif ($order->productsType == 'nutritionSpecialist') {
            $title = trans('notifications.wallet.orderReturnedAmount', ['value' => $amount]) . ' ' . $order->id . ' أخصائي ';
        } elseif ($order->productsType == 'clubs') {
            $title = trans('notifications.wallet.orderReturnedAmount', ['value' => $amount]) . ' ' . $order->id  . 'نوادي ';
        }
        $this->walletsRepository->deposit([
            'customer' => $order->customer['id'],
            'amount' => $amount,
            'orderId' => $order->id,
            'pushNotification' => true,
            'title' => $title,
            'reason' => $title,
            'amount' => $amount,
        ]);
    }

    /**
     * Method returnToWalletReturn
     *
     * @param Model $order
     *
     * @return void
     */
    private function returnToWalletReturn(OrderItem $orderItem)
    {
        $amount = $orderItem['totalPrice'];

        $shipppingFees = $order['shippingFees'] ?? 0;

        // $amount = $amount == 0 ? $orderItem->finalPrice : $amount;
        $title = trans('notifications.wallet.orderReturnedAmount', ['value' => $amount]) . ' ' . $orderItem->id;
        $this->walletsRepository->deposit([
            'customer' => $orderItem['customerId'],
            'amount' => $amount,
            'orderId' => $orderItem->id,
            'pushNotification' => true,
            'title' => $title,
            'reason' => $title,
            'amount' => $amount,
        ]);
    }

    /**
     * Method sendOrderForDelivery
     *
     * @param $order $order
     *
     * @return void
     */
    public function sendOrderForDelivery($order, $status)
    {
        $locationRestaurant = $order['restaurantManager']['restaurant']['location'];

        //The delivery is available by the admin, and his status is connected, and he does not have more than 2 orders
        $deliveryMens = $this->deliveryMenRepository->getQuery()->where('approved', DeliveryMensRepository::APPROVED_STATUS)->where('status', true)->where('published', true)->where('requested', '<', 2)->get();

        //this location restaurant
        $restaurantLocation = [
            $locationRestaurant['coordinates'][0],
            $locationRestaurant['coordinates'][1],
        ];

        $isAvaliable = false;

        $deliveries = [];

        foreach ($deliveryMens as $key => $deliveryMen) {
            //In the delivery pocket, I accept the order, or we go to the restaurant to receive the order
            $orderDelivery = $this->orderDeliveryRepository->getQuery()->where('deliveryMenId', (int) $deliveryMen->id)->where('status', OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS)->first();
            //The order from the restaurant is not the same restaurant as the new order
            if ($orderDelivery && $orderDelivery['restaurantId'] != $order['restaurantManager']['restaurant']['id']) {
                continue;
            }

            //this location Delivery Men
            $locationDeliveryMen = [
                $deliveryMen['location']['coordinates'][0],
                $deliveryMen['location']['coordinates'][1],
            ];

            //key Google Map
            $key = KEY_GOOGLE_MAB;
            $URL = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$locationDeliveryMen[0]},{$locationDeliveryMen[1]}&destinations={$restaurantLocation[0]},{$restaurantLocation[1]}&key={$key}";
            //Method getCurlExec
            $getkm = $this->getCurlExec($URL);
            $getkm = intval(preg_replace('/[^\d.]/', '', $getkm));
            // dd($getkm);
            $checkAruaKm = $this->settingsRepository->getSetting('deliveryMen', 'checkAruaKm');

            // if ($checkAruaKm == true) {
            //     $deliveryMenRangeInKm = $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenRangeInKm');
            // } else {
            //     $deliveryMenRangeInKm = 60000;
            // }
            $deliveryMenRangeInKm = $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenRangeInKm');


            if ((float) $getkm > $deliveryMenRangeInKm) { // 30Km For Admin Setting
                continue;
            } else {
                $isAvaliable = true;
                $deliveries[$key] = $deliveryMen;
                $deliveries[$key]['distance'] = (float) $getkm;
            }
        }

        if ($isAvaliable) {
            array_multisort(
                array_column($deliveries, 'distance'),
                SORT_ASC,
                $deliveries
            );
            // Assien Delivery And craete notification
            $this->createOrderDelivery($order, current($deliveries), $status);
        }
        if ($deliveryMens->count() == 0 || $isAvaliable == false) {
            //  create Order Is Not Delivery
            $this->createOrderIsNotDelivery($order, $status);
        }
    }

    /**
     * Method createOrderDelivery
     *
     * @param $order $order
     * @param $deliveryMen $deliveryMen
     * @param $status $status
     *
     * @return void
     */
    public function createOrderDelivery($order, $deliveryMen, $status)
    {
        $createOrderDelivery = $this->orderDeliveryRepository->getByModel('orderId', $order->id);
        $restaurant = $this->restaurantsRepository->get((int) $order['restaurantManager']['restaurant']['id']);

        $distanceToTheRestaurant = $this->orderDeliveryRepository->distanceToTheRestaurant($restaurant, $deliveryMen['id']);
        $distanceToTheCustomer = $this->orderDeliveryRepository->distanceToTheCustomer($order->shippingAddress, $restaurant);
        $totalDistance = $this->orderDeliveryRepository->totalDistance($distanceToTheRestaurant[0], $distanceToTheCustomer[0]);
        $totalMinute = $this->orderDeliveryRepository->totalMinute($distanceToTheRestaurant[1], $distanceToTheCustomer[1]);
        $createOrderDelivery->update([
            'orderId' => $order->id,
            'deliveryMenId' => $deliveryMen['id'],
            'restaurantId' => $order['restaurantManager']['restaurant']['id'],
            'customerId' => $order['customer']['id'],
            'addressOrderCustomer' => $order->shippingAddress,
            'finalPrice' => $order->finalPrice,
            'deliveryCost' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenCost'), // setting dashboard
            'deliveryCommission' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'), // setting dashboard
            'paymentMethod' => $order->paymentMethod, // setting dashboard
            'status' => OrderDeliveryRepository::DELIVERY_PENDING_STATUS,
            'timer' => OrderDeliveryRepository::SECONDS_STATUS,
            'timerDate' => Carbon::now()->format('Y-m-d H:i:s'),
            'nextStatus' => OrderDeliveryRepository::NEXT_ORDER_STATUS[OrderDeliveryRepository::DELIVERY_PENDING_STATUS] ?? [],
            'distanceToTheRestaurant' => $distanceToTheRestaurant[0],
            'minuteToTheRestaurant' => $distanceToTheRestaurant[1] . 'د',
            'distanceToTheCustomer' => $distanceToTheCustomer[0],
            'minuteToTheCustomer' => $distanceToTheCustomer[1] . 'د',
            'totalDistance' => $totalDistance,
            'totalDistanceInt' => (float) $totalDistance,
            'totalMinute' => $totalMinute,
        ]);

        $deliveryMen = $this->deliveryMenRepository->get((int) $deliveryMen['id']);
        $deliveryMen['requested'] = $deliveryMen->requested + 1;
        $deliveryMen->save();
        if (user()->accountType() == 'user') {
            $user = user()->name;
        } elseif (user()->accountType() == 'deliveryMen') {
            $user = user()->firstName . ' ' . user()->lastName;
        } elseif (user()->accountType() == 'customer') {
            $user = user()->firstName . ' ' . user()->lastName;
        }
        $orderDeliveryStatus = new OrderStatusDelivery([
            'orderId' => $createOrderDelivery['orderId'],
            'orderDeliveryId' => $createOrderDelivery->id,
            'status' => OrderDeliveryRepository::DELIVERY_PENDING_STATUS,
            'creator' => user() ? user()->accountType() : null,
            'creatorBy' => user() ? $user : null,

        ]);

        $orderDeliveryStatus->save();

        $this->orderDeliveryRepository->saveStatusLog($createOrderDelivery->id, $orderDeliveryStatus);

        //craete notification For delivery
        $deliveryMenModel = $this->deliveryMenRepository->getModel($deliveryMen->id);
        $this->notificationsRepository->create([
            'title' => "تم اسناد طلب جديد [#{$createOrderDelivery->id}] قم بقبول طلب",
            'content' => "تم اسناد طلب جديد [#{$createOrderDelivery->id}] قم بقبول طلب",
            'type' => 'order',
            'user' => $deliveryMenModel,
            'pushNotification' => true,
            'extra' => [
                'type' => 'order',
                'orderId' => $createOrderDelivery->id,
                'notificationCount' => $deliveryMenModel->totalNotifications + 1,
                'status' => OrderDeliveryRepository::DELIVERY_PENDING_STATUS,
            ],
        ]);
        $order->update([
            'deliveryName' => $deliveryMenModel->firstName . ' ' . $deliveryMenModel->lastName,
        ]);
        // $changeStatusOrder = $this->ordersRepository->getQuery()->where('id', (int)$order->orderId)->first();
        // // dd($deliveryMenModel->firstName);
        // $changeStatusOrder->deliveryName = $deliveryMenModel->firstName . ' ' . $deliveryMenModel->lastName;
        // $changeStatusOrder->save();
    }

    /**
     * Method createOrderIsNotDelivery
     *
     * @param $order $order
     * @param $status $status
     *
     * @return void
     */
    public function createOrderIsNotDelivery($order, $status)
    {
        $createOrderDelivery = $this->orderDeliveryRepository->getByModel('orderId', $order->id);
        $createOrderDelivery->update([
            'orderId' => $order->id,
            'deliveryMenId' => null,
            'restaurantId' => $order['restaurantManager']['restaurant']['id'],
            'customerId' => $order['customer']['id'],
            'addressOrderCustomer' => $order->shippingAddress,
            'finalPrice' => $order->finalPrice,
            'deliveryCost' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenCost'), // setting dashboard
            'deliveryCommission' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'), // setting dashboard
            'paymentMethod' => $order->paymentMethod, // setting dashboard
            'status' => OrderDeliveryRepository::DELIVERY_IS_NOT_SET_STATUS,
            'nextStatus' => OrderDeliveryRepository::NEXT_ORDER_STATUS[OrderDeliveryRepository::DELIVERY_IS_NOT_SET_STATUS] ?? [],
        ]);
        if (user()->accountType() == 'user' || user()->accountType() == 'RestaurantManager') {
            $user = user()->name;
        } elseif (user()->accountType() == 'deliveryMen') {
            $user = user()->firstName . ' ' . user()->lastName;
        } elseif (user()->accountType() == 'customer') {
            $user = user()->firstName . ' ' . user()->lastName;
        }
        $orderDeliveryStatus = new OrderStatusDelivery([
            'orderId' => $createOrderDelivery['orderId'],
            'orderDeliveryId' => $createOrderDelivery->id,
            'status' => OrderDeliveryRepository::DELIVERY_IS_NOT_SET_STATUS,
            // 'message' => $message,
            'creator' => user() ? user()->accountType() : null,
            'creatorBy' => user() ? $user : null,

        ]);

        $orderDeliveryStatus->save();
        $this->orderDeliveryRepository->saveStatusLog($createOrderDelivery->id, $orderDeliveryStatus);
    }

    /**
     * Method cancelOrderDeliveryBycustomer
     *
     * @param $order $order
     * @param $status $status
     *
     * @return void
     */
    public function cancelOrderDeliveryBycustomer($order, $status)
    {
        $createOrderDelivery = $this->orderDeliveryRepository->getByModel('orderId', $order->id);

        $createOrderDelivery->update([
            'orderId' => $order->id,
            'deliveryMenId' => null,
            'restaurantId' => $order['restaurantManager']['restaurant']['id'],
            'customerId' => $order['customer']['id'],
            'addressOrderCustomer' => $order->shippingAddress,
            'finalPrice' => $order->finalPrice,
            'deliveryCost' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenCost'), // setting dashboard
            'deliveryCommission' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'), // setting dashboard
            'paymentMethod' => $order->paymentMethod, // setting dashboard
            'status' => OrderDeliveryRepository::ORDER_CANCELBYCUSTOMER_STATUS,
            'nextStatus' => OrderDeliveryRepository::NEXT_ORDER_STATUS[OrderDeliveryRepository::ORDER_CANCELBYCUSTOMER_STATUS] ?? [],
        ]);
        if (user()->accountType() == 'user') {
            $user = user()->name;
        } elseif (user()->accountType() == 'deliveryMen') {
            $user = user()->firstName . ' ' . user()->lastName;
        } elseif (user()->accountType() == 'customer') {
            $user = user()->firstName . ' ' . user()->lastName;
        }
        $orderDeliveryStatus = new OrderStatusDelivery([
            'orderId' => $createOrderDelivery['orderId'],
            'orderDeliveryId' => $createOrderDelivery->id,
            'status' => OrderDeliveryRepository::ORDER_CANCELBYCUSTOMER_STATUS,
            'creator' => user() ? user()->accountType() : null,
            'creatorBy' => user() ? $user : null,

        ]);

        $orderDeliveryStatus->save();

        $this->orderDeliveryRepository->saveStatusLog($createOrderDelivery->id, $orderDeliveryStatus);
    }

    /**
     * Method cancelOrderDeliveryByAdminAndSendNotifications
     *
     * @param $order $order
     * @param $status $status
     *
     * @return void
     */
    public function cancelOrderDeliveryByAdminAndSendNotifications($order, $status)
    {
        $createOrderDelivery = $this->orderDeliveryRepository->getByModel('orderId', $order->id);

        if ($createOrderDelivery->deliveryMenId) {
            $deliveryMenModel = $this->deliveryMenRepository->getModel($createOrderDelivery->deliveryMenId);
            $this->notificationsRepository->create([
                'title' => "تم إلغاء الطلب [#{$createOrderDelivery->id}]",
                'content' => "تم إلغاء الطلب [#{$createOrderDelivery->id}]",
                'type' => 'cancelOrder',
                'user' => $deliveryMenModel,
                'pushNotification' => true,
                'extra' => [
                    'type' => 'cancelOrder',
                    'orderId' => $createOrderDelivery->id,
                    'notificationCount' => $deliveryMenModel->totalNotifications + 1,
                    'status' => OrderDeliveryRepository::ORDER_CANCELBYCUSTOMER_STATUS,
                ],
            ]);
        }

        $deliveryMen = $this->deliveryMenRepository->get((int) $createOrderDelivery->deliveryMenId);
        if ($deliveryMen) {
            $deliveryMen['requested'] = $deliveryMen->requested - 1;
            $deliveryMen->save();
        }

        $createOrderDelivery->update([
            'orderId' => $order->id,
            'deliveryMenId' => $createOrderDelivery->deliveryMenId,
            'restaurantId' => $order['restaurantManager']['restaurant']['id'],
            'customerId' => $order['customer']['id'],
            'addressOrderCustomer' => $order->shippingAddress,
            'finalPrice' => $order->finalPrice,
            'deliveryCost' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenCost'), // setting dashboard
            'deliveryCommission' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'), // setting dashboard
            'paymentMethod' => $order->paymentMethod, // setting dashboard
            'status' => OrderDeliveryRepository::ORDER_CANCELBYCUSTOMER_STATUS,
            'nextStatus' => OrderDeliveryRepository::NEXT_ORDER_STATUS[OrderDeliveryRepository::ORDER_CANCELBYCUSTOMER_STATUS] ?? [],
        ]);
        if (user()->accountType() == 'user') {
            $user = user()->name;
        } elseif (user()->accountType() == 'deliveryMen') {
            $user = user()->firstName . ' ' . user()->lastName;
        } elseif (user()->accountType() == 'customer') {
            $user = user()->firstName . ' ' . user()->lastName;
        }
        $orderDeliveryStatus = new OrderStatusDelivery([
            'orderId' => $createOrderDelivery['orderId'],
            'orderDeliveryId' => $createOrderDelivery->id,
            'status' => OrderDeliveryRepository::ORDER_CANCELBYCUSTOMER_STATUS,
            'creator' => user() ? user()->accountType() : null,
            'creatorBy' => user() ? $user : null,

        ]);

        $orderDeliveryStatus->save();

        $this->orderDeliveryRepository->saveStatusLog($createOrderDelivery->id, $orderDeliveryStatus);
    }

    /**
     * Method createOrderAndCreateRequest
     *
     * @param $order $order
     * @param $status $status
     *
     * @return void
     */
    public function createOrderAndCreateRequest($order, $status)
    {
        $createNewOrderDelivery = $this->orderDeliveryRepository->create([
            'orderId' => $order->id,
            // 'deliveryMenId' => $deliveryMen->id,
            'restaurantId' => $order['restaurantManager']['restaurant']['id'],
            'customerId' => $order['customer']['id'],
            'addressOrderCustomer' => $order->shippingAddress,
            'finalPrice' => $order->finalPrice,
            'deliveryCost' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryMenCost'), // setting dashboard
            'deliveryCommission' => $this->settingsRepository->getSetting('deliveryMen', 'deliveryCommission'), // setting dashboard
            'paymentMethod' => $order->paymentMethod, // setting dashboard
            'status' => OrderDeliveryRepository::DELIVERY_MEAL_HAS_NOT_BEEN_RESTAURANT_STATUS,
            'nextStatus' => OrderDeliveryRepository::NEXT_ORDER_STATUS[OrderDeliveryRepository::DELIVERY_MEAL_HAS_NOT_BEEN_RESTAURANT_STATUS] ?? [],

        ]);
        $orderDeliveryStatus = new OrderStatusDelivery([
            'orderId' => $createNewOrderDelivery['orderId'],
            'orderDeliveryId' => $createNewOrderDelivery->id,
            'status' => OrderDeliveryRepository::DELIVERY_MEAL_HAS_NOT_BEEN_RESTAURANT_STATUS,
            // 'message' => $message,
            'creator' => user() ? user()->accountType() : null,
            'creatorBy' => $order['customer']['firstName'],
        ]);

        $orderDeliveryStatus->save();

        $this->orderDeliveryRepository->saveStatusLog($createNewOrderDelivery->id, $orderDeliveryStatus);
    }

    /**
     * Method getCurlExec
     *
     * @param $URL $URL
     *
     * @return String
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
        }

        return trim($getdistance, ' km');
    }

    /**
     * Check if order can be changed to the given status
     *
     * @param Model $order
     * @param string $nextStatus
     * @return bool
     */
    public function nextStatusIs($order, $nextStatus): bool
    {
        if (request()->type == 'clubs') {
            if (!isset(static::NEXT_ORDER_STATUS_CLUBS[$order->status])) {
                return false;
            }

            return in_array($nextStatus, static::NEXT_ORDER_STATUS_CLUBS[$order->status]);
        } elseif (request()->type == 'nutritionSpecialist') {
            if (!isset(static::NEXT_ORDER_STATUS_NUTRITION[$order->status])) {
                return false;
            }

            return in_array($nextStatus, static::NEXT_ORDER_STATUS_NUTRITION[$order->status]);
        } elseif ($order->productsType == 'food') {
            if ($order['restaurantManager']['restaurant']['delivery'] == false && $order->deliveryType == 'inHome') {
                if (!isset(static::NEXT_ORDER_STATUS_DELIVERY_BY_DITE_MARKIT_FOOD[$order->status])) {
                    return false;
                }

                return in_array($nextStatus, static::NEXT_ORDER_STATUS_DELIVERY_BY_DITE_MARKIT_FOOD[$order->status]);
            } else {
                if (!isset(static::NEXT_ORDER_STATUS_FOOD[$order->status])) {
                    return false;
                }

                return in_array($nextStatus, static::NEXT_ORDER_STATUS_FOOD[$order->status]);
            }
        } else {
            // dd($order->status , $nextStatus);
            if ($order->status == 'completed' || $order->status == 'requestReturning' || $order->status == 'requestReturningAccepted' || $order->status == 'requestReturningRejected') {
                // dd(static::NEXT_ORDER_STATUS_RETURNED_STATUS[$order->status]);
                if (!isset(static::NEXT_ORDER_STATUS_RETURNED_STATUS[$order->status])) {
                    return false;
                }

                return in_array($nextStatus, static::NEXT_ORDER_STATUS_RETURNED_STATUS[$order->status]);
            } else {
                if (!isset(static::NEXT_ORDER_STATUS[$order->status])) {
                    return false;
                }

                return in_array($nextStatus, static::NEXT_ORDER_STATUS[$order->status]);
            }
        }
    }

    /**
     * Get total orders for the given options.
     *
     * @param array $options
     * @return int
     */
    public function getTotal(array $options): int
    {
        $query = $this->getQuery();

        if (!empty($options['customer'])) {
            $query->where('customer.id', $options['customer']);
        }

        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        return $query->count();
    }

    /**
     * Get total orders for the given options.
     *
     * @param array $options
     * @return int
     */
    public function getTotalPrice(array $options): int
    {
        $query = $this->getQuery();

        if (!empty($options['customer'])) {
            $query->where('customer.id', $options['customer']);
        }

        if (!empty($options['status'])) {
            $query->where('status', $options['status']);
        }

        return $query->sum('finalPrice');
    }

    /**
     * Rate the given order
     *
     * @param Model $order
     * @param array $review
     * @return Model
     */
    public function rate(Model $order, array $review): ?Model
    {
        if ($order->rating) {
            return $order;
        }

        $order->rating = $review + ['ratedAt' => Carbon::now()];

        $order->save();

        return $order;
    }

    /**
     * Get order model and make sure it belongs to current customer
     *
     * @param int $orderId
     * @return Model|null
     */
    public function getCustomerOrder($orderId): ?Model
    {
        $order = $this->getModel($orderId);

        if (!$order || $order->customer['id'] != user()->id) {
            return null;
        }

        return $order;
    }

    /**
     * Get Orders Reports Generator
     *
     * @return OrdersReports
     */
    public function reports(): OrdersReports
    {
        return new OrdersReports($this);
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        $this->query->where('status', '!=', static::PAYING_STATUS);

        if ($seller = $this->option('seller')) {
            $this->query->where('seller.id', (int) $seller);
        }
        if ($store = $this->option('store')) {
            // dd($store);
            $this->query->where('seller.store.id', (int) $store);
        }

        if ($customer = $this->option('customer')) {
            // dd($customer);
            $this->query->where('customer.id', (int) $customer);
        }

        if ($type = $this->option('type')) {
            $this->query->where('productsType', $type);
        }

        if ($restaurantManager = $this->option('restaurantManager')) {
            $this->query->where('restaurantManager.id', (int) $restaurantManager);
        }

        if ($restaurant = $this->option('restaurant')) {
            $this->query->where('restaurantManager.restaurant.id', (int) $restaurant);
        }
        if ($nutritionSpecialist = $this->option('nutritionSpecialist')) {
            $this->query->where('nutritionSpecialist.id', (int) $nutritionSpecialist);
        }
        if ($nutritionSpecialistManager = $this->option('nutritionSpecialistManager')) {
            $this->query->where('nutritionSpecialistManager.id', (int) $nutritionSpecialistManager);
        }

        if ($clubId = $this->option('clubId')) {
            $this->query->where('club.id', (int) $clubId);
        }
    }

    public function saveBankTransfer(int $orderId, $bankTransfer)
    {
        $order = $this->getModel($orderId);
        $order->bankTransfer = $bankTransfer->sharedInfo();
        $order->save();

        // if (!isset($order->bankTransfer) && $bankTransfer->status == static::ACCEPTED_STATUS) {
        //     $this->ordersRepository->changeStatus($order, OrdersRepository::PROCESSING_STATUS, 'تم الموافقه علي التحويل البنكي');
        // }
    }

    /**
     * save product review in order
     *
     * @param $review
     */
    public function saveProductsReview($review)
    {
        $order = $this->getModel($review->orderId);
        $order->reassociate($review->sharedInfo(), 'productsReview')->save();

        foreach ($order->items as $item) {
            if ($item['product']['id'] == $review->productId) {
                OrderItem::find($item['id'])->update([
                    'isRated' => true,
                ]);

                $item['isRated'] = true;

                $order->reassociate($item, 'items')->save();
            }
        }
    }

    /**
     * Method saveRestaurantsReview
     *
     * @param $review $review
     *
     * @return void
     */
    public function saveRestaurantsReview($review)
    {
        $order = $this->getModel($review->orderId);
        $order->reassociate($review->sharedInfo(), 'restaurantsReviews')->save();
    }

    /**
     * Method saveClubsReview
     *
     * @param $review $review
     *
     * @return void
     */
    public function saveClubsReview($review)
    {
        $order = $this->getModel($review->orderId);
        $order->reassociate($review->sharedInfo(), 'clubsReviews')->save();
    }

    /**
     * Method saveNutritionSpecialistsReview
     *
     * @param $review $review
     *
     * @return void
     */
    public function saveNutritionSpecialistsReview($review)
    {
        $order = $this->getModel($review->orderId);
        $order->reassociate($review->sharedInfo(), 'nutritionSpecialistsReviews')->save();
    }

    /**
     * Method listReturnedOrders
     *
     * @param $options $options
     *
     * @return void
     */
    public function listReturnedOrders($options)
    {
        $orderRep = $this->orderItemsRepository->getQuery()->where('requestReturning', true)->where('customerId', user()->id)->orderBy('id', 'DESC')->paginate(15);
        $orderItems = $this->orderItemsRepository->wrapMany($orderRep->items());
        $orders = [];
        foreach ($orderItems as $key => $orderItem) {
            $orderStatus = $this->orderStatusRepository->wrap($this->orderStatusRepository->getQuery()->where('orderId', $orderItem->orderId)->where('orderItem', $orderItem->id)->latest('id')->first());
            $orderItem['orderStatus'] = $orderStatus;
            $orders[] = $orderItem;
        }

        return $orders;
    }

    /**
     * Method getPaginateInfoReturnedOrders
     *
     * @return void
     */
    public function getPaginateInfoReturnedOrders()
    {
        $data = $this->orderItemsRepository->getQuery()->where('requestReturning', true)->where('customerId', user()->id)->orderBy('id', 'DESC')->paginate(15);

        return $this->paginationInfo = [
            'currentResults' => $data->count(),
            'totalRecords' => $data->total(),
            'numberOfPages' => $data->lastPage(),
            'itemsPerPage' => $data->perPage(),
            'currentPage' => $data->currentPage(),
        ];
    }

    /**
     * Method listItemOrder
     *
     * @param $id $id
     *
     * @return void
     */
    public function listItemOrder($id)
    {
        // return $this->ordersRepository->wrap($this->ordersRepository->getQuery()->where('id', $id)->Where('status', OrdersRepository::REQUEST_RETURNING_STATUS)->first());
        // dd($id);
        $REQUEST_RETURNING_STATUS = OrdersRepository::REQUEST_RETURNING_STATUS;
        $REQUEST_RETURNING_ACCEPTED_STATUS = OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS;
        $REQUEST_RETURNING_REJECTED_STATUS = OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS;
        $ALTERNATIVE_PRODUCT = OrdersRepository::ALTERNATIVE_PRODUCT;
        $WALLET_PRODUCT = OrdersRepository::WALLET_PRODUCT;

        return  $this->orderItemsRepository->wrap($this->orderItemsRepository->getQuery()->where('id', $id)->Where('type', 'products')->where(function ($query) use ($REQUEST_RETURNING_STATUS, $REQUEST_RETURNING_ACCEPTED_STATUS, $REQUEST_RETURNING_REJECTED_STATUS, $ALTERNATIVE_PRODUCT, $WALLET_PRODUCT) {
            $query->where('status', '=', $REQUEST_RETURNING_STATUS)
                ->orWhere('status', '=', $REQUEST_RETURNING_ACCEPTED_STATUS)
                ->orWhere('status', '=', $REQUEST_RETURNING_REJECTED_STATUS)
                ->orWhere('status', '=', $ALTERNATIVE_PRODUCT)
                ->orWhere('status', '=', $WALLET_PRODUCT);
        })->first());
    }

    /**
     * Method listItemOrderNotes
     *
     * @param $id $id
     *
     * @return void
     */
    public function listItemOrderNotes($orderItem)
    {
        // $test = $this->orderStatusRepository->wrap($this->orderStatusRepository->getQuery()->where('orderId', (int)$orderItem['orderId'])->Where('status', OrdersRepository::REQUEST_RETURNING_STATUS)->first());
        // dd($test);
        return  $this->orderStatusRepository->wrap($this->orderStatusRepository->getQuery()->where('orderId', (int) $orderItem['orderId'])->where('orderItem', $orderItem->id)->Where('status', OrdersRepository::REQUEST_RETURNING_STATUS)->first());
    }

    /**
     * Method returnStatus
     *
     * @param $id $id
     *
     * @return void
     */
    public function returnStatus($orderItem)
    {
        return  $this->orderStatusRepository->wrap($this->orderStatusRepository->getQuery()->where('orderId', (int) $orderItem['orderId'])->where('orderItem', $orderItem->id)->latest('id')->first());
    }

    /**
     * Method sellerOrder
     *
     * @param $order $order
     *
     * @return void
     */
    public function sellerOrder($order)
    {
        $sellerCarts = $this->storeManagersRepository->wrapMany($order['seller']);

        $items = collect($order['items']);

        $seller = [];

        foreach ($sellerCarts as $sellerCart) {
            // $cartItems = CartItem::query()->where('seller.id', $sellerCart['id'])->get();
            $cartItems = $items->where('product.storeManager.id', $sellerCart['id'])->toArray();
            $sellerCart['shippingMethod'] = $order['shippingMethod'];

            $sellerCart['items'] = $cartItems;

            $seller[] = $sellerCart;
        }

        return $seller;
    }

    /**
     * If the order status is "alternativeProduct" or "walletProduct" then return the corresponding
     * translation, otherwise return the translation for "pendingReplace".
     * </code>
     * 
     * @param orderItem This is the order item that is being replaced.
     * 
     * @return the value of the last return statement.
     */
    public function replace($orderItem)
    {
        $orderStatus = $this->orderStatusRepository->wrap($this->orderStatusRepository->getQuery()->where('orderId', (int) $orderItem['orderId'])->where('orderItem', $orderItem->id)->latest('id')->first());

        if ($orderStatus->status == "alternativeProduct") {
            return trans('orders.alternativeProduct');
        } elseif ($orderStatus->status == "walletProduct") {
            return trans('orders.walletProduct');
        } else {
            return trans('orders.pendingReplace');
        }
    }

    /**
     * Method listReturnedOrderAdmin
     *
     * @param $options $options
     *
     * @return void
     */
    public function listReturnedOrderAdmin($options)
    {
        $orderRep = $this->orderItemsRepository->getQuery()->where('requestReturning', true)->orderBy('id', 'DESC');
        if ($options['id']) {
            $orderRep->where('id', $options['id']);
        } elseif ($options['status']) {
            $orderRep->where('status', $options['status']);
        } elseif ($options['customer']) {
            $orderRep->where('customerId', $options['customer']);
        } elseif ($options['from']) {
            $startOfDay = Carbon::parse($options['from']);
            $endOfDay = Carbon::parse($options['to']);
            // dd();
            $orderRep->whereBetween('createdAt', [$startOfDay->startOfDay(), $endOfDay->endOfDay()]);
        }
        $orderRep = $orderRep->paginate(15);

        $orderItems = $this->orderItemsRepository->wrapMany($orderRep->items());
        $orders = [];
        foreach ($orderItems as $key => $orderItem) {
            // dd( $orderItem);
            $orderStatus = $this->orderStatusRepository->wrap($this->orderStatusRepository->getQuery()->where('orderId', $orderItem->orderId)->where('orderItem', $orderItem->id)->latest('id')->first());
            $orderItem['orderStatus'] = $orderStatus;
            $orderItem['customer'] = $this->customersRepository->getQuery()->where('id', $orderItem->customerId)->first(['id', 'firstName', 'lastName']);

            $orders[] = $orderItem;
        }

        return $orders;
    }

    /**
     * {@inheritdoc}
     */
    public function onUpdate($model, $request, $oldModel)
    {
        if (in_array($model->status, [static::ADMIN_CANCELED_STATUS, static::CANCELED_STATUS, static::UNSUBSCRIBE_STATUS])) {
            // dd($model, $request->all(), $oldModel);
            if ($model->paymentMethod == static::VISA_PAYMENT_METHOD || $model->paymentMethod == static::MADA_PAYMENT_METHOD || $model->paymentMethod == static::MASTER_PAYMENT_METHOD || $model->paymentMethod == static::APPLE_PAY_PAYMENT_METHOD || $model->paymentMethod == static::WALLET_PAYMENT_METHOD) {
                if ($model->productsType == 'nutritionSpecialist' || $model->productsType == 'clubs') {
                    if ($request->isAmountFull == true) {
                        $this->returnToWallet($model);
                    }
                }
            }
        }
    }

    /**
     * Method updateClubForOrders
     *
     * @param $club $club
     *
     * @return void
     */
    public function updateClubForOrders($club)
    {
        Model::where('club.id', $club->id)->update([
            'club' => $club->sharedInfo(),
        ]);
    }

    /**
     * Method countSeenReturnedOrder
     *
     * @return void
     */
    public function countSeenReturnedOrder()
    {
        return $this->orderItemsRepository->getQuery()->where('isCountSeen', true)->count();
    }

    /**
     * If the order has a seller, deposit the money in the seller's wallet, otherwise if the order has
     * a restaurant manager, deposit the money in the restaurant manager's wallet, otherwise if the
     * order has a club manager, deposit the money in the club manager's wallet, otherwise if the order
     * has a nutrition specialist manager, deposit the money in the nutrition specialist manager's
     * wallet.
     * 
     * @param order is the order object
     */
    public function saveMoneyInTheServiceProvidersWallet($order)
    {
        if ($order->seller) {
            $this->depositWalletSeller($order);
        } elseif ($order->restaurantManager) {
            $this->depositWalletRestaurant($order);
        } elseif ($order->clubManager) {
            $this->depositWalletClub($order);
        } elseif ($order->nutritionSpecialistManager) {
            $this->depositWalletNutritionSpecialist($order);
        }
    }

    /**
     * "It takes the order, calculates the profit ratio, and then deposits the money into the seller's
     * wallet."
     * </code>
     * 
     * @param order The order object
     */
    public function depositWalletSeller($order)
    {
        $orderSellers = $order->seller;
        $type = 'StoreManager';
        foreach ($orderSellers as $orderSeller) {
            $seller = $this->storeManagersRepository->sharedInfo($orderSeller['id']);

            if ($seller['store']['profitRatio'] == -1) {
                $profitRatio = $this->settingsRepository->getSetting('store', 'profitRatio');
            } else {
                $profitRatio = $seller['store']['profitRatio'];
            }
            $items = collect($order->items)->where('seller.id', (int)$seller['id']);
            $subTotal = $items->sum('totalPrice');
            $totalRequired = 0.0;
            $profitRatio = (($subTotal * $profitRatio) / 100);
            $totalRequired = $subTotal - $profitRatio;
            $this->depositWalletProvider($order, $totalRequired, $subTotal, $seller, $type, $profitRatio);
        }
    }

    /**
     * It takes the order, calculates the profit ratio, and then deposits the amount into the wallet
     * 
     * @param order The order object
     */
    public function depositWalletRestaurant($order)
    {
        $seller = $this->restaurantManagersRepository->sharedInfo($order->restaurantManager['id']);
        $type = 'RestaurantManager';
        if ($seller['restaurant']['profitRatio'] == -1) {
            $profitRatio = $this->settingsRepository->getSetting('restaurant', 'profitRatio');
        } else {
            $profitRatio = $seller['restaurant']['profitRatio'] ?? 0;
        }

        $subTotal = $order['subTotal'];
        $totalRequired = 0.0;
        $profitRatio = (($subTotal * $profitRatio) / 100);
        $totalRequired = $subTotal - $profitRatio;
        if ($order->paymentMethod == 'cashOnDelivery') {
            $this->withdrawWalletProvider($order, $totalRequired, $subTotal, $seller, $type, $profitRatio);
        } else {
            $this->depositWalletProvider($order, $totalRequired, $subTotal, $seller, $type, $profitRatio);
        }
    }

    /**
     * It takes the order, subtracts the profit ratio from the subTotal, and then deposits the
     * totalRequired into the wallet.
     * 
     * @param order is the order object
     */
    public function depositWalletClub($order)
    {
        $seller = $this->clubManagersRepository->sharedInfo($order->clubManager['id']);
        $type = 'ClubManager';
        if ($seller['club']['profitRatio'] == -1) {
            $profitRatio = $this->settingsRepository->getSetting('club', 'profitRatio');
        } else {
            $profitRatio = $seller['club']['profitRatio'];
        }
        $subTotal = $order['subTotal'];
        $totalRequired = 0.0;
        $profitRatio = (($subTotal * $profitRatio) / 100);
        $totalRequired = $subTotal - $profitRatio;
        if ($order->paymentMethod == 'cashOnDelivery') {
            $this->withdrawWalletProvider($order, $totalRequired, $subTotal, $seller, $type, $profitRatio);
        } else {
            $this->depositWalletProvider($order, $totalRequired, $subTotal, $seller, $type, $profitRatio);
        }
    }

    /**
     * It's a function that deposits money into a wallet.
     * 
     * @param order the order object
     */
    public function depositWalletNutritionSpecialist($order)
    {
        $seller = $this->nutritionSpecialistMangersRepository->sharedInfo($order->nutritionSpecialist['id']);
        $type = 'NutritionSpecialistManger';
        if ($seller['nutritionSpecialist']['profitRatio'] == -1) {
            $profitRatio = $this->settingsRepository->getSetting('nutritionSpecialist', 'profitRatio');
        } else {
            $profitRatio = $seller['nutritionSpecialist']['profitRatio'];
        }
        $subTotal = $order['subTotal'];
        $totalRequired = 0.0;
        $profitRatio = (($subTotal * $profitRatio) / 100);
        $totalRequired = $subTotal - $profitRatio;
        if ($order->paymentMethod == 'cashOnDelivery') {
            $this->withdrawWalletProvider($order, $totalRequired, $subTotal, $seller, $type, $profitRatio);
        } else {
            $this->depositWalletProvider($order, $totalRequired, $subTotal, $seller, $type, $profitRatio);
        }
    }

    /**
     * A function that adds money to the seller's wallet.
     * 
     * @param order the order object
     * @param totalRequired the amount to be deposited in the wallet
     * @param subTotal the total amount of the order
     * @param seller the seller's id
     * @param type 1 for deposit, 2 for withdrawal
     * @param profitRatio is the commission that the site takes from the seller
     * 
     * @return the result of the function deposit() in the WalletProviderRepository class.
     */
    public function depositWalletProvider($order, $totalRequired, $subTotal, $seller, $type, $profitRatio)
    {
        $title = 'ايداع في المحفظة';
        $content = 'تم اضافة رصيد ' . $totalRequired . ' ر.س الي محفظتك بسبب وجود طلب جديد برقم ' . $order->id . '';
        return $this->walletProviderRepository->deposit([
            'provider' => $seller['id'],
            'amount' => $totalRequired,
            'totalAmountOrder' => $subTotal,
            'commissionDiteMarket' => $profitRatio,
            'orderId' => $order->id,
            'title' => $title,
            'reason' => $content,
            'type' => $type,
        ]);
    }

    /**
     * A function that withdraws the amount of the commission from the seller's wallet and adds it to
     * the site's wallet.
     * 
     * @param order the order object
     * @param totalRequired the total amount of the order
     * @param subTotal the total amount of the order
     * @param seller the seller's id
     * @param type 1
     * @param profitRatio the amount to be withdrawn from the wallet
     * 
     * @return the result of the function call to the function walletProviderRepository->withdraw().
     */
    public function withdrawWalletProvider($order, $totalRequired, $subTotal, $seller, $type, $profitRatio)
    {
        if (is_int($order)) {
            $order = $order;
            $amount = $subTotal;
        } else {
            $order = $order->id;
            $amount = $profitRatio;
        }
        $title = 'سحب في المحفظة';
        $content = 'تم سحب عمولة من رصيد ' . $amount . ' ر.س الي محفظتك بسبب وجود طلب جديد برقم ' . $order . '';
        return $this->walletProviderRepository->withdraw([
            'provider' => $seller['id'],
            'amount' => $amount,
            'totalAmountOrder' => $subTotal,
            'commissionDiteMarket' => $profitRatio,
            'orderId' => $order,
            'title' => $title,
            'reason' => $content,
            'type' => $type,
        ]);
    }

    /**
     * "It takes the order, calculates the profit ratio, and then deposits the money into the seller's
     * wallet."
     * </code>
     * 
     * @param order The order object
     */
    public function withdrawWalletSeller($orderItem)
    {
        $type = 'StoreManager';
        $seller = $this->storeManagersRepository->sharedInfo((int)$orderItem['seller']['id']);
        $walletProvider = $this->walletProviderRepository->getQuery()->where('orderId', (int)$orderItem->orderId)->where('provider.id', $seller['id'])->first();

        $this->withdrawWalletProvider($orderItem['orderId'], $walletProvider['amount'], $walletProvider['amount'], $seller, $type, $walletProvider['commissionDiteMarket']);
    }
}
