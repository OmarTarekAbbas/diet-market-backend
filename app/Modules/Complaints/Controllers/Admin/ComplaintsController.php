<?php

namespace App\Modules\Complaints\Controllers\Admin;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ComplaintsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'complaints',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                'title' => 'required',
                'orderId' => 'required',
                'reason' => 'required',
                'note' => 'required',
                'images.*' => 'image',
                'userId' => 'required',
                'userType' => 'required|in:client,provider',
            ],
            'store' => [],
            'update' => [],
        ],
    ];

    public function replay(Request $request, $id)
    {
        if ($request->missing(['title', 'message'])) {
            return $this->badRequest(['error' => 'missing param']);
        }

        return $this->success([
            'record' => $this->repository->replay($request, $id),
        ]);
    }
}
