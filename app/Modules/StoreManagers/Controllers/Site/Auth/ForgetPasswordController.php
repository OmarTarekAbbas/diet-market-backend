<?php

namespace App\Modules\StoreManagers\Controllers\Site\Auth;

use Mail;
use Validator;
use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;

class ForgetPasswordController extends ApiController
{
    /**
     * Send an email to reset password
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $repository = repo(config('app.users-repo'));

        $validator = $this->isValid($repository, $request);

        if ($validator->passes()) {
            $customer = $repository->getByModel('email', $request->email);

            $customer->resetPasswordCode = mt_rand(1000, 9999);
            $customer->save();
            Mail::send([], [], function ($message) use ($customer) {
                $url = env('APP_URL') . '/reset-password/' . $customer->resetPasswordCode;
                $message->to($customer->email)
                    ->subject('إستعادة كلمة المرور')
                // here comes what you want
                    ->setBody("
                    <p>
                        مرحبا بك {$customer->name}
                    </p>
                    <p>
                    لقد إستلمت هذا البريد لكي تستعيد كلمة المرور الخاصة بك
                    </p>
                    </p>
                        إذا لم تكن طلبت تغيير كلمة المرور من فضلك تجاهل هذا البريد
                    </p>
                    <p>
                        <p>كود التفعيل: <strong>{$customer->resetPasswordCode}</strong></p>
                    </p>
                ", 'text/html'); // assuming text/plain
            });

            return $this->success([
                'resetCode' => $customer->resetPasswordCode,
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
    private function isValid(RepositoryInterface $repository, Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|exists:' . $repository->getTableName(),
        ]);
    }
}
