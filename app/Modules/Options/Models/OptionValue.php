<?php

namespace App\Modules\Options\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class OptionValue extends Model
{
    /**
     * {@inheritdoc}
     */
    const SHARED_INFO = ['id', 'name', 'sortOrder', 'image', 'defaultPrice', 'price','subtractFromStock'];
}
