<?php

namespace App\Modules\DeliveryMen\Controllers\Site;

use Carbon\Carbon;
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
    protected $repository = 'deliveryMen';

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
            'password' => 'nullable|confirmed|min:6|max:255',
            'email' => [
                'nullable',
                "unique:{$table},email,{$user->email},email",
            ],
            'phoneNumber' => [
                'nullable',
                "unique:{$table},phoneNumber,{$user->phoneNumber},phoneNumber",
            ],
            'idNumber' => 'required',
            'nationality' => 'required',
            'birthDate' => 'required',
        ]);
    }

    /**
     * Method index
     *
     * @param Request $request
     *
     * @return
     */
    public function index(Request $request)
    {
        $deliveryMan = user();
        $this->repository = $this->{config('app.users-repo')};

        $validator = $this->scan($request);

        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        } elseif ($request->password && !$deliveryMan->isMatchingPassword($request->oldPassword)) {
            return $this->badRequest(trans('auth.invalidPassword'));
        } else {
            $verificationCode = $this->updatePhoneNumber($request);
            if ($verificationCode == "oldPhoneNumber") {
                if ($request->type == 'update') {
                    $deliveryMan = $this->repository->updateProfileUpdate($deliveryMan->id, $request);
                    if ($deliveryMan) {
                        return $this->success([
                            'message' => 'تم ارسال التعديلات و في انتظار مراجعة الادارة',
                            'deliveryMen' => $this->repository->wrap($deliveryMan),
                        ]);
                    }
                } else {
                    $deliveryMan = $this->repository->update($deliveryMan->id, $request);

                    return $this->success([
                        $deliveryMan->accountType() => $this->repository->wrap($deliveryMan),
                    ]);
                }
            } elseif (isset($verificationCode['message']) && $verificationCode['message']) {
                return $this->badRequest($verificationCode['message']);
            } else {
                $deliveryMan = $this->repository->updateProfileUpdate($deliveryMan->id, $request);

                return $this->success([
                    'resetCode' => $verificationCode,
                ]);
            }
        }

        if ($request->type == 'update') {
            $deliveryMan = $this->repository->updateProfileUpdate($deliveryMan->id, $request);
            if ($deliveryMan) {
                return $this->success([
                    'message' => 'تم ارسال التعديلات و في انتظار مراجعة الادارة',
                    'deliveryMen' => $this->repository->wrap($deliveryMan),
                ]);
            }
        } else {
            return $this->success([
                $deliveryMan->accountType() => $this->repository->wrap($deliveryMan),
            ]);
        }
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
        $now = Carbon::now();

        // If the current time is greater than the time that user can request resending code, then reset the counter of sent code tries and clear the time limit of sending code.
        if ($now->greaterThan($user->canResendCodeAt)) {
            $user->countResendCode = 0;
            $user->canResendCodeAt = null;
            $user->save();
        }
        // $verificationCode = $this->repository->sendVerificationToNewNumber($user, $request);
        // if ($verificationCode != "oldPhoneNumber") {
        //     if (request()->url() == url('api/deliveryMen/me')) {
        //         // user can try rsending code only 3 times
        //         if ($user->countResendCode >= 3) {
        //             return ['message' => sprintf('تم استنفاذ المحأولاًت اللازمة لإرسال كود التحقق يجب المحاولة خلال %d دقيقة من الآن أو تواصل مع الادمن', $now->diffInMinutes($user->canResendCodeAt))];
        //         }
        //     } else {
        //         return $this->badRequest([
        //             'message' => sprintf('تم استنفاذ المحأولاًت اللازمة لإرسال كود التحقق يجب المحاولة خلال %d دقيقة من الآن أو تواصل مع الادمن', $now->diffInMinutes($user->canResendCodeAt))
        //         ]);
        //     }
        // }


        $verificationCode = $this->repository->sendVerificationToNewNumber($user, $request);
        if (request()->url() == url('api/deliveryMen/me')) {
            return $verificationCode;
        } elseif (request()->url() == url('api/deliveryMen/resend/verify')) {
            if ((isset($verificationCode['message']) && $verificationCode['message'])) {
                return $this->badRequest($verificationCode);
            } else {
                return $this->success([
                    'resetCode' => $verificationCode,
                ]);
            }
        } else {
            return $this->success([
                'resetCode' => $verificationCode,
            ]);
        }
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
     * Update phone number and get verification code
     *
     * @param Request $request
     * @return string
     */
    public function verifyUpdatedPhoneNumber(Request $request)
    {
        $user = user();
        // dd($user);
        if ($user->newVerificationCode != $request->verificationCode) {
            return $this->badRequest(trans('errors.invalidCode'));
        }

        $this->repository->updatePhoneNumber($user, $request);

        return $this->success();
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
            'deliveryMen' => $this->repository->wrap($user),
        ]);
    }

    /**
     * Determine whether the passed values are valid
     *
     * @return mixed
     */
    protected function scanUpdateVehicle(Request $request)
    {
        return Validator::make($request->all(), [
            'vehicleType' => 'required',
            'vehicleBrand' => 'required|min:2',
            'vehicleModel' => 'required|min:2',
            'yearManufacture' => 'required',
            'VehicleSerialNumber' => 'required',
        ]);
    }

    /**
     * Method updateVehicle
     *
     * @param Request $request
     *
     * @return void
     */
    public function updateVehicle(Request $request)
    {
        $user = user();

        $validator = $this->scanUpdateVehicle($request);

        if ($validator->passes()) {
            if ($request->type == 'update') {
                if ($this->repository->updateVehicleUpdate($user, $request)) {
                    return $this->success([
                        'message' => 'تم ارسال التعديلات و في انتظار مراجعة الادارة',
                        'deliveryMen' => $this->repository->wrap($user),
                    ]);
                }
            } else {
                if ($this->repository->updateVehicle($user, $request)) {
                    return $this->success([
                        'deliveryMen' => $this->repository->wrap($user),
                    ]);
                }
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
    protected function scanUpdateImage(Request $request)
    {
        if ($request->type == 'update') {
            return Validator::make($request->all(), [
                'cardIdImage' => 'max:' . kbit,
                'driveryLicenseImage' => 'max:' . kbit,
                'VehicleFrontImage' => 'max:' . kbit,
                'VehicleBackImage' => 'max:' . kbit,
            ]);
        } else {
            return Validator::make($request->all(), [
                'cardIdImage' => 'required|max:' . kbit,
                'driveryLicenseImage' => 'required|max:' . kbit,
                'VehicleFrontImage' => 'required|max:' . kbit,
                'VehicleBackImage' => 'required|max:' . kbit,
            ]);
        }
    }

    /**
     * Method updateImage
     *
     * @param Request $request
     *
     * @return void
     */
    public function updateImage(Request $request)
    {
        $user = user();
        $validator = $this->scanUpdateImage($request);
        if ($validator->passes()) {
            if ($request->type == 'update') {
                $updateImage = $this->repository->get((int) $user->id);

                if ($request->has('cardIdImage')) {
                    $destinationPathCardIdImage = '/data/deliveryMen/' . $user->id . '/';
                    $cardIdImage = date('YmdHis') . uniqid() . '.' . $request->cardIdImage->getClientOriginalExtension();
                    $request->cardIdImage->move(public_path($destinationPathCardIdImage), $cardIdImage);
                } else {
                    $destinationPathCardIdImage = null;
                    $cardIdImage = null;
                }

                if ($request->has('driveryLicenseImage')) {
                    $destinationPathDriveryLicenseImage = '/data/deliveryMen/' . $user->id . '/';
                    $driveryLicenseImage = date('YmdHis') . uniqid() . '.' . $request->driveryLicenseImage->getClientOriginalExtension();
                    $request->driveryLicenseImage->move(public_path($destinationPathDriveryLicenseImage), $driveryLicenseImage);
                } else {
                    $destinationPathDriveryLicenseImage = null;
                    $driveryLicenseImage = null;
                }

                if ($request->has('VehicleFrontImage')) {
                    $destinationPathVehicleFrontImage = '/data/deliveryMen/' . $user->id . '/';
                    $VehicleFrontImage = date('YmdHis') . uniqid() . '.' . $request->VehicleFrontImage->getClientOriginalExtension();
                    $request->VehicleFrontImage->move(public_path($destinationPathVehicleFrontImage), $VehicleFrontImage);
                } else {
                    $destinationPathVehicleFrontImage = null;
                    $VehicleFrontImage = null;
                }

                if ($request->has('VehicleBackImage')) {
                    $destinationPathVehicleBackImage = '/data/deliveryMen/' . $user->id . '/';
                    $VehicleBackImage = date('YmdHis') . uniqid() . '.' . $request->VehicleBackImage->getClientOriginalExtension();
                    $request->VehicleBackImage->move(public_path($destinationPathVehicleBackImage), $VehicleBackImage);
                } else {
                    $destinationPathVehicleBackImage = null;
                    $VehicleBackImage = null;
                }

                // dd($cardIdImage,$extension);
                $updateImage = $this->repository->get((int) $user->id)->update([
                    'newCardIdImage' => $destinationPathCardIdImage . $cardIdImage,
                    'newDriveryLicenseImage' => $destinationPathDriveryLicenseImage . $driveryLicenseImage,
                    'newVehicleFrontImage' => $destinationPathVehicleFrontImage .  $VehicleFrontImage,
                    'newVehicleBackImage' => $destinationPathVehicleBackImage . $VehicleBackImage,
                    'updateData' => true,
                ]);
                if ($updateImage) {
                    return $this->success([
                        'message' => 'تم ارسال التعديلات و في انتظار مراجعة الادارة',
                        'deliveryMen' => $this->repository->wrap($user),
                    ]);
                }
            } else {
                if ($this->repository->update($user->id, $request)) {
                    return $this->success([
                        'deliveryMen' => $this->repository->wrap($user),
                    ]);
                }
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
    protected function scanUpdateBank(Request $request)
    {
        return Validator::make($request->all(), [
            'accountCardName' => 'required|min:2',
            'bankAccountNumber' => 'required|min:24',
        ]);
    }

    /**
     * Method updateBanck
     *
     * @param Request $request
     *
     * @return void
     */
    public function updateBank(Request $request)
    {
        $user = user();
        $validator = $this->scanUpdateBank($request);
        if ($validator->passes()) {
            if ($request->type == 'update') {
                $updateBank = $this->repository->get((int) $user->id)->update([
                    'newAccountCardName' => $request->accountCardName,
                    'newBankAccountNumber' => $request->bankAccountNumber,
                    'updateData' => true,
                ]);
                if ($updateBank) {
                    return $this->success([
                        'message' => 'تم ارسال التعديلات و في انتظار مراجعة الادارة',
                        'deliveryMen' => $this->repository->wrap($user),
                    ]);
                }
            } else {
                if ($this->repository->update($user->id, $request)) {
                    return $this->success([
                        'deliveryMen' => $this->repository->wrap($user),
                    ]);
                }
            }
        } else {
            return $this->badRequest($validator->errors());
        }
    }

    /**
     * Method updateLocation
     *
     * @param Request $request
     *
     * @return void
     */
    public function updateLocation(Request $request)
    {
        $user = user();
        if ($this->repository->update($user->id, $request)) {
            return $this->success([
                'deliveryMen' => $this->repository->wrap($user),
            ]);
        }
    }

    /**
     * Determine whether the passed values are valid
     *
     * @return mixed
     */
    protected function scanUpdatStatus(Request $request)
    {
        return Validator::make($request->all(), [
            'status' => 'required',
        ]);
    }

    /**
     * Method updateStatus
     *
     * @param Request $request
     *
     * @return void
     */
    public function updateStatus(Request $request)
    {
        $user = user();
        if ($request->status == 0) {
            $checkUpdateStatuOrder = $this->orderDeliveryRepository->checkUpdateStatuOrders($user);
            if ($checkUpdateStatuOrder) {
                return $this->badRequest('لديك طلبات قيد التسليم قم بتسليم الطلبات قبل تغير الحالة الي غير مفعل');
            }
        }

        $validator = $this->scanUpdatStatus($request);
        if ($validator->passes()) {
            if ($this->repository->update($user->id, $request)) {
                return $this->success([
                    'deliveryMen' => $this->repository->wrap($user),
                    'countDeliveryNotifications' => user()->totalNotifications ?? 0,

                ]);
            }
        } else {
            return $this->badRequest($validator->errors());
        }
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
            'password' => 'required|confirmed|min:6|max:255',
        ]);

        $deliveryMan = user();
        $repository = repo(config('app.users-repo'));

        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        } elseif (!$deliveryMan->isMatchingPassword($request->oldPassword)) {
            return $this->badRequest(trans('auth.invalidPassword'));
        } else {
            $deliveryMan->updatePassword($request->password);
        }

        return $this->success([
            'deliveryMen' => $repository->wrap($deliveryMan),
        ]);
    }

    /**
     * Method cronJobsForUpdateDelivery
     *
     * @return void
     */
    public function cronJobsForUpdateDelivery()
    {
        $this->repository->cronJobsForUpdateDelivery();
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
