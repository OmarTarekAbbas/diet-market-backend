<?php

namespace App\Modules\Transactions\Models;

use HZ\Illuminate\Mongez\Traits\MongoDB\RecycleBin;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Transaction extends Model
{
    use RecycleBin;
}
