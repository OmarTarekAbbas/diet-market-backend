<?php

namespace App\Modules\Reports\Controllers\Admin;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class SalesReportsController extends ApiController
{
    /**
     * Get sales reports by week in days
     *
     * @param Request $request
     * @return Response
     */
    public function weekReport(Request $request)
    {
        $day = $request->day ?: date('d');
        $month = $request->month ?: date('m');
        $year = $request->year ?: date('Y');

        return $this->success([
            'records' => $this->ordersRepository->reports()->sales([
                'from' => $from = date($day . '-' . $month . '-' . $year),
                'to' => date('d-m-Y', strtotime('+7 days', strtotime($from))),
            ]),
        ]);
    }

    /**
     * Get sales reports by month in weeks
     *
     * @param Request $request
     * @return Response
     */
    public function monthReport(Request $request)
    {
        $month = $request->month ?: date('m');
        $year = $request->year ?: date('Y');

        return $this->success([
            'records' => $this->ordersRepository->reports()->monthSales($year, $month),
        ]);
    }

    /**
     * Get sales reports by year in months
     *
     * @param Request $request
     * @return Response
     */
    public function yearReport(Request $request)
    {
        return $this->success([
            'records' => $this->ordersRepository->reports()->yearSales($request->year ?: date('Y')),
        ]);
    }
}
