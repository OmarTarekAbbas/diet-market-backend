<?php

namespace App\Modules\DeliveryMen\Controllers\Site\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Modules\Users\Controllers\Site\Auth\ResetPasswordController as BaseResetPasswordController;

class ResetPasswordController extends BaseResetPasswordController
{
    /**
     * Verify user code
     *
     * @param Request $request
     * @return mixed
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    public function index(Request $request)
    {
        $validator = $this->scan($request);

        if ($validator->passes()) {
            $repository = repo(config('app.users-repo'));

            $user = $repository->getByModel('phoneNumber', (string) strtolower($request->phoneNumber));
            // dd($user);
            if (!$user) {
                return $this->badRequest('رقم الهاتف غير صحيح');
            }

            $user->resetPasswordCode = null;

            $user->updatePassword($request->password);

            Auth::login($user);
            if ($request->device) {
                $this->deliveryMenRepository->addNewDeviceToken($user, $request->device);
            }
            $usersRepository = $this->{config('app.users-repo')};
            $accessToken = $usersRepository->generateAccessToken($user, $request);
            $userInfo = $usersRepository->wrap($user)->toArray($request);
            $userInfo['accessToken'] = $accessToken;
            $user->loginUpdateDateAt = Carbon::now();
            $user->save();

            return $this->success([
                $user->accountType() => $repository->wrap($user),
            ]);
        } else {
            return $this->badRequest($validator->errors());
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
            'phoneNumber' => 'required',
            'password' => 'required|confirmed|min:6|max:255',
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|string
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    public function verify(Request $request)
    {
        if (!$request->has(['phoneNumber', 'verificationCode'])) {
            return $this->badRequest(trans('auth.invalidData'));
        }

        $repository = repo(config('app.users-repo'));

        $user = $repository->getByModel('phoneNumber', (string) strtolower($request->phoneNumber));

        if (!$user || $user->resetPasswordCode != $request->verificationCode) {
            return $this->badRequest(trans('auth.invalidResetCode'));
        }

        return $this->success([
            'success' => true,
        ]);
    }
}
