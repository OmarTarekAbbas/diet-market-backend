<?php

namespace App\Modules\Users\Controllers\Site\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ResetPasswordNotification;
use HZ\Illuminate\Mongez\Managers\ApiController;
use App\Modules\Newsletters\Services\Gateways\SMS;

class RegisterController extends ApiController
{
    /**
     * Create new users
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->repository = $this->{config('app.users-repo')};

        $validator = $this->scan($request);

        $alreadyRegisteredEmail = $this->repository->getBy('email', $request->email);
        $alreadyRegisteredPhone = $this->repository->getByModel('phoneNumber', $request->phoneNumber);

        if ($alreadyRegisteredEmail || $alreadyRegisteredPhone) {
            $customer = ($alreadyRegisteredPhone) ? $alreadyRegisteredPhone : $alreadyRegisteredEmail;
            // dd($customer);
            if ($customer->isVerified == false) {
                $this->repository->delete($customer->id);
            }
        }

        if ($validator->passes()) {
            $alreadyRegisteredEmail = $this->repository->getBy('email', $request->email);
            $alreadyRegisteredPhone = $this->repository->getByModel('phoneNumber', $request->phoneNumber);

            if ($alreadyRegisteredPhone && $alreadyRegisteredPhone->isVerified == false) {
                $alreadyRegisteredPhone->update($request->all());

                $this->repository->sendVerificationToNewNumber($alreadyRegisteredPhone, $request);

                if ($request->device) {
                    $alreadyRegisteredPhone->addNewDeviceToken($request->device);
                }

                return $this->success([
                    $alreadyRegisteredPhone->accountType() => $this->repository->wrap($alreadyRegisteredPhone),
                ]);
            } elseif ($alreadyRegisteredPhone) {
                return $this->badRequest([
                    'errors' => [
                        [
                            'key' => 'phoneNumber',
                            'value' => 'قيمة رقم الهاتف مستخدمة من قبل',
                        ],
                    ],
                ]);
            } elseif ($alreadyRegisteredEmail) {
                return $this->badRequest([
                    'errors' => [
                        [
                            'key' => 'email',
                            'value' => 'قيمة البريد الالكتروني مستخدمة من قبل',
                        ],
                    ],
                ]);
            }

            $user = $this->repository->create($request);
            $userInfo = $this->repository->wrap($user)->toArray($request);
            $userInfo['accessToken'] = $user->accessTokens[0]['token'];

            if ($request->device) {
                $user->addNewDeviceToken($request->device);
            }

            return $this->success([
                $user->accountType() => $userInfo,
                'healthydata' => $this->healthyDatasRepository->getByModel('customerId', $user->id),
                // 'record' => 'تم إرسال كود تحقق إلى رقم الهاتف الذي قمت بإدخاله'

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
        $table = $this->repository->getTableName();

        return Validator::make($request->all(), [
            'name' => 'required|min:4',
            'password' => 'required|min:8',
            'phoneNumber' => 'required|unique:' . $table,
            'email' => 'required|unique:' . $table,
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
        $this->repository = $this->{config('app.users-repo')};
        // dd($this->customersRepository->verify($request->verificationCode));
        if ($customer = $this->repository->verify((int) $request->verificationCode)) {
            $user = $customer;

            return $this->success([
                $user->accountType() => $this->repository->wrap($customer),
                'healthydata' => $this->healthyDatasRepository->getByModel('customerId', $user->id),

            ]);
        }

        return $this->badRequest([
            'message' => 'كود التحقق غير صحيح',
        ]);
    }

    /**
     * Resend Verification Code
     *
     * @param Request $request
     * @return mixed
     */
    public function resendVerificationCode(Request $request)
    {
        $this->repository = $this->{config('app.users-repo')};

        if ($request->phoneNumber) {
            $user = $this->repository->getByModel('phoneNumber', $request->phoneNumber);

            // dd($user);
            if (!$user) {
                return $this->badRequest([
                    'message' => 'رقم الهاتف غير صحيح',
                ]);
            }

            $user->verificationCode = mt_rand(1000, 9999);
            $user->save();

            try {
                // $url = env('APP_URL') . '/reset-password/' . $user->resetPasswordCode;
                // Notification::send($user, new ResetPasswordNotification($url, $user));

                $sms = App::make(SMS::class);
                $message = " كود التحقق الخاص بك هو : {$user->verificationCode} ";
                $sms->send($message, $request->phoneNumber);
            } catch (\Exception $exception) {
                return $this->success([
                    'code' => $user->verificationCode,
                    'error' => $exception->getMessage(),
                ]);
            }

            return $this->success([
                'code' => $user->verificationCode,
            ]);
        }

        // $user = $this->repository->getByModel('newPhoneNumber', $request->phoneNumber);

        // if ($user) {
        //     $this->repository->sendVerificationToNewNumber($user, $request);

        //     $returnedUser = $this->repository->wrap($user);

        //     return $this->success([
        //         'code' => $returnedUser->newVerificationCode,
        //         $user->accountType() => $returnedUser,
        //     ]);
        // }

        // $user = $this->repository->getByModel('phoneNumber', $request->phoneNumber);

        // if (!$user) {
        //     return $this->badRequest([
        //         'message' => 'يرجى اعادة التسجيل'
        //     ]);
        // }

        // if ($user->isVerified) {
        //     return $this->badRequest([
        //         'message' => 'لقد تم التحقق من رقم هاتفك بالفعل'
        //     ]);
        // }

        // $this->repository->sendVerificationToNewNumber($user, $request);

        // $returnedUser = $this->repository->wrap($user);

        // return $this->success([
        //     'code' => $returnedUser->newVerificationCode,
        //     $user->accountType() => $returnedUser,
        // ]);
    }
}
