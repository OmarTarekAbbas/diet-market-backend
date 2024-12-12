<?php

namespace App\Modules\Compromises\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class CompromisesController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'compromises';

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
     * add auction
     */
    public function store(Request $request)
    {
        $rules = [
            'product' => 'required|integer',
            'price' => 'required',
        ];
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->badRequest($validator->errors());
        }

        if ($this->customersRepository->sellerHasProduct($request->product)) {
            return $this->badRequest(trans('error.cannotAddAuction'));
        }

        if ($this->repository->hasCompromise($request)) {
            return $this->badRequest(trans('error.alreadyAdded'));
        }

        $this->repository->create($request);
        
        return $this->success();
    }
}
