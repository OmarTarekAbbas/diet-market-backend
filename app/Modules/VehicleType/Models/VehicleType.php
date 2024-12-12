<?php

namespace App\Modules\VehicleType\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class VehicleType extends Model
{
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
            'published' => $this->published,
        ];
    }
}
