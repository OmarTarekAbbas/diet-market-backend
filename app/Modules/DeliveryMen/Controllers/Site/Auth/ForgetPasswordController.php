<?php

namespace App\Modules\DeliveryMen\Controllers\Site\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;
use App\Modules\Newsletters\Services\Gateways\SMS;
use App\Modules\DeliveryMen\Repositories\DeliveryMensRepository;

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

        $validator = $this->scan($request);

        if ($validator->passes()) {
            $user = $repository->getByModel('phoneNumber', $request->phoneNumber);
            if (!$user) {
                return $this->badRequest('رقم الجوال غير صحيح تحقق وجرب مره اخري');
            }

            if ($user->approved == DeliveryMensRepository::PENDING_STATUS) {
                return $this->badRequest('حسابك قيد المراجعة برجاء الانتظار وسوف يتم اشعاركم بريدياً في حالة الموافقة');
            } elseif ($user->approved == DeliveryMensRepository::REJECTED_STATUS || $user->published == false) {
                // return $this->badRequest('الحساب غير نشط تواصل مع الادمن');
                return $this->badRequest('لا يمكنك استعادة كلمة المرور لهذا الحساب');
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

            $user->resetPasswordCode = mt_rand(1000, 9999);
            $user->countResendCode++;
            $user->canResendCodeAt = $now->addHour();
            $user->save();
            $phoneNumber = DeliveryMensRepository::CODE_PHONE_NUMBER . $request->phoneNumber;

            try {
                $sms = App::make(SMS::class);
                $message = "لقد قمت بعمل طلب إعادة تعيين كلمة المرور الخاص بك في تطبيق ديت ماركت كود التفعيل: {$user->resetPasswordCode} ";
                $sms->send($message, DeliveryMensRepository::CODE_PHONE_NUMBER . $request->phoneNumber);
            } catch (\Exception $exception) {
                return $this->success([
                    'resetCode' => $user->resetPasswordCode,
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
    protected function scan(Request $request)
    {
        return Validator::make($request->all(), [
            'phoneNumber' => 'required',
        ]);
    }
}
