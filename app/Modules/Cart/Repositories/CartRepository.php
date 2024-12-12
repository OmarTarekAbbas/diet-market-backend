<?php

namespace App\Modules\Cart\Repositories;

use Exception;
use Carbon\Carbon;
use App\Modules\Cart\Models\CartItem;
use App\Modules\General\Helpers\Visitor;
use App\Modules\Products\Models\Product;
use App\Modules\Cart\Models\Cart as Model;
use App\Modules\Customers\Models\Customer;
use App\Modules\Products\Models\ProductOption;
use App\Modules\Restaurants\Models\Restaurant;
use App\Modules\Cart\Resources\Cart as Resource;
use App\Modules\StoreManagers\Models\StoreManager;
use App\Modules\RestaurantManager\Models\RestaurantManager;
use HZ\Illuminate\Mongez\Contracts\Repositories\RepositoryInterface;
use HZ\Illuminate\Mongez\Managers\Database\MongoDB\RepositoryManager;

class CartRepository extends RepositoryManager implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'cart';

    /**
     * {@inheritDoc}
     */
    const MODEL = Model::class;

    /**
     * {@inheritDoc}
     */
    const RESOURCE = Resource::class;

    /**
     * Set the columns of the data that will be auto filled in the model
     *
     * @const array
     */
    const DATA = ['type'];

    /**
     * Auto save uploads in this list
     * If it's an indexed array, in that case the request key will be as database column name
     * If it's associated array, the key will be request key and the value will be the database column name
     *
     * @const array
     */
    const UPLOADS = [];

    /**
     * Auto fill the following columns as arrays directly from the request
     * It will encoded and stored as `JSON` format,
     * it will be also auto decoded on any database retrieval either from `list` or `get` methods
     *
     * @const array
     */
    const ARRAYBLE_DATA = [];

    /**
     * Set columns list of integers values.
     *
     * @cont array
     */
    const INTEGER_DATA = [];

    /**
     * Set columns list of float values.
     *
     * @cont array
     */
    const FLOAT_DATA = [];

    /**
     * Set columns of booleans data type.
     *
     * @cont array
     */
    const BOOLEAN_DATA = ['subscription'];

    /**
     * Set the columns will be filled with single record of collection data
     * i.e [country => CountryModel::class]
     *
     * @const array
     */
    const DOCUMENT_DATA = [
        // 'seller' => StoreManager::class,
        // 'restaurantManger' => RestaurantManager::class,
        // 'restaurant' => Restaurant::class,

    ];

    /**
     * Set the columns will be filled with array of records.
     * i.e [tags => TagModel::class]
     *
     * @const array
     */
    const MULTI_DOCUMENTS_DATA = [
        'seller' => StoreManager::class,
    ];

    /**
     * Add the column if and only if the value is passed in the request.
     *
     * @cont array
     */
    const WHEN_AVAILABLE_DATA = ['couponDiscount', 'coupon'];

    /**
     * Filter by columns used with `list` method only
     *
     * @const array
     */
    const FILTER_BY = [];

    /**
     * Set all filter class you will use in this module
     *
     * @const array
     */
    const FILTERS = [];

    /**
     * Determine wether to use pagination in the `list` method
     * if set null, it will depend on pagination configurations
     *
     * @const bool
     */
    const PAGINATE = false;

    /**
     * Number of items per page in pagination
     * If set to null, then it will taken from pagination configurations
     *
     * @const int|null
     */
    const ITEMS_PER_PAGE = null;

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function create($request)
    {
        if (is_array($request)) {
            $request = (object) $request;
        }

        $itemId = (int) $request->item; // item id

        $type = $request->type;

        if ($type == 'products') {
            $item = $this->productsRepository->getQuery()->where('type', $type)->where('id', $itemId)->first();
        } elseif ($type == 'food') {
            $item = $this->productMealsRepository->getQuery()->where('type', $type)->where('id', $itemId)->first();
        } else {
            throw new Exception(trans('cart.missingData.invalidType'));
        }

        $quantity = (((int) $request->quantity) ?: 1);

        if (!$item || !$item->published) {
            if ($type == 'products') {
                throw new Exception(trans('cart.notFoundItem'));
            } else {
                if (request()->url() == url('api/orders/' . request()->route('orderId') . '/reorder')) {
                } else {
                    throw new Exception(trans('cart.notFoundItemMeals'));
                }
            }
        }



        // if (is_null($item->price)) {
        //     throw new Exception(trans('cart.errorInPrice'));
        // }
        if ($type == 'products') {
            if (is_null($item->price) || is_null($item->finalPrice)) {
                throw new Exception(trans('cart.errorInPrice'));
            }
        } else {
            if (is_null($item->price)) {
                throw new Exception(trans('cart.errorInPrice'));
            }
        }


        if ($item->restaurant) {
            $restaurants = $this->restaurantsRepository->isClosedRestaurants($item->restaurant['id']);
            if (isset($restaurants->isBusy) && $restaurants->isBusy) {
                throw new Exception(trans('cart.isBusyRestaurants'));
            }
            if (isset($restaurants->isClosed) && $restaurants->isClosed) {
                throw new Exception(trans('cart.isClosedRestaurants'));
            }

            if (isset($restaurants->published) && $restaurants->published == false) {
                throw new Exception(trans('cart.isClosedPublished'));
            }
        }

        $customer = $request->customerId ? $this->customersRepository->getModel($request->customerId) : user();

        $cart = $this->getCurrentCart($customer->id ?? null);
        if (isset($cart['restaurant']) && $item->restaurant['id'] != $cart['restaurant']['id']) {
            if (app()->getLocale() == 'en') {
                $name = $cart['restaurant']['name'][1];
            } else {
                $name = $cart['restaurant']['name'][0];
            }

            throw new Exception(trans('cart.restaurantIdNotMatchClearCart', ['value' => $name['text']]));
        }

        // this is set in CustomersRepository Login method
        // it will ignore adding more quantity as we just only update it to be for the customer
        // instead of visitor
        if (request()->withoutQuantity) {
            $quantity = 0;
        }

        // get user

        // get Visitor id
        $deviceId = Visitor::getDeviceId();

        if (!$customer && $request->customerId) {
            $customer = $this->customersRepository->getModel((int) $request->customerId);
            $deviceId = null;
        }

        $options = $request->options ?? [];

        $cart['subscription'] = (bool) ($request->subscription ?? false);

        $uniqueId = $this->getUniqueIdForCartItem([
            'customerId' => $customer->id ?? null,
            'itemId' => $itemId,
            'options' => $options,
            'subscription' => $cart['subscription'],
            // 'deviceId' => $deviceId,
            // 'type' => ($request->type == 'products') ? 1 : 2,
            // 'type' => $request->type,
        ]);

        $cartItem = CartItem::where('uniqueId', $uniqueId);

        if ($customer) {
            $cartItem = $cartItem->where('customer.id', $customer->id);
        } else {
            $cartItem = $cartItem->where('deviceId', $deviceId);
        }

        $cartItem = $cartItem->first();
        if ($request->quantity) {
            $itemQuantityWithExistingQuantity = $quantity + ($cartItem ? $cartItem->quantity : 0);
        } else {
            $itemQuantityWithExistingQuantity = $quantity;
        }
        if ($type == 'products') {
            $this->validateQuantity($item, $itemQuantityWithExistingQuantity);
        }
        $itemData = $item->sharedInfo();
        if ($itemData['hasRequiredOptions'] && empty($options)) {
            throw new Exception(trans('cart.productHasRequiredOptions'));
        }
        if ($type == 'products') {
            if (request()->url() == url('api/orders/' . request()->route('orderId') . '/reorder')) {
                if ($item['availableStock'] != 0 && $item['availableStock'] < $itemQuantityWithExistingQuantity) {
                    $itemQuantityWithExistingQuantity = $item['availableStock'];
                }
            }
        }

        if (!$cartItem) {
            $cartItem = CartItem::query()->updateOrCreate([
                'product' => $itemData,
                'customer' => $customer ? $customer->sharedInfo() : null,
                // 'deviceId' => $deviceId,
                'subscription' => $cart['subscription'],
                'options' => $cartItem->options ?? null,
                'seller' => $item->storeManager ?? null,

            ], [
                'deviceId' => $deviceId,
                'uniqueId' => $uniqueId,
                'purchaseRewardPoints' => $itemData['purchaseRewardPoints'] ?? 0,
                'rewardPoints' => $itemData['rewardPoints'] ?? 0,
                'quantity' => $itemQuantityWithExistingQuantity,
                'options' => $options,
                'type' => $type,
                'notes' => $request->notes ?? null,
                'seller' => $item->storeManager ?? null,
            ]);
        } else {
            $cartItem->quantity = $itemQuantityWithExistingQuantity;
            $cartItem->product = $itemData;
            $cartItem->deviceId = $deviceId;
            $cartItem->notes = $request->notes ?? null;
        }


        $cartItem->customer = $customer ? $customer->sharedInfo() : null;



        if ($cart['subscription']) {
            $finalPrice = $this->getSubscriptionPrice($cartItem->product);
        } else {
            $finalPrice = $this->getProductPrice($cartItem->product);
        }
        $itemOptions = collect($item->options ?? []);
        foreach ($options as $optionKey => &$option) {
            $option['id'] = (int) $option['id'];
            $itemOption = $itemOptions->where('id', (int) $option['id'])->first();

            if (!$itemOption) {
                unset($options[$optionKey]);

                continue;
            }

            $valuesList = $option['values'];
            $option = $itemOption;

            $itemOptionValues = collect($itemOption['values'] ?? []);

            $optionValues = [];

            foreach ($valuesList as $key => &$value) {
                $value = (int) $value;

                $optionValue = $itemOptionValues->where('id', (int) $value)->first();



                if (!$optionValue) {
                    unset($option['values'][$key]);

                    continue;
                }

                $optionValues[] = $optionValue;
            }
            $finalPrice += $optionValue['price'];

            $option['values'] = $optionValues;
        }

        $cartItem->options = $options;

        $cartItem->price = round($finalPrice, 2);

        $cartItem->totalPrice = round($finalPrice * $cartItem->quantity, 2);
        $cartItem->beforeSalePrice = round($cartItem->product['price']);

        if ($cart['subscription']) {
            $cartItem->totalSubscription = round(($cartItem->totalPrice * 4), 2);
        }

        $cartItem->rewardPoints = $itemData['rewardPoints'] * $cartItem->quantity;
        $cartItem->purchaseRewardPoints = $itemData['purchaseRewardPoints'] * $cartItem->quantity;

        if ($type == 'products') {
            // dd($item['sku']['name'] . $item->skuSeller , $item->width);
            if ($item->sku) {
                $cartItem->skuProduct = $item['sku']['name'] . $item->skuSeller;
                $cartItem->onlySku = $item['sku']['name'];
            }

            $cartItem->widthProduct = ($item->width * $itemQuantityWithExistingQuantity);
        }

        $cartItem->save();

        $cart->useRewardPoints = $request->useRewardPoints ?? false;


        // $cart->type = $request->type;


        if ($request->type == 'products') {
            $cart->reassociate($cartItem->seller, 'seller')->save();
        } elseif ($request->type == 'food') {
            $restaurantManager = $this->restaurantManagersRepository->getQuery()->where('restaurant.id', $item->restaurant['id'])->first();
            $restaurant = $this->restaurantsRepository->get($item->restaurant['id']);
            if (!$cart->restaurant) {
                $cart->restaurant = $restaurant->sharedInfo();
                if (!$restaurantManager) {
                    throw new Exception(trans('cart.Sorry-there-is-no-manager-for-thi-restaurant'));
                }
                $cart->restaurantManager = $restaurantManager->sharedInfo();
                // $cart->seller = null;
            }
        }
        // $cart->seller = $item->storeManager;

        $cart->reassociate($cartItem->sharedInfo(), 'items')->save();

        $cart->customer = $customer ? $customer->sharedInfo() : null;

        $this->updateTotalPrice($cart);

        // send customer if create action form visitor mode
        $this->saveCart($cart, $customer);


        return $cart;
    }

    /**
     * Get updated cart for the given customer
     *
     * @param string $type
     * @return array [CartModel| null, cartIsChanged]
     * @throws Exception
     */
    public function getUpdatedCart(string $type = '')
    {
        $cart = $this->getCurrentCart();
        $cartChanged = false;
        $cartItemData = $cart->items ?? [];
        // var_dump($cartItemData);die;
        if ($type !== '') {
            $cartItems = collect($cartItemData)->where('product.type', $type);
        } else {
            $cartItems = collect($cartItemData);
        }

        $products = $cartItems->pluck('product');

        $productsIds = $products->pluck('id')->toArray();

        $request = request();
        if (empty($productsIds)) {
            return [$cart, false];
        }

        $updatedProducts = $this->productsRepository->listPublished([
            'as-model' => true,
            'id' => $productsIds,
            'paginate' => false,
        ]);
        foreach ($cartItemData as $key => &$cartItem) {
            $product = $updatedProducts->firstWhere('id', $cartItem['product']['id']);

            if ($type == 'products' || $type == 'food') {
                if (!$product) {
                    // throw new Exception(trans('cart.pleaseDeleteIt'));
                    // // remove from cartItems & cart.items
                    $cartChanged = true;
                    CartItem::query()->where('id', $cartItemData[$key]['id'])->delete();
                    unset($cartItemData[$key]);

                    continue;
                }


                if ($type == 'products') {
                    if ($product['availableStock'] != 0 && $product['availableStock'] <= $cartItem['quantity']) {
                        $cartItem['quantity'] = $product['availableStock'];
                        $cartChanged = true;
                    }
                }
            }

            if ($type == 'food') {
                $restaurant = $this->restaurantsRepository->listPublished([
                    'as-model' => true,
                    'id' => $cart['restaurant']['id'],
                ]);
                if ($restaurant->count() == 0) {
                    // throw new Exception(trans('cart.pleaseDeleteIt'));
                    // // remove from cartItems & cart.items
                    $cartChanged = true;
                    CartItem::query()->where('id', $cartItemData[$key]['id'])->delete();
                    unset($cartItemData[$key]);

                    continue;
                }

                if ($cart['restaurant']['id'] != $cartItem['product']['restaurant']['id']) {
                    $cartChanged = true;
                    CartItem::query()->where('id', $cartItemData[$key]['id'])->delete();
                    unset($cartItemData[$key]);

                    continue;
                }
            }
            $originalOption = true;
            foreach ($updatedProducts as $product) {
                if ($cartItem['product']['id'] != $product->id) {
                    continue;
                }
                // check if name or short description or price changed
                if ($this->isProductChanged($cartItem['product'], $product) || $originalOption) {
                    $cartChanged = true;
                    $cartItem['product'] = $product->sharedInfo();
                    if ($type == 'products') {
                        $cartItem['seller'] = $product->storeManager;
                    } else {
                        $cartItem['restaurant'] = $product->restaurant;
                    }

                    if (request()->url() == url('api/orders/' . request()->route('orderId') . '/reorder') || (request()->url() == url('api/cart') && request()->method() == 'POST')) {
                    } else {
                        $cartItemCarts = CartItem::where('id', $cartItem['id'])->get();

                        foreach ($cartItemCarts as $key => $cartItemCart) {
                            if ($type == 'products') {
                                $customer = user();
                                if ($customer) {
                                    $deleteCartsellers = CartItem::where('seller.id', (int) $product->storeManager['id'])->where('customer.id', $customer['id'])->count();
                                    if ($deleteCartsellers < 1) {
                                        $cart->disassociate($cartItemCart['seller'], 'seller')->save();
                                    }

                                    if ($product['availableStock'] != 0 && $product['availableStock'] < $cartItemCart['quantity']) {
                                        $cartItemCart['quantity'] = $product['availableStock'];
                                    }
                                }
                            }

                            $cartItemCart->product = $product->sharedInfo();

                            $cartItemCart->options = $cartItemCart['options'];
                            $finalPrice = $this->getProductPrice($cartItemCart->product);
                            $itemOptions = collect($cartItemCart->options ?? []);

                            $options = $cartItemCart->options;
                            foreach ($options as $optionKey => &$option) {
                                $option['id'] = (int) $option['id'];
                                $itemOption = $itemOptions->where('id', (int) $option['id'])->first();
                                $itemOption = ProductOption::find((int) $option['id']);
                                if (!$itemOption) {
                                    unset($options[$optionKey]);

                                    continue;
                                }

                                $valuesList = $option['values'];

                                $option = $itemOption;

                                $itemOptionValues = collect($itemOption['values'] ?? []);

                                $optionValues = [];

                                foreach ($valuesList as $key => &$value) {
                                    // $value = (int) $value;
                                    $optionValue = $itemOptionValues->where('id', (int) $value['id'])->first();
                                    if (!$optionValue) {
                                        unset($option['values'][$key]);

                                        continue;
                                    }

                                    $optionValues[] = $optionValue;
                                }
                                //    $priceVal =  $optionValue['price'];
                                $finalPrice += $optionValue['price'];
                                $option['values'] = $optionValues;
                            }


                            $cartItemCart->price = round($finalPrice, 2);
                            $cartItemCart->totalPrice = round($finalPrice * $cartItemCart->quantity, 2);
                            $cartItemCart->beforeSalePrice = round($cartItemCart->product['price']);
                            if ($type == 'products') {
                                $cartItemCart->seller = $product['storeManager'];
                            }
                            $cartItemCart->save();
                            $cartItem['price'] = $cartItemCart->price;
                            $cartItem['totalPrice'] = $cartItemCart->totalPrice;
                            // $cart['seller'] = $cartItemCart->seller;
                            if ($type === 'products') {
                                $cart->reassociate($product['storeManager'], 'seller')->save();
                            } else {
                                $cart['restaurant'] = $product->restaurant;
                                $restaurantManager = $this->restaurantManagersRepository->getQuery()->where('restaurant.id', $product->restaurant['id'])->first();
                                if (!$restaurantManager) {
                                    throw new Exception(trans('cart.Sorry-there-is-no-manager-for-thi-restaurant'));
                                }
                                $cart['restaurantManager'] = $restaurantManager->sharedInfo();
                            }
                        }
                    }
                }
            }
        }
        $cart['items'] = $cartItemData;

        // check if user update walletBalance for customer
        if (user() && ($cart['wallet'] != user()->walletBalance || $cart['amountsDue'] != user()->walletBalance)) {
            $cartChanged = true;
        }
        if (
            request()->hasAny(['couponCode', 'address', 'shippingMethod', 'useRewardPoints']) ||
            (user() && user()->group)
        ) {
            $cartChanged = true;
        }

        if ($cartChanged || !empty($cart['shippingMethod']) || ($request->has('state') && in_array($request->state, ['items', 'shipping', 'cart']))) {
            $this->updateTotalPrice($cart);
            $this->saveCart($cart);
        }

        return [$cart, $cartChanged];
    }

    /**
     * Method checkCustomerGroup
     *
     * @param $cart $cart
     *
     * @return bool
     */
    public function checkCustomerGroup($cart): bool
    {
        $cartGroup = $cart['group'] ?? [];

        $userGroup = user()->group ?? [];

        foreach ($cartGroup as $key => $value) {
            if ($userGroup[$key] != $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a sha1 id for the given options of the item to indicate if the item exists
     *
     * @param array $data
     * @return string
     */
    public function getUniqueIdForCartItem(array $data): string
    {
        $options = $data['options'];

        $optionData = [];

        $ksort_recursive = function (&$array) use (&$ksort_recursive) {
            if (is_array($array)) {
                ksort($array);
                array_walk($array, $ksort_recursive);
            }
        };

        foreach ($options as $option) {
            foreach ($option['values'] as $value) {
                // dynamically create nested array elements
                $optionData[$option['id']][$value] = true;
            }
        }

        $ksort_recursive($optionData);

        $data['options'] = $optionData;

        return sha1(json_encode($data));
    }

    /**
     * Get cart fur current user
     *
     * @param null $userId
     * @param bool|null $isSubscription
     * @return Model
     */
    public function getCurrentCart($userId = null, $isSubscription = null)
    {
        $query = $this->getQuery();

        if (request()->url() == url('api/restaurants') || request()->url() == url('api/productMeals') || request()->url() == url('api/restaurants/' . request()->route('id'))) { // make countCart For restaurants
            $type = 'food';
        } else { //make countCart For store
            $type = 'products';
        }

        $type = request()->type ?? $type; //make new two cart
        $request = request();
        // if (!$isSubscription) {
        //     $isSubscription = (bool) $this->request->get('subscription', false);
        // }

        if ($userId) {
            $customer = $this->customersRepository->getModel((int) $userId);
        } else {
            $customer = user();
        }

        $deviceId = Visitor::getDeviceId();

        // dd($customer, $deviceId);
        if ($customer) {
            // $query->where('deviceId', null)->where('customer.id', $customer->id)->where('type', $type);
            $query->where(function ($query) use ($deviceId, $customer) {
                $query->where('customer.id', $customer->id)->orWhere('deviceId', $deviceId);
            })->where('type', $type);
            // $deviceId = $deviceId;
        } else {
            $query->where('deviceId', $deviceId)->where('type', $type);
            // $customer = null;
        }

        $cart = $query->first();
        // dd($cart);

        // dd(request()->type);
        // $cart->type = request()->type;

        if (!$cart) {
            $cart = Model::create([
                'items' => [],
                'customer' => $customer ? $customer->sharedInfo() : null,
                'deviceId' => $deviceId,
                'totalPrice' => 0.0,
                'taxes' => 0.0,
                'finalPrice' => 0.0,
                'originalPrice' => 0.0,
                'rewardPoints' => 0,
                'purchaseRewardPoints' => 0,
                'totalQuantity' => 0,
                'useRewardPoints' => false,
                'isActiveRewardPoints' => false,
                'subscription' => (bool) $isSubscription,
                'type' => $type,
            ]);
        }

        $this->saveCart($cart);
        // dd($cart['coupon']);
        if (request()->url() == url('api/cart')) {
            if ($cart['coupon']) {
                if ($cart['coupon']['typeCoupon']) {
                    $coupon = $this->couponsRepository->getQuery()->where('code', $cart['coupon']['code'])->where('typeCoupon', $request->type)->first();
                } else {
                    $coupon = $this->couponsRepository->getQuery()->where('code', $cart['coupon']['code'])->first();
                }

                // dd($coupon);
                if ($coupon && $coupon['published'] == false || $coupon == null) {
                    $cart['coupon'] = null;
                    $cart['couponDiscount'] = 0.0;
                }
                $this->updateTotalPrice($cart);
                $this->saveCart($cart);
            }
        }
        // dd($request->state);
        if ($request->couponCode) {
            # code...
        } else {
            if ($request->state == 'cart') {
                $cart['shippingFees'] = null;
                $cart['shippingMethod'] = null;
                $this->updateTotalPrice($cart);
                $this->saveCart($cart);
            }
        }



        return $cart;
    }

    /**
     * Update total price for given customer
     *
     * @param $cart
     * @return void
     * @throws Exception
     */
    public function updateTotalPrice(&$cart)
    {
        // dd('onat');
        $customer = user();
        // dd( $customer);

        $totalPrice = 0;
        $totalQuantity = 0;
        $totalSubscription = 0;
        $totalItems = 0;

        $request = request();
        // dd($cart['items']);
        foreach ($cart['items'] as $item) {
            $totalPrice += $item['totalPrice'];
            $totalQuantity += $item['quantity'];
            $totalItems += 1;

            $cart['purchaseRewardPoints'] += ($item['product']['purchaseRewardPoints'] ?? 0) * $item['quantity'];
            $cart['rewardPoints'] += ($item['product']['rewardPoints'] ?? 0) * $item['quantity'];

            if ($cart['subscription']) {
                $totalSubscription += ($item['totalSubscription'] ?? 0);
            }
        }
        $cart['totalPrice'] = round($totalPrice, 2);
        // dd($cart['totalPrice']);
        $taxes = $this->settingsRepository->getOrderTaxes();

        $cart['taxesValue'] = $taxes;

        if ($cart['subscription']) {
            $originalPrice = round(($cart['totalPrice'] * 4) / (1 + ($taxes / 100)), 2);

            $cart['taxes'] = round(((($cart['totalPrice'] * 4) * $taxes) / 100), 2);

            $cart['finalPrice'] = round((($cart['totalPrice'] * 4) + $cart['taxes']), 2);
        } else {
            $originalPrice = $this->totalWithoutTaxes($cart['totalPrice'], $taxes);
            $cart['taxes'] = round($cart['totalPrice'] - $originalPrice, 2);
            $cart['finalPrice'] = round($cart['totalPrice'], 2);

            // dd($cart['totalPrice'], $taxes,$originalPrice , $cart['taxes'] , $cart['finalPrice']);
            // $originalPrice = round($cart['totalPrice'] / (1 + ($taxes / 100)), 2);
            // $cart['taxes'] = round((($cart['totalPrice'] * $taxes) / 100), 2);
            // $cart['finalPrice'] = round(($cart['totalPrice'] + $cart['taxes']), 2);
        }
        $cart['originalPrice'] = $originalPrice;
        $cart['totalWithoutTaxes'] = $originalPrice;
        $cart['totalQuantity'] = $totalQuantity;
        $cart['totalItems'] = $totalItems;

        $cart['totalSubscription'] = $totalSubscription;
        // dd($cart['finalPrice'],'sdsd');

        try {
            if ($request->couponCode || (isset($cart['coupon']) && isset($cart['coupon']['code']))) {
                $coupon = $this->couponsRepository->getValidCoupon($request->couponCode ?? $cart['coupon']['code'], $request);
                if ($coupon == null) {
                    unset($cart['couponDiscount']);
                    unset($cart['coupon']);
                    $this->saveCart($cart);
                } else {
                    $cart['coupon'] = $coupon->sharedInfo();
                    $cart['couponDiscount'] = $coupon->couponDiscount;
                    $cart['finalPrice'] -= $coupon->couponDiscount;
                }
            }
        } catch (\Exception $th) {
            // dd($cart['coupon']);
            unset($cart['couponDiscount']);
            unset($cart['coupon']);
            $this->saveCart($cart);

            throw new Exception('errorCoupon');
        }

        $this->clearCustomerGroup($cart);

        if ($customer) {
            $customer = $customer->refresh();

            if ($request->address || $cart['shippingAddress']) {
                // dd($cart['shippingAddress']['id']);
                $address = $this->addressBooksRepository->getValidAddress(($request->address ?? $cart['shippingAddress']['id']));

                if ($request->type == 'products') {
                    if ($address) {
                        $cart['shippingAddress'] = $address->sharedInfo();
                    } else {
                        throw new Exception('cannotUseThisAddress');
                    }
                } elseif ($request->type == 'food') {
                    $checkDistanceAddressCart = $this->restaurantsRepository->checkDistanceAddressCart($address, $cart->restaurant);
                    if ($checkDistanceAddressCart) {
                        $cart['shippingAddress'] = $address->sharedInfo();
                    } elseif ($cart['shippingAddress']) {
                        if ($request->deliveryType == 'inHome' && $cart['shippingAddress']) {
                            throw new Exception('cannotUseThisAddressMeals');
                        }
                    } else {
                        throw new Exception('cannotUseThisAddressMeals');
                    }
                }


                $shippingMethod = $request->shippingMethod ?? ($cart['shippingMethod'] ? $cart['shippingMethod']['id'] : null);
                // dd($this->shippingMethodsRepository->has((int) $shippingMethod));

                if ($shippingMethod && $this->shippingMethodsRepository->has((int) $shippingMethod)) {
                    if (empty($address->city['id'])) {
                        throw new Exception(trans('cart.cannotUseThisAddressCityNotMatch'));
                    }

                    $shippingMethod = $this->shippingMethodsRepository->getByCity((int) $shippingMethod, (int) $address->city['id']);

                    // dd(count($cart['seller']));
                    if ($shippingMethod->resource) {
                        $shippingMethodInfo = $shippingMethod->sharedInfo();
                        // dd($shippingMethodInfo['shippingFees']);

                        $cart['shippingMethod'] = $shippingMethodInfo;

                        $cart['shippingFees'] = $shippingMethodInfo['shippingFees'] * count($cart['seller']);

                        $cart['finalPrice'] += $cart['shippingFees'];
                    }
                    //  else {
                    //     throw new Exception(trans('cart.cannotUseThisShippingMethod'));
                    // }
                }
            }

            // check & calculate customer group
            $this->customerGroup($cart, $customer, $request->type);

            if ($request->useRewardPoints || $cart['isActiveRewardPoints']) {
                if ($customer && $customer->rewardPoint >= $cart['purchaseRewardPoints']) {
                    $cart['usedRewardPoints'] = $cart['purchaseRewardPoints'];
                    $cart['rewordDiscount'] = round($cart['totalPrice'], 2);
                    $cart['finalPrice'] -= $cart['rewordDiscount'];
                    $cart['isActiveRewardPoints'] = true;
                } else {
                    $cart['isActiveRewardPoints'] = false;

                    throw new Exception(trans('cart.cannotUseReward', ['value' => ($customer->rewardPoint - $cart['purchaseRewardPoints'])]));
                }
            }

            // if ($customer->walletBalance > 0 && !((bool) $cart['subscription'])) {
            //     if ($customer->walletBalance >= $cart['finalPrice']) {
            //         $cart['wallet'] = $cart['finalPrice'];
            //         $cart['finalPrice'] = 0;
            //     } else {
            //         $cart['wallet'] = $customer->walletBalance;
            //         $cart['finalPrice'] -= $customer->walletBalance;
            //     }

            //     $cart['wallet'] = round($cart['wallet'], 2);
            // }

            // setting shipping method
            $groupIsFreeShipping = (!empty($customer->group) && ($customer->group['freeShipping'] || $customer->group['freeExpressShipping']));

            if ($cart['subscription']) {
                $cart['shippingFees'] = $this->settingsRepository->getSubscriptionShippingAmount();
                $cart['finalPrice'] += $cart['shippingFees'];
            }

            // TODO : Delete If not necessary 
            // elseif (!$groupIsFreeShipping && $cart['totalPrice'] >= $this->settingsRepository->getFreeShippingOrderValue()) {

            //     $shippingFees = null;

            //     if ($cart['shippingMethod']) {
            //         if ($cart['shippingMethod']['type'] == 'fast') {
            //             $cart['freeExpressShipping'] = true;
            //             $cart['freeExpressShippingDiscount'] = $cart['shippingFees'];
            //         } elseif ($cart['shippingMethod']['type'] == 'standard') {
            //             $cart['freeShipping'] = true;
            //             $cart['freeShippingDiscount'] = $cart['shippingFees'];
            //         }

            //         $shippingFees = $cart['shippingMethod']['shippingFees'];
            //     }

            //     $cart['shippingFees'] = $shippingFees ?? 0;

            // }
            // elseif (!$groupIsFreeShipping && $cart['totalPrice'] <= $this->settingsRepository->getTotalOrdersShippingDiscount()) {

            //     $shippingFees = null;

            //     if ($cart['shippingMethod']) {
            //         if ($cart['shippingMethod']['type'] == 'fast') {
            //             $cart['freeExpressShipping'] = true;
            //             $cart['freeExpressShippingDiscount'] = $this->settingsRepository->getShippingAmount();
            //         } elseif ($cart['shippingMethod']['type'] == 'standard') {
            //             $cart['freeShipping'] = true;
            //             $cart['freeShippingDiscount'] = $this->settingsRepository->getShippingAmount();
            //         }

            //         $shippingFees = $cart['shippingMethod']['shippingFees'];
            //     }

            //     $cart['shippingFees'] = $shippingFees ?? 0;
            //     $cart['finalPrice'] += $cart['shippingFees'];
            // }

            if ($customer->walletBalance < 0) {
                $cart['amountsDue'] = $customer->walletBalance * -1;
            } else {
                $cart['amountsDue'] = 0;
            }
        }

        if ($request->has('state') && in_array($request->state, ['items', 'cart', 'shipping'])) {
            $cart['finalPrice'] =
                ($cart['subscription'] ? $cart['totalSubscription'] : $cart['totalWithoutTaxes'])
                + ($cart['taxes'] ?? 0)
                + ($cart['amountsDue'] ?? 0)
                - ($cart['rewordDiscount'] ?? 0)
                - ($cart['specialDiscount'] ?? 0)
                - ($cart['couponDiscount'] ?? 00)
                - ($cart['wallet'] ?? 0);

            if (in_array($request->state, ['shipping', 'cart'])) {
                $cart['finalPrice'] = $cart['finalPrice']
                    + ($cart['shippingFees'] ?? 0)
                    - ($cart['freeExpressShipping'] ? $cart['freeExpressShippingDiscount'] : 0)
                    - ($cart['freeShipping'] ? $cart['freeShippingDiscount'] : 0);
            } else {
                $cart['shippingFees'] = 0;
                $cart['freeExpressShipping'] = $cart['freeExpressShippingDiscount'] = 0;
                $cart['freeShipping'] = $cart['freeShippingDiscount'] = 0;
            }
        } else {
            $cart['finalPrice'] =
                ($cart['subscription'] ? $cart['totalSubscription'] : $cart['totalWithoutTaxes'])
                + ($cart['taxes'] ?? 0)
                + ($cart['shippingFees'] ?? 0)
                + ($cart['amountsDue'] ?? 0)
                - ($cart['rewordDiscount'] ?? 0)
                - ($cart['specialDiscount'] ?? 0)
                - ($cart['freeExpressShipping'] ? $cart['freeExpressShippingDiscount'] : 0)
                - ($cart['freeShipping'] ? $cart['freeShippingDiscount'] : 0)
                - ($cart['couponDiscount'] ?? 00)
                - ($cart['wallet'] ?? 0);
        }

        if ($cart['finalPrice'] < 0) {
            $cart['finalPrice'] = 0;
        }

        $cart['finalPrice'] = round($cart['finalPrice'], 2);

        $this->saveCart($cart);
    }

    /**
     * Get Total Without Taxes
     *
     * @param $originalTotal
     * @param $taxes
     * @return float
     */
    public function totalWithoutTaxes($originalTotal, $taxes, $rounding = true)
    {
        $price = $originalTotal / (1 + ($taxes / 100));

        return $rounding ? round($price, 2) : $price;
    }

    /**
     * @param $cart
     * @param $customer
     * @throws Exception
     */
    public function customerGroup(&$cart, $customer, $type)
    {
        if ($type == 'products' && $customer->group) {
            foreach ($customer->group['nameGroup']  as $nameGroup) {
                if ($nameGroup['name'] != 'productsInStores') {
                    continue;
                } else {
                    if ($customer->group && $this->customerGroupsRepository->checkAvailable($customer->group['id'])) {
                        if ($customer->group['specialDiscount'] && $customer->group['specialDiscount'] != null && $customer->group['specialDiscount'] != 0) {
                            if ($cart['totalPrice'] >= $customer->group['specialDiscount']) {
                                $specialDiscount = (($cart['totalPrice'] * $customer->group['specialDiscount']) / 100);
                                $cart['specialDiscount'] = round($specialDiscount, 2);
                                $cart['finalPrice'] -= round($specialDiscount, 2);
                            } else {
                                $cart['specialDiscount'] = $cart['totalPrice'];
                                $cart['finalPrice'] -= $cart['totalPrice'];
                            }
                            $cart['group'] = $customer->group;
                        }
                        $this->saveCart($cart);
                    }
                }
            }
        } elseif ($type == 'food' && $customer->group) {
            foreach ($customer->group['nameGroup']  as $nameGroup) {
                if ($nameGroup['name'] != 'typesInRestaurant') {
                    continue;
                } else {
                    if ($customer->group && $this->customerGroupsRepository->checkAvailable($customer->group['id'])) {
                        if ($customer->group['specialDiscount'] && $customer->group['specialDiscount'] != null && $customer->group['specialDiscount'] != 0) {
                            if ($cart['totalPrice'] >= $customer->group['specialDiscount']) {
                                $specialDiscount = (($cart['totalPrice'] * $customer->group['specialDiscount']) / 100);
                                $cart['specialDiscount'] = round($specialDiscount, 2);
                                $cart['finalPrice'] -= round($specialDiscount, 2);
                            } else {
                                $cart['specialDiscount'] = $cart['totalPrice'];
                                $cart['finalPrice'] -= $cart['totalPrice'];
                            }
                            $cart['group'] = $customer->group;
                        }
                        $this->saveCart($cart);
                    }
                }
            }
        }
    }

    /**
     * clear customer group info
     *
     * @param $cart
     * @throws Exception
     */
    public function clearCustomerGroup(&$cart)
    {
        if (
            $cart['freeExpressShipping'] || $cart['freeExpressShippingDiscount'] ||
            $cart['freeShipping'] || $cart['freeShippingDiscount'] || $cart['specialDiscount']
        ) {
            //            unset($cart['freeShipping']);
            //            unset($cart['freeShippingDiscount']);
            //            unset($cart['freeExpressShipping']);
            //            unset($cart['freeExpressShippingDiscount']);
            //            unset($cart['specialDiscount']);
            //
            //            unset($cart['group']);

            $cart['freeShipping'] = false;
            $cart['freeShippingDiscount'] = 0;
            $cart['freeExpressShipping'] = false;
            $cart['freeExpressShippingDiscount'] = 0;
            $cart['specialDiscount'] = 0;

            $cart['group'] = null;

            // update price
            // $this->updateTotalPrice($cart);
            $this->saveCart($cart);
        }
    }

    /**
     * Set cart item quantity
     *
     * @param Model $cartItem
     * @param int $quantity
     * @return \Illuminate\Http\Resources\Json\JsonResource|\JsonResource|void
     * @throws Exception
     */
    public function setQuantity($cartItem, int $quantity)
    {
        $request = request();
        if ($request->type == 'products') {
            $this->validateQuantity($cartItem, $quantity);
        }

        if ($request->type == 'products') {
            $item = $this->productsRepository->getQuery()->where('type', $request->type)->where('id', $cartItem->product['id'])->first();

            $options = $cartItem->options ?? [];
            if ($options) {
                $itemOptions = collect($item->options ?? []);

                foreach ($options as $optionKey => &$option) {
                    $option['id'] = (int) $option['id'];
                    $itemOption = $itemOptions->where('id', (int) $option['id'])->first();
                    $itemOptionValues = collect($itemOption['values'] ?? []);

                    $valuesList = $option['values'];
                    foreach ($valuesList as $key => &$value) {
                        if (is_array($value)) {
                            $value = (int) $value['id'];
                        } else {
                            $value = (int) $value;
                        }
                        $optionValue = $itemOptionValues->where('id', (int) $value)->first();
                        if ($quantity > $optionValue['quantity']) {
                            throw new Exception(trans('cart.Quantity-available-product', ['value' => $optionValue['quantity']]));
                        }
                    }
                }
            }
        }

        $cartItem->quantity = (int) $quantity;
        $cartItem->totalPrice = round($cartItem->price * $cartItem->quantity, 2);
        // $cartItem->purchaseRewardPoints = ($cartItem->product['purchaseRewardPoints'] ?? 0) * (int) $quantity;
        // $cartItem->rewardPoints = ($cartItem->product['rewardPoints'] ?? 0) * (int) $quantity;

        if ($cartItem['subscription']) {
            $cartItem->totalSubscription = round(($cartItem->totalPrice * 4), 2);
        }

        $cartItem->save();
        $cart = $this->getCurrentCart($cartItem->customer['id'] ?? null, $cartItem->subscription);

        $cart->reassociate($cartItem->sharedInfo(), 'items')->save();

        $this->saveCart($cart);

        $this->updateTotalPrice($cart);

        $this->saveCart($cart);

        return $this->wrap($cart);
    }

    /**
     * Save the given cart and update it in the customer if logged in
     *
     * @param Model $cart
     * @param null $customer
     * @return void
     * @throws \Throwable
     */
    public function saveCart(Model $cart, $customer = null)
    {
        $cart->save();

        if (!$customer) {
            $customer = user();
        }
        // dd($customer->cart);
        if (!$customer) {
            return;
        }
        // dd($cart->type);
        // if ($cart['subscription']) {
        //     $customer->cartSubscription = $cart->sharedInfo();
        // } else {
        //     $customer->cart = $cart->sharedInfo();
        // }
        if ($cart->type == "products") {
            $customer->cart = $cart->sharedInfo();
        } elseif ($cart->type == "food") {
            $customer->cartMeal = $cart->sharedInfo();
        }

        $customer->save();
    }

    /**
     * Check if the given cart item belongs to the given customer
     *
     * @param CartItem $cartItem
     * @return bool
     */
    public function belongsTo(CartItem $cartItem): bool
    {
        $cart = $this->getCurrentCart(null, $cartItem['subscription']);

        if (user()) {
            return $cartItem->customer['id'] === $cart->customer['id'];
        } else {
            return $cartItem->deviceId === $cart['deviceId'];
        }
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function onDelete($model, $id)
    {
        //        $customer = $this->customersRepository->getModel($model->customer['id']);
        //
        //        if (!$customer) return;
        //
        ////        $cart = $this->getCurrentCart();
        ////
        ////       $cart->disassociate($model->sharedInfo(), 'items');
        ////
        ////
        ////        $this->updateTotalPrice($cart);
        ////
        ////        $this->checkItemInCart();
    }

    /**
     * Method checkDeleteCartItem
     *
     * @param $request $request
     *
     * @return void
     */
    public function checkDeleteCartItem($request)
    {
        $cartItem = CartItem::find($request->id);

        if (isset($cartItem['type']) && $cartItem->type != $request->type) {
            throw new Exception(trans('cart.notFoundItem'));
        }
    }

    /**
     * delete item from cart
     *
     * @param CartItem $cartItem
     * @throws Exception
     */
    public function deleteCartItem(CartItem $cartItem)
    {
        // $this->checkDeleteCartItem($cartItem);
        // dd((int)$cartItem['seller']['id']);
        $customer = user();

        $request = request();
        // dd($request->type);
        if ($customer && $request->type == 'products') {
            $deleteCartsellers = CartItem::where('seller.id', (int) $cartItem['seller']['id'])->where('customer.id', $customer['id'])->count();
        }
        // unset($cartItemData[$key]);

        $cart = $this->getCurrentCart(null, $cartItem['subscription']);
        // dd($cartItem['seller']);
        if ($customer && $request->type == 'products') {
            if ($deleteCartsellers <= 1) {
                $cart->disassociate($cartItem['seller'], 'seller')->save();
            }
        }
        $cart->disassociate($cartItem->sharedInfo(), 'items')->save();

        $this->updateTotalPrice($cart);

        $this->checkItemInCart();

        $this->saveCart($cart);

        $cartItem->delete();
    }

    /**
     * Flush the entire cart
     *
     * @param null $customerId
     * @param bool $isSubscription
     * @return void
     * @throws Exception
     */
    public function flush($customerId = null, $isSubscription = false)
    {
        // todo : fix this
        if ($customerId) {
            $customer = $this->customersRepository->getModel($customerId);
        } elseif (user()) {
            $customer = user();
        }

        $isSubscription = (bool) $isSubscription;
        $deviceId = Visitor::getDeviceId();

        Model::where('customer.id', $customer->id ?? null)->orWhere('deviceId', $deviceId)->where('type', request()->type)->delete();
        CartItem::where('customer.id', $customer->id ?? null)->orWhere('deviceId', $deviceId)->where('type', request()->type)->delete();

        $this->removeCoupon();

        $cart = $this->getCurrentCart(null, $isSubscription);

        $cart->items = [];
        $cart->totalPrice = 0.0;
        $cart->taxes = 0.0;
        $cart->finalPrice = 0.0;
        $cart->originalPrice = 0.0;
        $cart->rewardPoints = 0;
        $cart->purchaseRewardPoints = 0;
        $cart->useRewardPoints = false;
        $cart->isActiveRewardPoints = false;
        $cart->subscription = $isSubscription;

        $this->updateTotalPrice($cart);

        $cart->save();

        $this->saveCart($cart);
    }

    /**
     * Method flushFood
     *
     * @param $customerId $customerId
     * @param $isSubscription $isSubscription
     *
     * @return void
     */
    public function flushFood($customerId = null, $isSubscription = false)
    {
        // dd('flushFood');

        // todo : fix this
        if ($customerId) {
            $customer = $this->customersRepository->getModel($customerId);
            $deviceId = $customer->deviceCart;
        } elseif (user()) {
            $customer = user();
            $deviceId = Visitor::getDeviceId();
        }
        // dd($customer->id);
        $isSubscription = (bool) $isSubscription;

        // Model::where('customer.id', $customer->id ?? null)->where('type', 'food')->delete();
        // CartItem::where('customer.id', $customer->id ?? null)->where('type', 'food')->delete();

        Model::where(function ($query) use ($deviceId, $customer) {
            $query->where('customer.id', $customer->id)->orWhere('deviceId', $deviceId);
        })->where('type', 'food')->delete();

        CartItem::where(function ($query) use ($deviceId, $customer) {
            $query->where('customer.id', $customer->id)->orWhere('deviceId', $deviceId);
        })->where('type', 'food')->delete();

        $this->removeCoupon();

        $cart = $this->getCurrentCart(null, $isSubscription);

        $cart->items = [];
        $cart->totalPrice = 0.0;
        $cart->taxes = 0.0;
        $cart->finalPrice = 0.0;
        $cart->originalPrice = 0.0;
        $cart->rewardPoints = 0;
        $cart->purchaseRewardPoints = 0;
        $cart->useRewardPoints = false;
        $cart->isActiveRewardPoints = false;
        $cart->subscription = $isSubscription;

        $this->updateTotalPrice($cart);

        $cart->save();

        $this->saveCart($cart);
    }

    /**
     * Method flushPrdouct
     *
     * @param $customerId $customerId
     * @param $isSubscription $isSubscription
     *
     * @return void
     */
    public function flushPrdouct($customerId = null, $isSubscription = false)
    {
        // todo : fix this
        if ($customerId) {
            $customer = $this->customersRepository->getModel($customerId);
            $deviceId = $customer->deviceCart;
        } elseif (user()) {
            $customer = user();
            $deviceId = Visitor::getDeviceId();
        }
        // dd($deviceId, $customer, $customerId);
        $isSubscription = (bool) $isSubscription;

        // Model::where('customer.id', $customer->id ?? null)->where('type', 'products')->delete();
        // CartItem::where('customer.id', $customer->id ?? null)->where('type', 'products')->delete();
        Model::where(function ($query) use ($deviceId, $customer) {
            $query->where('customer.id', $customer->id)->orWhere('deviceId', $deviceId);
        })->where('type', 'products')->delete();

        CartItem::where(function ($query) use ($deviceId, $customer) {
            $query->where('customer.id', $customer->id)->orWhere('deviceId', $deviceId);
        })->where('type', 'products')->delete();

        $this->removeCoupon();

        $cart = $this->getCurrentCart(null, $isSubscription);

        $cart->items = [];
        $cart->totalPrice = 0.0;
        $cart->taxes = 0.0;
        $cart->finalPrice = 0.0;
        $cart->originalPrice = 0.0;
        $cart->rewardPoints = 0;
        $cart->purchaseRewardPoints = 0;
        $cart->useRewardPoints = false;
        $cart->isActiveRewardPoints = false;
        $cart->subscription = $isSubscription;

        $this->updateTotalPrice($cart);

        $cart->save();

        $this->saveCart($cart);
    }

    /**
     * Do any extra filtration here
     *
     * @return  void
     */
    protected function filter()
    {
    }

    /**
     * check Quantity
     *
     * @param $item
     * @param int $quantity
     * @throws Exception
     */
    public function validateQuantity($item, int $quantity)
    {
        if ($item['product']) {
            $item = $this->productsRepository->getQuery()->where('id', $item['product']['id'])->first();
        } else {
            $item = $item;
        }
        // check availableStock if come form update Quantity or add to cart
        $availableStock = $item->availableStock ?? $item['product']['availableStock'];

        if (request()->url() == url('api/orders/' . request()->route('orderId') . '/reorder')) {
            if ($availableStock != 0 && $availableStock < $quantity) {
            }
        } else {
            if ($availableStock == 0 || $availableStock < $quantity) {
                throw new Exception(trans('cart.unavailableStock'));
            }
        }

        // check maxQuantity if come form update Quantity or add to cart
        $maxQuantity = $item['maxQuantity'] ?? $item['product']['maxQuantity'];
        // if ($maxQuantity == 0) {
        //     $maxQuantity = 999;
        // }
        if (($maxQuantity !== 0 && $maxQuantity !== -1) && ($quantity > $maxQuantity)) {
            // dd($maxQuantity);
            throw new Exception(trans('cart.maxQuantity', ['value' => $maxQuantity]));
        }

        // check minQuantity if come form update Quantity or add to cart
        $minQuantity = $item['minQuantity'] ?? $item['product']['minQuantity'];

        if ($minQuantity !== 0 && ($quantity < $minQuantity)) {
            throw new Exception(trans('cart.minQuantity', ['value' => $minQuantity]));
        }
    }

    /**
     * get final price based on discount
     *
     * @param $product
     * @return mixed
     */
    public function getProductPrice($product)
    {
        if (isset($product['discount']) && isset($product['discount']['startDate']) && isset($product['discount']['endDate']) && isset($product['discount']['type']) && $product['discount']['type'] != 'none') {
            if (
                !(is_array($product['discount']['startDate']) || is_array($product['discount']['endDate'])) &&
                Carbon::now()->between(Carbon::createFromTimestampMs($product['discount']['startDate']), Carbon::createFromTimestampMs($product['discount']['endDate']))
            ) {
                return $product['finalPrice'];
            }
        }

        return $product['price'];
    }

    /**
     * @param $product
     * @return mixed
     * @throws Exception
     */
    public function getSubscriptionPrice($product)
    {
        if ($product['inSubscription'] && $product['priceInSubscription']) {
            return $product['priceInSubscription'];
        }

        throw new Exception(trans('products.notFoundPriceInSubscription'));
    }

    /**
     * @throws Exception
     */
    public function checkItemInCart()
    {
        $cart = $this->getCurrentCart();
        if (count($cart['items']) == 0) {
            $this->flush(null);
        }
    }

    public function countCart()
    {
        $cart = $this->getCurrentCart();

        return count($cart['items']);
    }

    /**
     * @param $deviceId
     * @param bool $subscription
     * @return mixed
     */
    public function getByDeviceId($deviceId, bool $subscription = false)
    {
        $cart = $this->getByModel('deviceId', $deviceId);

        if ($cart) {
            return $cart->sharedInfo();
        }

        return [];
    }

    /**
     * check if product in cart has updated
     *
     * @param $cartItem
     * @param $product
     * @return bool
     */
    public function isProductChanged($cartItem, $product): bool
    {
        foreach ($cartItem as $key => $value) {
            if ($product->{$key} != $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function deleteOldProductInCart(int $itemId, $customer, $deviceId, bool $subscription = false)
    {
        $cartItems = CartItem::where('product.id', $itemId);

        if ($customer) {
            $cartItems = $cartItems->where('customer.id', $customer->id)->where('subscription', $subscription);
        } else {
            $cartItems = $cartItems->where('deviceId', $deviceId)->where('subscription', $subscription)->where('customer', null);
        }

        $cartItems = $cartItems->get();

        foreach ($cartItems as $item) {
            $this->deleteCartItem($item);
        }
    }

    /**
     * Method getshippingAddressForCart
     *
     * @return void
     */
    public function getshippingAddressForCart()
    {
        $customer = user();

        return $this->getCurrentCart($customer->id ?? null);
    }

    /**
     * remove coupon from cart and update prices
     * @throws Exception
     */
    public function removeCoupon()
    {
        if (user()) {
            user()->coupon()->clear();
        }

        $cart = $this->getCurrentCart();
        $cart['coupon'] = null;
        $cart['couponDiscount'] = 0.0;
        $cart->save();
        $this->updateTotalPrice($cart);
    }

    /**
     * Method canNotAddToCartAndTypeError
     *
     * @param $request $request
     *
     * @return void
     */
    public function canNotAddToCartAndTypeError($request)
    {
        if (empty($request->type)) {
            throw new Exception(trans('cart.missingData.type'));
        }

        $type = ($request->type == 'products' || $request->type == 'food') ? $request->type : false;
        if (!$type) {
            throw new Exception(trans('cart.missingData.invalidType'));
        }
    }

    /**
     * Method sellerCart
     *
     * @return void
     */
    public function sellerCart()
    {
        $cart = $this->getCurrentCart();
        // dd($cart['seller']);
        // return $cart['seller'];

        $sellerCarts = $this->storeManagersRepository->wrapMany($cart['seller']);

        $items = collect($cart['items']);

        $seller = [];

        foreach ($sellerCarts as $sellerCart) {
            // $cartItems = CartItem::query()->where('seller.id', $sellerCart['id'])->get();
            $cartItems = $items->where('seller.id', $sellerCart['id'])->toArray();

            $sellerCart['shippingMethod'] = $cart['shippingMethod'];

            $sellerCart['items'] = $cartItems;
            $seller[] = $sellerCart;
        }

        return $seller;
    }

    /**
     * Update products in modules
     *
     * @param Product $product
     * @return void
     */
    public function updateProduct(Product $product)
    {
        $info = $product->sharedInfo();

        Model::where('items.products.id', $product->id)->update([
            'items.products.$' => $info,
        ]);
    }

    /**
     * Method onDeleteAddress
     *
     * @param Type $var
     *
     * @return void
     */
    public function onDeleteAddress($id)
    {
        Model::where('shippingAddress.id', $id)->update([
            'shippingAddress' => null,
            'shippingFees' => null,
            'shippingMethod' => null,
        ]);
    }
}
