<?php

namespace App\Modules\General\Helpers;

class Visitor
{
    /**
     * Get current visitor device id
     * Usually used with mobile apps requests
     *
     * @return string
     */
    public static function getDeviceId(): ?string
    {
        return request()->header('DEVICE-ID', null);
    }
}
