<?php

namespace App\Modules\Products\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Product extends Model
{
    /**
     * {@inheritdoc}
     */
    const SHARED_INFO = [
        'id', 'name', 'slug',
        'images', 'hasRequiredOptions', 'discount',
        'unit', 'minQuantity', 'maxQuantity',
        'imported', 'price', 'finalPrice',
        'published',
        'availableStock', 'inSubscription', 'priceInSubscription', 'purchaseRewardPoints', 'rewardPoints', 'specialDietGrams', 'specialDietPercentage', 'storeManager', 'brand', 'restaurant', 'type', 'description', 'totalRating', 'rating', 'width', 'sku', 'skuSeller',
    ];

    /**
     * Method sharedInfo
     *
     * @return array
     */
    // public function sharedInfoCart(): array
    // {
    //     return [
    //         'id' => $this->id,
    //         'name' => $this->name,
    //         'slug' => $this->slug,
    //         'specialDietGrams' => $this->specialDietGrams,
    //         'specialDietPercentage' => $this->specialDietPercentage,
    //         'storeManager' => $this->storeManager,
    //         'brand' => $this->brand,
    //         'images' => $this->images,
    //         'hasRequiredOptions' => $this->hasRequiredOptions,
    //         'discount' => $this->discount,
    //         'unit' => $this->unit,
    //         'minQuantity' => $this->minQuantity,
    //         'maxQuantity' => $this->maxQuantity,
    //         'imported' => $this->imported,
    //         'price' => $this->price,
    //         'finalPrice' => $this->finalPrice,
    //         'published' => $this->published,
    //         'availableStock' => $this->availableStock,
    //         'inSubscription' => $this->inSubscription,
    //         'priceInSubscription' => $this->priceInSubscription,
    //         'purchaseRewardPoints' => $this->purchaseRewardPoints,
    //         'rewardPoints' => $this->rewardPoints,
    //         'restaurant' => $this->restaurant,
    //         'type' => $this->type,
    //         'description' => $this->description,
    //         'totalRating' => $this->totalRating,
    //         'rating' => $this->rating,
    //         'options' => $this->options,
    //     ];
    // }

    /**
     * {@inheritdoc}
     */
    protected $dates = [
        'discount.startDate',
        'discount.endDate',
    ];
}
