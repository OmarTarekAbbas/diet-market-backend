<?php

namespace App\Modules\Rewards\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Reward extends Model
{
    protected $dates = ['expireDate'];
}
