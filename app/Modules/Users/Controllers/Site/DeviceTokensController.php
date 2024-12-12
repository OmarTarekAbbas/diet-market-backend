<?php

namespace App\Modules\Users\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class DeviceTokensController extends ApiController
{
    /**
     * Add new device token to current user
     *
     * @param Request $request
     * @return Response|\Illuminate\Http\Response|string
     */
    public function addDeviceToken(Request $request)
    {
        $validator = $this->scan($request);

        try {
            if ($validator->passes()) {
                user()->addNewDeviceToken($request->device);

                return $this->success();
            } else {
                return $this->badRequest($validator->errors());
            }
        } catch (\Exception $exception) {
            return $this->badRequest($exception->getMessage());
        }
    }

    /**
     * Add new device token to current user
     *
     * @param Request $request
     * @return Response|\Illuminate\Http\Response|string
     */
    public function removeDeviceToken(Request $request)
    {
        $validator = $this->scan($request);

        try {
            if ($validator->passes()) {
                user()->removeDeviceToken($request->device);

                return $this->success();
            } else {
                return $this->badRequest($validator->errors());
            }
        } catch (\Exception $exception) {
            return $this->badRequest($exception->getMessage());
        }
    }

    /**
     * Determine whether the passed values are valid
     *
     * @param Request $request
     * @return mixed
     */
    protected function scan(Request $request)
    {
        return Validator::make($request->all(), [
            'device.token' => 'required',
            'device.type' => 'required',
        ]);
    }
}
