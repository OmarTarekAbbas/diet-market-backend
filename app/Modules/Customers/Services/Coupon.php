<?php

namespace App\Modules\Customers\Services;

use App\Modules\Customers\Models\Customer;
use App\Modules\Coupons\Models\Coupon as Model;
use HZ\Illuminate\Mongez\Traits\RepositoryTrait;

class Coupon
{
    use RepositoryTrait;
    
    /**
     * Customer Object
     *
     * @var Customer
     */
    private $customer;

    /**
     * Current Coupon Model
     *
     * @var Model
     */
    private $currentCoupon;

    /**
     * Constructor
     *
     * @param Customer $customer
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;

        if (! empty($this->customer->currentCoupon['id'])) {
            $this->currentCoupon = $this->couponsRepository->getModel($this->customer->currentCoupon['id']);
        }
    }

    /**
     * Get Customer Current Applied Coupon and clear it
     *
     * @return Model
     */
    public function pluck(): Model
    {
        $coupon = $this->get();

        $this->clear();

        return $coupon;
    }

    /**
     * Get current coupon
     *
     * @return Model|null
     */
    public function get():? Model
    {
        return $this->currentCoupon;
    }

    /**
     * Remove current coupon
     *
     * @return void
     */
    public function clear(): void
    {
        $this->currentCoupon = null;
        $this->customer->currentCoupon = null;
        $this->customer->save();
    }

    /**
     * Set the given coupon as current coupon for customer
     *
     * @param Model $coupon
     * @return void
     */
    public function set(Model $coupon): void
    {
        $this->customer->currentCoupon = $coupon->sharedInfo();
        $this->customer->save();
    }

    /**
     * Check if customer has a current coupon
     *
     * @return bool
     */
    public function exists(): bool
    {
        return ! empty($this->customer->currentCoupon);
    }
}
