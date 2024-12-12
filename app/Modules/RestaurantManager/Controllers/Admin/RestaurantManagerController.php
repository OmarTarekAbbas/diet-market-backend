<?php

namespace App\Modules\RestaurantManager\Controllers\Admin;

use Illuminate\Validation\Rule;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class RestaurantManagerController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'restaurantManagers',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [
                'name' => 'required|max:25',
                'password' => 'confirmed|min:6',
                'email' => 'required|unique:restaurant_managers',
            ],
            'update' => [],
        ],
    ];

    /**
     * Make custom validation for store.
     *
     * @param mixed $request
     *
     * @return array
     */
    protected function storeValidation($request): array
    {
        return [
            'email' => [
                Rule::unique($this->repository->getTableName(), 'email')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            'phoneNumber' => [
                'required',
                Rule::unique($this->repository->getTableName(), 'phoneNumber')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
        ];
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
            'phoneNumber' => [
                'required',
                Rule::unique($this->repository->getTableName(), 'phoneNumber')->where(function ($query) use ($id) {
                    $query->whereNull('deleted_at');
                    $query->where('id', '!=', (int) $id);
                }),
            ],
        ];
    }
}
