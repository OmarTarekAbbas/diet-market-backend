<?php

// Copyright
declare(strict_types=1);

namespace App\Modules\Users\Events;

use App\Modules\General\Helpers\Visitor;
use App\Modules\Cart\Helpers\VisitorCart;

class WithVisitorCart
{
    /**
     * set cart in response visitor
     * @param $response
     * @return mixed
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    public function sendCart($response)
    {
        if (!empty(Visitor::getDeviceId()) && !user() && !isset($response['cart'])) {
            $response['cart'] = VisitorCart::getCart(Visitor::getDeviceId(), false);
        }

        return $response;
    }
}
