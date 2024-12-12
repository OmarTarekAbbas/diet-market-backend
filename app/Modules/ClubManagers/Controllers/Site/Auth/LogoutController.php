<?php

namespace App\Modules\ClubManagers\Controllers\Site\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use HZ\Illuminate\Mongez\Managers\ApiController;

class LogoutController extends ApiController
{
    /**
     * Login the user
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $user = user();
        // dd($user);
        $accessTokens = $user->accessTokens;

        $currentAccessToken = $request->authorizationValue();

        if ($request->device) {
            $this->ClubManagersRepository->removeDeviceToken($user, $request->device);
        }

        foreach ($accessTokens as $key => $accessToken) {
            if ($accessToken['token'] == $currentAccessToken) {
                unset($accessTokens[$key]);

                break;
            }
        }

        $user->accessTokens = array_values($accessTokens);

        $user->save();

        Auth::logout();

        return $this->success();
    }
}
