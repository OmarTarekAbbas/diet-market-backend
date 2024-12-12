<?php

// Copyright
declare(strict_types=1);

namespace App\Modules\General\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;
use App\Modules\General\Services\PaymentMethodList;

class PaymentMethodController extends ApiController
{
    /**
     * Get Payment Method list
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|string
     */
    public function index(Request $request)
    {
        $payment = PaymentMethodList::list();

        return $this->success([
            'records' => $payment,
        ]);
    }
}
