<?php

namespace App\Modules\AddressBook\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Modules\Cities\Models\City;
use Illuminate\Support\Facades\App;
use App\Modules\Countries\Models\Country;
use App\Modules\Newsletters\Services\Gateways\SMS;
use App\Modules\AddressBook\Models\AddressBook as Model;

use App\Modules\AddressBook\Filters\AddressBook as Filter;
use App\Modules\AddressBook\Resources\AddressBook as Resource;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class AddressBooksRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'addressBooks';

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
     * type => work | home
     *
     * @const array
     */
    const DATA = [
        'firstName', 'lastName', 'email',
        'address', 'phoneNumber', 'buildingNumber', 'flatNumber', 'floorNumber', 'district', 'specialMark', 'location',
    ];

    /**
     * Geo Location data
     *
     * @const array
     */
    const LOCATION_DATA = ['location'];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = [];

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
    const INTEGER_DATA = ['customerId'];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = [];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = ['isPrimary'];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [
        'city' => City::class,
        'country' => Country::class,
    ];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel::class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = [
        'firstName', 'lastName', 'email',
        'address', 'phoneNumber', 'specialMark', 'location',
        'isPrimary', 'customerId', 'district',
    ];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [];

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

    /**
     * Set any extra data or columns that need more customizations
     * Please note this method is triggered on create or update call
     *
     * @param mixed $model
     * @param \Illuminate\Http\Request $request
     * @return  void
     */
    protected function setData($model, $request)
    {
        $address = $this->getModel($model->id);
        if ($request->method() == 'PUT') {
            $request->phoneNumber = $address->phoneNumber;
        }


        if (!$model->customerId && user()->AccountType() == 'customer') {
            $user = user();

            $model->customerId = $user->id;
        } elseif (user()->AccountType() == 'user') {
            $model->customerId = (int) $request->customerId;
            $model->verified = true;
        }

        if (!$model->id && user()->AccountType() == 'customer') {
            $model->verified = false;
        }

        $this->checkUserData($model, $request); // Method check Customer Data

        $address = $this->addressBooksRepository->getQuery()->where('phoneNumber', $request->phoneNumber)->where('verified', true)->first();
        if ($address) {
            $model->verified = true;
        }

        $model->location = $request->location = [
            'type' => 'Point',
            'coordinates' => [(float) $request->location['lat'], (float) $request->location['lng']],
            'address' => $request['address'] ?? null,
        ];
    }

    /**
     * Method checkUserData
     *
     * @param $model $model
     * @param $request $request
     * check User Data
     * @return
     */
    public function checkUserData($model, $request)
    {
        $model->firstName = ($request->firstName) ? $request->firstName : user()->firstName;
        $model->lastName = ($request->lastName) ? $request->lastName : user()->lastName;
        $model->email = ($request->email) ? $request->email : user()->email;
        $model->phoneNumber = ($request->phoneNumber) ? $request->phoneNumber : user()->phoneNumber;

        return $model;
    }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function onList($addresses)
    // {
    //     if (empty($addresses[0]->resource)) return $addresses;
    //     $this->updateCustomerAddresses($addresses[0]->resource);

    //     return $addresses;
    // }

    /**
     * {@inheritDoc}
     */
    public function onSave($model, $request, $oldModel = null)
    {
        if ($model->isPrimary) {
            $this->setAddressesToNonPrimaryExcept($model);
        }

        $this->updateCustomerAddresses($model);

        // if (!$model->verificationCode || ($oldModel && $model->phoneNumber != $oldModel->phoneNumber)) {
        if (!$model->verificationCode && !$model->verified) {
            $this->sendVerificationCode($model);
        }
    }

    /**
     * Update all customer addresses
     *
     * @param Model $model
     * @return void
     */
    public function updateCustomerAddresses($model)
    {
        $customer = $this->customersRepository->getModel($model->customerId);

        $customer->addresses = $this->list([
            'as-model' => true,
            'customer' => $customer->id,
        ])->map(function ($address) {
            return $address->sharedInfo();
        })->toArray();

        $customer->save();
        $user = user();

        if ($user->is($customer)) {
            $user->addresses = $customer->addresses;
        }
    }

    /**
     * Send verification code to the given address
     *
     * @param Model $model
     * @return  void
     */
    public function sendVerificationCode($model)
    {
        $model->verificationCode = mt_rand(1000, 9999);
        $model->verified = false;
        $model->sentVerificationAt = Carbon::now();
        $model->save();
        $this->updateCustomerAddresses($model);

        $this->sendMessage($model->phoneNumber, $model->verificationCode);
    }

    /**
     * Send new verification phone number
     *
     * @param Model $address
     * @param Request $request
     * @return int
     */
    public function sendVerificationToNewNumber($address, Request $request)
    {
        $addressVerified = $this->addressBooksRepository->getQuery()->where('phoneNumber', $request->phoneNumber)->where('verified', true)->where('customerId', $address->customerId)->first();
        if ($addressVerified) {
            $address->phoneNumber = $request->phoneNumber;
            $address->verified = true;
            $address->save();

            return 'addressVerified';
        } else {
            $address->newVerificationCode = mt_rand(1000, 9999);
            $address->newPhoneNumber = $request->phoneNumber;
            $address->save();
            $this->sendMessage($address->newPhoneNumber, $address->newVerificationCode);

            return $address->newVerificationCode;
        }
    }

    /**
     * Check if address can get another verification code
     *
     * @param Model $model
     * @return boolean
     */
    public function canSendAnotherVerificationCode($model)
    {
        if (!$model->sentVerificationAt) {
            return true;
        }

        if (is_array($model->sentVerificationAt)) {
            $model->sentVerificationAt = new Carbon($model->sentVerificationAt['date']);
        }

        // if last sent code is less than 5 minutes ago then stop sending
        // if ($model->sentVerificationAt->addMinutes(5)->timestamp > time()) return false;

        return true;
    }

    /**
     * Update phone number
     *
     * @param $address
     * @param Request $request
     * @return void
     */
    public function updatePhoneNumber($address, $request)
    {
        $address->verified = true;
        $address->verificationCode = $address->newVerificationCode;
        $address->phoneNumber = $address->newPhoneNumber;

        $address->newPhoneNumber = null;
        $address->newVerificationCode = null;

        $address->save();

        $this->updateCustomerAddresses($address);
    }

    /**
     * > The function verifies an address by setting the `verified` attribute to `true` and the
     * `verificationCode` attribute to `null`
     *
     * @param address The address object to verify.
     */
    public function verifyAddress($address)
    {
        $address->verified = true;
        $address->verificationCode = null;
        $address->save();

        $this->updateCustomerAddresses($address);
    }

    /**
     * Get valid address and verify it belongs to current customer
     *
     * @param int $id
     * @return Model|null
     */
    public function getValidAddress($id, $customerId = null)
    {
        $address = $this->getModel($id);
        // $address = $this->addressBooksRepository->get($id);
        if (!$address) {
            return null;
        }

        $customer = $this->customersRepository->getModel($customerId) ?? user();

        if ($address->customerId != $customer->id) {
            return null;
        }

        return $address;
    }

    /**
     * Set all addresses to non primary except the given one
     *
     * @param Model $model
     * @return void
     */
    public function setAddressesToNonPrimaryExcept(Model $model)
    {
        $this->getQuery()->where('customerId', $model->customerId)->where('id', '!=', $model->id)->update([
            'isPrimary' => false,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function onDelete($model, $id)
    {
        $this->updateCustomerAddresses($model);
        $this->cartRepository->onDeleteAddress($id);
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {

        if ($customer = $this->option('customer')) {
            $this->query->where('customerId', (int) $customer);
        }
        if (($verified = $this->option('verified')) !== null) {
            $this->query->where('verified', (bool) true);
        }

        // todo: filter all addressBook verified
        $this->query->where('verified', true);
    }

    public function sendMessage($phoneNumber, $verificationCode)
    {
        $sms = App::make(SMS::class);
        $message = "كود التحقق الخاص بك هو : {$verificationCode}";
        $sms->send($message, $phoneNumber);
    }
}
