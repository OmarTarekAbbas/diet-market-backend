<?php

namespace App\Modules\Wallet\Resources;

use App\Modules\Orders\Repositories\OrdersRepository;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;

class WalletProvider extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'notes', 'title', 'creatorType', 'transactionType', 'reason', 'amount', 'orderId', 'commissionDiteMarket', 'totalAmountOrder', 'type', 'provider'];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = ['notes', 'reason'];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = ['createdAt' => 'd-m-Y'];

    /**
     * Data that has multiple values based on locale codes
     * Mostly this is used with mongodb driver
     *
     * @const array
     */
    const LOCALIZED = [];

    /**
     * List of assets that will have a full url if available
     */
    const ASSETS = [];

    /**
     * Data that will be returned as a resources
     *
     * i.e [city => CityResource::class],
     * @const array
     */
    const RESOURCES = [
        //        'createdBy' => User::class,
    ];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [];

    /**
     * List of keys that will be unset before sending
     *
     * @var array
     */
    protected static $disabledKeys = [];

    /**
     * List of keys that will be taken only
     *
     * @var array
     */
    protected static $allowedKeys = [];

    protected function extend($request)
    {
        $customer = $this->typeProvider($this);
        if ($this->amount) {
            $this->set('amountText', trans('cart.price', ['value' => $this->amount]));
            $this->set('balanceBefore', trans('cart.price', ['value' => $this->balanceBefore]));
            $this->set('balanceAfter', trans('cart.price', ['value' => $customer->walletBalance]));
            $this->set('commissionDiteMarketText', trans('cart.price', ['value' => $this->commissionDiteMarket]));
            if ($this->totalAmountOrder == 0) {
                $this->set('totalAmountOrderText', trans('cart.price', ['value' => $this->amount]));
            } else {
                $this->set('totalAmountOrderText', trans('cart.price', ['value' => $this->totalAmountOrder]));
            }
        }
    }

    /**
     * It returns the model of the provider based on the type of the provider.
     * </code>
     * 
     * @param request 
     * 
     * @return The return value is the result of the function.
     */
    public function typeProvider($request)
    {
        if ($request->type === 'StoreManager') {
            return repo('storeManagers')->get($request->provider['id']);
        } elseif ($request->type === 'RestaurantManager') {
            return repo('restaurantManagers')->get($request->provider['id']);
        } elseif ($request->type === 'ClubManager') {
            return repo('clubManagers')->get($request->provider['id']);
        } elseif ($request->type === 'NutritionSpecialistManger') {
            return repo('nutritionSpecialistMangers')->get($request->provider['id']);
        }
    }
}
