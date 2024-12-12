<?php

namespace App\Modules\ReceiptRequests\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class ReceiptRequestsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'receiptRequests';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [];

        return $this->success([
            'records' => $this->repository->list($options),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function show($id, Request $request)
    {
        return $this->success([
            'record' => $this->repository->get($id),
        ]);
    }

    /**
     * Method createReceiptRequestsRestaurants
     *
     * @param Request $request
     *crate for Receipt Requests Restaurants
     * @return array
     */
    public function createReceiptRequestsRestaurants(Request $request)
    {
        $validator = $this->validatorFormForRestaurants($request);

        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        }

        return $this->success([
            'record' => $this->repository->create($request),
        ]);
    }

    /**
     * Method createReceiptRequestsHome
     *
     * @param Request $request
     *crate for Receipt Requests Home
     * @return array
     */
    public function createReceiptRequestsHome(Request $request)
    {
        $validator = $this->validatorFormForHome($request);

        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        }

        return $this->success([
            'record' => $this->repository->create($request),
        ]);
    }

    /**
     * Method validatorFormForRestaurants
     *
     * @param $request $request
     * validator Form For Restaurants
     * @return object
     */
    public function validatorFormForRestaurants($request)
    {
        return Validator::make($request->all(), [
            'items' => 'required',
            'receiptRequestsHours' => 'required',
            'type' => 'required',
        ]);
    }

    /**
     * Method validatorFormForHome
     *
     * @param $request $request
     * validator Form For Home
     * @return object
     */
    public function validatorFormForHome($request)
    {
        return Validator::make($request->all(), [
            'firstName' => 'required|min:2',
            'lastName' => 'required|min:2',
            'phoneNumber' => 'required|numeric',
            'city' => 'required',
            'items' => 'required',
            'type' => 'required',
        ]);
    }
}
