<?php

namespace App\Modules\Stores\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Store extends Model
{
    /**
     * {@inheritDoc}
     */
    const SHARED_INFO = [
        'id', 'name', 'description', 'commercialRecordId', 'logo', 'commercialRecordImage', 'published', 'location', 'shippingMethods','profitRatio',
    ];
}
