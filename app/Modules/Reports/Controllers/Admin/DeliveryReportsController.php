<?php

namespace App\Modules\Reports\Controllers\Admin;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class DeliveryReportsController extends ApiController
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
            'records' => $this->orderDeliveryRepository->reports()->sales([
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
            'records' => $this->orderDeliveryRepository->reports()->monthSales($year, $month),
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
            'records' => $this->orderDeliveryRepository->reports()->yearSales($request->year ?: date('Y')),
        ]);
    }

    /**
     * Method weekReportCount
     *
     * @param Request $request
     *
     * @return void
     */
    public function weekReportCount(Request $request)
    {
        $day = $request->day ?: date('d');
        $month = $request->month ?: date('m');
        $year = $request->year ?: date('Y');

        return $this->success([
            'records' => $this->orderDeliveryRepository->reports()->counts([
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
    public function monthReportCount(Request $request)
    {
        $month = $request->month ?: date('m');
        $year = $request->year ?: date('Y');

        return $this->success([
            'records' => $this->orderDeliveryRepository->reports()->monthCount($year, $month),
        ]);
    }

    /**
     * Get sales reports by year in months
     *
     * @param Request $request
     * @return Response
     */
    public function yearReportCount(Request $request)
    {
        return $this->success([
            'records' => $this->orderDeliveryRepository->reports()->yearCount($request->year ?: date('Y')),
        ]);
    }
}
