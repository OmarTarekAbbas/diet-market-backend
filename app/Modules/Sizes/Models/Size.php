<?php

namespace App\Modules\Sizes\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Size extends Model
{
    /**
     * {@Inheritdoc}
     */
    const SHARED_INFO = ['id', 'name', 'price', 'published'];

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
            'price' => $this->price,
        ];
    }
}
