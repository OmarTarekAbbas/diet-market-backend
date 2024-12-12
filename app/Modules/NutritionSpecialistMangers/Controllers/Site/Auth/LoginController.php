<?php

namespace App\Modules\NutritionSpecialistMangers\Controllers\Site\Auth;

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
            if (!($nutritionSpecialistMangers = $this->nutritionSpecialistMangersRepository->login($request))) {
                return $this->unauthorized(trans('auth.invalidData'));
            }

            if ($request->device) {
                $this->restaurantManagersRepository->addNewDeviceToken($nutritionSpecialistMangers->resource, $request->device);
            }

            return $this->success([
                'nutritionSpecialistMangers' => $nutritionSpecialistMangers,
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
