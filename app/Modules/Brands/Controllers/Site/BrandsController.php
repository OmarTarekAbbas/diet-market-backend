<?php

namespace App\Modules\Brands\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class BrandsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'brands';

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
