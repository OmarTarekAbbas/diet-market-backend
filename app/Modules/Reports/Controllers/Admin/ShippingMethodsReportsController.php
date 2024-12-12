<?php

namespace App\Modules\Reports\Controllers\Admin;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class ShippingMethodsReportsController extends ApiController
{
    /**
     * Get sales reports by week in days
     *
     * @param Request $request
     * @return Response|\Illuminate\Http\Response|string
     */
    public function index(Request $request)
    {
        $options = $request->all();

        return $this->success([
            'records' => $this->ordersRepository->reports()->getShippingMethodsReport($options),
        ]);
    }
}
