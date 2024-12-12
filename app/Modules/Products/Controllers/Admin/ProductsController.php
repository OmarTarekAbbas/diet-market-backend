<?php

namespace App\Modules\Products\Controllers\Admin;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class ProductsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'products',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                // 'discount.type' => 'in:amount,percentage',
                'name' => 'required',
                'price' => 'required',
                'discount.value' => 'required_if:discount.type,percentage,amount',
                'discount.startDate' => 'required_if:discount.type,percentage,amount|date',
                'discount.endDate' => 'required_if:discount.type,percentage,amount|date',
                'priceInSubscription' => 'required_if:inSubscription,true',
                'minQuantity' => 'required|numeric|min:1',
                // 'unit' => 'required',
                'category' => 'required',
            ],
            'store' => [
                'images' => 'required|array|max:' . kbit,
                'images.*' => 'image|max:' . kbit,
            ],
            'update' => [
                'images' => 'nullable|array|max:' . kbit,
                'images.*' => 'image|max:' . kbit,
            ],
        ],
    ];

    /**
     * Method update
     *
     * @param Request $request
     * update for restaurantManager/categories
     * @return array
     */
    public function update(Request $request, $id)
    {
        try {
            if ($this->repository->get($id)) {
                $updateSection = $this->repository->update($id, $request);
                return $this->success([
                    'record' => $this->repository->wrap($updateSection),
                    'success' => true,
                ]);
            }

            return $this->badRequest(trans('errors.notFound'));
        } catch (\Exception $exception) {
            return $this->badRequest($exception->getMessage());
        }
    }

    /**
     * Method store
     *
     * @param Request $request
     *
     * @return void
     */
    public function store(Request $request) 
    {
        try {
            $updateSection = $this->repository->create($request);

            return $this->success([
                'record' => $this->repository->wrap($updateSection),
                'success' => true,
            ]);
        } catch (\Exception $exception) {
            return $this->badRequest($exception->getMessage());
        }
    }
}
