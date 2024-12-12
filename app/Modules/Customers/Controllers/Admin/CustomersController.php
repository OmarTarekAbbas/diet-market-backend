<?php

namespace App\Modules\Customers\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class CustomersController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'customers',
        'listOptions' => [
            'select' => [],
            'filterBy' => ['published'],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [],
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
                'nullable',
                Rule::unique($this->repository->getTableName(), 'email')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            // 'phoneNumber' => 'required|min:12|max:12', Rule::unique($this->repository->getTableName(), 'phoneNumber')->where(function ($query) {
            //     $query->whereNull('deleted_at');
            // }),
            'phoneNumber' => [
                'required',
                Rule::unique($this->repository->getTableName(), 'phoneNumber')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
        ];
    }

    /**
     * Make custom validation for store.
     *
     * @param int $id
     * @param mixed $request
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

    public function verifyPhone($id, Request $request)
    {
        try {
            $customer = $this->repository->verifyPhone($id, $request->isVerifiedPhone);

            return $this->success([
                'customer' => $customer,
            ]);
        } catch (\Exception $exception) {
            return $this->badRequest($exception->getMessage());
        }
    }

    /**
     * Method destroy
     *
     * @param $id $id
     * @param Request $request
     *delete categories
     * @return
     */
    public function destroy($id)
    {
        $destroy = $this->repository->deleteCustomerOrder((int) $id);
        if ($destroy) {
            return $this->badRequest(trans('cart.The customer cannot be deleted due to requests'));
        } else {
            if ($this->repository->get($id)) {
                $this->repository->delete($id);

                return $this->success();
            }

            return $this->badRequest(trans('errors.notFound'));
        }
    }
}
