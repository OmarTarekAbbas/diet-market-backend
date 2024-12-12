<?php

namespace App\Modules\Customers\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Modules\Users\Controllers\Site\UpdateAccountController as BaseUpdateAccountController;

class UpdateAccountController extends BaseUpdateAccountController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'customers';

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
            $user = $this->repository->update($user->id, $request);
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
            'firstName' => 'min:2',
            'lastName' => 'min:2',
            'password' => 'confirmed|min:8',
            'email' => [
                'nullable',
                "unique:{$table},email,{$user->email},email",
            ],
            'phoneNumber' => 'prohibited',
            //            'phoneNumber' => [
            //                'nullable',
            //                "unique:$table,phoneNumber,{$user->phoneNumber},phoneNumber",
            //                'regex:/^([+]966)(50|53|55|51|58|59|54|56|57)(\d{7})$/'
            //            ],
        ], [
            'phoneNumber.numeric' => trans('auth.invalidPhoneNumber'),
            'phoneNumber.regex' => trans('auth.invalidPhoneNumber'),
        ]);
    }

    public function me()
    {
        $user = user();

        return $this->success([
            'customer' => $this->repository->wrap($user),
            'healthydata' => $this->healthyDatasRepository->getByModel('customerId', $user->id),
        ]);
    }

    /**
     * Update phone number and get verification code
     *
     * @param Request $request
     * @return string
     */
    public function updatePhoneNumber(Request $request)
    {
        $validator = $this->validatorUpdatedPhoneNumber($request);

        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        }

        $user = user();

        $verificationCode = $this->repository->sendVerificationToNewNumber($user, $request);

        return $this->success([
            'verificationCode' => $verificationCode,
        ]);
    }

    /**
     * Update phone number and get verification code
     *
     * @param Request $request
     * @return string
     */
    public function verifyUpdatedPhoneNumber(Request $request)
    {
        $user = user();

        if ($user->newVerificationCode != $request->verificationCode) {
            return $this->badRequest(trans('errors.invalidCode'));
        }

        $this->repository->updatePhoneNumber($user, $request);

        return $this->success();
    }

    /**
     * Determine whether the passed values are valid
     *
     * @param Request $request
     * @return mixed
     */
    public function validatorUpdatedPhoneNumber(Request $request)
    {
        $user = user();
        $table = $this->repository->getTableName();

        return Validator::make($request->all(), [
            'phoneNumber' => [
                'required',
                Rule::unique($table)->ignore($user->phoneNumber, 'phoneNumber'),
            ],
        ], [
            'phoneNumber.numeric' => trans('auth.invalidPhoneNumber'),
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
            'healthydata' => $this->healthyDatasRepository->getByModel('customerId', $customer->id),

        ]);
    }

    /**
     * Method indexWeb
     *
     * @param Request $request
     *
     * @return void
     */
    public function indexWeb(Request $request)
    {
        $user = user();

        $this->repository = $this->{config('app.users-repo')};

        $validator = $this->scanWeb($request);

        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        } elseif ($request->password && !$user->isMatchingPassword($request->oldPassword)) {
            return $this->badRequest(trans('auth.invalidPassword'));
        }

        if ($request->oldPassword && $request->password) {
            $user->updatePassword($request->password);
        }

        $user = $this->repository->update($user->id, $request);

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
    protected function scanWeb(Request $request)
    {
        $user = user();

        $table = $this->repository->getTableName();

        if ($request->oldPassword && $request->password) {
            return Validator::make($request->all(), [
                'oldPassword' => 'required',
                'password' => 'confirmed|required|min:6',
            ]);
        } else {
            return Validator::make($request->all(), [
                'firstName' => 'min:2',
                'lastName' => 'min:2',
                'email' => [
                    'nullable',
                    "unique:{$table},email,{$user->email},email",
                ],
            ]);
        }
    }

    /**
     * > Delete the user's profile
     *
     * @return A JSON response with a success message.
     */
    public function deleteProfile()
    {
        $this->repository->delete(user()->id);

        return response()->json(['success' => true]);
    }
}
