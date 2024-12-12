<?php

namespace App\Modules\NutritionSpecialistMangers\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class NutritionSpecialistMangersController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'nutritionSpecialistMangers',
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
                'email' => 'required|unique:nutrition_specialist_mangers',
                'image' => 'max:' . kbit,

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
     * Method update
     *
     * @param Request $request
     * @param $id $id
     *
     * @return void
     */
    public function update(Request $request, $id)
    {
        if ($this->repository->get($id)) {
            $nutritionSpecialist = $this->repository->get($id);

            if ((int) $request->nutritionSpecialist != (int) $nutritionSpecialist->nutritionSpecialist['id']) {
                $updateNutritionSpecialistManger = $this->repository->updateNutritionSpecialistManger((int) $id);
                if ($updateNutritionSpecialistManger) {
                    return $this->badRequest(trans('cart.The updateNutritionSpecialistManger cannot be update due to requests'));
                }
            }
            $updateSection = $this->repository->update($id, $request);

            return $this->success([
                'record' => $this->repository->wrap($updateSection),
            ]);
        }

        return $this->badRequest(trans('errors.notFound'));
    }
}
