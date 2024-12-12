<?php

namespace App\Modules\Services\Models;

use HZ\Illuminate\Mongez\Traits\MongoDB\RecycleBin;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class NoonPaymentLog extends Model
{
    use RecycleBin;

    public function log($data)
    {
    }
}
