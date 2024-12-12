<?php

namespace App\Modules\ClubBookings\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class ClubBooking extends Model
{
    protected $dates = ['date'];

    const SHARED_INFO = ['id','date','time','name','phone','status'];
}
