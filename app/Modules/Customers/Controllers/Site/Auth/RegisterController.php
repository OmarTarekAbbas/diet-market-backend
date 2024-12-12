<?php

namespace App\Modules\Customers\Controllers\Site\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Modules\Users\Controllers\Site\Auth\RegisterController as BaseRegisterController;

class RegisterController extends BaseRegisterController
{
    /**
     * Determine whether the passed values are valid
     *
     * @param Request $request
     * @return mixed
     */
    protected function scan(Request $request)
    {
        $table = $this->repository->getTableName();

        $data = $request->all();

        $data['email'] = strtolower($request->email);

        return Validator::make($data, [
            'firstName' => 'min:2',
            'lastName' => 'min:2',
            'password' => 'min:6',
            'phoneNumber' => 'required|max:12|unique:' . $table,
            'email' => 'nullable|unique:' . $table,
        ], [
            'phoneNumber.numeric' => trans('auth.invalidPhoneNumber'),
            'phoneNumber.regex' => trans('auth.invalidPhoneNumber'),
        ]);
    }

    /**
     * Method allVerify
     *
     * @param Request $request
     * Make All Verify
     * I can't do that way return app(App\Modules\Customers\Controllers\Site\Auth\RegisterController::class)->verify($request); *due to an error Target class *[App\Modules\Customers\Controllers\Site\Auth\App\Modules\Customers\Controllers\Site\Auth\RegisterController] does not exist
     * @return void
     */
    public function allVerify(Request $request)
    {
        if ($request->type == 'register') {
            return app('App\Modules\Customers\Controllers\Site\Auth\RegisterController')->verify($request);
        } elseif ($request->type == 'updatePhone') {
            return app('App\Modules\Customers\Controllers\Site\UpdateAccountController')->verifyUpdatedPhoneNumber($request);
        } elseif ($request->type == 'createAddressBook') {
            return app('App\Modules\AddressBook\Controllers\Site\AddressBookController')->verify($request->addressId, $request->verificationCode);
        } elseif ($request->type == 'updateAddressBook') {
            return app('App\Modules\AddressBook\Controllers\Site\AddressBookController')->verifyUpdatedPhoneNumber($request->addressId, $request->verificationCode, $request);
        } elseif ($request->type == 'forgetPassword') {
            return app('App\Modules\Customers\Controllers\Site\Auth\ResetPasswordController')->verify($request);
        }
    }

    /**
     * Method allVerify
     *
     * @param Request $request
     * Make All Verify
     * I can't do that way return app(App\Modul es\Customers\Controllers\Site\Auth\RegisterController::class)->verify($request); *due to an error Target class *[App\Modules\Customers\Controllers\Site\Auth\App\Modules\Customers\Controllers\Site\Auth\RegisterController] does not exist
     * @return void
     */
    public function resendAllVerify(Request $request)
    {
        // dd($request->type);
        if ($request->type == 'register') {
            return app('App\Modules\Customers\Controllers\Site\Auth\RegisterController')->resendVerificationCode($request);
        } elseif ($request->type == 'updatePhone') {
            return app('App\Modules\Customers\Controllers\Site\UpdateAccountController')->updatePhoneNumber($request);
        } elseif ($request->type == 'createAddressBook') {
            return app('App\Modules\AddressBook\Controllers\Site\AddressBookController')->ResndVerifiAddAdress($request->addressId, $request);
        } elseif ($request->type == 'updateAddressBook') {
            return app('App\Modules\AddressBook\Controllers\Site\AddressBookController')->updatePhoneNumber($request->addressId, $request);
        } elseif ($request->type == 'forgetPassword') {
            return app('App\Modules\Customers\Controllers\Site\Auth\ForgetPasswordController')->index($request);
        }
    }
}
