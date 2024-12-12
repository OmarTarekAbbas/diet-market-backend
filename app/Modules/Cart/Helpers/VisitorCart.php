<?php

namespace App\Modules\Cart\Helpers;

use Illuminate\Support\Arr;
use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Models\CartItem;
use App\Modules\General\Helpers\Visitor;
use App\Modules\Customers\Models\Customer;
use HZ\Illuminate\Mongez\Traits\RepositoryTrait;

class VisitorCart
{
    use RepositoryTrait;

    /**
     * Get current visitor cart items
     *
     * @param $deviceId
     * @param $subscription
     * @return array
     * @throws \HZ\Illuminate\Mongez\Exceptions\NotFoundRepositoryException
     */
    public static function getCart($deviceId, $subscription)
    {
        $cart = repo('cart')->getByDeviceId($deviceId, $subscription);

        return repo('cart')->wrap($cart);
    }

    /**
     * Get current visitor cart items
     *
     * @param $deviceId
     * @return array
     */
    public static function getCartItem($deviceId): array
    {
        return CartItem::where('deviceId', $deviceId)->get()->map(function ($item) {
            return [
                'item' => $item->product['id'],
                'quantity' => $item->quantity,
            ];
        })->toArray();
    }

    /**
     * Get current visitor cart items and delete form visitor cart
     *
     * @param $deviceId
     * @return array
     */
    public static function getCartItemWithDelete($deviceId): array
    {
        $cartItem = CartItem::where('deviceId', $deviceId)->where('customer', null)->get()->map(function ($item) {
            $options = [];

            foreach ($item['options'] as $option) {
                $options[] = [
                    'id' => $option['id'],
                    'values' => Arr::pluck($option['values'], 'id'),
                ];
            }

            return [
                'item' => $item->product['id'],
                'quantity' => $item->quantity,
                'type' => $item->type,
                'options' => $options ?? [],
                'subscription' => (bool) ($item['subscription'] ?? false),
            ];
        })->toArray();

        Cart::where('deviceId', $deviceId)->where('customer', null)->delete();
        CartItem::where('deviceId', $deviceId)->where('customer', null)->delete();

        return $cartItem;
    }

    /**
     * Update visitor cart items to belong to the given customer
     *
     * @param  string $deviceId
     * @param  Customer $customer
     * @return array
     */
    public static function updateVisitorItemsForCustomer(string $deviceId, Customer $customer): array
    {
        $cartItems = CartItem::where('deviceId', $deviceId)->whereNull('customer')->get();

        $customerInfo = $customer->sharedInfo();

        foreach ($cartItems as $cartItem) {
            $cartItem->customer = $customerInfo;
            $cartItem->save();
        }

        return $cartItems->map(function ($item) {
            $options = [];

            foreach ($item['options'] as $option) {
                $options[] = [
                    'id' => $option['id'],
                    'values' => Arr::pluck($option['values'], 'id'),
                ];
            }

            return [
                'itemId' => $item->id,
                'item' => $item->product['id'],
                'quantity' => $item->quantity,
                'type' => $item->type,
                'options' => $options ?? [],
                'subscription' => (bool) ($item['subscription'] ?? false),
            ];
        })->toArray();
    }

    public static function getCartItems($deviceId): array
    {
        $cartItem = CartItem::where('deviceId', $deviceId)->where('customer', null)->get()->map(function ($item) {
            $options = [];

            foreach ($item['options'] as $option) {
                $options[] = [
                    'id' => $option['id'],
                    'values' => Arr::pluck($option['values'], 'id'),
                ];
            }

            return [
                'item' => $item->product['id'],
                'quantity' => $item->quantity,
                'type' => $item->type,
                'options' => $options ?? [],
                'subscription' => (bool) ($item['subscription'] ?? false),
            ];
        })->toArray();

        return $cartItem;
    }

    public static function getCartItemWithUpdate($deviceId, $user)
    {
        $cartItem = CartItem::where('deviceId', $deviceId)->where('customer', null)->update([
            'customer' => $user->sharedInfo(),
        ]);

        $cart = Cart::where('deviceId', $deviceId)->where('customer', null)->update([
            'customer' => $user->sharedInfo(),
        ]);





        /*
        $cartItems = CartItem::where('deviceId', $deviceId)->get();



        $cartItemsTypes = $cartItems->unique('type')->toArray();

        if (in_array('products', $cartItemsTypes)) {
            $customerProductsCart = getCustomerCart($user, 'products');
        }
        if (in_array('meal', $cartItemsTypes)) {
            $customerCartMeals = getCustomerCart($user, 'food');
        }
        $customerInfo = $user->sharedInfo();
        foreach ($cartItems as $cartItem) {
            $cartItem->customer = $customerInfo;
            $cartItem->save();
            if ($cartItem->type === 'products') {
                $customerProductsCart->reassociate($cartItem, 'items');
            } elseif ($cartItem->type === 'food') {
                $customerCartMeals->reassociate($cartItem, 'items');
            }
        }
        if (!empty($customerProductsCart)) {
            repo('cart')->updateCartTotals($customerProductsCart);

            $customerProductsCart->save();
        }
        if (!empty($customerCartMeals)) {
            repo('cart')->updateCartTotals($customerCartMeals);
            $customerCartMeals->save();
        }
        $user->save();

    */
    }
}
