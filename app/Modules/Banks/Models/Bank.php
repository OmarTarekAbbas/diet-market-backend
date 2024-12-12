<?php

namespace App\Modules\Banks\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Bank extends Model
{
    public const SHARED_INFO = ['id', 'name', 'accountNumber', 'IBAN'];
}
