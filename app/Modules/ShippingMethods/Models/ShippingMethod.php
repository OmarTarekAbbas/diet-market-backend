<?php

namespace App\Modules\ShippingMethods\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class ShippingMethod extends Model
{
    public const SHARED_INFO = ['id', 'name', 'shippingFees', 'expectedDeliveryIn', 'type', 'image', 'cities', 'deliveryOptionId', 'skus'];
}
