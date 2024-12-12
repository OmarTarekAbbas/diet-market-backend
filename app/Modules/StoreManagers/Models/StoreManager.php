<?php

namespace App\Modules\StoreManagers\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Modules\Users\Traits\Auth\UpdatePassword;
use HZ\Illuminate\Mongez\Traits\MongoDB\RecycleBin;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class StoreManager extends Model implements Authenticatable
{
    use AuthenticatableTrait, UpdatePassword, RecycleBin, Notifiable;

    /**
     * {@Inheritdoc}
     */
    const SHARED_INFO = ['id', 'firstName', 'lastName', 'email', 'phoneNumber', 'address', 'store', 'totalRating', 'city', 'published'];

    /**
     * {@inheritDoc}
     */
    public function accountType(): string
    {
        return 'StoreManager';
    }

    /**
     * Get StoreManager's devices ids for firebase
     *
     * @return array
     */
    public function getFireBaseDevicesIds(): array
    {
        return collect($this->devices)->pluck('token')->toArray();
    }
}
