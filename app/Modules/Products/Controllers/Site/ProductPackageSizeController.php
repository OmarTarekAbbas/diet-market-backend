<?php

namespace App\Modules\Products\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class ProductPackageSizeController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'productPackageSizes';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->listPublished($options),
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
