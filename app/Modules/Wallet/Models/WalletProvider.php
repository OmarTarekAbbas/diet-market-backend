<?php

namespace App\Modules\Wallet\Models;

use HZ\Illuminate\Mongez\Traits\MongoDB\RecycleBin;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class WalletProvider extends Model
{
    use RecycleBin;
}
