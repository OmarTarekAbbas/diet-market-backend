<?php

namespace App\Modules\Transactions\Controllers\Admin;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class TransactionsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'transactions',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => true, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [],
            'update' => [],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'store' => $request->store,
            'restaurant' => $request->restaurant,
            'club' => $request->club,
            'nutritionSpecialist' => $request->nutritionSpecialist,
            'type' => $request->type,
            'city' => (int) $request->city,
        ];
        
        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->list($options),
            'paginationInfo' => $this->repository->getPaginateInfo(),
            'amountDiteMarket' => $this->repository->amountDiteMarket(),
        ]);
    }
}
