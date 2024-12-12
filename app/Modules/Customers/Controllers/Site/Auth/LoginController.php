<?php

namespace App\Modules\Customers\Controllers\Site\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Modules\Users\Controllers\Site\Auth\LoginController as BaseLoginController;

class LoginController extends BaseLoginController
{
    // /**
    //  * Login the user
    //  *
    //  * @param Request $request
    //  * @return mixed
    //  */
    // public function index(Request $request)
    // {
    //     $validator = $this->scan($request);
    //     try {
    //         if ($validator->passes()) {
    //             $repository = repo(config('app.users-repo'));

    //             if (!($user = $repository->login($request))) {
    //                 return $this->unauthorized(trans('auth.invalidData'));
    //             }

    //             if ($request->device) {
    //                 $user->resource->addNewDeviceToken($request->device);
    //             }

    //             return $this->success([
    //                 $user->resource->accountType() => $user,
    //             ]);
    //         } else {
    //             return $this->badRequest($validator->errors());
    //         }
    //     } catch (\Exception $exception) {
    //         return $this->badRequest($exception->getMessage());
    //     }
    // }

    public function sendLoginOTP(Request $request)
    {
        $validator = $this->scanOTP($request);

        try {
            if ($validator->passes()) {
                $repository = repo(config('app.users-repo'));

                if (!$repository->exist($request)) {
                    $date['phoneNumber'] = $request->Phone;

                    $repository->create($date);
                }

                $verificationCode = $repository->sendLoginOTP($request);

                if ($verificationCode == false) {
                    return $this->badRequest(trans('auth.invalidData'));
                }

                return $this->success([
                    'verificationCode' => $verificationCode,
                ]);
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
     * @return mixed
     */
    protected function scanOTP(Request $request)
    {
        return Validator::make($request->all(), [
            'Phone' => ['required', 'regex:/^([+]966)(50|53|55|51|58|59|54|56|57)(\d{7})$/'],
        ], [
            'Phone.numeric' => trans('auth.invalidPhoneNumber'),
            'Phone.regex' => trans('auth.invalidPhoneNumber'),
        ]);
    }

    /**
     * Determine whether the passed values are valid
     *
     * @return mixed
     */
    protected function scan(Request $request)
    {
        return Validator::make($request->all(), [
            //     'Phone' => ['required', 'regex:/^([+]966)(50|53|55|51|58|59|54|56|57)(\d{7})$/'],
            //     'verificationCode' => ['required', 'numeric', 'min:4'],
            // ], [
            //     'Phone.numeric' => trans('auth.invalidPhoneNumber'),
            //     'Phone.regex' => trans('auth.invalidPhoneNumber'),
        ]);
    }
}
