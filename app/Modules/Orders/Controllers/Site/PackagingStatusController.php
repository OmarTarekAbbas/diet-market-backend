<?php

namespace App\Modules\Orders\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class PackagingStatusController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'packagingStatus';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'alphabetic' => true,
        ];

        return $this->success([
            'records' => $this->repository->listPublished($options),
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
