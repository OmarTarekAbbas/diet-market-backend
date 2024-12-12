<?php

namespace App\Modules\Customers\Controllers\Site\Auth;

use Illuminate\Http\Request;
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

            $user = $repository->getByModel('phoneNumber', (string) strtolower($request->Phone));

            // if (!$user || $user->resetPasswordCode != $request->resetCode) {
            //     return $this->badRequest(trans('auth.invalidResetCode'));
            // }

            $user->resetPasswordCode = null;

            $user->updatePassword($request->password);

            return $this->success([
                $user->accountType() => $repository->wrap($user),
                'healthydata' => $this->healthyDatasRepository->getByModel('customerId', $user->id),
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
            'Phone' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|string
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    public function verify(Request $request)
    {
        if (!$request->has(['phoneNumber', 'resetCode'])) {
            return $this->badRequest(trans('auth.invalidData'));
        }

        $repository = repo(config('app.users-repo'));

        $user = $repository->getByModel('phoneNumber', (string) strtolower($request->phoneNumber));
        if (!$user || $user->resetPasswordCode != $request->resetCode) {
            return $this->badRequest(trans('auth.invalidResetCode'));
        }

        return $this->success([
            'success' => true,
        ]);
    }
}
