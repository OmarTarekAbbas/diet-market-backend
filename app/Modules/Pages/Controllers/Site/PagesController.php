<?php

namespace App\Modules\Pages\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class PagesController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'pages';

    // /**
    //  * {@inheritDoc}
    //  */
    // public function index(Request $request)
    // {
    //     $options = [];

    //     return $this->success([
    //         'records' => $this->repository->list($options),
    //     ]);
    // }
    
    // /**
    //  * {@inheritDoc}
    //  */
    // public function show($id, Request $request)
    // {
    //     return $this->success([
    //         'record' => $this->repository->get($id),
    //     ]);
    // }

    /**
     * Show Page Content By Name
     */
    public function showByName($name)
    {
        return $this->success([
            'record' => $this->repository->getContentByName($name),
        ]);
    }
}
