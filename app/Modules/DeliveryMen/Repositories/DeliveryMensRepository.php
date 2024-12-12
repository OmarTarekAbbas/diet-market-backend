<?php

namespace App\Modules\DeliveryMen\Repositories;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Modules\Orders\Models\OrderDelivery;
use App\Modules\Users\Traits\Auth\AccessToken;
use App\Modules\Nationality\Models\Nationality;
use App\Modules\VehicleType\Models\VehicleType;
use App\Modules\Newsletters\Services\Gateways\SMS;
use App\Modules\DeliveryMen\Models\DeliveryMan as Model;
use App\Modules\DeliveryMen\Filters\DeliveryMan as Filter;
use App\Modules\DeliveryMen\Models\DeviceToken as DeviceToken;
use App\Modules\DeliveryMen\Resources\DeliveryMan as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class DeliveryMensRepository extends RepositoryManager implements RepositoryInterface
{
    use AccessToken;

    /**
     * {@inheritDoc}
     */
    const NAME = 'deliveryMen';

    /**
     * {@inheritDoc}
     */
    const MODEL = Model::class;

    /**
     * {@inheritDoc}
     */
    const RESOURCE = Resource::class;

    /**
     * Set the columns of the data that will be auto filled in the model
     *
     * @const array
     */
    const DATA = [
        'firstName', 'lastName',  'email', 'password',  'idNumber', 'birthDate', 'vehicleBrand', 'vehicleModel', 'accountCardName', 'approved', 'dataState', 'phoneNumber', 'name', 'bankAccountNumber', 'VehicleSerialNumber',
    ];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = ['image', 'cardIdImage', 'driveryLicenseImage', 'VehicleFrontImage', 'VehicleBackImage'];

    /**
     * Auto fill the following columns as arrays directly from the request
     * It will encoded and stored as `JSON` format,
     * it will be also auto decoded on any database retrieval either from `list` or `get` methods
     *
     * @const array
     */
    const ARRAYBLE_DATA = [];

    /**
     * Set columns list of integers values.
     *
     * @cont array
     */
    const INTEGER_DATA = ['codePhoneNumber',  'yearManufacture', 'countResendCode', 'requested'];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = ['walletBalance'];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = ['published', 'status', 'NewPublished'];

    /**
     * Geo Location data
     *
     * @const array
     */
    const LOCATION_DATA = ['location', 'profileLocation', 'newProfileLocation'];

    /**
     * Set columns list of date values.
     *
     * @cont array
     */
    const DATE_DATA = [];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [
        'nationality' => Nationality::class,
        'vehicleType' => VehicleType::class,
    ];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel::class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [
        'orders' => OrderDelivery::class,
    ];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = [
        'firstName', 'lastName', 'phoneNumber', 'codePhoneNumber', 'email', 'password', 'idNumber', 'bankAccountNumber', 'birthDate', 'vehicleBrand', 'vehicleModel', 'yearManufacture', 'VehicleSerialNumber', 'accountCardName', 'image', 'cardIdImage', 'driveryLicenseImage', 'VehicleFrontImage', 'VehicleBackImage', 'published', 'approved', 'requested', 'location', 'nationality', 'vehicleType', 'accessTokens', 'accessToken', 'dataState', 'countResendCode', 'status', 'orders', 'name', 'NewPublished', 'walletBalance', 'walletBalanceDeposit', 'walletBalanceWithdraw',
    ];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [
        'int' => ['id'],
        'like' => [
            'firstName',
            'lastName',
            'phoneNumber',
            'name' => ['firstName', 'lastName'],
            'email',
        ],
        'boolean' => [
            'published',
            'status',
        ],
    ];

    /**
     * Set all filter class you will use in this module
     *
     * @const array
     */
    const FILTERS = [
        Filter::class,
    ];

    /**
     * Determine wether to use pagination in the `list` method
     * if set null, it will depend on pagination configurations
     *
     * @const bool
     */
    const PAGINATE = false;

    /**
     * Number of items per page in pagination
     * If set to null, then it will taken from pagination configurations
     *
     * @const int|null
     */
    const ITEMS_PER_PAGE = null;

    const CODE_PHONE_NUMBER = '966';

    const PENDING_STATUS = 'pending';

    const APPROVED_STATUS = 'Approved';

    const REJECTED_STATUS = 'Rejected';

    /**
     * Set any extra data or columns that need more customizations
     * Please note this method is triggered on create or update call
     *
     * @param   mixed $model
     * @param   \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {

        if ($request->phoneNumber) {
            $model->phoneNumber = $request->phoneNumber;
            $model->codePhoneNumber = DeliveryMensRepository::CODE_PHONE_NUMBER;
        }
        if (!$model->id) {
            // generate 6 digits
            $model->verificationCode = mt_rand(1000, 9999);
            $model->isVerified = $request->isVerified ?: false;
            $model->approved = DeliveryMensRepository::PENDING_STATUS;
            $model->walletBalance = 0;
            $model->published = false;
            // $model->NewPublished = false;
            $model->status = false;
            $model->name = $request->firstName . $request->lastName;
            $model->requested = 0;
            $model->dataState = [
                'vehicleInfo' => false,
                'images' => false,
                'bankInfo' => false,
            ];
        }

        if ($request->driveryLicenseImage || $request->VehicleFrontImage) {
            $dataState = $model->dataState;
            $dataState['images'] = true;
            $model->dataState = $dataState;
        } elseif ($request->accountCardName || $request->bankAccountNumber) {
            $dataState = $model->dataState;
            $dataState['bankInfo'] = true;
            $model->dataState = $dataState;
        }
    }

    /**
     * Clear order from being delivered
     *
     * @param  Order $order
     * @return void
     */
    public function clearOrderFromBeingDelivered($order)
    {
        $deliveryMan = user();

        if ($deliveryMan->accountType() !== 'deliveryMan') {
            $deliveryMan = $this->getModel($order->deliveryMan['id']);
        }

        $deliveryMan->disassociate($order, 'deliveringOrders')->save();
    }

    /**
     * Add new device to customer
     * Device options contains: type: ios|android, token: string
     *
     * @param deliveryman $deliveryman
     * @param array $deviceOptions
     * @return void
     */
    public function addNewDeviceToken(Model $user, array $deviceOptions)
    {
        if ($this->getDeviceToken($user, $deviceOptions)) {
            return;
        }

        $deviceToken = new DeviceToken([
            'serviceProviderId' => $user->id,
            'type' => $deviceOptions['type'],
            'token' => $deviceOptions['token'],
        ]);

        $deviceToken->save();

        $user->associate($deviceToken, 'devices')->save();
    }

    /**
     * Get device token for the given customer and device options
     *
     * @param deliveryman $deliveryman
     * @param array $deviceOptions
     * @return DeviceToken|null
     */
    public function getDeviceToken(Model $deliveryman, array $deviceOptions): ?DeviceToken
    {
        return DeviceToken::where('token', $deviceOptions['token'])->where('serviceProviderId', $deliveryman->id)->where('type', $deviceOptions['type'])->first();
    }

    /**
     * Remove device from customer
     *
     * @param deliveryman
     * @param array $deviceOptions
     * @return void
     * @throws Exception
     */
    public function removeDeviceToken(Model $deliveryman, array $deviceOptions)
    {
        $deviceToken = $this->getDeviceToken($deliveryman, $deviceOptions);
        if (!$deviceToken) {
            return;
        }

        $deliveryman->disassociate($deviceToken, 'devices')->save();

        $deviceToken->delete();
    }

    /**
     * Method updateWalletBalanceForDelivery
     * update Wallet Balance For Delivery
     * @param int $deliveryMen
     * @return void
     */
    public function updateWalletBalanceForDelivery(int $deliveryMen)
    {
        $user = user() ?? $this->deliveryMenRepository->getModel($deliveryMen);
        if ($user->accountType() === 'deliveryMen' && $user->id === $deliveryMen) {
            $deliveryMen = $user;
        } else {
            $deliveryMen = $this->getModel($deliveryMen);
        }
        $deliveryMen->walletBalanceDeposit = round($this->walletDeliveryRepository->getBalanceFor($deliveryMen->id, 'deposit'), 2);

        $deliveryMen->walletBalanceWithdraw = round($this->walletDeliveryRepository->getBalanceFor($deliveryMen->id, 'withdraw'), 2);

        $deliveryMen->walletBalance = round($deliveryMen->walletBalanceDeposit - $deliveryMen->walletBalanceWithdraw, 2);

        $deliveryMen->save();
    }

    /**
     * Add order to list of delivering orders at the moment
     *
     * @param  Order $order
     * @param  DeliverMan $deliveryMan
     * @return void
     */
    public function MarkOrderAsBeingDelivered($order, $deliveryMan)
    {
        $deliveryMan->reassociate($order->pluck(['id', 'status', 'requestReturning']), 'deliveringOrders')->save();
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
        $this->query->where('isVerified', true);

        if ($this->option('approved')) {
            $this->query->where('approved', DeliveryMensRepository::APPROVED_STATUS);
        }

        if ($vehicleType = $this->option('vehicleType')) {
            // $this->query->where('vehicleType.name.0.text', $vehicleType);
            $this->query->where('vehicleType.id', (int) $vehicleType);
        }

        if ($delegateStatus = $this->option('delegateStatus')) {
            if ($delegateStatus == 'false') {
                $status = false;
            } else {
                $status = true;
            }
            $this->query->where('published', $status);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function onCreate($user, $request)
    {
        $this->sendOTP($user->phoneNumber, $user->verificationCode);

        $this->generateAccessToken($user, $request);
    }

    /**
     * Method verify
     *
     * @param int $verificationCode
     *
     * @return void
     */
    public function verify(int $verificationCode, $phoneNumber)
    {
        $customer = $this->getQuery()->where('verificationCode', $verificationCode)->where('phoneNumber', $phoneNumber)->first();

        if (!$customer) {
            return false;
        }
        // dd(filter_var($customer->email, FILTER_VALIDATE_EMAIL));

        // clear the verification code
        $customer->verificationCode = null;
        $customer->isVerified = true;
        $customer->published = true;
        $customer->NewPublished = true;
        $customer->save();

        $this->generateAccessToken($customer);

        if (false !== strpos(request()->url(), 'master')) {
            $URL = 'https://dashboard.diet.market/delegates/' . $customer->id;
        } elseif (false !== strpos(request()->url(), 'test')) {
            $URL = 'https://dashboard.diet.market/delegates/' . $customer->id;
        } else {
            $URL = 'https://dashboard.diet.market/delegates/' . $customer->id;
        }

        $activateSendingMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'activateSendingMail');
        $storeNameMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'storeNameMail');
        if ($activateSendingMail == true) {
            $extensionEmail = pathinfo($customer->email, PATHINFO_EXTENSION);
            if ($extensionEmail == 'net' || $extensionEmail == 'com' || $extensionEmail == 'org' || $extensionEmail == 'market' || $extensionEmail == 'sa' || $extensionEmail == 'eg') {
                Mail::send([], [], function ($message) use ($customer, $storeNameMail) {
                    $message->to($customer->email)
                        ->subject('الموافقة مطلوبة من المسؤول')
                        // here comes what you want
                        ->setBody("
                    <p>
                        مرحبا يا  [{$customer->firstName}]
                    </p>
                    </br>
                    </br>
                    <hr>
                    </br>
                    <p>
                    تم إرسال طلبك للموافقة عليه إلى المسؤول ، وبعد الموافقة يمكنك تسجيل الدخول إلى حسابك.                                        </p>

                                        شكرا مع تحياتي
                                        [{$storeNameMail}]
                ", 'text/html'); // assuming text/plain
                });
            } else {
                throw new Exception('برجاء التاكد من صيغة الاميل');
            }

            $deliveryManger = $this->usersRepository->getQuery()->where('type', 'delivery')->first();
            $adminEmail = $this->usersRepository->getByModel('name', 'admin');

            if ($deliveryManger) {
                $extensionEmail = pathinfo($deliveryManger->email, PATHINFO_EXTENSION);
                if ($extensionEmail == 'net' || $extensionEmail == 'com' || $extensionEmail == 'org' || $extensionEmail == 'market' || $extensionEmail == 'sa' || $extensionEmail == 'eg') {
                    Mail::send([], [], function ($message) use ($customer, $deliveryManger, $storeNameMail, $URL) {
                        $message->to($deliveryManger->email)
                            ->subject('تم تسجيل مندوب توصيل جديد')
                            // here comes what you want
                            ->setBody("
                        <p>
                            مرحبا يا  [{$deliveryManger->name}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        <p>
                        تم تسجيل مندوب توصيل جديد بنجاح
                        [{$customer->firstName}]  يمكنك عرض بيانات المندوب <a href='{$URL}'>{$URL}</a>
                                        </p>

                                            [{$storeNameMail}]
                    ", 'text/html'); // assuming text/plain
                    });
                } else {
                    throw new Exception('برجاء التاكد من صيغة الاميل');
                }

                $extensionEmail = pathinfo($adminEmail->email, PATHINFO_EXTENSION);
                if ($extensionEmail == 'net' || $extensionEmail == 'com' || $extensionEmail == 'org' || $extensionEmail == 'market' || $extensionEmail == 'sa' || $extensionEmail == 'eg') {
                    Mail::send([], [], function ($message) use ($customer, $adminEmail, $storeNameMail, $URL) {
                        $message->to($adminEmail->email)
                            ->subject('تم تسجيل مندوب توصيل جديد')
                            // here comes what you want
                            ->setBody("
                        <p>
                            مرحبا يا  [{$adminEmail->name}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        <p>
                        تم تسجيل مندوب توصيل جديد بنجاح
                        [{$customer->firstName}]  يمكنك عرض بيانات المندوب  <a href='{$URL}'>{$URL}</a>
                                        </p>

                                            [{$storeNameMail}]
                    ", 'text/html'); // assuming text/plain
                    });
                } else {
                    throw new Exception('برجاء التاكد من صيغة الاميل');
                }
            } else {
                $extensionEmail = pathinfo($adminEmail->email, PATHINFO_EXTENSION);
                if ($extensionEmail == 'net' || $extensionEmail == 'com' || $extensionEmail == 'org' || $extensionEmail == 'market' || $extensionEmail == 'sa' || $extensionEmail == 'eg') {
                    Mail::send([], [], function ($message) use ($customer, $adminEmail, $storeNameMail, $URL) {
                        $message->to($adminEmail->email)
                            ->subject('تم تسجيل مندوب توصيل جديد')
                            // here comes what you want
                            ->setBody("
                        <p>
                            مرحبا يا  [{$adminEmail->name}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        <p>
                        تم تسجيل مندوب توصيل جديد بنجاح
                        [{$customer->firstName}]  يمكنك عرض بيانات المندوب  <a href='{$URL}'>{$URL}</a>
                                        </p>

                                            [{$storeNameMail}]
                    ", 'text/html'); // assuming text/plain
                    });
                } else {
                    throw new Exception('برجاء التاكد من صيغة الاميل');
                }
            }
        }

        return $customer;
    }

    /**
     * Method verifyPhone
     *
     * @param int $customerId
     * @param bool $isVerified
     *
     * @return void
     */
    public function verifyPhone(int $customerId, bool $isVerified = true)
    {
        $customer = $this->getModel($customerId);

        if (!$customer) {
            throw new Exception(trans('auth.invalidData'));
        }

        $customer->isVerified = $isVerified;

        $customer->save();

        return $this->wrap($customer->refresh());
    }

    /**
     * {@inheritDoc}
     */
    public function onUpdate($model, $request, $oldModel)
    {
        if ($model->published == false) {
            $model->accessTokens = [];
            $model->accessToken = null;
            $model->save();
        }
    }

    /**
     * Send new verification phone number
     *
     * @param Request $request
     * @return false|\Illuminate\Http\Resources\Json\JsonResource|\JsonResource
     */
    public function sendLoginOTP(Request $request)
    {
        $filter = [];

        $value = strtolower($request->phoneNumber);

        // if the phone number is numeric, then search in phone numbers
        // otherwise search in email
        if (is_numeric($value) || is_numeric(str_replace('+', '', $value))) {
            $filter[] = 'phoneNumber';
        } else {
            $filter[] = 'email';
        }

        $filter[] = $value;

        $customer = Model::where('phoneNumber', $request->phoneNumber)->first();


        if (!$customer) {
            return false;
        }

        $customer->verificationCode = mt_rand(1000, 9999);
        $customer->isVerified = false;

        $customer->save();

        $this->sendOTP($customer->phoneNumber, $customer->verificationCode);

        return $customer->verificationCode;
    }

    /**
     * Method updateVehicle
     *
     * @param $user $user
     * @param $request $request
     *
     * @return void
     */
    public function updateVehicle($user, $request)
    {
        $vehicleType = $this->vehicleTypesRepository->get((int) $request->vehicleType);
        $dataState = $user->dataState;
        $dataState['vehicleInfo'] = true;

        return $user->update([
            'vehicleType' => $vehicleType->sharedInfo(),
            'vehicleBrand' => $request->vehicleBrand,
            'vehicleModel' => $request->vehicleModel,
            'yearManufacture' => $request->yearManufacture,
            'VehicleSerialNumber' => $request->VehicleSerialNumber,
            'dataState' => $dataState,
        ]);
    }

    /**
     * Method updateVehicleUpdate
     *
     * @param $user $user
     * @param $request $request
     *
     * @return void
     */
    public function updateVehicleUpdate($user, $request)
    {
        $vehicleType = $this->vehicleTypesRepository->get((int) $request->vehicleType);
        $dataState = $user->dataState;
        $dataState['vehicleInfo'] = true;

        return $user->update([
            'newVehicleType' => $vehicleType->sharedInfo(),
            'newVehicleBrand' => $request->vehicleBrand,
            'newVehicleModel' => $request->vehicleModel,
            'newYearManufacture' => $request->yearManufacture,
            'newVehicleSerialNumber' => $request->VehicleSerialNumber,
            'updateData' => true,
        ]);
    }

    /**
     * Method deliveryMenAccepted
     *
     * @param int $id
     *
     * @return void
     */
    public function deliveryMenAccepted(int $id)
    {
        $deliveryMenAccepted = Model::find($id);

        $deliveryMenAccepted->approved = DeliveryMensRepository::APPROVED_STATUS; // 0 = NotShow / 1 = Accepted / 2 = Rejected

        if ($deliveryMenAccepted->save()) {
            $activateSendingMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'activateSendingMail');
            $storeNameMail = $this->settingsRepository->getSetting('deliveryMenEmails', 'storeNameMail');
            if ($activateSendingMail == true) {
                $extensionEmail = pathinfo($deliveryMenAccepted->email, PATHINFO_EXTENSION);
                if ($extensionEmail == 'net' || $extensionEmail == 'com' || $extensionEmail == 'org' || $extensionEmail == 'market' || $extensionEmail == 'sa' || $extensionEmail == 'eg') {
                    Mail::send([], [], function ($message) use ($deliveryMenAccepted, $storeNameMail) {
                        $message->to($deliveryMenAccepted->email)
                            ->subject('تم تسجيل حسابك بنجاح')
                            // here comes what you want
                            ->setBody("
                        <p>
                            مرحبا يا  [{$deliveryMenAccepted->firstName}]
                        </p>
                        </br>
                        </br>
                        <hr>
                        </br>
                        <p>
                        تم تسجيل حسابك بنجاح،الآن يمكنك تسجيل الدخول في حسابك
                        </p>

                                            شكرا مع تحياتي
                                            [{$storeNameMail}]

                    ", 'text/html'); // assuming text/plain
                    });
                } else {
                    throw new Exception('برجاء التاكد من صيغة الاميل');
                }
            }
        }

        return $deliveryMenAccepted;
    }

    /**
     * Method deliveryMenRejected
     *
     * @param int $id
     *
     * @return void
     */
    public function deliveryMenRejected(int $id)
    {
        $deliveryMenAccepted = Model::find($id);

        $deliveryMenAccepted->approved = DeliveryMensRepository::REJECTED_STATUS; // 0 = NotShow / 1 = Accepted / 2 = Rejected

        if ($deliveryMenAccepted->save()) {
            $extensionEmail = pathinfo($deliveryMenAccepted->email, PATHINFO_EXTENSION);
            if ($extensionEmail == 'net' || $extensionEmail == 'com' || $extensionEmail == 'org' || $extensionEmail == 'market' || $extensionEmail == 'sa' || $extensionEmail == 'eg') {
                Mail::send([], [], function ($message) use ($deliveryMenAccepted) {
                    $message->to($deliveryMenAccepted->email)
                        ->subject('تم رفض حسابك كامندوب')
                        // here comes what you want
                        ->setBody("
                    <p>
                        مرحبا بك {$deliveryMenAccepted->firstName}
                    </p>
                    </br>
                    </br>
                    <hr>
                    </br>
                    <p>
                    تم رفض الطلب لحسابك كامندوب توصيل في خدمة دايت ماركت
                                        </p>
                ", 'text/html'); // assuming text/plain
                });
            } else {
                throw new Exception('برجاء التاكد من صيغة الاميل');
            }
        }

        return $deliveryMenAccepted;
    }

    /**
     * Method deliveryMenAcceptedData
     *
     * @param int $id
     *
     * @return void
     */
    public function deliveryMenAcceptedData(int $id)
    {
        $deliveryMen = Model::find($id);

        if ($deliveryMen->newImage == '') {
            $deliveryMen->newImage = null;
        }
        if ($deliveryMen->newCardIdImage == '') {
            $deliveryMen->newCardIdImage = null;
        }
        if ($deliveryMen->newDriveryLicenseImage == '') {
            $deliveryMen->newDriveryLicenseImage = null;
        }
        if ($deliveryMen->newVehicleFrontImage == '') {
            $deliveryMen->newVehicleFrontImage = null;
        }
        if ($deliveryMen->newVehicleBackImage == '') {
            $deliveryMen->newVehicleBackImage = null;
        }
        $deliveryMen->accountCardName = $deliveryMen->newAccountCardName ?? $deliveryMen->accountCardName;
        $deliveryMen->bankAccountNumber = $deliveryMen->newBankAccountNumber ?? $deliveryMen->bankAccountNumber;
        $deliveryMen->vehicleBrand = $deliveryMen->newVehicleBrand ?? $deliveryMen->vehicleBrand;
        $deliveryMen->vehicleModel = $deliveryMen->newVehicleModel ?? $deliveryMen->vehicleModel;
        $deliveryMen->VehicleSerialNumber = $deliveryMen->newVehicleSerialNumber ?? $deliveryMen->VehicleSerialNumber;
        $deliveryMen->vehicleType = $deliveryMen->newVehicleType ?? $deliveryMen->vehicleType;
        $deliveryMen->yearManufacture = $deliveryMen->newYearManufacture ?? $deliveryMen->yearManufacture;
        $deliveryMen->cardIdImage = $deliveryMen->newCardIdImage ?? $deliveryMen->cardIdImage;
        $deliveryMen->driveryLicenseImage = $deliveryMen->newDriveryLicenseImage ?? $deliveryMen->driveryLicenseImage;
        $deliveryMen->VehicleFrontImage = $deliveryMen->newVehicleFrontImage ?? $deliveryMen->VehicleFrontImage;
        $deliveryMen->VehicleBackImage = $deliveryMen->newVehicleBackImage ?? $deliveryMen->VehicleBackImage;
        $deliveryMen->firstName = $deliveryMen->newFirstName ?? $deliveryMen->firstName;
        $deliveryMen->lastName = $deliveryMen->newLastName ?? $deliveryMen->lastName;
        $deliveryMen->email = $deliveryMen->newEmail ?? $deliveryMen->email;
        $deliveryMen->idNumber = $deliveryMen->newIdNumber ?? $deliveryMen->idNumber;
        $deliveryMen->nationality = $deliveryMen->newNationality ?? $deliveryMen->nationality;
        $deliveryMen->image = $deliveryMen->newImage ?? $deliveryMen->image;
        $deliveryMen->birthDate = $deliveryMen->newBirthDate ?? $deliveryMen->birthDate;
        $deliveryMen->phoneNumber = $deliveryMen->newPhoneNumberUpdate ?? $deliveryMen->phoneNumber;
        $deliveryMen->profileLocation = $deliveryMen->newProfileLocation ?? $deliveryMen->profileLocation;
        $deliveryMen->updateData = false;
        $deliveryMen->newAccountCardName = null;
        $deliveryMen->newBankAccountNumber = null;
        $deliveryMen->newVehicleBrand = null;
        $deliveryMen->newVehicleModel = null;
        $deliveryMen->newVehicleSerialNumber = null;
        $deliveryMen->newVehicleType = null;
        $deliveryMen->newYearManufacture = null;
        $deliveryMen->newCardIdImage = null;
        $deliveryMen->newDriveryLicenseImage = null;
        $deliveryMen->newVehicleFrontImage = null;
        $deliveryMen->newVehicleBackImage = null;
        $deliveryMen->newFirstName = null;
        $deliveryMen->newLastName = null;
        $deliveryMen->newEmail = null;
        $deliveryMen->newIdNumber = null;
        $deliveryMen->newNationality = null;
        $deliveryMen->newImage = null;
        $deliveryMen->newBirthDate = null;
        $deliveryMen->newPhoneNumberUpdate = null;
        $deliveryMen->newProfileLocation = null;
        $deliveryMen->save();

        return $deliveryMen;
    }

    /**
     * Method deliveryMenRejectedData
     *
     * @param int $id
     *
     * @return void
     */
    public function deliveryMenRejectedData(int $id)
    {
        $deliveryMen = Model::find($id);
        $deliveryMen->updateData = false;
        $deliveryMen->newAccountCardName = null;
        $deliveryMen->newBankAccountNumber = null;
        $deliveryMen->newVehicleBrand = null;
        $deliveryMen->newVehicleModel = null;
        $deliveryMen->newVehicleSerialNumber = null;
        $deliveryMen->newVehicleType = null;
        $deliveryMen->newYearManufacture = null;
        $deliveryMen->newCardIdImage = null;
        $deliveryMen->newDriveryLicenseImage = null;
        $deliveryMen->newVehicleFrontImage = null;
        $deliveryMen->newVehicleBackImage = null;
        $deliveryMen->newFirstName = null;
        $deliveryMen->newLastName = null;
        $deliveryMen->newEmail = null;
        $deliveryMen->newIdNumber = null;
        $deliveryMen->newNationality = null;
        $deliveryMen->newImage = null;
        $deliveryMen->newBirthDate = null;
        $deliveryMen->newPhoneNumberUpdate = null;
        $deliveryMen->newProfileLocation = null;
        $deliveryMen->save();

        return $deliveryMen;
    }

    /**
     * send random number (otp) to sms gateway
     *
     * @param string $phoneNumber
     * @param string $verificationCode
     */
    public function sendOTP(string $phoneNumber, string $verificationCode)
    {
        $sms = App::make(SMS::class);
        $message = "كود التحقق الخاص بك هو : {$verificationCode}";
        $sms->send($message, DeliveryMensRepository::CODE_PHONE_NUMBER . $phoneNumber);
    }

    /**
     * Method removeUnverfiedUsersByEmailAndPhoneNumber
     *
     * @param $request $request
     * remove Unverfied Users ByEmailAndPhoneNumber
     * @return int
     */
    public function removeUnverfiedUsersByEmailAndPhoneNumber($request)
    {
        return $this->getQuery()->where(function ($query) use ($request) {
            $query->where('email', $request->email)->orWhere('phoneNumber', $request->phoneNumber);
        })->where('isVerified', false)->delete();
    }

    /**
     * Send new verification phone number
     *
     * @param Model $model
     * @param Request $request
     * @return int
     */
    public function sendVerificationToNewNumber($model, Request $request)
    {
        // dd($model);
        if ($model->phoneNumber == $request->phoneNumber) {
            return 'oldPhoneNumber';
        } else {
            $now = Carbon::now();

            // // If the current time is greater than the time that user can request resending code, then reset the counter of sent code tries and clear the time limit of sending code.
            if ($now->greaterThan($model->canResendCodeAt)) {
                $model->countResendCode = 0;
                $model->canResendCodeAt = null;
                $model->save();
            }
            if ($model->countResendCode >= 3) {
                return ['message' => sprintf('تم استنفاذ المحأولاًت اللازمة لإرسال كود التحقق يجب المحاولة خلال %d دقيقة من الآن أو تواصل مع الادمن', $now->diffInMinutes($model->canResendCodeAt))];
            }
            $model->newVerificationCode = mt_rand(1000, 9999);
            $model->newPhoneNumber = $request->phoneNumber;
            $model->countResendCode++;
            $model->canResendCodeAt = $now->addHour();
            $model->save();
            $this->sendOTP($model->newPhoneNumber, $model->newVerificationCode);

            return $model->newVerificationCode;
        }
    }

    /**
     * Update phone number
     *
     * @param Model $model
     * @param Request $request
     * @return void
     */
    public function updatePhoneNumber($model, $request)
    {
        // $model->verified = true;
        $model->verificationCode = $model->newVerificationCode;
        // $model->phoneNumber = $model->newPhoneNumber;
        $model->newPhoneNumberUpdate = $model->newPhoneNumber;
        $model->updateData = true;

        $model->newPhoneNumber = null;
        $model->newVerificationCode = null;

        $model->save();
    }

    /**
     * Method updateProfile
     *
     * @param $request $request
     *
     * @return void
     */
    public function updateProfile($deliveryMan, $request)
    {
        $nationality = $this->nationalitiesRepository->get((int) $request->nationality);
        // dd($nationality);
        $deliveryManUpdate = $this->deliveryMenRepository->get((int) $deliveryMan->id);

        return $deliveryManUpdate->update([
            'firstName' => $request->firstName ?? $deliveryManUpdate->firstName,
            'lastName' => $request->lastName ?? $deliveryManUpdate->lastName,
            'email' => $request->email ?? $deliveryManUpdate->email,
            'idNumber' => $request->idNumber,
            'birthDate' => $request->birthDate,
            'name' => $request->firstName . $request->lastName,
            'nationality' => $nationality->sharedInfo(),
        ]);
    }

    /**
     * Method updateProfileUpdate
     *
     * @param $deliveryMan $deliveryMan
     * @param $request $request
     *
     * @return void
     */
    public function updateProfileUpdate($deliveryMan, $request)
    {
        $nationality = $this->nationalitiesRepository->get((int) $request->nationality);
        $deliveryManUpdate = $this->deliveryMenRepository->get((int) $deliveryMan);

        if ($request->has('image')) {
            $destinationPath = '/data/deliveryMen/' . $deliveryMan . '/';
            $image = date('YmdHis') . uniqid() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path($destinationPath), $image);
        } else {
            $destinationPath = null;
            $image = null;
        }
        $profileLocation = $request->profileLocation = [
            'type' => 'Point',
            'coordinates' => [(float) $request->profileLocation['lat'], (float) $request->profileLocation['lng']],
            'address' => $request->profileLocation['address'] ?? null,
        ];
        // dd($profileLocation);
        return $deliveryManUpdate->update([
            'newFirstName' => $request->firstName ?? $deliveryManUpdate->firstName,
            'newLastName' => $request->lastName ?? $deliveryManUpdate->lastName,
            'newEmail' => $request->email ?? $deliveryManUpdate->email,
            'newIdNumber' => $request->idNumber,
            'newBirthDate' => $request->birthDate,
            'newImage' => $destinationPath . $image,
            'name' => $request->firstName . $request->lastName,
            'newProfileLocation' => $profileLocation,
            'updateData' => true,
            'newNationality' => $nationality->sharedInfo(),
        ]);
    }

    /**
     * Method onDelete
     *
     * @param $model $model
     * @param $id $id
     *
     * @return void
     */
    public function onDelete($model, $id)
    {
        $this->orderDeliveryRepository->deleteOrderByDelivery($id, $model);
    }

    /**
     * Method logOutForPublishedEquelFalse
     *
     * @param Request $request
     *
     * @return void
     */
    public function logOutForPublishedEquelFalse($request)
    {
        $user = user();
        $accessTokens = $user->accessTokens;

        $currentAccessToken = $request->authorizationValue();

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
        $user->save();

        Auth::logout();

        // return $this->success();
    }

    /**
     * Method cronJobsForUpdateDelivery
     *
     * @return void
     */
    public function cronJobsForUpdateDelivery()
    {
        $deliveryMens = $this->deliveryMenRepository->getQuery()->get();
        foreach ($deliveryMens as $key => $deliveryMen) {
            $now = Carbon::now();
            if ($now->diffInMinutes($deliveryMen->updatedAt) >= 10) {
                $deliveryMen->status = false;
                $deliveryMen->save();
            }
            echo 'done';
        }
    }
}
