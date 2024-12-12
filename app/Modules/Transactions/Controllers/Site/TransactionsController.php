<?php

namespace App\Modules\Transactions\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class TransactionsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'transactions';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'store' => $request->store,
            'restaurant' => $request->restaurant,
            'club' => $request->club,
            'nutritionSpecialist' => $request->nutritionSpecialist,
            'type' => $request->type,
            'city' => (int) $request->city,
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        if ($request->type == 'products') {
            $id = $request->store;
        } elseif ($request->type == 'food') {
            $id = $request->restaurant;
        } elseif ($request->type == 'club') {
            $id = $request->club;
        } elseif ($request->type == 'nutritionSpecialist') {
            $id = $request->nutritionSpecialist;
        }

        return $this->success([
            'records' => $this->repository->list($options),
            'paginationInfo' => $this->repository->getPaginateInfo(),
            'amount' => $this->repository->amountServiceProviders($request->type, (int) $id),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function sellerWallet(Request $request)
    {
        $options = [
            'seller' => 15,
            'wallet' => true,
        ];

        return $this->success([
            'records' => $this->repository->list($options),
            'total' => $this->repository->getTotalWallet(15),
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
}
