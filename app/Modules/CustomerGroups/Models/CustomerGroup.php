<?php

namespace App\Modules\CustomerGroups\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class CustomerGroup extends Model
{
    public const SHARED_INFO = ['id', 'name', 'conditionType', 'conditionValue', 'specialDiscount', 'freeShipping', 'freeExpressShipping', 'nameGroup', 'published'];
}
