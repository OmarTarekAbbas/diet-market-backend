<?php

namespace App\Modules\DeliveryMen\Models;

use App\Modules\Users\Models\User;
use Illuminate\Notifications\Notifiable;
use App\Modules\Users\Traits\Auth\UpdatePassword;

class DeliveryMan extends User
{
    use Notifiable, UpdatePassword;

    /**
     * {@inheritdoc}
     */
    const SHARED_INFO = ['id', 'firstName', 'lastName', 'phoneNumber', 'codePhoneNumber', 'email', 'idNumber', 'bankAccountNumber', 'birthDate', 'vehicleBrand', 'vehicleModel', 'yearManufacture', 'VehicleSerialNumber', 'accountCardName', 'image', 'cardIdImage', 'driveryLicenseImage', 'VehicleFrontImage', 'VehicleBackImage', 'published', 'approved', 'requested', 'location', 'nationality', 'vehicleType','status','walletBalance'];

    /**
     * {@inheritdoc}
     */
    protected $dates = ['canResendCodeAt','loginUpdateDateAt','logoutUpdateDateAt'];

    /**
     * {@inheritdoc}
     */
    public function accountType(): string
    {
        return 'deliveryMen';
    }

    /**
     * Get Delivery's devices ids for firebase
     *
     * @return array
     */
    public function getFireBaseDevicesIds(): array
    {
        return collect($this->devices)->pluck('token')->toArray();
    }
}
