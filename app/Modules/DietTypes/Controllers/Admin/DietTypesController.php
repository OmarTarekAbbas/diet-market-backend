<?php

namespace App\Modules\DietTypes\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class DietTypesController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'dietTypes',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                'name' => 'required',
                'proteinRatio' => 'required',
                'carbohydrateRatio' => 'required',
                'fatRatio' => 'required',
                // 'published' => 'required',
                'image' => 'max:' . kbit,
            ],
            'store' => [],
            'update' => [],
        ],
    ];

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
        $destroy = $this->repository->deleteDietType((int) $id);
        if ($destroy) {
            return $this->badRequest('لا يمكنك حذف نوع الدايت بسبب وجود به مستخدمين');
        } else {
            if ($this->repository->get($id)) {
                $this->repository->delete($id);

                return $this->success();
            }

            return $this->badRequest(trans('errors.notFound'));
        }
    }
}
