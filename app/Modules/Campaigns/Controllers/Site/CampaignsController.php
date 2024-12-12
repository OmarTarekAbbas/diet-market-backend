<?php

namespace App\Modules\Campaigns\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class CampaignsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'campaigns';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [];

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
