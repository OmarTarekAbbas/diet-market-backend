<?php

namespace App\Modules\DeliveryMen\Controllers\Site\Auth;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use App\Modules\Newsletters\Services\Gateways\SMS;
use App\Modules\DeliveryMen\Repositories\DeliveryMensRepository;
use App\Modules\Users\Controllers\Site\Auth\RegisterController as BaseRegisterController;

class RegisterController extends BaseRegisterController
{
    /**
     * Determine whether the passed values are valid
     *
     * @param Request $request
     * @return mixed
     */
    protected function scan(Request $request)
    {
        $table = $this->repository->getTableName();

        return Validator::make($request->all(), [
            'firstName' => 'required|min:1',
            'lastName' => 'required|min:1',
            'password' => 'required|confirmed|min:6|max:255',
            'phoneNumber' => 'required|unique:' . $table,
            'email' => 'required|unique:' . $table,
            'idNumber' => 'required',
            'nationality' => 'required',
            'image' => 'required|max:' . kbit,
            'birthDate' => 'required',
        ]);
    }

    /**
     * Create new deliveryMan
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->repository = $this->{config('app.users-repo')};

        $this->repository->removeUnverfiedUsersByEmailAndPhoneNumber($request);

        $validator = $this->scan($request);

        if ($validator->fails()) {
            return $this->badRequest($validator->errors());
        }
        $deliveryMan = $this->repository->create($request);
        if ($request->device) {
            $deliveryMan->addNewDeviceToken($request->device);
        }

        return $this->success([
            'verificationCode' => $deliveryMan->verificationCode,
        ]);
    }

    /**
     * Verify Register
     *
     * @param Request $request
     * @return mixed
     */
    public function verify(Request $request)
    {
        try {
            $this->repository = $this->{config('app.users-repo')};
            if ($customer = $this->repository->verify((int) $request->verificationCode, $request->phoneNumber)) {
                $user = $customer;

                return $this->success([
                    $user->accountType() => $this->repository->wrap($customer),
                ]);
            }

            return $this->badRequest([
                'message' => 'كود التحقق غير صحيح',
            ]);
        } catch (Exception $exception) {
            return $this->badRequest($exception->getMessage());
        }
    }

    /**
     * Resend Verification Code
     *
     * @param Request $request
     * @return mixed
     */
    public function resendVerificationCode(Request $request)
    {
        if (!$request->phoneNumber) {
            return $this->badRequest([
                'message' => 'رقم الهاتف غير صحيح',
            ]);
        }

        $this->repository = $this->{config('app.users-repo')};

        $user = $this->repository->getByModel('phoneNumber', $request->phoneNumber);
        if (!$user) {
            return $this->badRequest([
                'message' => 'رقم الهاتف غير صحيح',
            ]);
        }

        $now = Carbon::now();

        // If the current time is greater than the time that user can request resending code, then reset the counter of sent code tries and clear the time limit of sending code.
        if ($now->greaterThan($user->canResendCodeAt)) {
            $user->countResendCode = 0;
            $user->canResendCodeAt = null;
            $user->save();
        }

        // user can try rsending code only 3 times
        if ($user->countResendCode == 3) {
            return $this->badRequest([
                'message' => sprintf('تم استنفاذ المحأولاًت اللازمة لإرسال كود التحقق يجب المحاولة خلال %d دقيقة من الآن أو تواصل مع الادمن', $now->diffInMinutes($user->canResendCodeAt)),
            ]);
        }

        $user->verificationCode = mt_rand(1000, 9999);
        // incrase number of tress by one
        $user->countResendCode++;
        $user->canResendCodeAt = $now->addHour();
        $user->save();

        try {
            $sms = App::make(SMS::class);
            $message = " كود التحقق الخاص بك هو : {$user->verificationCode} ";
            $sms->send($message, DeliveryMensRepository::CODE_PHONE_NUMBER . $request->phoneNumber);
        } catch (\Exception $exception) {
            return $this->badRequest([
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->success([
            'resetCode' => $user->verificationCode,
        ]);
    }

    /**
     * Method allVerify
     *
     * @param Request $request
     *
     * @return void
     */
    public function allVerify(Request $request)
    {
        if ($request->type == 'register') {
            return app('App\Modules\DeliveryMen\Controllers\Site\Auth\RegisterController')->verify($request);
        } elseif ($request->type == 'updatePhone') {
            return app('App\Modules\DeliveryMen\Controllers\Site\UpdateAccountController')->verifyUpdatedPhoneNumber($request);
        } elseif ($request->type == 'forgetPassword') {
            return app('App\Modules\DeliveryMen\Controllers\Site\Auth\ResetPasswordController')->verify($request);
        }
    }

    /**
     * Method resendAllVerify
     *
     * @param Request $request
     *
     * @return void
     */
    public function resendAllVerify(Request $request)
    {
        if ($request->type == 'register') {
            return app('App\Modules\DeliveryMen\Controllers\Site\Auth\RegisterController')->resendVerificationCode($request);
        } elseif ($request->type == 'updatePhone') {
            return app('App\Modules\DeliveryMen\Controllers\Site\UpdateAccountController')->updatePhoneNumber($request);
        } elseif ($request->type == 'forgetPassword') {
            return app('App\Modules\DeliveryMen\Controllers\Site\Auth\ForgetPasswordController')->index($request);
        }
    }
}
