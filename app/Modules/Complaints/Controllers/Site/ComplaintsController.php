<?php

namespace App\Modules\Complaints\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HZ\Illuminate\Mongez\Managers\ApiController;

class ComplaintsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected $repository = 'complaints';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $options = [
            'customer' => user()->id,
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
     * {@inheritdoc}
     * @param Request $request
     */
    public function store(Request $request)
    {
        $validator = $this->isValid($request);

        if ($validator->passes()) {
            if (!$this->ordersRepository->getModel($request->get('orderId'))) {
                return $this->badRequest('رقم الطلب غير صحبح');
            }

            $this->repository->create($request);

            return $this->success(['message' => 'تم تقديم الشكوي']);
        } else {
            return $this->badRequest($validator->errors());
        }
    }

    /**
     * Determine whether the passed values are valid
     *
     * @param Request $request
     * @return mixed
     */
    private function isValid(Request $request)
    {
        return Validator::make($request->all(), [
            'orderId' => 'required',
            'reason' => 'required',
            'images.*' => 'required|image',
        ]);
    }
}
