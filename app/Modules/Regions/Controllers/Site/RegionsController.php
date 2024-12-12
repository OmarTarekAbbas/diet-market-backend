<?php

namespace App\Modules\Regions\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class RegionsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'regions';

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
}
