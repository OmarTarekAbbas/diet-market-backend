<?php

namespace App\Modules\ShippingMethods\Controllers\Site;

use Illuminate\Http\Request;
use HZ\Illuminate\Mongez\Managers\ApiController;

class ShippingMethodsController extends ApiController
{
    /**
     * Repository name
     *
     * @var string
     */
    protected const REPOSITORY_NAME = 'shippingMethods';

    /**
     * {@inheritDoc}
     */
    public function index(Request $request)
    {
        $shippingAddressForCart = repo('cart')->getshippingAddressForCart();
        if ($shippingAddressForCart->shippingAddress && $shippingAddressForCart->shippingAddress['city']) {
            $city = $shippingAddressForCart->shippingAddress['city'];
            $city = $city['id'];
        }

        $listOnlySellers = [];
        if ($shippingAddressForCart->seller) {
            foreach (collect($shippingAddressForCart->seller) as $seller) {
                $listOnlySellers[] = $seller['city']['id'];
            }
        }

        $listOnlySku = [];
        if ($shippingAddressForCart->items) {
            foreach (collect($shippingAddressForCart->items) as $item) {
                $listOnlySku[] = $item['onlySku'];
            }
        }

        $options = [
            'city' => $city ?? null,
            'sellerCity' => $listOnlySellers ?? null,
            'skus' => $listOnlySku ?? null,
            'published' => true,
        ];

        $shoppings =  $this->repository->list($options);

        $listShoppings = [];
        foreach ($shoppings as $key => $shopping) {

            if ($shippingAddressForCart->shippingAddress && $shippingAddressForCart->shippingAddress['city']) {
                foreach (collect($shippingAddressForCart->seller) as $seller) {

                    $city = collect($shopping->cities ?? [])->where('city.id', (int)$shippingAddressForCart->shippingAddress['city']['id'])->where('sellerCity.id', (int)$seller['city']['id'])
                        ->first();
                }
                // dd($city);
                if ($city === null) {
                    continue;
                } else {
                    $listShoppings[] = $shopping;
                }
            }
        }

        return $this->success([
            'records' => $listShoppings,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function show($id, Request $request)
    {
        return $this->success([
            'record' => $this->repository->get($id),
        ]);
    }
}
