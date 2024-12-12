<?php

namespace App\Modules\Orders\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Modules\Orders\Traits\ChangeStatus;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class OrdersController extends AdminApiController
{
    use ChangeStatus;

    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'orders',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [],
            'update' => [],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function store(Request $request)
    {
        $validator = $this->scan($request);

        if ($validator->passes()) {
            try {
                $model = $this->repository->create($request);
            } catch (\Throwable $th) {
                return $this->badRequest($th->getMessage());
            }

            return $this->success([
                'record' => $this->repository->get($model->id),
            ]);
        } else {
            return $this->badRequest($validator->errors());
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
        return Validator::make($request->all(), [
            'customer' => 'required',
            'items' => 'required',
            'shippingMethod' => 'required',
            'address' => 'required',
        ]);
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
            'status' => $request->status,
            'id' => (int) $request->id,
            'returnOrderId' => (int) $request->returnOrderId,
            'customer' => (int) $request->customer,
            'from' => $request->from,
            'to' => $request->to,
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }


        return $this->success([
            'records' => $this->repository->listReturnedOrderAdmin($options),
            'paginationInfo' => $this->repository->getPaginateInfoReturnedOrders(),
            'countSeen' => $this->repository->countSeenReturnedOrder(),
        ]);
    }

    /**
     * Method listReturnedOrdersId
     *
     * @param $id $id
     *
     * @return void
     */
    public function listReturnedOrdersId(Request $request, $id)
    {
        $orderItem = $this->orderItemsRepository->get($id);
        if (!$orderItem) {
            return $this->badRequest(trans('errors.notFound'));
        }
        if ($request->isCountSeen) {
            $request->request->add(['isCountSeen' => false]);
            $this->orderItemsRepository->update($id, $request);
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

    /**
     * Method listReturnedOrdersChangeStatus
     *
     * @return void
     */
    public function listReturnedOrdersChangeStatus(int $id, $status, Request $request)
    {
        $order = $this->orderItemsRepository->get($id);

        if (!$order) {
            return $this->notFound(trans('errors.notFound'));
        }

        // dd($order);
        if (!$this->repository->nextStatusIs($order, $status)) {
            return $this->badRequest(trans('errors.cannotChangeStatus'));
        }

        // if(in_array($status, [static::RETURNED_STATUS, static::REQUEST_RETURNING_STATUS, static::REQUEST_PARTIAL_RETURN_STATUS])) {
        //     $this->transactionsRepository->updateTransactionSuspended($orderId);
        // }
        $this->repository->changeStatus($id, $status, $request, (string) ($request->cancelingReason ?? $request->returningReason), $request->returnedItems ?? [], $request);
        $order = $this->orderItemsRepository->get($id);

        return $this->success([
            'record' => $order,
        ]);
    }

    /**
     * Method update
     *
     * @param Request $request
     * update for restaurantManager/categories
     * @return array
     */
    public function update(Request $request, $id)
    {
        // dd($request->all(), $id);
        if ($this->repository->get((int) $id)) {
            if ($request->isAmountFull) {
                $request->request->add(['isCheckAmountFull' => true]);
            }

            $updateSection = $this->repository->update((int) $id, $request);

            return $this->success([
                'record' => $this->repository->wrap($updateSection),
            ]);
        }

        return $this->badRequest(trans('errors.notFound'));
    }

    /**
     * It updates the box item by seller.
     * 
     * @param id The id of the order
     * @param Request request 
     */
    public function updateBoxItem($id, Request $request)
    {
        $order = $this->repository->get($id);

        if (!$order) {
            return $this->badRequest(trans('errors.notFound'));
        }
        $updateBoxItem = $this->repository->updateBoxItem($id, $request);
        return $this->success([
            'record' => $updateBoxItem,
        ]);
    }

     /**
     * It updates the box item by seller.
     * 
     * @param id The id of the order
     * @param Request request 
     */
    public function updateBoxItemBySeller($id, Request $request)
    {
        $order = $this->repository->get($id);

        if (!$order) {
            return $this->badRequest(trans('errors.notFound'));
        }
        
        $updateBoxItem = $this->repository->updateBoxItemBySeller($id, $request);
        return $this->success([
            'record' => $updateBoxItem,
        ]);
    }
}
