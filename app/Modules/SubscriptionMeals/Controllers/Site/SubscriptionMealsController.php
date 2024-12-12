<?php

namespace App\Modules\SubscriptionMeals\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class SubscriptionMealsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'subscriptionMeals';

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
     * Method create
     *
     * @param Request $request
     *crate for restaurantManager/sections
     * @return array
     */
    public function create(Request $request)
    {
        $validator = $this->validatorForm($request);

        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        }

        return $this->success([
            'record' => $this->repository->create($request),
        ]);
    }

    /**
     * Method validatorForm
     *
     * @param $request $request
     * validator Form
     * @return object
     */
    public function validatorForm($request)
    {
        return Validator::make($request->all(), [
            'items' => 'required',
            'type' => 'required_if:type,weekly,monthly',
            'dateTimeMeals' => 'required',
        ]);
    }
}
