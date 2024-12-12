<?php

namespace App\Modules\NutritionSpecialist\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class NutritionSpecialist extends Model
{
    const SHARED_INFO = ['id', 'name', 'workTimes', 'commercialRegisterImage','commercialRegisterNumber', 'rating', 'totalRating', 'published', 'isBusy', 'location','finalPrice', 'rewardPoints', 'purchaseRewardPoints','city','profitRatio'];
}
