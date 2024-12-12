<?php

namespace App\Modules\NutritionSpecialist\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class NutritionSpecialistNotesCustomer extends Model
{
    const SHARED_INFO = ['id', 'customer', 'notes'];
}
