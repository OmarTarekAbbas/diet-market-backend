<?php

namespace App\Modules\Products\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class ProductMeal extends Model
{
    /**
     * {@inheritdoc}
     */
    const SHARED_INFO = [
        'id', 'name', 'slug',
        'images', 'hasRequiredOptions',
        'imported', 'price', 'finalPrice',
        'published',
        'inSubscription', 'priceInSubscription', 'purchaseRewardPoints', 'rewardPoints', 'specialDietGrams', 'specialDietPercentage', 'restaurant', 'type', 'description','options',
    ];

    /**
     * Method sharedInfo
     *
     * @return array
     */
    // public function sharedInfo(): array
    // {
    //     return [
    //         'id' => $this->id,
    //         'name' => $this->name,
    //         'slug' => $this->slug,
    //         'specialDietGrams' => $this->specialDietGrams,
    //         'specialDietPercentage' => $this->specialDietPercentage,
    //         'storeManager' => $this->storeManager,
    //         'dietTypes' => $this->dietTypes,
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
