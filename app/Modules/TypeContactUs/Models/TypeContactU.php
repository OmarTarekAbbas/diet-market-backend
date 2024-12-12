<?php

namespace App\Modules\TypeContactUs\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class TypeContactU extends Model
{
    /**
     * {@Inheritdoc}
     */
    const SHARED_INFO = ['id', 'name', 'published'];
}
