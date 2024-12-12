<?php

namespace App\Modules\ClubManagers\Controllers\Admin;

use Illuminate\Validation\Rule;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ClubManagersController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'clubManagers',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [
                'name' => 'min:2|required',
                'password' => 'min:8|required',
                'phone' => 'required|unique',
                'email' => 'required|unique|email',
                'club' => 'required|numeric',
            ],
            'update' => [],
        ],
    ];

    /**
     * Method storeValidation
     *
     * @param $request $request
     *
     * @return array
     */
    protected function storeValidation($request): array
    {
        $rules = [
            'email' => [
                'required',
                'email',
                Rule::unique($this->repository->getTableName(), 'email'),
            ],
            'password' => 'required|min:8',
        ];

        return $rules;
    }

    /**
     * Method updateValidation
     *
     * @param $id $id
     * @param $request $request
     *
     * @return array
     */
    protected function updateValidation($id, $request): array
    {
        return [
            'email' => [
                'nullable',
                Rule::unique($this->repository->getTableName(), 'email')->where(function ($query) use ($id) {
                    $query->whereNull('deleted_at');
                    $query->where('id', '!=', (int) $id);
                }),
            ],
            // 'phoneNumber' => [
            //     'required',
            //     Rule::unique($this->repository->getTableName(), 'phoneNumber')->where(function ($query) use ($id) {
            //         $query->whereNull('deleted_at');
            //         $query->where('id', '!=', (int) $id);
            //     }),
            // ],
        ];
    }
}
