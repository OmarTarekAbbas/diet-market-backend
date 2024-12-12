<?php

namespace App\Modules\PackagesClubs\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class PackagesClub extends Model
{
    const SHARED_INFO = ['id', 'name', 'rewardPoints', 'purchaseRewardPoints', 'finalPrice', 'published', 'club','monthsNumber'];
}
