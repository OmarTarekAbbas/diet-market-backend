<?php

namespace App\Modules\StoreManagers\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class DeviceToken extends Model
{
    /**
     * {@Inheritdoc}
     */
    const SHARED_INFO = ['id', 'type', 'token'];
}
