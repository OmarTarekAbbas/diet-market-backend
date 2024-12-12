<?php

namespace App\Modules\Banks\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class BankTransfer extends Model
{
    public const SHARED_INFO = [
        'id', 'accountName', 'accountNumber', 'notes', 'amount', 'status', 'bankTransferFrom', 'bankTransferTo', 'transferDate', 'bankImage',
    ];
}
