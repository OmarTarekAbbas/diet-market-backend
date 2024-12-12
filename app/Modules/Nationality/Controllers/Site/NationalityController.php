<?php

namespace App\Modules\Nationality\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class NationalityController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'nationalities';

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

    /**
     * Method createNationality
     *
     * @param Request $request
     *
     * @return void
     */
    public function createNationality(Request $request)
    {
        return $this->success([
            'record' => $this->repository->createNationality(),
        ]);
    }
}
