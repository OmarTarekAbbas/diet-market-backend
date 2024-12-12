<?php

namespace App\Modules\Wallet\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class WalletProviderController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'walletProvider',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => false, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [],
            'update' => [],
        ],
    ];

    /**
     * add withdraw for client or provider
     *
     * @param Request $request
     * @return string
     */
    public function withdraw(Request $request)
    {
        $validator = $this->scan($request);

        if ($validator->passes()) {
            $withdraw = $this->repository->createAction((int) $request->provider, $request->amount, $request->title, $request->reason, $request->notes, $request->type, 'withdraw');

            return $this->success([
                'record' => $this->repository->wrap($withdraw),
            ]);
        } else {
            return $this->badRequest($validator->errors());
        }
    }

    /**
     * add deposit for client or provider
     *
     * @param Request $request
     * @return string
     */
    public function deposit(Request $request)
    {
        $validator = $this->scan($request);

        if ($validator->passes()) {
            $deposit = $this->repository->createAction((int) $request->provider, $request->amount, $request->title, $request->reason, $request->notes, $request->type, 'deposit');

            return $this->success([
                'record' => $this->repository->wrap($deposit),
            ]);
        } else {
            return $this->badRequest($validator->errors());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function scan(Request $request)
    {
        return Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'title' => 'required',
            'reason' => 'required',
            'provider' => 'required',
            'type' => 'required',
        ]);
    }
}
