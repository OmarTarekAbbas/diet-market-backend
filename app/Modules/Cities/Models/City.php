<?php

namespace App\Modules\Cities\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class City extends Model
{
    const SHARED_INFO = ['id', 'name'];
}
