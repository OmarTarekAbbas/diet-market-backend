<?php

namespace App\Modules\Cities\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class CitiesController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'cities';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'published' => true,
            'countryPublished' => true,
            'country' => $request->country,

        ];

        return $this->success([
            'records' => $this->repository->listPublished($options),
            // 'searchArea' => $this->settingsRepository->getSetting('general', 'searchArea') ?: 50,
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
