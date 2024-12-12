<?php

namespace App\Modules\Rewards\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class RewardsController extends AdminApiController
{
    /** TODO: sasdsad
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'rewards',
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
    public function show($id, Request $request)
    {
        $record = $this->repository->get($id);
        $customer = $record->customer;
        $customerId = $customer['id'];

        return $this->success([
            'record' => $record,
            'totalPoints' => $this->rewardsRepository->getBalanceFor($customerId, 'deposit'),
            'usedPoints' => $this->rewardsRepository->getBalanceFor($customerId, 'withdraw'),
            'remainPoints' => $this->rewardsRepository->getRemainBalanceFor($customerId),
            'lastTransaction' => $this->rewardsRepository->getLastTransaction($customerId),
        ]);
    }

    /**
     * add withdraw for customer
     *
     * @param Request $request
     * @return string
     */
    public function withdraw(Request $request)
    {
        $validator = $this->scan($request);

        if ($validator->passes()) {
            // dd((int) $request->customer, $request->points, $request->title, $request->reason, $request->notes, 'withdraw');
            $withdraw = $this->repository->createAction((int) $request->customer, $request->points, $request->title, $request->reason, $request->notes, 'withdraw');

            return $this->success([
                'record' => $this->repository->wrap($withdraw),
            ]);
        } else {
            return $this->badRequest($validator->errors());
        }
    }

    /**
     * add deposit for customer
     *
     * @param Request $request
     * @return string
     */
    public function deposit(Request $request)
    {
        $validator = $this->scan($request);

        if ($validator->passes()) {
            $deposit = $this->repository->createAction((int) $request->customer, $request->points, $request->title, $request->reason, $request->notes, 'deposit');

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
            'points' => 'required|numeric|min:1',
            'title' => 'required',
            'customer' => 'required',
        ]);
    }
}
