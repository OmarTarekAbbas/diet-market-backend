<?php

namespace App\Modules\SubscriptionMeals\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class SubscriptionMeal extends Model
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
            'items' => $this->items,
            'type' => $this->type,
            'dateTimeMeals' => $this->dateTimeMeals,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];
    }
}
