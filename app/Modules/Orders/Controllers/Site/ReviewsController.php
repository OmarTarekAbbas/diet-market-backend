<?php

namespace App\Modules\Orders\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;
use App\Modules\Orders\Repositories\ReviewsRepository;

class ReviewsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'reviews';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'status' => ReviewsRepository::ACCEPTED_STATUS,
            'id' => $request->id,

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
}
