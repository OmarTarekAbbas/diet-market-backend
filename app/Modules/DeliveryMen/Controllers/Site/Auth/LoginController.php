<?php

namespace App\Modules\DeliveryMen\Controllers\Site\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;
use App\Modules\DeliveryMen\Repositories\DeliveryMensRepository;

class LoginController extends ApiController
{
    /**
     * Login the user
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $validator = $this->scan($request);
        if ($validator->passes()) {
            $repository = repo(config('app.users-repo'));

            $user = $repository->getByModel('phoneNumber', $request->phoneNumber);




            // dd($user);
            // dd($user->password ,$request->password);
            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->badRequest(trans('auth.invalidData'));
            } else {
                if ($user->dataState['vehicleInfo'] == true && $user->dataState['images'] == true && $user->dataState['bankInfo'] == true) {
                    if ($user->approved == DeliveryMensRepository::PENDING_STATUS) {
                        return $this->badRequest('حسابك قيد المراجعة برجاء الانتظار وسوف يتم اشعاركم بريدياً في حالة الموافقة');
                    } elseif ($user->approved == DeliveryMensRepository::REJECTED_STATUS || $user->published == false) {
                        return $this->badRequest('الحساب غير نشط تواصل مع الادمن');
                    }
                }

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
                    $user->accountType() => $userInfo,
                ]);
            }
        } else {
            return $this->badRequest($validator->errors());
        }
    }

    /**
     * Determine whether the passed values are valid
     *
     * @return mixed
     */
    private function scan(Request $request)
    {
        return Validator::make($request->all(), [
            'phoneNumber' => 'required',
            'password' => 'required|min:6|max:255',
        ]);
    }

    /**
     * Login the user
     *
     * @return mixed
     */
    public function logout(Request $request)
    {
        $user = user();
        $accessTokens = $user->accessTokens;

        $currentAccessToken = $request->authorizationValue();

        foreach ($accessTokens as $key => $accessToken) {
            if ($accessToken['token'] == $currentAccessToken) {
                unset($accessTokens[$key]);

                break;
            }
        }

        $user->accessTokens = array_values($accessTokens);

        $user->save();

        return $this->success();
    }
}
