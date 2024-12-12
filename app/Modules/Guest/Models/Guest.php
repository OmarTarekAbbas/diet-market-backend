<?php

namespace App\Modules\Guest\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Guest extends Model
{
    const SHARED_INFO = ['id', 'customerDeviceId', 'location'];
}
