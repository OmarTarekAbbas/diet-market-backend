<?php

namespace App\Modules\RestaurantManager\Models;

use App\Modules\Users\Models\User;
use Illuminate\Notifications\Notifiable;
use App\Modules\Users\Traits\Auth\UpdatePassword;
use HZ\Illuminate\Mongez\Traits\MongoDB\RecycleBin;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class RestaurantManager extends User
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
        return 'RestaurantManager';
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
            'restaurant' => $this->restaurant,
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
