<?php

namespace App\Modules\Products\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class ProductOption extends Model
{
    /**
     * {@inheritdoc}
     */
    const SHARED_INFO = ['id', 'option', 'type', 'required', 'values'];
}
