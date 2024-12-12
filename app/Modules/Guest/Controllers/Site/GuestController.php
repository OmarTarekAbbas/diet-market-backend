<?php

namespace App\Modules\Guest\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class GuestController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'guests';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [];

        return $this->success([
            'records' => $this->repository->list($options),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function show($id, Request $request)
    {
        return $this->success([
            'record' => $this->repository->get($id),
        ]);
    }

    /**
     * Method guest
     *
     * @param Request $request
     *
     * @return void
     */
    public function guest(Request $request)
    {
        $deviceId = request()->header('DEVICE-ID');

        $customerDeviceId = $this->repository->getByModel('customerDeviceId', $deviceId);
        if ($customerDeviceId) {
            $record = $this->repository->update($customerDeviceId->id, $request);
        } else {
            $record = $this->repository->create($request);
        }

        return $this->success([
            'record' => $record,
            'healthydata' => $this->repository->getByModel('customerDeviceId', $record->customerDeviceId),
        ]);
    }
}
