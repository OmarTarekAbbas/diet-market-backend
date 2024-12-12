<?php

namespace App\Modules\Restaurants\Resources;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Modules\Cities\Resources\City;
use App\Modules\Categories\Resources\Category;
use HZ\Illuminate\Mongez\Managers\Resources\JsonResourceManager;
use App\Modules\TypeOfFoodRestaurant\Resources\TypeOfFoodRestaurant;

class Restaurant extends JsonResourceManager
{
    /**
     * Data that must be returned
     *
     * @const array
     */
    const DATA = ['id', 'logoText', 'workTimes', 'location', 'transaction', 'metaTag', 'KeyWords'];

    /**
     * String Data
     *
     * @const array
     */
    const STRING_DATA = [];

    /**
     * Boolean Data
     *
     * @const array
     */
    const BOOLEAN_DATA = ['published', 'delivery', 'isBusy', 'cashOnDelivery', 'closedDb'];

    /**
     * Integer Data
     *
     * @const array
     */
    const INTEGER_DATA = ['commercialRegisterNumber', 'minimumOrders', 'countItems', 'countProductsDiet'];

    /**
     * Float Data
     *
     * @const array
     */
    const FLOAT_DATA = ['rating', 'totalRating', 'deliveryValue', 'profitRatio', 'rateDelivery', 'TotalOrders', 'profitRatioRestaurant', 'profitRatioDiteMarket', 'cashOnDeliveryValue'];

    /**
     * Object Data
     *
     * @const array
     */
    const OBJECT_DATA = [];

    /**
     * Data that should be returned if exists
     *
     * @const array
     */
    const WHEN_AVAILABLE = ['name', 'logoText', 'address', 'logoImage', 'commercialRegisterImage', 'commercialRegisterNumber', 'workTimes', 'minimumOrders', 'city', 'delivery', 'published', 'slug', 'categories', 'typeOfFoodRestaurant', 'deliveryValue', 'rateDelivery', 'TotalOrders', 'profitRatioRestaurant', 'profitRatioDiteMarket', 'metaTag', 'KeyWords'];

    /**
     * Set that columns that will be formatted as dates
     * it could be numeric array or associated array to set the date format for certain columns
     *
     * @const array
     */
    const DATES = [];

    /**
     * Data that has multiple values based on locale codes
     * Mostly this is used with mongodb driver
     *
     * @const array
     */
    const LOCALIZED = ['name', 'logoText', 'slug'];

    /**
     * List of assets that will have a full url if available
     */
    const ASSETS = ['logoImage', 'commercialRegisterImage', 'cover'];

    /**
     * Data that will be returned as a resources
     *
     * i.e [city => CityResource::class],
     * @const array
     */
    const RESOURCES = [
        'city' => City::class,
    ];

    /**
     * Data that will be returned as a collection of resources
     *
     * i.e [cities => CityResource::class],
     * @const array
     */
    const COLLECTABLE = [
        'categories' => Category::class,
        'typeOfFoodRestaurant' => TypeOfFoodRestaurant::class,
    ];

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

    /**
     * {@inheritdoc}
     */
    protected function extend($request)
    {
        $this->isOpen();

        $this->set('minimumOrdersText', trans('products.price', ['value' => $this->minimumOrders]));
        // dd($this->delivery);
        if ($this->delivery == true) {
            $this->set('deliveryValueText', trans('products.price', ['value' => $this->deliveryValue]));
        } else {
            // dd($deliveryMenCost);
            $this->set('deliveryValueText', trans('products.price', ['value' => repo('settings')->getSetting('deliveryMen', 'deliveryMenCost')]));
        }

        if (config('app.type') === 'site') {
            if (request()->url() != url('api/login')) {
                $this->set('deliveryTime', trans('products.minutes', ['value' => $this->getDeliveryTime()]));
            }
        }
        // repo('restaurants')->getPublishedRestaurants($this->id);
        if ($this->transaction) {
            $this->set('TotalOrders', $this->transaction['totalOrder'] ?? 0);
            $this->set('profitRatioRestaurant', $this->transaction['totalRequired'] ?? 0);
            $this->set('profitRatioDiteMarket', $this->transaction['profitRatio'] ?? 0);
            $this->set('paySeller', $this->transaction['totalRequired'] ?? 0);
            $this->set('payDiet', $this->transaction['profitRatio'] ?? 0);
        }

        if (user() && user()->accountType() === 'customer') {
            $restaurants = repo('restaurants')->getQuery()->get();
            foreach ($restaurants as $restaurant) {
                repo('restaurants')->noFoodInRestaurants($restaurant['id']);
            }
        }
    }

    /**
     * Method getDeliveryTime
     *
     * @return void
     */
    public function getDeliveryTime()
    {
        $customer = repo('customers')->getCustomer();
        if ($customer->location) {
            $responseLocation = $this->getMinutes($customer->location, $this->location);
            $decodeResponseLocation = json_decode($responseLocation, true); // Set second argument as TRUE
            $rowResponseLocation = $decodeResponseLocation['rows'];
            // dd($rowResponseLocation);
            foreach ($rowResponseLocation as $row) {
                $elementMinutes = $row['elements'];
                foreach ($elementMinutes as $minute) {
                    $getSecond = $minute['duration']['value'] ?? 0;
                }
            }
            $minutes = round($getSecond / 60);

            return $minutes;
        } else {
            return 0;
        }
    }

    /**
     * @param $request
     * @param $location
     * @param bool $twoLocation
     * @return float|int
     */
    public static function getMinutes($user, $location)
    {
        $key = KEY_GOOGLE_MAB;
        $userLocation = [
            $user['coordinates'][0],
            $user['coordinates'][1],
        ];

        if (Arr::has($location, 'coordinates')) {
            $location = $location['coordinates'];
        } else {
            $location = [$location['lat'], $location['lng']];
        }

        $URL = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$location[0]},{$location[1]}&destinations={$userLocation[0]},{$userLocation[1]}&key={$key}";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);

        return $response;
    }

    /**
     * Method isOpen
     *
     * @return void
     */
    public function isOpen()
    {
        $currentDay = Carbon::now()->locale('en')->format('l');
        $workingDays = $this->workTimes ?? [];

        $time = Carbon::now();

        foreach ($workingDays as $workingDay) {
            if (Str::lower($currentDay) == Str::lower($workingDay['day'])) {
                $this->set('openToday', [
                    'open' => $workingDay['open'],
                    'close' => $workingDay['close'],
                    'day' => $workingDay['day'],
                    'available' => $workingDay['available'],
                ]);
                if ($workingDay['close'] == '00:00') {
                    $workingDay['close'] = '23:59';
                }
                if ($workingDay['available'] == 'yes' && $time->between($workingDay['open'], $workingDay['close'])) {
                    $this->set('isClosed', false);
                    if (user() && user()->accountType() === 'customer') {
                        repo('restaurants')->makeClosed($this['id']);
                    }
                } else {
                    $this->set('isClosed', true);
                    if (user() && user()->accountType() === 'customer') {
                        repo('restaurants')->makeOpen($this['id']);
                    }
                };
            }
        }
    }
}
