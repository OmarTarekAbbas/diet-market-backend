<?php

namespace App\Modules\StoreManagers\Controllers\Site\Auth;

use Validator;
use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class LoginController extends ApiController
{
    /**
     * Login the user
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $validator = $this->scan($request);

        if ($validator->passes()) {
            if (!($storeManagers = $this->storeManagersRepository->login($request))) {
                return $this->unauthorized(trans('auth.invalidData'));
            }

            if ($request->device) {
                $this->storeManagersRepository->addNewDeviceToken($storeManagers->resource, $request->device);
            }

            return $this->success([
                'storeManagers' => $storeManagers,
            ]);
        } else {
            return $this->badRequest($validator->errors());
        }
    }

    /**
     * Determine whether the passed values are valid
     *
     * @return mixed
     */
    private function scan(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:8',
        ]);
    }
}
