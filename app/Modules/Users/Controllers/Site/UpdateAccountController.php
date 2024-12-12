<?php

namespace App\Modules\Users\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class UpdateAccountController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'users';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $user = user();

        $this->repository = $this->{config('app.users-repo')};

        $validator = $this->scan($request);

        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        } elseif ($request->password && !$user->isMatchingPassword($request->oldPassword)) {
            return $this->badRequest(trans('auth.invalidPassword'));
        } else {
            $extensionEmail = pathinfo($request->email, PATHINFO_EXTENSION);
            if ($extensionEmail == 'net' || $extensionEmail == 'com' || $extensionEmail == 'org' || $extensionEmail == 'market' || $extensionEmail == 'sa' || $extensionEmail == 'eg') {
                $user = $this->repository->update($user->id, $request);
            } else {
                return $this->badRequest('برجاء التاكد من صيغة الاميل');
            }
        }

        return $this->success([
            $user->accountType() => $this->repository->wrap($user),
            'healthydata' => $this->healthyDatasRepository->getByModel('customerId', $user->id),

        ]);
    }

    /**
     * Determine whether the passed values are valid
     *
     * @return mixed
     */
    protected function scan(Request $request)
    {
        $user = user();

        $table = $this->repository->getTableName();

        return Validator::make($request->all(), [
            'name' => 'required|min:2',
            'password' => 'confirmed|min:8',
            'email' => [
                'required',
                "unique:{$table},email,{$user->email},email",
            ],
            // 'phoneNumber' => [
            //     'required',
            //     "unique:$table,phoneNumber,{$user->phoneNumber},phoneNumber"
            // ],
        ]);
    }

    /**
     * Method me
     *
     * @return void
     */
    public function me()
    {
        $user = user();

        return $this->success([
            'customer' => $this->repository->wrap($user),
        ]);
    }

    /**
     * update password
     * @param Request $request
     * @return \Illuminate\Http\Response|string
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'password' => 'confirmed|required|min:6',
        ]);

        $customer = user();
        $repository = repo(config('app.users-repo'));

        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        } elseif (!$customer->isMatchingPassword($request->oldPassword)) {
            return $this->badRequest(trans('auth.invalidPassword'));
        } else {
            $customer->updatePassword($request->password);
        }

        return $this->success([
            'customer' => $repository->wrap($customer),
        ]);
    }
}
