<?php

namespace App\Modules\Coupons\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Coupon extends Model
{
    /**
     * {@inheritDoc}
     */
    const SHARED_INFO = ['id', 'code', 'type', 'value', 'startsAt', 'endsAt', 'couponDiscount','typeCoupon'];

    /**
     * {@inheritDoc}
     */
    protected $dates = ['startsAt', 'endsAt'];
}
