<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait SendMessage
{
    public function sendMessage($numbers, $msg)
    {
        if (!Str::startsWith($numbers, '+')) {
            if (!Str::startsWith($numbers, '966')) {
                $numbers = '966' . $numbers;
            }

            $numbers = '+' . $numbers;
        }
    }
}
