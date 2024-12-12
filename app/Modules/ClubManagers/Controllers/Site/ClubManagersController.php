<?php

namespace App\Modules\ClubManagers\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class ClubManagersController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'clubManagers';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [];

        return $this->success([
            'records' => $this->repository->list($options),
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
