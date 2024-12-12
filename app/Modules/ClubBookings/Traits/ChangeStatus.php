<?php

namespace App\Modules\ClubBookings\Traits;

use Illuminate\Http\Request;

trait ChangeStatus
{
    /**
     * Change the status of the given booking id
     *
     * @param int $bookingId
     * @param string $status
     * @return response
     */
    public function changeStatus($clubBookingId, $status, Request $request)
    {
        $booking = $this->repository->getModel($clubBookingId);

        if (!$booking) {
            return $this->notFound(trans('errors.notFound'));
        }

        if (!$this->repository->nextStatusIs($booking, $status)) {
            return $this->badRequest(trans('errors.cannotChangeStatus'));
        }


        return $this->success([
            'record' => $this->repository->changeStatus($clubBookingId, $status),
        ]);
    }
}
