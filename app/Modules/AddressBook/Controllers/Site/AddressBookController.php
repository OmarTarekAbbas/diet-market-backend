<?php

namespace App\Modules\AddressBook\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\AdminApiController;

class AddressBookController extends AdminApiController
{
    /**
     * Controller info
     *
     * @var array
     */
    protected $controllerInfo = [
        'repository' => 'addressBooks',
        'listOptions' => [
            'select' => [],
            'filterBy' => [],
            'paginate' => false, // if set null, it will be automated based on repository configuration option
        ],
        'rules' => [
            'all' => [],
            'store' => [
                'phoneNumber' => 'required|min:12|max:12',
            ],
            'update' => [],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    protected function listOptions(Request $request): array
    {
        $listOptions = $this->controllerInfo('listOptions');
        $listOptions = array_merge($listOptions, [
            'customer' => user()->id,
        ]);

        return array_merge($request->all(), $listOptions);
    }

    /**
     * Resend verification code to the given address id
     *
     * @param int $id
     * @return Response
     */
    public function requestVerification($id)
    {
        $address = $this->repository->getValidAddress($id);

        if (!$address) {
            return $this->badRequest(trans('errors.notFound'));
        }

        if ($address->verified) {
            return $this->badRequest(trans('errors.alreadyVerified'));
        }

        if (!$this->repository->canSendAnotherVerificationCode($address)) {
            return $this->badRequest(trans('errors.alreadySent'));
        }

        $this->repository->sendVerificationCode($address);

        return $this->success([
            'record' => $this->repository->wrap($address),
        ]);
    }

    /**
     * Update phone number and get verification code
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function updatePhoneNumber($id, Request $request)
    {
        $validator = $this->scan($request);
        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        }
        $address = $this->repository->getValidAddress($id);

        if (!$address) {
            return $this->badRequest(trans('errors.notFound'));
        }

        $verificationCode = $this->repository->sendVerificationToNewNumber($address, $request);

        if ($verificationCode == 'addressVerified') {
            return $this->success([
                'verified' => true,
            ]);
        }

        return $this->success([
            'verificationCode' => $verificationCode,
        ]);
    }

    /**
     * Update phone number and get verification code
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function verifyUpdatedPhoneNumber($id, $verificationCode, Request $request)
    {
        $address = $this->repository->getValidAddress($id);

        if (!$address) {
            return $this->badRequest(trans('errors.notFound'));
        }

        if ($address->newVerificationCode != $verificationCode) {
            return $this->badRequest(trans('errors.invalidCode'));
        }

        $this->repository->updatePhoneNumber($address, $request);

        return $this->success();
    }

    /**
     * Verify the given address
     *
     * @param int $id
     * @param string $verificationCode
     * @return Response|\Illuminate\Http\Response|string
     */
    public function verify($id, $verificationCode)
    {
        $address = $this->repository->getValidAddress($id);

        if (!$address) {
            return $this->badRequest(trans('errors.notFound'));
        }

        if (!$address->verificationCode || $address->verificationCode != $verificationCode) {
            return $this->badRequest(trans('errors.invalidCode'));
        }

        $this->repository->verifyAddress($address);

        return $this->success([
            'record' => $this->repository->wrap($address),
        ]);
    }

    /**
     * Method ResndVerifiAddAdress
     *
     * @param $id $id
     * @param Request $request
     *
     * @return void
     */
    public function ResndVerifiAddAdress($id, Request $request)
    {
        $address = $this->repository->getValidAddress($id);
        if (!$address) {
            return $this->badRequest(trans('errors.notFound'));
        }
        $this->repository->sendVerificationCode($address);

        return $this->success([
            'record' => $address->verificationCode,
        ]);
    }

    protected function scan(Request $request)
    {
        $user = user();

        $table = $this->repository->getTableName();

        return Validator::make($request->all(), [
            // 'phoneNumber' => 'required|min:9|max:9'
        ]);
    }

    // public function index(Request $request)
    // {
    //     $options = [
    //         'verified' => $request->verified,
    //     ];

    //     if ($request->page) {
    //         $options['page'] = (int) $request->page;
    //     }

    //     return $this->success([
    //         'records' => $this->repository->list($options),
    //         'paginationInfo' => $this->repository->getPaginateInfo(),

    //     ]);
    // }
}
