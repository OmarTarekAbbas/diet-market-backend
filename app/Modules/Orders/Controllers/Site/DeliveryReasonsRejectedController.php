<?php

namespace App\Modules\Orders\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class DeliveryReasonsRejectedController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'deliveryReasonsRejecteds';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'alphabetic' => true,
            'paginate' => false,
        ];

        return $this->success([
            'records' => $this->repository->published($options),
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
