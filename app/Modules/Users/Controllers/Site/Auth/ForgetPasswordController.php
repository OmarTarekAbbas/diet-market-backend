<?php

namespace App\Modules\Users\Controllers\Site\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ResetPasswordNotification;
use HZ\Illuminate\Mongez\Managers\ApiController;
use App\Modules\Newsletters\Services\Gateways\SMS;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;

class ForgetPasswordController extends ApiController
{
    /**
     * Send an email to reset password
     *
     * @param Request $request
     * @return mixed
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    public function index(Request $request)
    {
        $repository = repo(config('app.users-repo'));

        $validator = $this->scan($repository, $request);

        if ($validator->passes()) {
            $user = $repository->getByModel('phoneNumber', $request->Phone);
            if (!$user) {
                return $this->badRequest(trans('errors.theNumberIsNotRegisteredBefore'));
            }
            $user->resetPasswordCode = mt_rand(1000, 9999);
            $user->save();
            // dd($user);

            try {
                // $url = env('APP_URL') . '/reset-password?Phone=' . $request->Phone;
                // Notification::send($user, new ResetPasswordNotification($url, $user));
                $sms = App::make(SMS::class);
                $message = " كود التحقق الخاص بك هو : {$user->resetPasswordCode} ";
                $sms->send($message, $request->Phone);
            } catch (\Exception $exception) {
                return $this->success([
                    'resetCode' => $user->resetPasswordCode,
                    'error' => $exception->getMessage(),
                ]);
            }

            return $this->success([
                'resetCode' => $user->resetPasswordCode,
            ]);
        } else {
            return $this->badRequest($validator->errors());
        }
    }

    /**
     * Determine whether the passed values are valid
     *
     * @param RepositoryInterface $repository
     * @param Request $request
     * @return mixed
     */
    protected function scan(RepositoryInterface $repository, Request $request)
    {
        return Validator::make($request->all(), [
            'Phone' => 'required',
        ]);
    }
}
