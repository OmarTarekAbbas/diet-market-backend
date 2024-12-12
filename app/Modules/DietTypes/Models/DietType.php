<?php

namespace App\Modules\DietTypes\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class DietType extends Model
{
    /**
     * {@Inheritdoc}
     */
    const SHARED_INFO = ['id', 'name', 'description', 'image', 'proteinRatio', 'carbohydrateRatio', 'fatRatio', 'published'];
}
