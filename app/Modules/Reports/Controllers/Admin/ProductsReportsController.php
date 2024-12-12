<?php

namespace App\Modules\Reports\Controllers\Admin;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class ProductsReportsController extends ApiController
{
    /**
     * Get sales reports by week in days
     *
     * @param Request $request
     * @return Response|\Illuminate\Http\Response|string
     */
    public function getTotalViews(Request $request)
    {
        $options = $request->all();

        return $this->success([
            'records' => $this->productsRepository->reports()->getTotalViews($options),
        ]);
    }

    /**
     * Get sales reports by week in days
     *
     * @param Request $request
     * @return Response|\Illuminate\Http\Response|string
     */
    public function getTotalSales(Request $request)
    {
        $options = $request->all();

        return $this->success([
            'records' => $this->productsRepository->reports()->getTotalSales($options),
        ]);
    }

    /**
     * Get sales reports by week in days
     *
     * @param Request $request
     * @return Response|\Illuminate\Http\Response|string
     */
    public function getOutOfStock(Request $request)
    {
        $options = $request->all();

        return $this->success([
            'records' => $this->productsRepository->reports()->getOutOfStock($options),
        ]);
    }
}
