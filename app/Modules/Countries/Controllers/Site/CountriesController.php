<?php

namespace App\Modules\Countries\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class CountriesController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'countries';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'published' => true,
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
