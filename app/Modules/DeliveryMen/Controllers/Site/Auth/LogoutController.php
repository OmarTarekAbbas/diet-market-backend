<?php

namespace App\Modules\DeliveryMen\Controllers\Site\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\Users\Controllers\Site\Auth\LogoutController as BaseLogoutController;

class LogoutController extends BaseLogoutController
{
    /**
     * Login the user
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $user = user();
        $checkUpdateStatuOrder = $this->orderDeliveryRepository->checkUpdateStatuOrders($user);
        if ($checkUpdateStatuOrder) {
            return $this->badRequest('لديك حالات طلبات قيد التوصيل حاليا لا يمكنك تسجيل الخروج');
        }

        $accessTokens = $user->accessTokens;

        $currentAccessToken = $request->authorizationValue();

        // if ($request->device) {
        //     $user->removeDeviceToken($request->device);
        // }

        if ($request->device) {
            $this->deliveryMenRepository->removeDeviceToken($user, $request->device);
        }

        foreach ($accessTokens as $key => $accessToken) {
            if ($accessToken['token'] == $currentAccessToken) {
                unset($accessTokens[$key]);

                break;
            }
        }

        $user->accessTokens = array_values($accessTokens);

        $user->status = false;
        $user->logoutUpdateDateAt = Carbon::now();
        $user->save();

        Auth::logout();

        // return $this->success();

        return $this->success([
            'success' => true,
            'activateTheDeliveryMenRegistrationSetting' => $this->settingsRepository->getSetting('deliveryMen', 'registrationDeliveryMen'),
        ]);
    }
}
