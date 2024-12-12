<?php

namespace App\Modules\NutritionSpecialist\Controllers\Admin;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class NutritionSpecialistController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'nutritionSpecialists',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [
                'commercialRegisterImage' => 'max:' . kbit,
            ],
            'store' => [],
            'update' => [],
        ],
    ];

    /**
     * Method schedule
     *
     * @param $id $id
     * @param Request $request
     *
     * @return void
     */
    public function schedule($id)
    {
        $nutritionSpecialists = $this->nutritionSpecialistMangersRepository->get($id);

        if (!$nutritionSpecialists) {
            return $this->badRequest(trans('nutritionSpecialist.notfound'));
        }

        $nutritionSpecialists = $this->repository->schedule($nutritionSpecialists);

        return $this->success([
            'nutritionSpecialists' => $nutritionSpecialists,
        ]);
    }

    /**
     * Method getHealthyDataUser
     *
     * @param $id $id
     *
     * @return void
     */
    public function getHealthyDataUser($id)
    {
        $optionStore = [
            'customer' => $id,
            'type' => 'products',
        ];
        $optionFood = [
            'customer' => $id,
            'type' => 'food',
        ];

        return $this->success([
            'customer' => $this->customersRepository->get((int) $id),
            'healthydata' => $this->healthyDatasRepository->getByModel('customerId', (int) $id),
            'orderStore' => $this->ordersRepository->list($optionStore),
            'orderFood' => $this->ordersRepository->list($optionFood),
            'nutritionSpecialistsNotesCustomer' => $this->nutritionSpecialistsCustomerNotesRepository->nutritionSpecialistsNotesCustomer($id),

        ]);
    }

    /**
     * Method index
     *
     * @param Request $request
     *
     * @return void
     */
    public function index(Request $request)
    {
        $options = [
            'name' => $request->name,
            'id' => $request->id,
        ];

        if ($request->page) {
            $options['page'] = (int) $request->page;
        }

        return $this->success([
            'records' => $this->repository->listNutritionSpecialistMangers($options),
            'paginationInfo' => $this->repository->getPaginateInfo(),
        ]);
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
        $destroy = $this->repository->deleteClinicOrder((int) $id);
        if ($destroy) {
            return $this->badRequest(' لا يمكن حذف العيادة من لوحة التحكم  في حالة وجود حجز مفعل');
        } else {
            if ($this->repository->get($id)) {
                $this->repository->delete($id);

                return $this->success();
            }

            return $this->badRequest(trans('errors.notFound'));
        }
    }
}
