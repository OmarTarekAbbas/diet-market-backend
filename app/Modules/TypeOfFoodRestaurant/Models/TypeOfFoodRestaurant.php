<?php

namespace App\Modules\TypeOfFoodRestaurant\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class TypeOfFoodRestaurant extends Model
{
    const SHARED_INFO = ['id', 'name', 'published'];
}
