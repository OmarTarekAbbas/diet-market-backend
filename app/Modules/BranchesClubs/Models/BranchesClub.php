<?php

namespace App\Modules\BranchesClubs\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class BranchesClub extends Model
{
    /**
     * TEST Comment 2
     * {@inheritDoc}
     */
    const SHARED_INFO = [
        'id', 'location', 'published', 'mainBranch','workTimes','city',
    ];
}
