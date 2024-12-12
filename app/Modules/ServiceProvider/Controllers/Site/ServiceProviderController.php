<?php

namespace App\Modules\ServiceProvider\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class ServiceProviderController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'serviceProviders';

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
     *crate for serviceProvider
     * @return array
     */
    public function create(Request $request)
    {
        $validator = $this->scan($request);
        if (!$validator->passes()) {
            return $this->badRequest($validator->errors());
        }

        return $this->success([
            'record' => $this->repository->create($request),
        ]);
    }

    /**
     * Method serviceProviderWebView
     *
     * @param Request $request
     *
     * @return url
     */
    public function serviceProviderWebView(Request $request)
    {
        return $this->success([
            // 'records' => env('APP_URL').'/serviceProvider', 
            'records' => 'https://diet.market/join-request',
        ]);
    }

    /**
     * Determine whether the passed values are valid
     *
     * @return mixed
     */
    private function scan(Request $request)
    {
        return \Validator::make($request->all(), [
            'firstName' => 'min:2',
            'lastName' => 'min:2',
            'email' => 'required',
            'commercialNumber' => 'required',
            'commercialImage' => 'required',
            'tradeName' => 'required|min:2',
            'address' => 'required|min:2',
            'type' => 'required|min:2',
        ]);
    }
}
