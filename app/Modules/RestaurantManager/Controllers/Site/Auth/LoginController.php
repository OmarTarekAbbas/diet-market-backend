<?php

namespace App\Modules\RestaurantManager\Controllers\Site\Auth;

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
            if (!($RestaurantManager = $this->restaurantManagersRepository->login($request))) {
                return $this->unauthorized(trans('auth.invalidData'));
            }

            if ($request->device) {
                $this->restaurantManagersRepository->addNewDeviceToken($RestaurantManager->resource, $request->device);
            }

            return $this->success([
                'restaurantManager' => $RestaurantManager,
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
