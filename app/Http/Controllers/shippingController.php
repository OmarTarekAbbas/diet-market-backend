<?php

namespace App\Http\Controllers;

use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Repositories\OrdersRepository;
use Illuminate\Http\Request;

class shippingController extends Controller
{

    /**
     * Main Repository
     *
     * @var OrdersRepository
     */
    protected ?OrdersRepository $repository;

    /**
     * Constructor

     * @param OrdersRepository $repository
     */
    public function __construct(OrdersRepository $repository)
    {
        $this->ordersrepository = $repository;
    }

    /**
     * It takes the request from the webhook, writes it to a text file, and returns 'done'
     * 
     * @param Request request The request object.
     * 
     * @return The webhook is returning a response of "done"
     */
    public function webhooksShipping(Request $request)
    {
        $getContent = $request->getContent();
        $getDataOtoArray = json_decode($getContent, true); // Set second argument as TRUE
        $explodeIdOrderAndIdSeller = explode("-", $getDataOtoArray['orderId']);

        $statusOto = $getDataOtoArray['status'];
        $printAWBURL = $getDataOtoArray['printAWBURL'];
        $orderId = $explodeIdOrderAndIdSeller[0];
        $sellerId = $explodeIdOrderAndIdSeller[1];
        return $this->ordersrepository->changeOrderItemByOtoOrderStatus($orderId, $statusOto, $sellerId, $printAWBURL, $request);
    }

    /**
     * It returns the URL of the webhook file
     * 
     * @param Request request The request object.
     * 
     * @return The url of the webhook.txt file.
     */
    public function getFileWebhooks(Request $request)
    {
        $url =  asset('webhook.txt');
        return $url;
    }
}
