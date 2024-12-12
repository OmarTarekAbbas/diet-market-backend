<?php

namespace App\Modules\Auctions\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class AuctionsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'auctions';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'customer' => $request->customer,
            'product' => $request->product,
        ];

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
            return $this->badRequest(trans('errors.cannotAddAuction'));
        }
        
        if ($this->productsRepository->checkAuctionIsExpired($request->product)) {
            return $this->badRequest(trans('errors.auctionIsExpire'));
        }

        if (!$this->repository->checkPrice($request)) {
            return $this->badRequest(trans('errors.invalidPrice'));
        }

        $this->repository->create($request);
        
        return $this->success();
    }

    public function getAllProductAuctions(Request $request)
    {
        $options = [
            'customerHasAuctions' => true,
        ];

        return $this->success([
            'record' => $this->productsRepository->list($options),
        ]);
    }
}
