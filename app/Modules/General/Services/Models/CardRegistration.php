<?php

namespace App\Modules\Services\Models;

use HZ\Illuminate\Mongez\Traits\MongoDB\RecycleBin;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class CardRegistration extends Model
{
    use RecycleBin;

    const SHARED_INFO = ['id', 'card', 'paymentBrand'];
}
