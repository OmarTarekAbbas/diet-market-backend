<?php

namespace App\Modules\ClubManagers\Models;

use App\Modules\Users\Models\User;

class ClubManager extends User
{
    /**
     * Get StoreManager's devices ids for firebase
     *
     * @return array
     */
    public function getFireBaseDevicesIds(): array
    {
        return collect($this->devices)->pluck('token')->toArray();
    }

    public function accountType(): string
    {
        return 'ClubManager';
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
            'phone' => $this->phoneNumber,
            'club' => $this->club,
        ];
    }
}
