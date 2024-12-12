<?php

namespace App\Modules\Cart\Controllers\Site;

use Illuminate\Http\Request;
use App\Modules\Cart\Models\CartItem;
use HZ\Illuminate\Mongez\Managers\ApiController;

class CartController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $repository = 'cart';

    /**
     * {@inheritdoc}s
     */
    public function index(Request $request)
    {
        try {
            $this->repository->canNotAddToCartAndTypeError($request);
            [$cart, $cartChanged] = $this->repository->getUpdatedCart($request->type);
        } catch (\Exception $exception) {
            if ($exception->getMessage() == 'cannotUseThisAddressMeals') {
                return response()->json([
                    'errors' => [
                        [
                            'key' => 'error',
                            'value' => trans('cart.cannotUseThisAddressMeals'),
                        ],
                    ],
                ], 400);
            }

            if ($exception->getMessage() == 'cannotUseThisAddress') {
                return response()->json([
                    'errors' => [
                        [
                            'key' => 'error',
                            'value' => trans('cart.cannotUseThisAddress'),
                        ],
                    ],
                ], 400);
            }

            if ($exception->getMessage() == 'errorCoupon') {
                return response()->json([
                    'errors' => [
                        [
                            'key' => 'error',
                            'value' => 'كود قسيمة غير صحيح الرجاء ادخال كود الصحيح',
                        ],
                    ],
                ], 400);
            }

            // return $this->badRequest(trans('cart.missingData.' . $exception));
            return (env('APP_ENV') == 'dev') ? $this->badRequest($exception->getMessage() . $exception->getTraceAsString()) : $this->badRequest($exception->getMessage());

            // return $this->badRequest($exception->getMessage()); //canNotAddToCartAndTypeError
        }

        // if ($request->page) {
        //     $options['page'] = (int) $request->page;
        // }
        return $this->success([
            'success' => true,
            'cart' => $this->repository->wrap($cart),
            'cartChanged' => $cartChanged,
            'seller' => $this->repository->sellerCart(),
        ]);
    }

    /**
     * Add New Item To Cart
     *
     * @param Request $request
     * @return bool
     */
    public function store(Request $request)
    {
        $itemId = (int) $request->item; // item id
        $type = $request->type;
        $quantity = $request->quantity;
        if ($type == 'products') {
            $item = $this->productsRepository->getQuery()->where('type', $type)->where('id', $itemId)->first();
        } elseif ($type == 'food') {
            $item = $this->productMealsRepository->getQuery()->where('type', $type)->where('id', $itemId)->first();
        } else {
            return $this->badRequest(trans('cart.missingData.invalidType'));
        }

        $options = $request->options ?? [];
        if ($request->type == 'products') {
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

                        $optionValue = $itemOptionValues->where('id', $value)->first();
                        if ($quantity > $optionValue['quantity']) {
                            return $this->badRequest(trans('cart.Quantity-available-product', ['value' => $optionValue['quantity']]));
                        }
                    }
                }
            }
        }

        try {
            $this->repository->canNotAddToCartAndTypeError($request);

            $this->repository->create($request);

            try {
                [$cart, $cartChanged] = $this->repository->getUpdatedCart($request->type);
            } catch (\Exception $exception) {
                return (env('APP_ENV') == 'dev') ? $this->badRequest($exception->getMessage() . $exception->getTraceAsString()) : $this->badRequest($exception->getMessage());
                // return $this->badRequest(trans('cart.missingData.' . $exception));
            }

            return $this->success([
                'success' => true,
                'cart' => $this->repository->wrap($cart),
            ]);
        } catch (\Throwable $th) {
            // return $this->badRequest(trans('cart.missingData.' . $th));
            return (env('APP_ENV') == 'dev') ? $this->badRequest($th->getMessage() . $th->getTraceAsString()) : $this->badRequest($th->getMessage());
        }
    }

    /**
     * Change Cart Item QUantity
     *
     * @param int $id
     * @param Request $request
     * @return Response|\Illuminate\Http\Response|string
     */
    public function update($id, Request $request)
    {
        $cartItem = CartItem::find((int) $id);

        if (!$cartItem /*|| !$this->repository->belongsTo($cartItem)*/) {
            return $this->badRequest('notFound');
        }

        try {
            $cart = $this->cartRepository->setQuantity($cartItem, $request->quantity);
            $cartChanged = [];
            [$cart, $cartChanged] = $this->repository->getUpdatedCart($request->type);

            return $this->success([
                'success' => true,
                'cart' => $this->repository->wrap($cart),
            ]);
        } catch (\Throwable $th) {
            // return $this->badRequest(trans('cart.missingData.' . $th));
            return (env('APP_ENV') == 'dev') ? $this->badRequest($th->getMessage() . $th->getTraceAsString()) : $this->badRequest($th->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($id, Request $request)
    {
        try {
            $this->repository->canNotAddToCartAndTypeError($request);
            $this->repository->checkDeleteCartItem($request);
        } catch (\Exception $exception) {
            return $this->badRequest($exception->getMessage());
        }

        $cartItem = CartItem::find((int) $id);

        if (!$cartItem /*|| !$this->repository->belongsTo($cartItem)*/) {
            return $this->badRequest('notFound');
        }

        $this->repository->deleteCartItem($cartItem);

        try {
            [$cart, $cartChanged] = $this->repository->getUpdatedCart($request->type);
        } catch (\Exception $exception) {
            return $this->badRequest($exception->getMessage());
        }

        return $this->success([
            'success' => true,
            'cart' => $this->repository->wrap($cart),
        ]);
    }

    /**
     * Flush customer cart
     *
     * @param Request $request
     * @return string
     */
    public function flush(Request $request)
    {
        $this->repository->flush(null, $request->subscription ?? false);

        return $this->success();
    }

    /**
     * Method removeCouponCode
     *
     * @return void
     */
    public function removeCouponCode(Request $request)
    {
        try {
            [$cart, $cartChanged] = $this->repository->getUpdatedCart($request->type);
            $this->repository->removeCoupon();

            return $this->success([
                'success' => true,
                'cart' => $this->repository->wrap($cart->refresh()),
            ]);
        } catch (\Exception $exception) {
            // return $this->badRequest(trans('cart.missingData.' . $exception));
            return $this->badRequest($exception->getMessage());
        }
    }
}
