<?php

namespace App\Modules\Wallet\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class WalletDeliveryController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'walletDelivery';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'delivery' => user()->id,
        ];

        $delivery = user();
        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->list($options),
            'walletBalance' => $delivery->walletBalance,
            'walletBalanceText' => $delivery->walletBalance . ' ر.س',
            'paginationInfo' => $this->repository->getPaginateInfo(),
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
