<?php

namespace App\Modules\HealthyData\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class HealthyDatum extends Model
{
    /**
     *  {@inheritDoc}
     */
    const SHARED_INFO = [
        'id', 'healthInfo','dietTypes','type','specialDiet','customerId','specialDietGrams','specialDietPercentage',
    ];
}
