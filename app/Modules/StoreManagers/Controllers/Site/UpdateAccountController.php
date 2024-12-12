<?php

namespace App\Modules\StoreManagers\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class UpdateAccountController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'storeManagers';

    /**
     * {@inheritDoc}
     */

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $validator = $this->isValid($request);

        $storeManagers = user();

        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        } elseif ($request->password && !$storeManagers->isMatchingPassword($request->oldPassword)) {
            return $this->badRequest(trans('auth.invalidPassword'));
        } else {
            // dd($storeManagers->id);
            $storeManagers = $this->repository->update($storeManagers->id, $request);
        }

        return $this->success([
            'storeManagers' => $this->repository->wrap($storeManagers),
        ]);
    }

    /**
     * get logged in user
     * @return Response|string
     */
    public function me()
    {
        return $this->success([
            'storeManagers' => $this->repository->wrap(user()),
        ]);
    }

    /**
     * Determine whether the passed values are valid
     *
     * @return mixed
     */
    private function isValid(Request $request)
    {
        $user = user();

        $table = $this->repository->getTableName();

        return Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'confirmed|min:6',
            'email' => [
                'required',
                Rule::unique($table)->ignore($user->email, 'email'),
            ],
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

        $storeManagers = user();
        $repository = repo(config('app.users-repo'));

        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        } elseif (!$storeManagers->isMatchingPassword($request->oldPassword)) {
            return $this->badRequest(trans('auth.invalidPassword'));
        } else {
            $storeManagers->updatePassword($request->password);
        }

        return $this->success([
            'storeManagers' => $repository->wrap($storeManagers),
        ]);
    }

    /**
     * Add new device token to current customer
     *
     * @param Request $request
     * @return Response
     */
    public function addDeviceToken(Request $request)
    {
        if ($request->device) {
            $this->storeManagersRepository->addNewDeviceToken(user(), $request->device);

            return $this->success();
        } else {
            return $this->badRequest(trans('validation.required', 'device'));
        }
    }

    /**
     * Add new device token to current customer
     *
     * @param Request $request
     * @return Response
     */
    public function removeDeviceToken(Request $request)
    {
        if ($request->device) {
            $this->storeManagersRepository->removeDeviceToken(user(), $request->device);

            return $this->success();
        } else {
            return $this->badRequest(trans('validation.required', 'device'));
        }
    }
}