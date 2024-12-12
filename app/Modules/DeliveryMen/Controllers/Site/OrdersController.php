<?php

namespace App\Modules\DeliveryMen\Controllers\Site;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Modules\Orders\Repositories\OrdersRepository;
use App\Modules\Orders\Repositories\OrderDeliveryRepository;
use App\Modules\Orders\Controllers\Site\OrdersController as BaseOrdersController;

class OrdersController extends BaseOrdersController
{
    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'deliveryMan' => user()->id,
            'paginate' => false,
            'status' => $request->status,
        ];


        $records = $this->repository->list($options);

        //        $notDeliveredOrders = $records->sortBy('id')->whereIn('status', [OrdersRepository::ON_THE_WAY_STATUS, OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS, OrdersRepository::REQUEST_RETURNING_STATUS])->values();
        //
        //        $currentDeliveringOrders = $notDeliveredOrders->whereNotNull('deliveryManStartedMovingAt');
        //
        //        $pending = $notDeliveredOrders->whereNull('deliveryManStartedMovingAt');
        //
        //        $returning = $pending->where('requestReturning', true)->first();
        //
        //        $new = $pending->where('requestReturning', '!=', true)->first();
        //
        //        $deliveryMan = user();
        //
        //        if (!empty($deliveryMan->deliveringOrders)) {
        //            foreach ($deliveryMan->deliveringOrders as $deliveringOrder) {
        //                if ($deliveringOrder['status'] === OrdersRepository::ON_THE_WAY_STATUS) {
        //                    $new = null;
        //                } elseif (in_array($deliveringOrder['status'], [OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS, OrdersRepository::REQUEST_RETURNING_STATUS])) {
        //                    $returning = null;
        //                }
        //            }
        //        }
        //
        //        $exceptions = [];
        //
        //        if ($new) {
        //            $exceptions[] = $new->id;
        //        }
        //
        //        if ($returning) {
        //            $exceptions[] = $returning->id;
        //        }
        //
        //        $pending = $pending->whereNotIn('id', $exceptions)->values();

        //        return $this->success([
        //            'records' => [
        ////                // 'delivering' => $delivering = $records->where('status', OrdersRepository::DELIVERY_ON_THE_WAY_STATUS)->values(),
        ////                // 'delivering' => $delivering = $records->where('status', OrdersRepository::ON_THE_WAY_STATUS)->values(),
        //                'delivering' => $currentDeliveringOrders->values(),
        //                'pending' => [
        //                    'other' => $pending,
        //                    'returning' => $returning ? [$returning] : [],
        //                    'new' => $new ? [$new] : [],
        //                ],
        //                'other' => $records->whereNotIn('status', [OrdersRepository::ON_THE_WAY_STATUS, OrdersRepository::ON_THE_WAY_STATUS, OrdersRepository::REQUEST_PARTIAL_RETURN_STATUS, OrdersRepository::REQUEST_RETURNING_STATUS])->values(),
        //            ],
        //            'total' => [
        //                'delivering' => $currentDeliveringOrders->count(),
        //                'pending' => $pending->count(),
        //            ]
        //        ]);

        return $this->success([
            'records' => $records,
        ]);
    }

    /**
     * A request that indicates the delivery man has started moved
     *
     * @param int $orderId
     * @return Response
     */
    public function startedMoving($orderId)
    {
        $this->ordersRepository->deliveryManStartedMoving($orderId);

        return $this->success();
    }

    /**
     * {@inheritDoc}
     */
    public function show($id, Request $request)
    {
        $order = $this->orderDeliveryRepository->get($id);

        if (!$order) {
            return $this->badRequest(trans('errors.notFound'));
        }

        return $this->success([
            'record' => $this->orderDeliveryRepository->show($order),
        ]);
    }

    /**
     * Method requestOrder
     *
     * @param Request $request
     *
     * @return void
     */
    public function requestOrder(Request $request)
    {
        return $this->success([
            'records' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->requestOrderIsPending($request)),
        ]);
    }

    /**
     * Method requestOrderCronJobs
     *
     * @return void
     */
    public function requestOrderCronJobs()
    {
        return $this->success([
            'records' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->requestOrderCronJobs()),
        ]);
    }

    /**
     * Method ordersCurrent
     *
     * @param Request $request
     *
     * @return void
     */
    public function ordersCurrent(Request $request)
    {
        return $this->success([
            'current' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->listCurrentAndDelivertOnTheWay($request)),
        ]);
    }

    /**
     * Method listCompleted
     *
     * @param Request $request
     *
     * @return void
     */
    public function listCompleted(Request $request)
    {
        $lastOrdeDelivered = $this->orderDeliveryRepository->lastOrdeDelivered($request);
        if ($lastOrdeDelivered) {
            return $this->success([
                'records' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->listCompleted($request)),

                'paginationInfo' => $this->orderDeliveryRepository->getPaginateInforOrderRecords($request),

                'lastOrdeDelivered' => $this->orderDeliveryRepository->wrap($this->orderDeliveryRepository->lastOrdeDelivered($request)), // error new req

                'assignDateDelivery' => Carbon::parse(user()->createdAt)->format('Y-m-d'),

                'countOrders' => $this->orderDeliveryRepository->countOrders($request),

                'countSumKm' => $this->orderDeliveryRepository->countSumKm($request),

                'countSumProfits' => $this->orderDeliveryRepository->countSumProfits($request),

                'countSumProfitsText' => $this->orderDeliveryRepository->countSumProfits($request) . ' ر.س',

                'date' => $this->orderDeliveryRepository->dateOrder($request),

                'dateText' => $this->orderDeliveryRepository->dateText($request),

                'dateTextYear' => $this->orderDeliveryRepository->dateTextYear($request),

            ]);
        } else {
            return $this->success([
                'records' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->listCompleted($request)),

                'paginationInfo' => $this->orderDeliveryRepository->getPaginateInforOrderRecords($request),


                'assignDateDelivery' => Carbon::parse(user()->createdAt)->format('Y-m-d'),

                'countOrders' => $this->orderDeliveryRepository->countOrders($request),

                'countSumKm' => $this->orderDeliveryRepository->countSumKm($request),

                'countSumProfits' => $this->orderDeliveryRepository->countSumProfits($request),

                'countSumProfitsText' => $this->orderDeliveryRepository->countSumProfits($request) . ' ر.س',

                'date' => $this->orderDeliveryRepository->dateOrder($request),

                'dateText' => $this->orderDeliveryRepository->dateText($request),

                'dateTextYear' => $this->orderDeliveryRepository->dateTextYear($request),

            ]);
        }
    }

    /**
     * Method deliveryOnTheWay
     *
     * @param Request $request
     *
     * @return void
     */
    public function deliveryOnTheWay(Request $request)
    {
        return $this->success([
            'current' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->deliveryOnTheWay($request->orders)),

        ]);
    }

    /**
     * Method listCurrentAndnewRequest
     *
     * @param Request $request
     *
     * @return void
     */
    public function listCurrentAndnewRequest(Request $request)
    {
        return $this->success([
            'current' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->listCurrentAndDelivertOnTheWay($request)),
            'newRequest' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->requestOrderIsPending($request)),
        ]);
    }

    /**
     * Method requestOrderAccepted
     *
     * @param $id $id
     * @param Request $request
     *
     * @return void
     */
    public function requestOrderAccepted($id, Request $request)
    {
        $orderDelivery = $this->orderDeliveryRepository->get($id);

        if (!$orderDelivery) {
            return $this->badRequest('لا يوجد اي دي');
        }
        if ($orderDelivery->status == OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS) {
            return $this->badRequest('تم الموافقة من قبل');
        }

        $user = user();
        if ($user->status == false) {
            return $this->badRequest('لم تتغير حالة الطلب بسبب ان حالتك غير متصل');
        }

        $restaurant = $this->restaurantsRepository->get((int) $orderDelivery->restaurantId);
        //In the delivery pocket, I accept the order, or we go to the restaurant to receive the order
        $orderDelivery = $this->orderDeliveryRepository->getQuery()->where('deliveryMenId', (int) $user->id)->where('status', OrderDeliveryRepository::DELIVERY_ACCEPTED_STATUS)->first();
        //The order from the restaurant is not the same restaurant as the new order
        if ($orderDelivery && $orderDelivery['restaurantId'] != $restaurant['id']) {
            return $this->badRequest('انت معاك اوردر لمطعم اخر لا يمكنك توصيل لاكثر من مطعم في نفس الوقت برجاء توصيل الطلب أولاً ثم قبول طلب اخر');
        }


        $requestOrderAccepted = $this->orderDeliveryRepository->requestOrderAccepted($id);

        // $cantAssentMoreThan2 = $this->orderDeliveryRepository->cantAssentMoreThan2();
        // dd($cantAssentMoreThan2);
        if ($requestOrderAccepted) {
            return $this->success([
                'message' => 'تم قبول الطلب بنجاح يمكنك بدء التوصيل الان',
                // 'records' => $this->orderDeliveryRepository->reternOrderAccepted($id),
                'current' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->listCurrentAndDelivertOnTheWay($request)),
            ]);
        }
    }

    /**
     * Method requestOrderRejected
     *
     * @param $id $id
     * @param Request $request
     *
     * @return void
     */
    public function requestOrderRejected($id, Request $request)
    {
        $orderDelivery = $this->orderDeliveryRepository->get($id);

        if (!$orderDelivery) {
            return $this->badRequest('لا يوجد اي دي');
        }

        $user = user();
        if ($user->status == false) {
            return $this->badRequest('لم تتغير حالة الطلب بسبب ان حالتك غير متصل');
        }

        // if ($orderDelivery->status == 'Rejected') {
        //     return $this->badRequest('تم الرفض من قبل');
        // }
        $requestOrderAccepted = $this->orderDeliveryRepository->requestOrderRejected($id, $request);
        if ($requestOrderAccepted) {
            return $this->success([
                'message' => 'success',
                'current' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->listCurrentAndDelivertOnTheWay($request)),
            ]);
        }
    }

    /**
     * Method deliveryNotCompletedOrder
     *
     * @param $id $id
     * @param Request $request
     *
     * @return void
     */
    public function deliveryNotCompletedOrder($id, Request $request)
    {
        $orderDelivery = $this->orderDeliveryRepository->get($id);

        if (!$orderDelivery) {
            return $this->badRequest('لا يوجد اي دي');
        }

        // if ($orderDelivery->status == 'Rejected') {
        //     return $this->badRequest('تم الرفض من قبل');
        // }
        $deliveryNotCompletedOrder = $this->orderDeliveryRepository->deliveryNotCompletedOrder($id, $request);
        if ($deliveryNotCompletedOrder == true) {
            return $this->success([
                'message' => 'عدم استلام الطلب',
                'current' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->listCurrentAndDelivertOnTheWay($request)),
            ]);
        } else {
            $this->orderDeliveryRepository->logOutForPublished((int) $orderDelivery->deliveryMenId);

            return response([
                'error' => 'Invalid Bearer Token',
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Method deliveryReasonsRejected
     *
     * @param Request $request
     *
     * @return void
     */
    public function deliveryReasonsRejected(Request $request)
    {
        $options = [
            'alphabetic' => true,
            'paginate' => false,
        ];

        return $this->success([
            'records' => $this->deliveryReasonsRejectedsRepository->listPublished($options),
        ]);
    }

    /**
     * Method deliveryReasonsNotCompleted
     *
     * @param Request $request
     *
     * @return void
     */
    public function deliveryReasonsNotCompleted(Request $request)
    {
        $options = [
            'alphabetic' => true,
            'paginate' => false,
        ];

        return $this->success([
            'records' => $this->deliveryReasonsNotCompletedOrdersRepository->listPublished($options),
        ]);
    }

    /**
     * Method deliveryCompletedOrder
     *
     * @param $id $id
     * @param Request $request
     *
     * @return void
     */
    public function deliveryCompletedOrder($id, Request $request)
    {
        $orderDelivery = $this->orderDeliveryRepository->get($id);

        if (!$orderDelivery) {
            return $this->badRequest('لا يوجد اي دي');
        }
        if ($orderDelivery->status == OrderDeliveryRepository::DELIVERY_COMPLETED_STATUS) {
            return $this->badRequest('تم تسليم الطلب من قبل');
        }
        $deliveryCompletedOrder = $this->orderDeliveryRepository->deliveryCompletedOrder($id, $request);
        // dd($deliveryCompletedOrder);
        if ($deliveryCompletedOrder == true) {
            return $this->success([
                'message' => 'تم تسليم الطلب بنجاح',
                'current' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->listCurrentAndDelivertOnTheWay($request)),
            ]);
        } else {
            $this->orderDeliveryRepository->logOutForPublished((int) $orderDelivery->deliveryMenId);

            return response([
                'error' => 'Invalid Bearer Token',
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Method locationListRestaurant
     *
     * @param Request $request
     *
     * @return void
     */
    public function locationListRestaurant(Request $request)
    {
        $deliveryLocation = $this->orderDeliveryRepository->deliveryLocation();
        if ($deliveryLocation) {
            return $this->success([
                'deliveryLocation' => $this->orderDeliveryRepository->deliveryLocation(),
                'restaurantLocation' => $this->orderDeliveryRepository->restaurantLocation(),
                'current' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->listCurrentAndDelivertOnTheWay($request)),
            ]);
        } else {
            return $this->success([
                'deliveryLocation' => null,
                'restaurantLocation' => [],
                'current' => [],
            ]);
        }
    }
}
