<?php

namespace App\Modules\Users\Controllers\Site\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class LoginController extends ApiController
{
    /**
     * Login the user
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $this->repository = $this->{config('app.users-repo')};
        $validator = $this->scan($request);

        try {
            if ($validator->passes()) {
                if (!($user = $this->repository->login($request))) {
                    return $this->unauthorized(trans('auth.invalidData'));
                }

                if ($request->device) {
                    $user->addNewDeviceToken($request->device);
                }

                if (request()->header('DEVICE-ID')) {
                    $repository = repo(config('app.users-repo'));
                    $userDeviceCart = $repository->getByModel('phoneNumber', $request->Phone);
                    $userDeviceCart->deviceCart = request()->header('DEVICE-ID', null);
                    $userDeviceCart->save();
                }


                return $this->success([
                    $user->resource->accountType() => $user,
                    'healthydata' => $this->healthyDatasRepository->getByModel('customerId', $user->id),

                ]);
            } else {
                return $this->badRequest($validator->errors());
            }
        } catch (\Exception $exception) {
            return $this->badRequest($exception->getMessage());
            // return $this->badRequest(trans('cart.missingData.' . $exception));
        }
    }

    /**
     * Determine whether the passed values are valid
     *
     * @return mixed
     */
    protected function scan(Request $request)
    {
        return Validator::make($request->all(), [
            'Phone' => 'required',
            'password' => 'required|min:8',
        ]);
    }
}
