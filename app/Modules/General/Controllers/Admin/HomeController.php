<?php

namespace App\Modules\General\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;
use App\Modules\Orders\Repositories\OrdersRepository;

class HomeController extends ApiController
{
    /**
     * Get Home Data
     *
     * @param Request $request
     * @return Response|\Illuminate\Http\Response|string
     */
    public function index(Request $request)
    {
        //        $today = Carbon::now()->subMonths(2)->format('d-m-Y');
        //        $today = '08-03-2020';
        $today = date('d-m-Y');

        $options = $request->all();

        $options['from'] = $today;

        $ordersReports = $this->ordersRepository->reports();

        $productsReports = $this->productsRepository->reports();

        return $this->success([
            'total' => [
                'orders' => [
                    'total' => $ordersReports->total($options),
                    'pendingOrders' => $ordersReports->total($options + ['status' => OrdersRepository::PENDING_STATUS]),
                    'processingOrders' => $ordersReports->total($options + ['status' => OrdersRepository::PROCESSING_STATUS]),
                    'completedOrders' => $ordersReports->total($options + ['status' => OrdersRepository::COMPLETED_STATUS]),
                ],
                'sales' => $ordersReports->totalSales($options),
                'customers' => $this->customersRepository->total($options),
                'countOutOfStock' => $productsReports->total($options + ['outOfStock' => true]),
            ],
            'orders' => $ordersReports->latestOrders($options),
            'currentWeek' => $ordersReports->weeklySales($today),


            'totalRestaurants' => [
                'ordersRestaurants' => [
                    'total' => $ordersReports->totalRestaurants($options),
                    'pendingOrders' => $ordersReports->totalRestaurants($options + ['status' => OrdersRepository::PENDING_STATUS]),
                    'processingOrders' => $ordersReports->totalRestaurants($options + ['status' => OrdersRepository::PROCESSING_STATUS]),
                    'completedOrders' => $ordersReports->totalRestaurants($options + ['status' => OrdersRepository::COMPLETED_STATUS]),
                ],
                'sales' => $ordersReports->totalSalesRestaurants($options),
                // 'customers' => $this->customersRepository->total($options),
                // 'countOutOfStock' => $productsReports->totalRestaurants($options + ['outOfStock' => true]),
            ],
            'orderRestaurants' => $ordersReports->latestOrderRestaurants($options),

            'totalStore' => [
                'ordersStore' => [
                    'total' => $ordersReports->totalStore($options),
                    'pendingOrders' => $ordersReports->totalStore($options + ['status' => OrdersRepository::PENDING_STATUS]),
                    'processingOrders' => $ordersReports->totalStore($options + ['status' => OrdersRepository::PROCESSING_STATUS]),
                    'completedOrders' => $ordersReports->totalStore($options + ['status' => OrdersRepository::COMPLETED_STATUS]),
                ],
                'sales' => $ordersReports->totalSalesStore($options),
                // 'customers' => $this->customersRepository->totalStore($options),
                // 'countOutOfStock' => $productsReports->totalStore($options + ['outOfStock' => true]),
            ],
            'orderStore' => $ordersReports->latestOrderStores($options),

            'totalClubs' => [
                'ordersStore' => [
                    'total' => $ordersReports->totalClubs($options),
                    'pendingOrders' => $ordersReports->totalClubs($options + ['status' => OrdersRepository::PENDING_STATUS]),
                    'processingOrders' => $ordersReports->totalClubs($options + ['status' => OrdersRepository::PROCESSING_STATUS]),
                    'completedOrders' => $ordersReports->totalClubs($options + ['status' => OrdersRepository::COMPLETED_STATUS]),
                ],
                'sales' => $ordersReports->totalSalesClubs($options),
                // 'customers' => $this->customersRepository->totalStore($options),
                // 'countOutOfStock' => $productsReports->totalStore($options + ['outOfStock' => true]),
            ],
            'orderClubs' => $ordersReports->latestOrderClubs($options),

        ]);
    }
}
