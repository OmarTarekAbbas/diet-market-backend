<?php

namespace App\Modules\Sections\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Section extends Model
{
    /**
     * {@Inheritdoc}
     */
    const SHARED_INFO = ['id', 'name', 'image', 'published', 'restaurantManager'];

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
            'restaurantManager' => $this->restaurantManager,
        ];
    }
}
