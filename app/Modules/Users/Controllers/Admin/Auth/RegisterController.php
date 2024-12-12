<?php

namespace App\Modules\Users\Controllers\Admin\Auth;

use Validator;
use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class RegisterController extends ApiController
{
    /**
     * Create new users
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        // $validator = $this->scan($request);

        // if ($validator->passes()) {
        $usersRepository = $this->{config('app.users-repo')};

        $usersGroupRepository = repo('usersGroups');

        $permissions['name'] = 'Super admin';
        $permissions['permissions'] = [
            'usersGroups' => [
                'list' => 1,
                'add' => 1,
                'edit' => 1,
                'delete' => 1,
            ],
        ];
        $group = $usersGroupRepository->create($permissions);

        $request['name'] = 'Admin';
        $request['password'] = '12312300';
        $request['email'] = 'admin@rowaad.net';
        $request['group'] = 1;

        $user = $usersRepository->create($request);
        $userInfo = $usersRepository->wrap($user)->toArray($request);
        $userInfo['accessToken'] = $user->accessTokens[0]['token'];


        return $this->success([
            'user' => $userInfo,
            'data' => $request,
            'group' => $group,
        ]);
    }

    /**
     * Determine whether the passed values are valid
     *
     * @return mixed
     */
    private function scan(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|min:8',
            'email' => 'required|unique:' . config('app.user-type'),
            'invitationCode' => 'exists:' . config('app.user-type'),
        ]);
    }
}
