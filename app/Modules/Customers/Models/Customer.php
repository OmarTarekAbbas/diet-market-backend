<?php

namespace App\Modules\Customers\Models;

use App\Modules\Users\Models\User;
use Illuminate\Notifications\Notifiable;
use App\Modules\Customers\Services\Coupon;
use App\Modules\Customers\Services\CustomerCart;
use App\Modules\Users\Traits\Auth\UpdatePassword;

class Customer extends User
{
    use Notifiable, UpdatePassword;

    /**
     * Device token model
     *
     * @const string
     */
    const DEVICE_TOKEN_MODEL = CustomerDeviceToken::class;

    /**
     * {@Inheritdoc}
     */
    const SHARED_INFO = ['id', 'firstName', 'lastName', 'phoneNumber'];

    /**
     * Customer cart
     */
    protected static $customerCart;

    /**
     * Customer coupon
     */
    protected static $coupon;

    /**
     * {@inheritDoc}
     */
    public function accountType(): string
    {
        return 'customer';
    }

    /**
     * Get cart manager for current user
     *
     * @param bool $isSubscription
     * @return CustomerCart
     */
    public function getCart($isSubscription = false): CustomerCart
    {
        if (!static::$customerCart) {
            static::$customerCart = new CustomerCart($this, $isSubscription);
        }

        return static::$customerCart;
    }

    /**
     * Get current customer coupon manager
     *
     * @return Coupon
     */
    public function coupon(): Coupon
    {
        if (!static::$coupon) {
            static::$coupon = new Coupon($this);
        }

        return static::$coupon;
    }

    public function sharedInfo(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber,

        ];
    }
}
