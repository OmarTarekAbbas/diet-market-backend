<?php

namespace App\Modules\Clubs\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Club extends Model
{
    const SHARED_INFO = ['id', 'name', 'logo', 'aboutClub',  'images', 'workHours', 'packagesClubs', 'rating', 'totalRating', 'published','city','bookAheadOfTime','mainBranchClub','profitRatio'];
}
