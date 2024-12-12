<?php

namespace App\Modules\Customers\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class CustomersController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'customers';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'paginate' => false,
        ];

        return $this->success([
            'records' => $this->repository->list($options),
            // 'paginationInfo' => $this->repository->getPaginateInfo(),

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
     * Method sendEmailForRewardCustomer
     *
     * @return void
     */
    public function sendEmailForRewardCustomer()
    {
        $this->repository->sendEmailForRewardCustomer();
    }
}
