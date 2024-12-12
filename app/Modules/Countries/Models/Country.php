<?php

namespace App\Modules\Countries\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Country extends Model
{
    /**
     * {@inheritdoc}
     */
    const SHARED_INFO = ['id', 'name','published'];
}
