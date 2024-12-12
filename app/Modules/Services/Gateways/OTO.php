<?php

declare(strict_types=1); //affects type coercion.

namespace App\Modules\Services\Gateways;

use Carbon\Carbon;

class OTO
{
    /**
     * It adds a warehouse to the OTO system.
     * 
     * @param model The model of the product.
     */
    public function addWarehouse($model)
    {
        $realUrl = config('services.oto.realUrl');
        $accessToken = 'Bearer' . ' ' . $this->refreshToken($realUrl);

        $url = $realUrl . 'createPickupLocation';
        $body = '{
                "code": "' . $model->id . '",
                "type": "warehouse",
                "name": "' . $model->store['name'][0]['text'] . '",
                "mobile": "' . $model->phoneNumber . '",
                "address": "' . $model->store['location']['address'] . '",
                "contactName": "' . $model->firstName . $model->lastName . '",
                "contactEmail": "' . $model->email . '",
                "lat": "' . $model->store['location']['coordinates'][0] . '",
                "lon": "' . $model->store['location']['coordinates'][1] . '",
                "city":"' . $model->city['name'][0]['text'] . '",
                "brandName": "' . $model->store['name'][0]['text'] . '",
            }';
        return $this->loadPost($url, $body, $accessToken);
    }

    /**
     * It Update a warehouse to the OTO system.
     * 
     * @param model The model of the product.
     */
    public function updateWarehouse($model)
    {
        $realUrl = config('services.oto.realUrl');
        $accessToken = 'Bearer' . ' ' . $this->refreshToken($realUrl);

        $url = $realUrl . 'updatePickupLocation';
        $body = '{
                "code": "' . $model->id . '",
                "type": "warehouse",
                "name": "' . $model->store['name'][0]['text'] . '",
                "mobile": "' . $model->phoneNumber . '",
                "address": "' . $model->store['location']['address'] . '",
                "contactName": "' . $model->firstName . $model->lastName . '",
                "contactEmail": "' . $model->email . '",
                "lat": "' . $model->store['location']['coordinates'][0] . '",
                "lon": "' . $model->store['location']['coordinates'][1] . '",
                "city":"' . $model->city['name'][0]['text'] . '",
                "brandName": "' . $model->store['name'][0]['text'] . '",
            }';
        return $this->loadPost($url, $body, $accessToken);
    }

    /**
     * It Update a warehouse to the OTO system.
     * 
     * @param model The model of the product.
     */
    public function createOrder($order, $currentSeller, $amount, $itemsCount, $packageWeight, $items)
    {
        $realUrl = config('services.oto.realUrl');
        $accessToken = 'Bearer' . ' ' . $this->refreshToken($realUrl);

        $url = $realUrl . 'createOrder';
        // deliveryOptionId": "1749",
        $arrayItems = [];
        foreach ($items as $item) {
            $item['productId'] = $item['product']['id'];
            $item['name'] = $item['product']['name'][0]['text'];
            $item['price'] = $item['originalPrice'];
            $item['taxAmount'] = $item['taxes'];
            $item['rowTotal'] = $item['totalPrice'];
            $item['quantity'] = $item['quantity'];
            $item['sku'] = $item['skuProduct'];
            $item['image'] = $item['product']['images'][0];
            $arrayItems[] = $item;
        }
        $expectedDeliveryIn = explode("-", $order->shippingMethod['expectedDeliveryIn']);
        $createdAt = $order->createdAt;
        $formatCreatedAt = Carbon::parse($createdAt)->addDays(-1)->format('d/m/Y H:m:i');

        $deliverySlotDate = date('d/m/Y', strtotime($createdAt . " + $expectedDeliveryIn[1] day"));
        // "deliveryOptionId": "' . $order->shippingMethod['deliveryOptionId'] . '",
        $itemsBox = $items->first();

        if (isset($itemsBox['weightBox'])) {
            $weightBox = (int)$itemsBox['weightBox'];
            $lengthBox = (int)$itemsBox['lengthBox'];
            $heightBox = (int)$itemsBox['heightBox'];
        } else {
            $weightBox = 10;
            $lengthBox = 10;
            $heightBox = 10;
        }
        // "deliveryOptionId": "' . $order->shippingMethod['deliveryOptionId'] . '",
        $body = '{
                "orderId": "' . $order['id'] . '-' . $currentSeller['id'] . '",
                "pickupLocationCode": "' . $currentSeller['id'] . '",
                "createShipment": "true",
                "deliveryOptionId": "' . $order->shippingMethod['deliveryOptionId'] . '",
                "payment_method": "paid",
                "amount": "' . $amount . '",
                "amount_due": "0",
                "currency": "SAR",
                "packageCount": "1",
                "packageWeight": ' . $packageWeight . ',
                "boxWidth": ' . $weightBox . ',    
                "boxLength": ' . $lengthBox . ',
                "boxHeight": ' . $heightBox  . ',
                "orderDate": "' . $formatCreatedAt . '",
                "deliverySlotDate": "' . $deliverySlotDate . '",
                "deliverySlotTo": "12am",
                "deliverySlotFrom": "11:30pm",
                "senderName":"Diet Market Company",
                 "customer": {
                            "name": "' . $order->shippingAddress['firstName'] . $order->shippingAddress['lastName']  . '",
                            "email": "' . $order->shippingAddress['email'] . '",
                            "mobile": "' . $order->shippingAddress['phoneNumber'] . '",
                            "address": "' . $order->shippingAddress['address'] . '",
                            "district": "' . $order->shippingAddress['district'] . '",
                            "city": "' . $order->shippingAddress['city']['name'][0]['text']  . '",
                            "country": "SA",
                            "lat": "' . $order->shippingAddress['location']['coordinates'][0] . '",
                            "lon": "' . $order->shippingAddress['location']['coordinates'][1] . '",
                },
                "items":' . collect($arrayItems) . ',
            }';
        return $this->loadPost($url, $body, $accessToken);
    }

    /**
     * It returns the return link for the order.
     * 
     * @param orderId The order number of the order to be returned.
     * @param orderItem The order item object returned by the OTO API.
     * 
     * @return The return link is being returned.
     */
    public function OtoOrderReturnRequest($order, $orderId, $orderItem)
    {
        $realUrl = config('services.oto.realUrl');
        $accessToken = 'Bearer' . ' ' . $this->refreshToken($realUrl);

        $url = $realUrl . 'createReturnShipment';
        $item = [];
        $item['quantity'] = $orderItem['quantity'];
        $item['sku'] = $orderItem['skuProduct'];
        $body = '{
                    "orderId": "' . $orderId . '-' . $orderItem->seller['id'] . '",
                    "deliveryOptionId": "' . $order->shippingMethod['deliveryOptionId'] . '",
                    "pickupLocationCode": "' . $orderItem->seller['id'] . '",
                    "items": ' . "[" . collect($item) . "]" . ',
                }';
        return $this->loadPost($url, $body, $accessToken);
    }

    /**
     * It takes the refresh token from the .env file and sends it to the OTO API to get a new access
     * token
     * 
     * @param realUrl The URL of the API you are calling.
     * 
     * @return A JSON object with the new access token and refresh token.
     */
    public function refreshToken($realUrl)
    {
        $refreshToken = config('services.oto.refreshToken');

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $realUrl . 'refreshToken',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                    "refresh_token": ' . $refreshToken . '
                }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $responseJson = json_decode($response, true); // Set second argument as TRUE
        return $responseJson['access_token'];
    }

    /**
     * It takes a URL, a body, and an access token and returns the response from the API
     * 
     * @param url The URL to which you are sending the request.
     * @param body The body of the request.
     * @param accessToken The access token you received from the previous step.
     * 
     * @return The response from the API.
     */
    public function loadPost($url, $body, $accessToken)
    {
        // dd($url, $body, $accessToken);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Authorization:' . $accessToken,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        // dd($response);
        return $response;
    }
}
