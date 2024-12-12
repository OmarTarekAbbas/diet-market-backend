<?php

namespace App\Modules\Meals\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Meal extends Model
{
    /**
     * {@Inheritdoc}
     */
    // const SHARED_INFO = ['id', 'name', 'image', 'protein', 'carbohydrates', 'fat', 'calories', 'published', 'restaurant'];

    /**
     * Method sharedInfo
     *
     * @return array
     */
    public function sharedInfo(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            // 'protein' => $this->protein,
            // 'carbohydrates' => $this->carbohydrates,
            // 'fat' => $this->fat,
            // 'calories' => $this->calories,
            'restaurant' => $this->restaurant,
            'specialDietGrams' => $this->specialDietGrams,
            'specialDietPercentage' => $this->specialDietPercentage,
        ];
    }
}
