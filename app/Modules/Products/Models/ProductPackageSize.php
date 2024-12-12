<?php

namespace App\Modules\Products\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class ProductPackageSize extends Model
{
    /**
     * {@inheritdoc}
     */
    const SHARED_INFO = [
        'id', 'name', 'heightBox', 'lengthBox', 'weightBox',
    ];
}
