<?php

namespace App\Modules\Orders\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class OrderDeliveryController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [

        'repository' => 'orderDelivery',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => null, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [],
            'update' => [],
        ],
    ];

    /**
     * Method index
     *
     * @param Request $request
     *
     * @return void
     */
    public function index(Request $request)
    {
        return $this->success([
            'records' => $this->orderDeliveryRepository->wrapMany($this->orderDeliveryRepository->listOrderForDshboard($request)),
            'paginationInfo' => $this->repository->getPaginateInforOrders($request),
            'countAll' => $this->orderDeliveryRepository->countAll(),
            'countMealHasNotBeenRestaurant' => $this->orderDeliveryRepository->countMealHasNotBeenRestaurant(),
            'countPending' => $this->orderDeliveryRepository->countPending(),
            'countAccepted' => $this->orderDeliveryRepository->countAccepted(),
            'countRejected' => $this->orderDeliveryRepository->countRejected(),
            'countDeliveryOnTheWayRestaurant' => $this->orderDeliveryRepository->countDeliveryOnTheWayRestaurant(),
            'countCompleted' => $this->orderDeliveryRepository->countCompleted(),
            'countNotCompleted' => $this->orderDeliveryRepository->countNotCompleted(),
            'countDeliveryIsNotSet' => $this->orderDeliveryRepository->countDeliveryIsNotSet(),
            'countOrderCancelByCustomer' => $this->orderDeliveryRepository->countOrderCancelByCustomer(),
            'countOrderCancelByAdmin' => $this->orderDeliveryRepository->countOrderCancelByAdmin(),
            'countDontTookMoreThan30Seconds' => $this->orderDeliveryRepository->countDontTookMoreThan30Seconds(),
        ]);
    }

    public function Show($id, Request $request)
    {
        $orderDelivery = $this->repository->get($id);

        if (!$orderDelivery) {
            return $this->badRequest('لا يوجد اي دي');
        }

        return $this->success([
            'records' => $this->orderDeliveryRepository->wrap($this->orderDeliveryRepository->showDashoard($orderDelivery)),
        ]);
    }

    /**
     * Method manualDeliveryAssignment
     *
     * @param $id $id
     * @param Request $request
     *
     * @return void
     */
    public function manualDeliveryAssignment($id, Request $request)
    {
        $orderDelivery = $this->repository->get($id);

        if (!$orderDelivery) {
            return $this->badRequest('لا يوجد اي دي');
        }

        try {
            $validator = $this->scanUpdatAssignment($request);

            if ($validator->passes()) {
                if ($this->repository->updateManualDeliveryAssignment($id, $request)) {
                    return $this->success([
                        'record' => $this->repository->get($id),
                    ]);
                }
            } else {
                return $this->badRequest($validator->errors());
            }
        } catch (Exception $exception) {
            return $this->badRequest($exception->getMessage());
        }
    }

    /**
     * Determine whether the passed values are valid
     *
     * @return mixed
     */
    protected function scanUpdatAssignment(Request $request)
    {
        return Validator::make($request->all(), [
            'deliveryMenId' => 'required',
        ]);
    }

    /**
     * Change the status of the given order id
     *
     * @param int $orderDeliveryId
     * @param string $status
     * @return response
     */
    public function changeStatus($orderDeliveryId, $status, Request $request)
    {
        $order = $this->repository->get((int) $orderDeliveryId);

        if (!$order) {
            return $this->notFound(trans('errors.notFound'));
        }

        $changeStatus = $this->repository->changeStatus($orderDeliveryId, $status, $request);
        if ($changeStatus) {
            $orderRefresh = $this->repository->get((int) $orderDeliveryId);

            return $this->success([
                'record' => $this->orderDeliveryRepository->wrap($this->orderDeliveryRepository->showDashoard($orderRefresh)),
            ]);
        } else {
            return $this->badRequest('المندوب معه اوردر لمطعم اخر لا يمكن توصيل لاكثر من مطعم في نفس الوقت');
        }
    }
}
