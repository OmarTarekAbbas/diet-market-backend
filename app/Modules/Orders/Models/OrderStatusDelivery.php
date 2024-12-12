<?php

namespace App\Modules\Orders\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class OrderStatusDelivery extends Model
{
    /**
     * {@inheritdoc}
     */
    const SHARED_INFO = ['id', 'orderId', 'orderDeliveryId', 'status', 'message', 'creator','creatorBy','deliveryMen','createdAt'];
}
