<?php

namespace App\Modules\Items\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Item extends Model
{
    /**
     * {@Inheritdoc}
     */
    // const SHARED_INFO = ['id', 'name', 'image', 'protein', 'carbohydrates', 'fat', 'calories','published', 'categories', 'sizes','restaurant'];

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
            'protein' => $this->protein,
            'carbohydrates' => $this->carbohydrates,
            'fat' => $this->fat,
            'calories' => $this->calories,
            'categories' => $this->categories,
            'sizes' => $this->sizes,
            'restaurant' => $this->restaurant,
        ];
    }
}
