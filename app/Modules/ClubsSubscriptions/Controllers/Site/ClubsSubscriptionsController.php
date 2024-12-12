<?php

namespace App\Modules\ClubsSubscriptions\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class ClubsSubscriptionsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'clubsSubscriptions';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'club' => $request->club,
            'user' => $request->user,
        ];

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

    /**
     * Cancel Subscription
     */
    public function changeStatus(Request $request)
    {
        $status = $this->repository->changeStatus($request);

        return $this->success([
            'success' => $status,
            'record' => $this->repository->wrap($this->repository->get($request->id)),
        ]);
    }
}
