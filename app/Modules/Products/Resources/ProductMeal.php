<?php

namespace App\Modules\Products\Resources;

class ProductMeal extends Product
{
    const WHEN_AVAILABLE = [
        'published', 'priceInSubscription', 'priceIncludesTax', 'hasRequiredOptions', 'imported', 'inSubscription', 'availableStock', 'maxQuantity',
        'minQuantity', 'totalViews', 'sales', 'images', 'name', 'description', 'nutritionalValue', 'model', 'slug', 'discount', 'discount.startDate',
        'discount.endDate', 'options', 'relatedProducts', 'discount.type', 'discount.value', 'isRated', 'rating', 'typeNutritionalValue', 'restaurant', 'type', 'storeManager', 'brand', 'specialDietGrams', 'specialDietPercentage', 'relatedProducts', 'unit','metaTag', 'KeyWords'
    ];

    /**
     * Method extend
     *
     * @param $request $request
     *
     * @return void
     */
    protected function extend($request)
    {
        // $hasMaxQuantity = true;

        if (!user() || user()->accountType() === 'customer') {
            if ($this->maxQuantity == 0) {
                // $this->set('maxQuantity',0);
                $hasMaxQuantity = false;
            }
        }
        $this->set('finalPriceText', trans('products.price', ['value' => $this->price]));

        // $this->set('hasMaxQuantity', $hasMaxQuantity);
    }
}
