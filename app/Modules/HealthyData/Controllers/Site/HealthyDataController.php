<?php

namespace App\Modules\HealthyData\Controllers\Site;

use Illuminate\Http\Request;
use App\Modules\General\Helpers\Visitor;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class HealthyDataController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'healthyDatas';

    /**
     * {@inheritDoc}
     */
    public function show($id, Request $request)
    {
        return $this->success([
            'record' => $this->repository->get($id),
        ]);
    }

    /**
     * Method createAndUpdateHealthyData
     *
     * @param Request $request
     *create And Update Healthy Data
     * @return array
     */
    public function createAndUpdateHealthyData(Request $request)
    {
        $validator = $this->scan($request);
        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        }

        $healthyDatas = $this->healthyDatas();
        if ($healthyDatas) {
            $record = $this->repository->update($healthyDatas->id, $request);
        } else {
            $record = $this->repository->create($request);
        }
        // $record = [
        //     'id' => $record->id,
        //     'healthInfo' => $record->healthInfo,
        //     'dietTypes' => $record->dietTypes['id'] ?? 0,
        //     'type' => $record->type,
        //     'specialDiet' => $record->specialDiet,
        //     'customerId' => $record->customerId,
        //     'specialDietGrams' => $record->specialDietGrams,
        //     'specialDietPercentage' => $record->specialDietPercentage,
        //     'createdAt' => $record->createdAt,
        //     'updatedAt' => $record->updatedAt,
        // ];
        return $this->success([
            'record' => $record,
        ]);
    }

    /**
     * Method healthyDatas
     *healthy Datas
     * @return object
     */
    public function healthyDatas()
    {
        $customer = user();
        if ($customer) {
            return  $this->repository->getByModel('customerId', $customer->id);
        }

        return $this->repository->getByModel('customerDeviceId', Visitor::getDeviceId());
    }

    /**
     * Determine whether the passed values are valid
     *
     * @return mixed
     */
    protected function scan(Request $request)
    {
        return Validator::make($request->all(), [
            'healthInfo' => 'required',

            'specialDiet' => ['required_if:dietTypes,0', 'array'],
            'specialDiet.fat' => ['required_if:dietTypes,0', 'numeric', 'min:1'],
            'specialDiet.protein' => ['required_if:dietTypes,0', 'numeric', 'min:1'],
            'specialDiet.carbohydrates' => ['required_if:dietTypes,0', 'numeric', 'min:1'],
            'specialDiet.calories' => ['nullable', 'required_if:type,percentage', 'numeric', 'min:1'],
        ]);
    }

    /**
     * Method healthyDataGuest
     *healthy Data Guest
     * @return object
     */
    public function healthyDataGuest()
    {
        $customerDeviceId = $this->repository->getByModel('customerDeviceId', Visitor::getDeviceId());
        if (!$customerDeviceId) {
            return $this->badRequest(trans('errors.notFound'));
        }

        return $this->success([
            'healthydata' => $this->repository->get($customerDeviceId->id),
            'guest' => $customerDeviceId = $this->guestsRepository->getByModel('customerDeviceId', Visitor::getDeviceId()),
        ]);
    }
}
