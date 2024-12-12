<?php

namespace App\Modules\Wallet\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class WalletController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'wallets';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            user()->AccountType() => user()->id,
        ];

        $customer = user();
        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->list($options),
            'walletBalance' => $customer->walletBalance,
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
