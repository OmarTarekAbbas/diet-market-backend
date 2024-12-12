<?php

namespace App\Modules\Stores\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class StoresController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'stores';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = $request->all();

        return $this->success([
            'records' => $this->repository->listPublished($options),
            'paginationInfo' => $this->repository->getPaginateInfo(),
            'returnOrderStatus' => $this->settingsRepository->getSetting('ReturnedOrder', 'returnSystemStatus'),
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
     * Update Seller's Store Info
     */
    public function updateMyStore(Request $request)
    {
        $user = user();

        if ($user->accountType() != 'StoreManager') {
            return $this->unauthorized(trans('auth.unauthorized'));
        }

        $storeId = $user->store['id'];
        $this->repository->update($storeId, $request->all());

        return $this->success([
            'record' => $this->repository->get($storeId),
        ]);
    }

    /**
     * get Seller's Store Info
     */
    public function getMyStore(Request $request)
    {
        $user = user();

        if ($user->accountType() != 'StoreManager') {
            return $this->unauthorized(trans('auth.unauthorized'));
        }

        $storeId = $user->store['id'];

        return $this->success([
            'record' => $this->repository->get($storeId),
        ]);
    }
}
