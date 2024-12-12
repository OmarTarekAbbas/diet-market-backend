<?php

namespace App\Modules\Users\Models;

use App\Modules\Users\Traits\Deviceable;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Modules\Users\Traits\Auth\UpdatePassword;
use HZ\Illuminate\Mongez\Traits\MongoDB\RecycleBin;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class User extends Model implements Authenticatable
{
    use AuthenticatableTrait, UpdatePassword, RecycleBin, Deviceable;

    /**
     * Device token model
     *
     * @const string
     */
    const DEVICE_TOKEN_MODEL = DeviceToken::class;

    /**
     * Get shared info for the user that will be stored as a sub document of another collection
     *
     * @return array
     */
    public function sharedInfo(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }

    /**
     * Get account type
     *
     * @return string
     */
    public function accountType(): string
    {
        return 'user';
    }
}
