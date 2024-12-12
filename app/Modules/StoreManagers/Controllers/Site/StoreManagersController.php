<?php

namespace App\Modules\StoreManagers\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class StoreManagersController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'storeManagers';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [];

        // if ($request->page) {
        //     $options['page'] = (int) $request->page;
        // }
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
}
