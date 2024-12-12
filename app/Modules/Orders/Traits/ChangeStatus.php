<?php

namespace App\Modules\Orders\Traits;

use Illuminate\Http\Request;

trait ChangeStatus
{
    /**
     * Change the status of the given order id
     *
     * @param int $orderId
     * @param string $status
     * @return response
     */
    public function changeStatus($orderId, $status, Request $request)
    {
        $order = $this->repository->getModel($orderId);

        if (!$order) {
            return $this->notFound(trans('errors.notFound'));
        }

        if (!$this->repository->nextStatusIs($order, $status)) {
            return $this->badRequest(trans('errors.cannotChangeStatus'));
        }

        // if(in_array($status, [static::RETURNED_STATUS, static::REQUEST_RETURNING_STATUS, static::REQUEST_PARTIAL_RETURN_STATUS])) {
        //     $this->transactionsRepository->updateTransactionSuspended($orderId);
        // }

        $record = $this->repository->changeStatus($orderId, $status, $request, (string) ($request->cancelingReason ?? $request->returningReason), $request->returnedItems ?? [], $request);

        $this->repository->changeOrderItemByOrderStatus($orderId, $status, $request);

        if ($status === 'requestReturning') {
            $requestReturningOto = $this->repository->requestReturningOto($order , $orderId, $request->returnedItems ?? []);
            $requestReturningOto = json_decode($requestReturningOto);
            return $this->success([
                'record' => $record,
                'webViewOto' => $requestReturningOto
            ]);
        } else {
            return $this->success([
                'record' => $record,
            ]);
        }
    }

    /**
     * It changes the status of an order
     * 
     * @param orderItemId The order item id
     * @param status the status of the order
     * @param Request request 
     * 
     * @return The record is being returned.
     */
    public function changeStatuItems($orderItemId, $status, Request $request)
    {
        $order = $this->orderItemsRepository->getModel($orderItemId);

        if (!$order) {
            return $this->notFound(trans('errors.notFound'));
        }

        // if (!$this->repository->nextStatusIs($order, $status)) {
        //     return $this->badRequest(trans('errors.cannotChangeStatus'));
        // }

        // if(in_array($status, [static::RETURNED_STATUS, static::REQUEST_RETURNING_STATUS, static::REQUEST_PARTIAL_RETURN_STATUS])) {
        //     $this->transactionsRepository->updateTransactionSuspended($orderId);
        // }

        return $this->success([
            'record' => $this->repository->changeOrderItemStatus($orderItemId, $status, $request),
        ]);
    }
}
