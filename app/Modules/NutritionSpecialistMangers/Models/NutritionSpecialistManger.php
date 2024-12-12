<?php

namespace App\Modules\NutritionSpecialistMangers\Models;

use App\Modules\Users\Models\User;
use Illuminate\Notifications\Notifiable;
use App\Modules\Users\Traits\Auth\UpdatePassword;
use HZ\Illuminate\Mongez\Traits\MongoDB\RecycleBin;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class NutritionSpecialistManger extends User
{
    use AuthenticatableTrait, UpdatePassword, RecycleBin, Notifiable;

    /**
     * {@Inheritdoc}
     */
    // const SHARED_INFO = ['id', 'name', 'email', 'restaurant'];

    /**
     * {@inheritDoc}
     */
    public function accountType(): string
    {
        return 'NutritionSpecialistManger';
    }

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
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber,
            'image' => $this->image,
            'description' => $this->description,
            'rating' => $this->rating,
            'totalRating' => $this->totalRating,
            'nutritionSpecialist' => $this->nutritionSpecialist,
        ];
    }

    /**
     * Get RestaurantManager's devices ids for firebase
     *
     * @return array
     */
    public function getFireBaseDevicesIds(): array
    {
        return collect($this->devices)->pluck('token')->toArray();
    }
}
