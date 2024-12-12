<?php

namespace App\Modules\Restaurants\Controllers\Admin;

use HZ\Illuminate\Mongez\Managers\AdminApiController;

class RestaurantsController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'restaurants',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                'logoImage' => 'max:' . kbit,
                'commercialRegisterImage' => 'max:' . kbit,
                'cover' => 'max:' . kbit,
            ],
            'store' => [
                'name' => 'required|max:25',
                'logoText' => 'required|max:25',
                // 'address' => 'required',
                'logoImage' => 'required',
                'commercialRegisterImage' => 'required',
                'commercialRegisterNumber' => 'required',
                'minimumOrders' => 'required',
                'city' => 'required',
                'delivery' => 'required',
                'workTimes' => 'required',
            ],
            'update' => [
                'name' => 'required|max:25',
            ],
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
        $destroy = $this->repository->deleteResturantOrder((int) $id);
        if ($destroy) {
            return $this->badRequest(trans('cart.The resturant cannot be deleted due to requests'));
        } else {
            if ($this->repository->get($id)) {
                $this->repository->delete($id);

                return $this->success();
            }

            return $this->badRequest(trans('errors.notFound'));
        }
    }
}
