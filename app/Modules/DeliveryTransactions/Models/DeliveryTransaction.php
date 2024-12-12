<?php

namespace App\Modules\DeliveryTransactions\Models;

use HZ\Illuminate\Mongez\Traits\MongoDB\RecycleBin;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class DeliveryTransaction extends Model
{
    use RecycleBin;
}
