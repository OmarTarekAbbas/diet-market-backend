<?php

namespace App\Modules\Orders\Controllers\Site;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Modules\Cart\Models\Cart;
use App\Modules\ClubBookings\Models\ClubBooking;
use App\Modules\Clubs\Models\Club;
use App\Modules\DeliveryMen\Models\DeliveryMan;
use App\Modules\Nationality\Models\Nationality;
use App\Modules\NutritionSpecialist\Models\NutritionSpecialist;
use Illuminate\Support\Facades\App;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use Illuminate\Support\Facades\Validator;
use App\Modules\Orders\Traits\ChangeStatus;
use App\Modules\Transactions\Models\Transaction;
use HZ\Illuminate\Mongez\Managers\ApiController;
use App\Modules\Orders\Repositories\OrdersRepository;
use App\Modules\Products\Models\Product;
use App\Modules\RestaurantManager\Models\RestaurantManager;
use App\Modules\Restaurants\Models\Restaurant;
use App\Modules\Services\Contracts\PaymentGatewayResponse;
use App\Modules\Services\Exceptions\InvalidPaymentException;
use App\Modules\StoreManagers\Models\StoreManager;
use App\Modules\Stores\Models\Store;
use App\Modules\Wallet\Models\Wallet;
use App\Modules\Wallet\Models\WalletDelivery;
use App\Modules\Wallet\Models\WalletProvider;

class OrdersController extends ApiController
{
    use ChangeStatus;

    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'orders';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        // dd('jjj');
        $options = [
            'customer' => user()->id,
            'type' => $request->type,
        ];

        if ($request->status) {
            $options['status'] = $request->status;
        }

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->list($options),
            'paginationInfo' => $this->repository->getPaginateInfo(),
        ]);
    }

    /**
     * Seller Orders
     *
     * @return json
     */
    public function sellerOrders(Request $request)
    {
        $options = [
            'seller' => user()->id,
        ];

        if ($request->status) {
            $options['status'] = $request->status;
        }

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->list($options),
            'paginationInfo' => $this->repository->getPaginateInfo(),
        ]);
    }

    /**
     * Reorder The given order id
     *
     * @param int $orderId
     * @param Request $request
     * @return Response|\Illuminate\Http\Response|string
     */
    public function reorder($orderId, Request $request)
    {
        try {
            return $this->success([
                'success' => $this->repository->reorder($orderId, $request->selectedItems ?? []),
            ]);
        } catch (Exception $exception) {
            // return $this->badRequest(trans('cart.missingData.' . $exception));
            return $this->badRequest($exception->getMessage());
        }
    }

    //    /**
    //     * HyperPay Webhook
    //     *
    //     * @return Response
    //     */
    //    public function updateOrderByWebHook(Request $request)
    //    {
    //        $data = file_get_contents('php://input');
    //
    //        $json_body = json_decode($data, true); //if the webhook format is JSON
    //
    //        $http_body = $json_body['encryptedBody'];
    //        $headers = getallheaders();
    //
    //        $key_from_configuration = '848E8C1D4B5D9CC5D9C3CDBD7E9F83C7DDC2A733F1B356B4F1A8AA690D49AF32';
    //        $iv_from_http_header = $headers['X-Initialization-Vector'];
    //        $auth_tag_from_http_header = $headers['X-Authentication-Tag'];
    //        $key = hex2bin($key_from_configuration);
    //        $iv = hex2bin($iv_from_http_header);
    //        $auth_tag = hex2bin($auth_tag_from_http_header);
    //        $cipher_text = hex2bin($http_body);
    //        $result = openssl_decrypt($cipher_text, "aes-256-gcm", $key, OPENSSL_RAW_DATA, $iv, $auth_tag);
    //        $response = json_decode($result);
    //
    //        file_put_contents(storage_path('hyper/webooks/' . time() . '.json'), json_encode([
    //            'headers' => $request->headers->all(),
    //            'result' => $response,
    //        ]));
    //
    //        return 'Done';
    //    }

    /**
     * {@inheritDoc}
     */
    public function store(Request $request)
    {
        // dd(user()->getCart()->isEmpty());
        // if ($request->type == 'products') {
        if ($request->type == 'products' || $request->type == 'food') {
            if (user()->getCart()->isEmpty($request->type)) {
                // return $this->badRequest('emptyCart');
                return $this->badRequest(trans('orders.emptyCart'));
                // }
            }
        }

        $validator = $this->scan($request);

        if ($validator->passes()) {
            if ($request->type == 'products') {
                [$isValid, $notValidData] = user()->getCart()->isValid(["shippingAddress", "shippingFees", "shippingMethod"]);
            } else {
                if ($request->deliveryType == 'inHome') {
                    [$isValid, $notValidData] = user()->getCart()->isValid(["shippingAddress"]);
                } else {
                    $isValid = true;
                }
            }



            if (!$isValid) {
                // dd($notValidData);
                // dd('sds');
                return $this->badRequest(trans('cart.missingData.' . $notValidData));
            }

            if ($request->couponCode) {
                try {
                    $coupon = $this->couponsRepository->getValidCoupon($request->couponCode, $request);
                    $request['coupon'] = $coupon;
                } catch (\Throwable $th) {
                    return $this->badRequest((env('APP_ENV') == 'dev') ? $this->badRequest($th->getMessage() . $th->getTraceAsString()) : $this->badRequest($th->getMessage()));
                }
            }

            try {
                return $this->success([
                    'record' => $this->repository->wrap(
                        $this->repository->create($request)
                    ),
                ]);
            } catch (\Exception $Exception) {
                // dd('sdsd');
                return $this->badRequest($Exception->getMessage());
                // return $this->badRequest(trans('cart.missingData.' . $Exception));
            }
        } else {
            return $this->badRequest($validator->errors());
        }
    }

    /**
     * Rate the given order
     *
     * @param int $order
     * @param Request $request
     * @return Response|\Illuminate\Http\Response|string
     */
    public function rate($order, Request $request)
    {
        $order = $this->repository->getCustomerOrder($order);

        if (!$order) {
            return $this->badRequest(trans('errors.notFound'));
        }

        if ($this->repository->isRatedBefore($order)) {
            return $this->badRequest(trans('errors.alreadyRated'));
        }

        $this->reviewsRepository->rate($order, $request);

        $order = $order->fresh();

        return $this->success([
            'message' => trans('orders.ratedSuccess'),
            'record' => $this->repository->wrap($order),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function show($id, Request $request)
    {
        $order = $this->repository->get($id);

        if (!$order) {
            return $this->badRequest(trans('errors.notFound'));
        }
        // dd($order->productsType);
        if ($order->productsType == "nutritionSpecialist") {
            return $this->success([
                'record' => $this->repository->wrap($order),
                'returnOrderStatus' => $this->settingsRepository->getSetting('ReturnedOrder', 'returnSystemStatus'),
                'nutritionSpecialistNotes' => $this->NutritionSpecialistsNotes->NutritionSpecialistsNotes($id),
            ]);
        } elseif ($order->productsType == 'products') {
            return $this->success([
                'record' => $this->repository->wrap($order),
                'returnOrderStatus' => $this->settingsRepository->getSetting('ReturnedOrder', 'returnSystemStatus'),
                'sellerOrder' => $this->repository->sellerOrder($order),
            ]);
        } else {
            return $this->success([
                'record' => $this->repository->wrap($order),
                'returnOrderStatus' => $this->settingsRepository->getSetting('ReturnedOrder', 'returnSystemStatus'),
            ]);
        }
    }

    /**
     * Determine whether the passed values are valid
     *
     * @param Request $request
     * @return mixed
     */
    protected function scan(Request $request)
    {
        // dd($bankTransfer);
        return Validator::make($request->all(), [
            'paymentMethod' => [
                'required',
                Rule::in([
                    OrdersRepository::CASH_ON_DELIVERY,
                    OrdersRepository::VISA_PAYMENT_METHOD,
                    OrdersRepository::MADA_PAYMENT_METHOD,
                    OrdersRepository::MASTER_PAYMENT_METHOD,
                    OrdersRepository::WALLET_PAYMENT_METHOD,
                    OrdersRepository::BANK_TRANSFER_METHOD,
                    OrdersRepository::APPLE_PAY_PAYMENT_METHOD,
                ]),
            ],
        ]);
    }

    /**
     * Confirm Online Payment
     *
     * @param Request $request
     * @return Response|\Illuminate\Http\Response|string
     */
    public function confirmOnlinePayment(Request $request)
    {
        $checkOutId = $request->checkOutId ?? $request->id ?? $request->orderId;

        try {
            $order = $this->repository->getByCheckOutId($checkOutId);
            // dd(env('FRONT_URL') . 'checkout/success/' . $order->productsType . '/' . $order->id);
            try {
                $dataconfirmPayment = $this->repository->confirmPayment($order);
                // dd($dataconfirmPayment ,'1');
                if ($dataconfirmPayment->paymentInfo['message'] == "Processed successfully") {
                    if ($order && $order->insideWhereType == 'web') {
                        return  redirect(env('FRONT_URL') . 'checkout/success/' . $order->productsType . '/' . $order->id);
                    } else {
                        return redirect('api/orders/successfully');
                    }
                } else {
                    if ($order && $order->insideWhereType == 'web') {
                        return redirect(env('FRONT_URL') . 'checkout/fail');
                    } else {
                        return redirect('api/orders/fail');
                    }
                }
                // return $this->success([
                //     'record' => $dataconfirmPayment,
                // ]);
            } catch (InvalidPaymentException $e) {
                // dd($e->getMessage() ,'2'); // لو عاوز تعرف ايه راجع غلط
                if ($order && $order->insideWhereType == 'web') {
                    return redirect(env('FRONT_URL') . 'checkout/fail');
                } else {
                    return redirect('api/orders/fail');
                }
                // return $this->badRequest([
                //     'error' => $e->getMessage(),
                //     'paymentResponse' => $e->response()->getResponse(),
                // ]);
            }
        } catch (\Exception $e) {
            // dd($e->getMessage() ,'3');
            if ($order && $order->insideWhereType == 'web') {
                return redirect(env('FRONT_URL') . 'checkout/fail');
            } else {
                return redirect('api/orders/fail');
            }
            // return $this->badRequest($e->getMessage());
        }
    }

    /**
     * list orders PARTIAL_RETURN or RETURNING
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|string
     */
    public function listReturnedOrders(Request $request)
    {
        $options = [
            'customer' => user()->id,
            'status' => [OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS, OrdersRepository::REQUEST_RETURNING_STATUS, OrdersRepository::REQUEST_RETURNING_ACCEPTED_STATUS, OrdersRepository::REQUEST_RETURNING_REJECTED_STATUS, OrdersRepository::ALTERNATIVE_PRODUCT, OrdersRepository::WALLET_PRODUCT],
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->listReturnedOrders($options),
            'paginationInfo' => $this->repository->getPaginateInfoReturnedOrders(),
        ]);
    }

    public function listReturnedOrdersId($id)
    {
        $orderItem = $this->orderItemsRepository->get($id);

        if (!$orderItem) {
            return $this->badRequest(trans('errors.notFound'));
        }

        return $this->success([
            'record' => $orderItem,
            'listOrders' => $this->repository->listItemOrder($orderItem->id),
            'notesReturned' => $this->repository->listItemOrderNotes($orderItem),
            'returnStatus' => $this->repository->returnStatus($orderItem),
            'replace' => $this->repository->replace($orderItem),
            'parentOrder' => $orderItem->orderId,
        ]);
    }

    public function orderSuccessfully()
    {
        dd('orderSuccessfully');
    }

    public function orderFail()
    {
        dd('orderSuccessfully');
    }

    public function ordersDelete() // make delete all order for database
    {
        $OrderDeliverys = WalletProvider::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = OrderItem::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = Transaction::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = Product::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = Store::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = StoreManager::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = Restaurant::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = RestaurantManager::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = Club::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = Wallet::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = DeliveryMan::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = WalletDelivery::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = Nationality::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = NutritionSpecialist::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }
        $OrderDeliverys = Order::all();
        foreach ($OrderDeliverys as $OrderDelivery) {
            $OrderDelivery->delete();
        }

        return 'done';
    }
}
