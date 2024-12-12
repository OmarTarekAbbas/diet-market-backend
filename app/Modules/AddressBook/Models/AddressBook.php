<?php

namespace App\Modules\AddressBook\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class AddressBook extends Model
{
    /**
     *  {@inheritDoc}
     */
    const SHARED_INFO = [
        'id', 'receiverName','email' ,'firstName', 'lastName', 'address', 'phoneNumber', 'buildingNumber', 'flatNumber', 'floorNumber', 'district', 'isPrimary', 'type', 'specialMark', 'verified', 'verificationCode', 'city', 'country','location',
    ];
}
