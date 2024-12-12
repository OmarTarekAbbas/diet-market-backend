<?php

namespace App\Modules\ContactUs\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ContactUsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'contactUs',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [],
            'update' => [],
        ],
    ];

    /**
     * Reply to the given contact message id
     *
     * @param  int $id
     * @param  Request $request
     * @return Response
     */
    public function reply($id, Request $request)
    {
        $validator = Validator::make($request->all(), ['reply' => 'required']);

        if ($validator->fails()) {
            return $this->badRequest($validator->errors());
        }

        $this->repository->reply($id, $request);

        return $this->success();
    }
}
