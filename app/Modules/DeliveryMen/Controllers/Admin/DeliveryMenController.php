<?php

namespace App\Modules\DeliveryMen\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Modules\Users\Controllers\Admin\UsersController;

class DeliveryMenController extends UsersController
{
    /**
     * A flag that determine if there is a validation on the user group
     *
     * @const bool
     */
    protected const HAS_GROUP = false;

    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'deliveryMen',
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
     * Method deliveryMenAccepted
     *
     * @param $id $id
     *
     * @return void
     */
    public function deliveryMenAccepted($id)
    {
        try {
            $deliveryMenAccepted = $this->repository->deliveryMenAccepted($id);

            return $this->success([
                'message' => 'success',
                'record' => $this->repository->wrap($deliveryMenAccepted),
            ]);
        } catch (Exception $exception) {
            return $this->badRequest($exception->getMessage());
        }
    }

    /**
     * Method deliveryMenRejected
     *
     * @param $id $id
     *
     * @return void
     */
    public function deliveryMenRejected($id)
    {
        try {
            $deliveryMenRejected = $this->repository->deliveryMenRejected($id);

            return $this->success([
                'message' => 'success',
                'record' => $this->repository->wrap($deliveryMenRejected),
            ]);
        } catch (Exception $exception) {
            return $this->badRequest($exception->getMessage());
        }
    }

    /**
     * Method deliveryMenAcceptedData
     *
     * @param $id $id
     *
     * @return void
     */
    public function deliveryMenAcceptedData($id)
    {
        $deliveryMenAcceptedData = $this->repository->deliveryMenAcceptedData($id);

        return $this->success([
            'message' => 'success',
            'record' => $this->repository->wrap($deliveryMenAcceptedData),
        ]);
    }

    /**
     * Method deliveryMenRejectedData
     *
     * @param $id $id
     *
     * @return void
     */
    public function deliveryMenRejectedData($id)
    {
        $deliveryMenRejectedData = $this->repository->deliveryMenRejectedData($id);

        return $this->success([
            'message' => 'success',
            'record' => $this->repository->wrap($deliveryMenRejectedData),
        ]);
    }

    /**
     * Method update
     *
     * @param Request $request
     * update for restaurantManager/categories
     * @return array
     */
    public function update(Request $request, $id)
    {
        $user = $this->deliveryMenRepository->get((int) $id);
        if ((int) $request->published == 0) {
            $checkUpdateStatuOrder = $this->orderDeliveryRepository->checkUpdateStatuOrders($user);
            // dd($checkUpdateStatuOrder);
            if ($checkUpdateStatuOrder) {
                // return $this->badRequest('لا يمكن تغير حالة المندوب لوجود طلبات قيد التوصيل');
                $request->NewPublished = false;
                $request->published = true;
                $request->request->add(['NewPublished' => false, 'published' => true]);
                if ($this->repository->get($id)) {
                    $updateSection = $this->repository->update($id, $request);

                    return $this->success([
                        'record' => $this->deliveryMenRepository->wrap($updateSection),
                        'message' => 'هذا المندوب لديه طلبات قيد التوصيل لذلك سوف يتم تعطيل حسابه فورا بعد تسليم الطلبات',
                    ]);
                }

                return $this->badRequest(trans('errors.notFound'));
            } else {
                $request->request->add(['status' => false]);
                if ($this->repository->get($id)) {
                    $updateSection = $this->repository->update($id, $request);

                    return $this->success([
                        'record' => $this->deliveryMenRepository->wrap($updateSection),
                    ]);
                }

                return $this->badRequest(trans('errors.notFound'));
            }
        } else {
            if ($this->repository->get($id)) {
                $updateSection = $this->repository->update($id, $request);

                return $this->success([
                    'record' => $this->deliveryMenRepository->wrap($updateSection),
                ]);
            }

            return $this->badRequest(trans('errors.notFound'));
        }
    }

    /**
     * Method destroy
     *
     * @param $id $id
     * @param Request $request
     * delete categories
     * @return
     */
    public function destroy($id)
    {
        $user = $this->deliveryMenRepository->get((int) $id);

        if ($user->walletBalance != 0) {
            return $this->badRequest('لا يمكن حذف المندوب لوجود رصيد في محفظة المندوب');
        }

        $checkUpdateStatuOrder = $this->orderDeliveryRepository->checkUpdateStatuOrders($user);
        if ($checkUpdateStatuOrder) {
            return $this->badRequest('لا يمكن حذف المندوب لوجود طلبات قيد التوصيل');
        }

        if ($this->repository->get($id)) {
            $this->repository->delete($id);

            return $this->success();
        }

        return $this->badRequest(trans('errors.notFound'));
    }
}
