<?php

namespace App\Modules\Complaints\Models;

use HZ\Illuminate\Mongez\Traits\MongoDB\RecycleBin;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Complaint extends Model
{
    use RecycleBin;

    const SHARED_INFO = ['id', 'customer', 'orderId', 'reason', 'note','images'];
}
