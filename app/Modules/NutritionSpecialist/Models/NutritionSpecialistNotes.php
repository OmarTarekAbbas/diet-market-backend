<?php

namespace App\Modules\NutritionSpecialist\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class NutritionSpecialistNotes extends Model
{
    const SHARED_INFO = ['id', 'order', 'notes'];
}
