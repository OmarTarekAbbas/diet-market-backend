<?php

namespace App\Modules\Customers\Services;

use App\Modules\Coupons\Models\Coupon;
use App\Modules\Customers\Models\Customer;
use HZ\Illuminate\Mongez\Traits\RepositoryTrait;

class CustomerCart
{
    use RepositoryTrait;

    /**
     * Customer Object
     *
     * @var Customer
     */
    private $customer;

    /**
     * @var false
     */
    private $isSubscription;

    /**
     * Constructor
     *
     * @param Customer $customer
     * @param bool $isSubscription
     */
    public function __construct(Customer $customer, $isSubscription = false)
    {
        $this->customer = $customer;
        $this->isSubscription = $isSubscription;
    }

    /**
     * Check if customer cart is empty
     *
     * @param bool $isSubscription
     * @return bool
     */
    public function isEmpty($request): bool
    {
        if ($request == "products") {
            return empty($this->customer->cart['items']);
        } elseif ($request == "food") {
            return empty($this->customer->cartMeal['items']);
        }
    }

    /**
     * check if valid cart
     *
     * @param array $checkData
     * @return array
     */
    public function isValid($checkData): array
    {
        // dd('sdsdsd');

        if ($this->isSubscription) {
            $cart = $this->customer->cartSubscription;
            $checkData = array_diff($checkData, ["shippingFees", "shippingMethod"]);
        } else {
            if (request()->type == 'food') {
                $cart = $this->customer->cartMeal;
            } else {
                $cart = $this->customer->cart;
            }
        }

        foreach ($checkData as $data) {
            if (!in_array($data, array_keys($cart))) {
                return [false, $data];
            }
        }

        return [true, null];
    }

    /**
     * Add multiple items to customer cart
     *
     * @param array $items
     * @param $userId
     * @return void
     */
    public function addMultiple(array $items, int $userId)
    {
        // dd($items, 'omar');
        foreach ($items as $item) {
            $this->cartRepository->create([
                'item' => (int) $item['item'],
                'quantity' => (int) $item['quantity'] ?? 1,
                'customerId' => $userId,
                'options' => $item['options'],
                'type' => $item['type'],
                'subscription' => (bool) $item['subscription'],
            ]);
        }
    }

    /**
     * Clear cart
     *
     * @return void
     */
    public function flush(): void
    {
        $this->cartRepository->flush($this->customer->id, $this->isSubscription);
    }

    /**
     * Clear cart
     *
     * @return void
     */
    public function flushFood(): void
    {
        $this->cartRepository->flushFood($this->customer->id, $this->isSubscription);
    }

    /**
     * Method flushPrdouct
     *
     * @return void
     */
    public function flushPrdouct(): void
    {
        $this->cartRepository->flushPrdouct($this->customer->id, $this->isSubscription);
    }

    /**
     * Set the given coupon as current coupon for customer
     *
     * @param Coupon $coupon
     * @return void
     */
    public function setCurrentCoupon(Coupon $coupon): void
    {
        $this->customer->currentCoupon = $coupon->sharedInfo();
        $this->customer->save();
    }

    /**
     * Check if customer has a current coupon
     *
     * @return bool
     */
    public function hasCoupon(): bool
    {
        return !empty($this->customer->currentCoupon);
    }

    /**
     * Get sub total price i.e total price for items only
     *
     * @param bool $isSubscription
     * @return float
     */
    public function getSubTotal($isSubscription = false): float
    {
        if ($this->isSubscription) {
            return $this->customer->cartSubscription['totalPrice'];
        } else {
            if (request()->type == 'food') {
                return $this->customer->cartMeal['totalPrice'];
            } else {
                return $this->customer->cart['totalPrice'];
            }
        }
    }

    /**
     * Get final total price i.e total price for items only
     *
     * @return float
     */
    public function getFinalPrice(): float
    {
        if ($this->isSubscription) {
            return $this->customer->cartSubscription['finalPrice'];
        } else {
            if (request()->type == 'food') {
                return $this->customer->cartMeal['finalPrice'];
            } else {
                return $this->customer->cart['finalPrice'];
            }
        }
    }
}
