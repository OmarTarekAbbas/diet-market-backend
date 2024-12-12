<?php

namespace App\Modules\Reports\Controllers\Admin;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class OrdersReportsController extends ApiController
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

        [$records, $paginationInfo] = $this->ordersRepository->reports()->customersReport($options);

        return $this->success([
            'records' => $records,
            'paginationInfo' => $paginationInfo,
        ]);
    }

    /**
     * Method financialReportStore
     *
     * @param Request $request
     *
     * @return void
     */
    public function financialReportStore(Request $request)
    {
        $options = $request->all();

        $records = $this->ordersRepository->reports()->financialReportStore($options);
        $paginationInfo = $this->ordersRepository->reports()->getPaginateInfoStore($options);

        return $this->success([
            'records' => $records,
            'paginationInfo' => $paginationInfo,
        ]);
    }

    /**
     * Method financialReportResturant
     *
     * @param Request $request
     *
     * @return void
     */
    public function financialReportResturant(Request $request)
    {
        $options = $request->all();

        $records = $this->ordersRepository->reports()->financialReportResturant($options);
        $paginationInfo = $this->ordersRepository->reports()->getPaginateInfoResturant($options);

        return $this->success([
            'records' => $records,
            'paginationInfo' => $paginationInfo,
        ]);
    }

    /**
     * Method financialReportClub
     *
     * @param Request $request
     *
     * @return void
     */
    public function financialReportClub(Request $request)
    {
        $options = $request->all();

        $records = $this->ordersRepository->reports()->financialReportClub($options);
        $paginationInfo = $this->ordersRepository->reports()->getPaginateInfoClub($options);

        return $this->success([
            'records' => $records,
            'paginationInfo' => $paginationInfo,
        ]);
    }

    /**
     * Method financialReportClinic
     *
     * @param Request $request
     *
     * @return void
     */
    public function financialReportClinic(Request $request)
    {
        $options = $request->all();

        $records = $this->ordersRepository->reports()->financialReportClinic($options);
        $paginationInfo = $this->ordersRepository->reports()->getPaginateInfoClinic($options);

        return $this->success([
            'records' => $records,
            'paginationInfo' => $paginationInfo,
        ]);
    }
}
