<?php

namespace App\Modules\Stores\Controllers\Admin;

use Illuminate\Validation\Rule;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class StoresController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'stores',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                // 'name' => 'required',
                // 'commercialRecordId' => 'required',
                'logo' => 'max:' . kbit,
                'commercialRecordImage' => 'max:' . kbit,
            ],
            'store' => [
                'commercialRecordImage' => 'required|image',
            ],
            'update' => [
                // 'commercialRecordImage' => 'nullable|string',
            ],
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
            'name' => [
                Rule::unique($this->repository->getTableName(), 'name')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
        ];
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
        $destroy = $this->repository->deleteStoreOrder((int) $id);
        if ($destroy) {
            return $this->badRequest(trans('cart.The store cannot be deleted due to requests'));
        } else {
            if ($this->repository->get($id)) {
                $this->repository->delete($id);

                return $this->success();
            }

            return $this->badRequest(trans('errors.notFound'));
        }
    }
}
