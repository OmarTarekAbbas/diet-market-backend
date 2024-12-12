<?php

namespace App\Modules\Brands\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Brand extends Model
{
    /**
     * {@inheritdoc}
     */
    const SHARED_INFO = ['id', 'name', 'logo'];
}
