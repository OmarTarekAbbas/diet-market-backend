<?php

namespace App\Modules\ServiceProvider\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class ServiceProvider extends Model
{
    /**
     * {@Inheritdoc}
     */
    const SHARED_INFO = ['id', 'serviceType', 'firstName', 'lastName', 'email', 'phoneNumber', 'tradeName', 'country', 'city', 'address', 'commercialNumber', 'commercialImage', 'published','type','joinRequest'];

    /**
     * Method sharedInfo
     *
     * @return array
     */
    // public function sharedInfo(): array
    // {
    //     return [
    //         'id' => $this->id,
    //         'serviceType' => $this->serviceType,
    //         'firstName' => $this->firstName,
    //         'lastName' => $this->lastName,
    //         'email' => $this->email,
    //         'phoneNumber' => $this->phoneNumber,
    //         'tradeName' => $this->tradeName,
    //         'country' => $this->country,
    //         'city' => $this->city,
    //         'address' => $this->address,
    //         'commercialNumber' => $this->commercialNumber,
    //         'commercialImage' => $this->commercialImage,
    //     ];
    // }
}
