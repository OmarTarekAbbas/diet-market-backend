<?php

namespace App\Modules\Banks\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class BanksController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'banks';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'published' => true,
        ];

        return $this->success([
            'appBanks' => $this->repository->list($options),
            'banks' => $this->settingsRepository->getSetting('general', 'banks') ?: [],
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
