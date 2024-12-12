<?php

namespace App\Modules\StoreManagers\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class StoreManagersController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'storeManagers',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [
                'firstName' => 'required|max:25',
                'lastName' => 'required|max:25',
                'password' => 'required|confirmed|min:6',
                'phoneNumber' => 'required|unique:StoreManagers',
                'email' => 'required|unique:StoreManagers',
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

    /**
     * Method create
     *
     * @param Request $request
     *
     * @return void
     */
    public function create(Request $request)
    {
        $validator = $this->isValid($request);
        if ($validator->fails()) {
            return $this->badRequest($validator->errors());
        }

        $checkStore = $this->repository->checkStore((int) $request->store);
        if ($checkStore) {
            return $this->badRequest('هذا المتجر بالفعل له مدير آخر');
        } else {
            return $this->success([
                'record' => $this->repository->wrap($this->repository->create($request)),
            ]);
        }
    }

    /**
     * Method update
     *
     * @param Request $request
     * @param $id $id
     *
     * @return void
     */
    public function update(Request $request, $id)
    {
        $validator = $this->isValidUpdate($request, $id);
        if ($validator->fails()) {
            return $this->badRequest($validator->errors());
        }

        if ($this->repository->get((int) $id)) {
            $checkStore = $this->repository->checkStoreUpdate((int) $request->store, (int) $id);
            if ($checkStore) {
                return $this->badRequest('هذا المتجر بالفعل له مدير آخر');
            } else {
                $updateSection = $this->repository->update((int) $id, $request);

                return $this->success([
                    'record' => $this->repository->wrap($updateSection),
                ]);
            }
        }

        return $this->badRequest(trans('errors.notFound'));
    }

    /**
     * Determine whether the passed values are valid
     *
     * @return mixed
     */
    private function isValid(Request $request)
    {
        return Validator::make($request->all(), [
            'firstName' => 'required|max:25',
            'lastName' => 'required|max:25',
            'password' => 'required|confirmed|min:6',
            'password' => 'confirmed|min:6',
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
        ]);
    }

    /**
     * Method isValidUpdate
     *
     * @param Request $request
     * @param $id $id
     *
     * @return mixed
     */
    private function isValidUpdate(Request $request, $id)
    {
        return Validator::make($request->all(), [
            'firstName' => 'required|max:25',
            'lastName' => 'required|max:25',
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
        ]);
    }
}
