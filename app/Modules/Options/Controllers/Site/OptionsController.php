<?php

namespace App\Modules\Options\Controllers\Site;

use Illuminate\Http\Request;
// use HZ\Illuminate\Mongez\Managers\ApiController;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class OptionsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'options',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                'name' => 'required',
            ],
            'store' => [],
            'update' => [],
        ],
    ];

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();

        // $this->middleware('auth', ['except' => ['index','show']]);
        // $this->middleware('isStoreManager', ['except' => ['index','show']]);
    }

    public function filters(Request $request)
    {
        return $this->success([
            'categories' => $this->categoriesRepository->listPublished([
                'paginate' => false,
            ]),
            'options' => $this->repository->list([
                'paginate' => false,
            ]),
        ]);
    }
}
