<?php

namespace App\Modules\Regions\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Region extends Model
{
    /**
     * {@inheritDoc}
     */
    const SHARED_INFO = ['id', 'name'];
}
