<?php

namespace App\Modules\NutritionSpecialistMangers\Controllers\Site\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class ResetPasswordController extends ApiController
{
    /**
     * Verify user code
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $validator = $this->isValid($request);

        if ($validator->passes()) {
            $repository = repo('nutritionSpecialistMangers');

            $user = $repository->getByModel('resetPasswordCode', (int) $request->resetCode);

            if (!$user) {
                return $this->badRequest(trans('validation.invalidResetCode'));
            }

            $user->resetPasswordCode = null;

            $user->updatePassword($request->password);

            return $this->success([
                'nutritionSpecialistMangers' => $repository->wrap($user),
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
    private function isValid(Request $request)
    {
        return Validator::make($request->all(), [
            'password' => 'required|confirmed|min:8',
        ]);
    }
}
