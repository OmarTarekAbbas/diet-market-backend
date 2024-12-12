<?php

namespace App\Modules\Nationality\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Nationality extends Model
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
            'code' => $this->code,
            'country_en' => $this->country_en,
            'country_ar' => $this->country_ar,
            'nationality_en' => $this->nationality_en,
            'natianality_ar' => $this->natianality_ar,
        ];
    }
}
