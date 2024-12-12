<?php

namespace App\Modules\Orders\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class OrderDelivery extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $dates = ['createdAt'];

    /**
     * {@inheritdoc}
     */
    const SHARED_INFO = ['id', 'deliveryMenId', 'customerId', 'addressOrderCustomer', 'status', 'distanceToTheRestaurant', 'distanceToTheCustomer', 'totalDistance', 'minuteToTheRestaurant', 'minuteToTheCustomer', 'totalMinute'];
}
