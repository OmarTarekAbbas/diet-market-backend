<?php

namespace App\Modules\Restaurants\Models;

use HZ\Illuminate\Mongez\Managers\Database\MongoDB\Model;

class Restaurant extends Model
{
    /**
     * {@Inheritdoc}
     */
    const SHARED_INFO = ['id', 'name', 'logoText', 'logoImage', 'commercialRegisterImage', 'commercialRegisterNumber', 'workTimes', 'minimumOrders', 'city', 'delivery', 'published', 'location', 'categories', 'rating', 'totalRating', 'restaurantStatus', 'typeOfFoodRestaurant', 'deliveryValue', 'profitRatio', 'isClosed', 'isBusy', 'countItems'];
}
