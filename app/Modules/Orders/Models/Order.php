<?php

namespace App\Modules\Orders\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Order extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $dates = [];

    /**
     * {@inheritdoc}
     */
    // const SHARED_INFO = ['items'];
}
