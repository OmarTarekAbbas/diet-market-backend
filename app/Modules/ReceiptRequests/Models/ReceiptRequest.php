<?php

namespace App\Modules\ReceiptRequests\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class ReceiptRequest extends Model
{
    /**
     * {@Inheritdoc}
     */
    // const SHARED_INFO = [
    //     'id', 'items', 'receiptRequestsHours', 'notes', 'type', 'firstName', 'lastName', 'phoneNumber', 'city',
    //     'residentialQuarter', 'address'
    // ];

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
            'receiptRequestsHours' => $this->receiptRequestsHours,
            'notes' => $this->notes,
            'type' => $this->type,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'phoneNumber' => $this->phoneNumber,
            'city' => $this->city,
            'residentialQuarter' => $this->residentialQuarter,
            'address' => $this->address,
        ];
    }
}
