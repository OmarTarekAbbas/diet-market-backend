<?php

namespace App\Modules\Options\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Option extends Model
{
    /**
     * {@inheritdoc}
     */
    const SHARED_INFO = ['id','name','type','isMultiSelection','typeProduct'];
}
